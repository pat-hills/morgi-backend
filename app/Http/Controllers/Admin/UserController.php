<?php


namespace App\Http\Controllers\Admin;


use App\Enums\RookieEnum;
use App\FaceRecognition\AwsFaceRekognitionFacesUtils;
use App\FaceRecognition\AwsFaceRekognitionSearchUtils;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\GlobalGroup;
use App\Models\Leader;
use App\Models\PasswordReset;
use App\Models\PasswordResetHistory;
use App\Models\PaymentPlatformRookie;
use App\Models\PaymentPlatformRookieHistory;
use App\Models\Photo;
use App\Models\PhotoHistory;
use App\Models\ProfileAlert;
use App\Models\ProfileAlertCode;
use App\Models\Rookie;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserBlockedHistory;
use App\Models\UserDescriptionHistory;
use App\Models\UserIdentityDocument;
use App\Models\UserIdentityDocumentHistory;
use App\Models\UserIdentityDocumentPhoto;
use App\Models\UserLoginHistory;
use App\Models\UserNote;
use App\Models\UserPath;
use App\Models\UserRejectHistory;
use App\Models\Video;
use App\Models\VideoHistory;
use App\Orazio\OrazioHandler;
use App\Services\Mailer\Mailer;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use App\Utils\Utils;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function redirect;

class UserController extends Controller
{
    private $users_table = 'users';
    private $rookies_table = 'rookies';
    private $user_identity_document_history_table = 'users_identities_documents_histories';
    private $leaders_table = 'leaders';
    private $payments_platform_table = 'payments_platforms';
    private $countries_table = 'countries';
    private $groups_table = 'users_ab_groups';
    private $payment_platform_rookie_table = 'payments_platforms_rookies';
    private $payment_platform_rookie_history_table = 'payments_platforms_rookies_histories';
    private $users_reject_history_table = 'users_rejected_histories';
    private $users_notes_table = 'users_notes';
    private $path_table = 'paths';
    private $users_path_table = 'users_paths';
    private $users_blocked_history = 'users_blocked_histories';
    private $spenders_group_table = 'spenders_groups';

    private function getQueryForUsersTable($type): Builder
    {
        if ($type == 'leaders') {
            $query = Leader::query();
            $to_join = $this->leaders_table;
        } elseif ($type == 'rookies') {
            $query = Rookie::query();
            $to_join = $this->rookies_table;
        }

        return $query->join($this->users_table, "$to_join.id", '=', "$this->users_table.id")
            ->leftJoin($this->user_identity_document_history_table, function ($leftJoin) use ($to_join) {
                $leftJoin->on("$this->user_identity_document_history_table.user_id", '=', "$to_join.id")
                    ->where("$this->user_identity_document_history_table.created_at", '=', DB::raw("(select created_at from $this->user_identity_document_history_table where $this->user_identity_document_history_table.user_id = $to_join.id order by created_at desc limit 1)"));
            })
            ->where("$this->users_table.status", '!=', 'deleted');

    }

    public function getCounterForTablesTabs($type): array
    {
        $data = [];

        $type_map = ($type === 'rookies') ? 'rookie' : 'leader';

        if ($type == 'rookies') {

            $data['pending'] = $this->getQueryForUsersTable($type)
                ->where("$this->users_table.status", '=', 'pending')
                ->count();

            $data['new'] = $this->getQueryForUsersTable($type)
                ->where("$this->users_table.status", '=', 'new')
                ->count();

            $data['favourite_rookies'] = $this->getQueryForUsersTable($type)
                ->where("rookies.is_favourite", true)
                ->count();
        }

        $data['updated'] = $this->getQueryForUsersTable($type)
            ->where("$this->users_table.admin_check", '=', 1)
            ->where("$this->users_table.status", '=', 'accepted')
            ->count();

        $data['id'] = $this->getQueryForUsersTable($type)
            ->where("$this->user_identity_document_history_table.status", '=', 'pending')
            ->count();

        $data['reject'] = $this->getQueryForUsersTable($type)
            ->where("$this->users_table.status", '=', 'rejected')
            ->count();

        $data['blocked'] = $this->getQueryForUsersTable($type)
            ->where("$this->users_table.status", '=', 'blocked')
            ->count();

        $data['all'] = $this->getQueryForUsersTable($type)
            ->where("$this->users_table.type", '=', $type_map)
            ->count();

        $data['accepted'] = $this->getQueryForUsersTable($type)
            ->where("$this->users_table.status", '=', 'accepted')
            ->count();

        $data['updated_username'] = $this->getQueryForUsersTable($type)
            ->where("$this->users_table.updated_username", 1)
            ->whereIn("$this->users_table.status",  ['accepted', 'new', 'pending'])
            ->count();


        return $data;
    }

    public function getUsersByType(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "username" => 'string|nullable',
            "email" => 'string|nullable',
            "created" => 'in:ASC,DESC',
            "hours" => 'in:ASC,DESC',
            "page" => 'integer|gt:0',
            "limit" => 'integer|gt:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $url = explode('/', $request->getPathInfo());
        $first_prefix = $url[1];
        $endpoint = end($url);

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('limit') ? $request->get('limit') : 25;

        $key = "$this->users_table.created_at";
        $direction = 'DESC';


        if ($request->has('hours') && !empty($request->hours)) {
            $key = 'updated_awaiting';
            $direction = strtoupper($request->hours);
        }

        $query = $this->getQueryForUsersTable($first_prefix);


        switch ($endpoint) {

            case 'updated_username':
                $query->where("$this->users_table.updated_username", 1)
                    ->whereIn("$this->users_table.status", ['accepted', 'new', 'pending']);
                break;

            case 'favourite_rookies':
                $query->where("rookies.is_favourite", true);
                break;

            case 'pending':
                $query->where("$this->users_table.status", '=', 'pending');
                $key = 'updated_awaiting';
                break;

            case 'new_accounts':
                $query->where("$this->users_table.status", '=', 'new');
                $key = 'updated_awaiting';
                break;

            case 'updated_accounts':
                $query->where("$this->users_table.admin_check", '=', 1)
                    ->where("$this->users_table.status", '=', 'accepted');
                $key = 'updated_awaiting';
                break;

            case 'pending_id_verification':
                $query->where("$this->user_identity_document_history_table.status", '=', 'pending');
                $key = 'updated_awaiting';
                break;

            case 'rejected_accounts':
                $query->where("$this->users_table.status", '=', 'rejected');
                break;

            case 'blocked_accounts':
                $query->where("$this->users_table.status", '=', 'blocked');
                break;

            case 'accepted':
                $query->where("$this->users_table.status", '=', 'accepted');
                break;
        }

        if ($request->has('created') && !empty($request->created)) {
            $key = "$this->users_table.created_at";
            $direction = $request->created;
        }

        if ($request->has('username') && !empty($request->username)) {
            $query->where("$this->users_table.username", 'LIKE', '%' . $request->username . '%');
        }

        if ($request->has('email') && !empty($request->email)) {
            $query->where("$this->users_table.email", 'LIKE', '%' . $request->email . '%');
        }

        $table = $first_prefix . '_table';

        $now = Carbon::now(config('app.timezone'))->toDateTimeString();

        $query->select([
            "" . $this->$table . ".*",
            "$this->users_table.email",
            "$this->users_table.admin_check",
            "$this->users_table.status",
            "$this->users_table.username",
            DB::raw("(select status from $this->user_identity_document_history_table where $this->user_identity_document_history_table.user_id = $this->users_table.id order by $this->user_identity_document_history_table.created_at desc limit 1) as doc_status")
        ])
            ->selectRaw("(
            CASE WHEN $this->users_table.status = 'new' THEN TIMESTAMPDIFF(minute, $this->users_table.email_verified_at, '$now')
            WHEN $this->users_table.status = 'pending' THEN TIMESTAMPDIFF(minute, $this->users_table.created_at, '$now')
            WHEN $this->users_table.admin_check = 1 THEN TIMESTAMPDIFF(minute, $this->users_table.updated_at, '$now')
            ELSE 0 END) AS updated_awaiting");

        $count = $query->count();

        $users = $query->groupBy("" . $this->$table . ".id")
            ->orderByRaw("$key $direction")
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get()
            ->append([]);

        $current = $page;
        $max_pages = ceil($count / $limit);

        $data = [];
        $data['page'] = $page;
        $data['limit'] = $limit;
        $data['username'] = $request->has('username') ? $request->get('username') : null;
        $data['email'] = $request->has('email') ? $request->get('email') : null;
        $data['key'] = $key;
        $data['direction'] = $direction;


        return view("admin.admin-pages.users_tables.show", compact('users', 'count', 'max_pages', 'data', 'current', 'limit', 'first_prefix'));
    }

    public function getUserByData(Request $request)
    {
        if ($request->has('data') && !empty($request->data)) {
            $data = $request->data;

            $validator = Validator::make($request->all(), [
                "username" => 'string|nullable',
                "email" => 'string|nullable',
                "user_type" => 'string|nullable',
                "page" => 'integer|gt:0',
                "limit" => 'integer|gt:0'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('limit') ? $request->get('limit') : 25;

            $select = [
                'users.id',
                'users.type',
                'users.username',
                'users.email',
                'users.type'
            ];

            $query = User::query();
            $query->select($select);

            if(is_numeric($data)){

                $query->where('users.id', $data);
            }else {

                $query
                    ->leftJoin('rookies', 'rookies.id', '=', 'users.id')
                    ->leftJoin('leaders_payments', 'leaders_payments.leader_id', '=', 'users.id')
                    ->where(function ($query) use ($data) {
                        $query
                            ->where('users.username', 'like', $data . '%')
                            ->orWhere('users.email', 'like', $data . '%')
                            ->orWhereRaw("CONCAT(first_name, ' ',last_name) LIKE '%$data%'");
                    });
            }

            if ($request->has('user_type')) {
                $query->where('users.type', $request->user_type);
            }else{
                $query->whereIn('users.type', ['rookie', 'leader']);
            }

            if ($request->has('username')) {
                $query->where('users.username', 'like', '%'. $request->username . '%');
            }

            if ($request->has('email')) {
                $query->where('users.email', 'like', $request->email . '%');
            }

            $query->groupBy('users.id');

            $count = $query->count();

            $users = $query->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get()
                ->append([]);

            $filters = [];
            $filters['page'] = $page;
            $filters['limit'] = $limit;
            $filters['username'] = $request->has('username') ? $request->get('username') : null;
            $filters['email'] = $request->has('email') ? $request->get('email') : null;
            $filters['type'] = $request->has('user_type') ? $request->get('user_type') : null;

            $current = $page;
            $max_pages = ceil($count / $limit);

            return view('admin.admin-pages.users_tables.index', compact('users', 'data', 'current', 'max_pages', 'limit', 'filters'));
        }

        return view('admin.admin-pages.users_tables.search_user');
    }

    public function editUserProfile($id)
    {
        $user = User::query()->find($id);
        if (!isset($user)) {
            return redirect()->back()->with(['fail' => "User #$id not found!"]);
        }

        $main_payment_method = null;
        $payments_history = null;

        $active_leaders = null;
        $past_leaders = null;

        $active_rookies = null;
        $past_rookies = null;
        $tot_bought = null;
        $global_id_data = null;

        $query_totals = Transaction::query()
            ->where($user->type . '_id', $id)->get();


        if ($user->type == 'rookie') {

            $user = User::query()->join("$this->rookies_table", "$this->users_table.id", '=', "$this->rookies_table.id")
                ->where("$this->users_table.id", $id)
                ->leftJoin($this->countries_table, "$this->rookies_table.country_id", '=', "$this->countries_table.id")
                ->leftJoin($this->groups_table, "$this->users_table.group_id", '=', "$this->groups_table.id")
                ->select("$this->users_table.*",
                    "$this->users_table.description as user_description",
                    "$this->rookies_table.*",
                    "$this->countries_table.name as country_name",
                    "$this->groups_table.name as group_name")->first();

            $payment_query = PaymentPlatformRookie::query()
                ->where('rookie_id', '=', $user->id)
                ->join($this->payments_platform_table, "$this->payment_platform_rookie_table.payment_platform_id", '=', "$this->payments_platform_table.id")
                ->select(
                    "$this->payment_platform_rookie_table.*",
                    "$this->payments_platform_table.name as payment_platform_name"
                )->get();

            $main_payment_method = $payment_query->where('main', '=', 1)->first() ?? $payment_query->first();

            $subscriptions = Subscription::query()->where('rookie_id', $user->id)->whereNotNull('last_subscription_at')->get();
            $active_leaders = $subscriptions->where('status', 'active')->groupBy('leader_id')->count();
            $past_leaders = $subscriptions->groupBy('leader_id')->count();

            $payments_history = PaymentPlatformRookieHistory::query()
                ->join("$this->payments_platform_table", "$this->payments_platform_table.id", '=', "$this->payment_platform_rookie_history_table.payment_platform_id")
                ->where("$this->payment_platform_rookie_history_table.rookie_id", $user->id)
                ->select(
                    "$this->payment_platform_rookie_history_table.updated_at",
                    "$this->payment_platform_rookie_history_table.is_reset",
                    "$this->payments_platform_table.name")
                ->get();

            $tot_mm_packages_bought = null;

            $dollars_earned = $query_totals->whereIn('type', ['gift', 'chat'])
                ->whereNull('refund_type')
                ->sum('dollars');

            $dollars_tot = number_format($dollars_earned, 2, '.', ' ');


        } elseif ($user->type == 'leader') {

            $user = User::query()->join($this->leaders_table, "$this->users_table.id", '=', "$this->leaders_table.id")
                ->where("$this->users_table.id", $id)
                ->leftJoin($this->groups_table, "$this->users_table.group_id", '=', "$this->groups_table.id")
                ->join($this->spenders_group_table, "$this->leaders_table.spender_group_id", '=', "$this->spenders_group_table.id")
                ->select(
                    "$this->users_table.*", "$this->users_table.description as user_description", "$this->leaders_table.*", "$this->groups_table.name as group_name",
                    "$this->spenders_group_table.name as spender_group_name"
                )
                ->first();

            $subscriptions = Subscription::query()->where('leader_id', $user->id)->whereNotNull('last_subscription_at')->get();
            $active_rookies = $subscriptions->where('status', 'active')->groupBy('rookie_id')->count();
            $past_rookies = $subscriptions->groupBy('rookie_id')->count();

            $tot_mm_packages_bought = Transaction::query()->where('type', '=', 'bought_micromorgi')->where('leader_id', '=', $user->id)->count();

            $dollars = $query_totals->whereIn('type', ['gift', 'bought_micromorgi'])
                ->whereNull('refund_type')
                ->sum('dollars');

            $dollars_tot = number_format($dollars, 2, '.', ' ');

            if($user->global_id){

                $select = [
                    'leaders.id',
                    'leaders_ccbill_data.paymentAccount as ucid',
                    'leaders_ccbill_data.firstName as first_name',
                    'leaders_ccbill_data.lastName as last_name',
                    'leaders_ccbill_data.billingCountry as location',
                    'leaders_ccbill_data.email as billing_email'
                ];


                $query_global = Leader::query()
                    ->select($select)
                    ->where('leaders.id', '!=', $id)
                    ->whereNotNull('global_id')
                    ->where('global_id', $user->global_id)
                    ->leftJoin('leaders_payments', 'leaders_payments.leader_id', '=', 'leaders.id')
                    ->leftJoin('leaders_ccbill_data', 'leaders_ccbill_data.leader_id', 'leaders.id');

                $global_id_sum = $query_global->sum('dollar_amount');
                $query_global->groupBy('leaders.id');
                $global_count = count($query_global->get());

                $global_id_data = [
                    'id' => GlobalGroup::find($user->global_id)->global_id,
                    'count' => $global_count,
                    'dollars' => $global_id_sum,
                    'users' => $query_global->get()
                ];

            }

        } else {
            $user = null;
            return redirect()->back()->with(['fail' => 'Something wrong']);
        }

        $reports = Complaint::query()->where('user_reported', $user->id)->get();

        $rejected_history = UserRejectHistory::query()
            ->select("$this->users_reject_history_table.*", "$this->users_table.username")
            ->join("$this->users_table", "$this->users_reject_history_table.admin_id", '=', "$this->users_table.id")
            ->where("$this->users_reject_history_table.user_id", $user->id)
            ->orderBy('id', 'DESC')
            ->get();

        $micromorgi_tot = $query_totals->where('type', 'chat')
            ->whereNull('refund_type');


        $morgi_tot = $query_totals->where('type', 'gift')
            ->whereNull('refund_type');

        if ($user->type == 'leader') {
            $micromorgi_tot = $micromorgi_tot->sum('micromorgi');
            $morgi_tot = $morgi_tot->sum('morgi');
        } elseif ($user->type == 'rookie') {
            $micromorgi_tot = $micromorgi_tot->sum('micromorgi');
            $morgi_tot = $morgi_tot->sum('morgi');
        }

        $notes = UserNote::query()
            ->select('*', DB::raw("$this->users_notes_table.created_at as created_at"))
            ->where('user_id', $user->id)
            ->leftJoin($this->users_table, "$this->users_notes_table.admin_id", '=', "$this->users_table.id")
            ->orderBy("$this->users_notes_table.id", 'DESC')
            ->get();


        $verified_data = UserIdentityDocument::query()->where('user_id', $user->id)->first();

        $history_id = UserIdentityDocumentHistory::query()
            ->where('user_id', $user->id)
            ->join("$this->users_table", "$this->users_table.id", '=', "$this->user_identity_document_history_table.admin_id")
            ->select("$this->user_identity_document_history_table.*", "$this->users_table.username")
            ->orderBy('id', 'DESC')->get();

        $psw_history = PasswordResetHistory::query()->where('user_id', $user->id)->orderBy('created_at', 'DESC')->get();

        $updates = $user->updated_fields;

        $new_description = null;
        $photo_uploaded = null;
        $video_uploaded = null;
        $document_to_verify = null;

        $only_id = FALSE;

        if (count(array_filter($updates)) === 1 && $updates['id']) {
            $only_id = TRUE;
        }

        if ($updates['bio']) {
            $new_description = UserDescriptionHistory::query()->where('user_id', $user->id)->orderBy('id', 'DESC')->first();
        }
        if ($updates['photo']) {
            $photo_uploaded = PhotoHistory::query()->where('user_id', $user->id)->where('status', '=', 'to_check')->get();
        }
        if ($updates['video']) {
            $video_uploaded = VideoHistory::query()->where('user_id', $user->id)->where('status', '=', 'to_check')->get();
        }
        if ($updates['id']) {
            $document_to_verify = UserIdentityDocumentHistory::query()->where('user_id', $user->id)->latest('id')->first();
        }

        $check_updates = array_filter($updates);
        if (empty($check_updates)) {
            if ($user->admin_check == 1) {
                $user->update(['admin_check' => 0]);
            }
        }

        $descriptions_history = UserDescriptionHistory::query()->where('user_id', $user->id)->orderBy('id', 'DESC')->get();

        $all_paths = UserPath::query()
            ->where('user_id', $user->id)
            ->join("$this->path_table", "$this->users_path_table.path_id", '=', "$this->path_table.id")
            ->select("$this->path_table.name as path_name")->get();

        $paths1 = $all_paths->where("$this->users_path_table.is_subpath", 0);
        $subpaths1 = $all_paths->where("$this->users_path_table.is_subpath", 1);

        $paths = [];
        foreach ($paths1 as $path) {
            $paths[] = $path->path_name;
        }

        $subpaths = [];
        foreach ($subpaths1 as $sub) {
            $subpaths[] = $sub->path_name;
        }


        $photos = Photo::query()->where('user_id', $user->id)->get();
        $videos = Video::query()->where('user_id', $user->id)->get();

        $query_login = UserLoginHistory::query()->where('user_id', $user->id)->orderBy('created_at', 'DESC');

        $login_history = $query_login->get();
        $last_login = $query_login->first();
        $signup = $query_login->where('is_signup_values', '=', 1)->first();

        $blocked_history = UserBlockedHistory::query()->where('user_id', $user->id)
            ->leftJoin("$this->users_table", "$this->users_blocked_history.admin_id", '=', "$this->users_table.id")
            ->select("$this->users_blocked_history.*", (new User())->getTable() . '.username')
            ->orderBy("$this->users_blocked_history.id", 'DESC')->get();

        return view('admin.admin-pages.user_profile.' . $user->type . '.edit_' . $user->type,
            compact('user', 'reports', 'micromorgi_tot', 'morgi_tot', 'dollars_tot',
                'main_payment_method', 'active_leaders', 'past_leaders', 'active_rookies', 'past_rookies',
                'notes', 'verified_data', 'history_id', 'new_description', 'psw_history',
                'descriptions_history', 'photos', 'last_login', 'login_history', 'paths',
                'signup', 'blocked_history', 'photo_uploaded', 'video_uploaded', 'videos',
                'tot_mm_packages_bought', 'document_to_verify', 'subpaths', 'payments_history', 'rejected_history',
                'only_id', 'global_id_data'));
    }

    public function resetRookiePayment(Request $request, $user_id)
    {

        $data = $request->only(['id_payment']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'id_payment' => 'required|integer|gt:0',
            'user_id' => 'required|integer|gt:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $payment_rookie = PaymentPlatformRookie::query()->where('id', $request->id_payment)->first();

        $payment_rookie->delete();

        $history_payment = PaymentPlatformRookieHistory::query()->where('payments_platforms_rookies_id', $request->id_payment)
            ->first();

        $history_payment->update(['payments_platforms_rookies_id' => null, 'is_reset' => 1]);

        return redirect()->back()->with(['success' => 'Payment successfully deleted']);
    }

    public function actionDocumentVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document_id' => 'required|integer|gt:0',
            'front_action' => 'required|in:approved,rejected',
            'back_action' => 'in:approved,rejected',
            'selfie_action' => 'required|in:approved,rejected',
            'front_decline_reason' => 'required_if:front_action,==,rejected',
            'back_decline_reason' => 'required_if:back_action,==,rejected',
            'selfie_decline_reason' => 'required_if:selfie_action,==,rejected'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $document = UserIdentityDocumentHistory::query()->find($request->document_id);

        UserIdentityDocumentPhoto::query()->where('id', $document->front_path_id)->update([
            'admin_id' => Auth::id(),
            'status' => $request->front_action,
            'decline_reason' => ReasonUtils::ALL_REASON[$request->front_decline_reason] ?? ReasonUtils::DECLINE_IDENTITY_DOCUMENT[$request->front_decline_reason] ?? $request->front_decline_reason
        ]);

        if (isset($document->back_path_id)) {
            UserIdentityDocumentPhoto::query()->where('id', $document->back_path_id)->update([
                'admin_id' => Auth::id(),
                'status' => $request->back_action,
                'decline_reason' => ReasonUtils::ALL_REASON[$request->back_decline_reason] ?? ReasonUtils::DECLINE_IDENTITY_DOCUMENT[$request->back_decline_reason] ?? $request->back_decline_reason
            ]);
        }

        UserIdentityDocumentPhoto::query()->where('id', $document->selfie_path_id)->update([
            'admin_id' => Auth::id(),
            'status' => $request->selfie_action,
            'decline_reason' => ReasonUtils::ALL_REASON[$request->selfie_decline_reason] ?? ReasonUtils::DECLINE_IDENTITY_DOCUMENT[$request->selfie_decline_reason] ?? $request->selfie_decline_reason
        ]);

        if (in_array('rejected', $request->only(['front_action', 'back_action', 'selfie_action']))) {

            if ($request->front_action === 'rejected') {
                NotificationUtils::sendNotification($document->user_id, 'front_id_card_rejected', now(), [
                    'reason' => ReasonUtils::NOTIFICATION_REASON_DECLINE_IDENTITY_DOCUMENT[$request->front_decline_reason] ?? $request->front_decline_reason
                ]);
            }

            if (isset($request->back_action) && $request->back_action === 'rejected') {
                NotificationUtils::sendNotification($document->user_id, 'back_id_card_rejected', now(), [
                    'reason' => ReasonUtils::NOTIFICATION_REASON_DECLINE_IDENTITY_DOCUMENT[$request->back_decline_reason] ?? $request->back_decline_reason
                ]);
            }

            if ($request->selfie_action === 'rejected') {
                NotificationUtils::sendNotification($document->user_id, 'selfie_id_card_rejected', now(), [
                    'reason' => ReasonUtils::NOTIFICATION_REASON_DECLINE_IDENTITY_DOCUMENT[$request->selfie_decline_reason] ?? $request->selfie_decline_reason
                ]);
            }

            $document->update([
                'admin_id' => Auth::id(),
                'status' => 'rejected'
            ]);

            return redirect()->back()->with(['success' => 'Document rejected!']);
        }

        $document->update([
            'admin_id' => Auth::id(),
            'status' => 'approved'
        ]);

        UserIdentityDocument::query()->create([
            'user_id' => $document->user_id,
            'front_path_id' => $document->front_path_id,
            'back_path_id' => $document->back_path_id ?? null,
            'selfie_path_id' => $document->selfie_path_id
        ]);

        $pending_id_code_id = ProfileAlertCode::query()->where('code', 'PA_ROOKIE_003')->first()->id;

        ProfileAlert::query()->where('user_id', $document->user_id)->where('code_id', $pending_id_code_id)->delete();
        NotificationUtils::sendNotification($document->user_id, 'verified_id_card', now());

        return redirect()->back()->with(['success' => 'ID successfully updated']);
    }

    public function editIsFavourite(Request $request, $user_id)
    {
        $rookie = Rookie::query()->find($user_id);
        if(!isset($rookie)){
            return redirect()->back()->with(['fail' => "Rookie not found"]);
        }

        if($request->is_favourite==='true'){
            $rookie->update(['is_favourite' => true]);
            return redirect()->back()->with(['success' => "Rookie is now favourite!"]);
        }

        $rookie->update(['is_favourite' => false]);
        return redirect()->back()->with(['success' => "Rookie is no longer favourite!"]);
    }

    public function addNoteToUser(Request $request, $id)
    {
        $data = $request->only(['new_note']);
        $data['user_id'] = $id;

        $validator = Validator::make($data, [
            'new_note' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $user = User::query()->find($id);
        if (!isset($user)) {
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        $created_note = UserNote::query()->create(['user_id' => $id, 'admin_id' => Auth::id(), 'note' => $request->new_note]);

        if (!empty($created_note)) {
            return redirect()->back()->with(['success' => 'Note created with id: ' . $created_note->id]);
        }

        return redirect()->back()->with(['fail' => 'Creation note failed']);
    }

    public function blockUserById(Request $request, $id)
    {
        $data = $request->only(['reasonBlockUser']);
        $data['user_id'] = $id;

        $validator = Validator::make($data, [
            'user_id' => 'required|integer',
            'reasonBlockUser' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $user = User::query()->find($id);
        if (!isset($user)) {
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        if ($user->status == 'blocked') {
            return redirect()->back()->with(['info' => 'Already blocked!']);
        }

        if (!empty($user)) {

            $user->createUserStatusHistory('blocked', Auth::user()->username, $request->reasonBlockUser);

            $user->update(['status' => 'blocked']);

            UserBlockedHistory::query()->create([
                'user_id' => $user->id,
                'admin_id' => Auth::id(),
                'reason' => $request->reasonBlockUser,
            ]);

            return redirect()->back()->with(['success' => "User blocked successfully"]);
        }

        return redirect()->back()->with(['fail' => "Couldn't block the user"]);
    }

    public function reActiveUserById($id)
    {

        $user = User::query()->find($id);
        if (!isset($user)) {
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        if ($user->status == 'untrusted') {
            if (array_key_exists('status', $user->id_verified)) {
                if ($user->id_verified['card_id_status'] == 'pending') {
                    return redirect()->back()->with(['fail' => 'Before proceed confirm the ID card']);
                } elseif ($user->id_verified['card_id_status'] != 'approved') {
                    return redirect()->back()->with(['fail' => 'Operation not permitted']);
                }
            }
        }

        $user->createUserStatusHistory('accepted', Auth::user()->username);

        if($leader = Leader::query()->find($id)){
            if($leader->internal_status){
                $leader->update(['internal_status' => null]);
            }
        }

        $user->update(['status' => 'accepted', 'decline_reason' => null, 'admin_id' => Auth::id()]);


        return redirect()->back()->with(['message' => 'User Re-activated']);
    }

    public function sendResetPasswordLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'exists:users,email']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user->status === 'deleted') {
            return redirect()->back()->with(['fail' => 'The account was deleted']);
        }

        $passwordReset = PasswordReset::query()->updateOrCreate(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => md5(uniqid('', true))
            ]
        );

        PasswordResetHistory::query()->create([
            'user_id' => $user->id,
            'email' => $request->email,
            'ip_address' => Utils::getRealIp($request)
        ]);

        try {
            $recovery_link = env('FRONTEND_URL') . env('PASSWORD_RESET_FRONTEND_PATH') . $passwordReset->token;
            Mailer::create($user)->setMisc(['recovery_link' => $recovery_link])->setTemplate('PASSWORD_RESET')->sendAndCreateUserEmailSentRow();
        } catch (Exception $e) {
            return redirect()->back()->with(['fail' => $e->getMessage()]);
        }

        return redirect()->back()->with(['success' => 'Send password reset link successfully!']);
    }

    public function updateBeautyIntelligenceLikelyScore(Request $request, $user_id){

        $data = $request->only(['beauty_score', 'intelligence_score', 'likely_score']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'user_id' => ['required', 'exists:users,id'],
            'beauty_score' =>  ['integer', 'min:0', 'max:10'],
            'intelligence_score' =>  ['integer', 'min:0', 'max:10'],
            'likely_score' =>  ['integer', 'min:0', 'max:3']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = User::find($user_id);
        if(!isset($user) OR $user->type != 'rookie'){
            return redirect()->back()->with(['fail' => 'Rookie not found']);
        }

        if($user->status != 'accepted'){
            return redirect()->back()->with(['fail' => 'Cannot add score to non accepted rookies']);
        }

        $user = Rookie::find($user_id);
        $note = [];
        if($request->beauty_score != $user->beauty_score){
            $note[] = "the beauty score to $request->beauty_score";
        }
        if($request->intelligence_score != $user->intelligence_score){
            $note[] = "the intelligence score to $request->intelligence_score";
        }
        if($request->likely_score != $user->likely_score){
            $note[] = "the likely score to ". RookieEnum::LIKELY_SCORE[$request->likely_score];
        }

        if(empty($note)){
            return redirect()->back()->with(['info' => "The score didn't change"]);
        }

        $user->update([
            'beauty_score' => $request->beauty_score,
            'intelligence_score' => $request->intelligence_score,
            'likely_receive_score' => $request->likely_score
        ]);

        UserNote::query()->create([
            'user_id' => $user->id,
            'admin_id' => Auth::id(),
            'note' => "SYSTEM: ". $request->user()->username . " set " . implode(" and ", $note),
        ]);

        return redirect()->back()->with(['success' => "updated " . implode(" and ", $note)]);
    }

    public function updateUserBirthDate(Request $request, $user_id){

        $data = $request->only(['birthdate']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'birthdate' => ['required', 'date', 'before_or_equal:'.\Carbon\Carbon::now()->subYears(18)->format('Y-m-d')],
            'user_id' => ['required', 'exists:users,id', 'exists:rookies,id']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = Rookie::query()->find($user_id);

        $user->update(['birth_date' => $request->birthdate]);

        NotificationUtils::sendNotification($user->id, 'rookie_birth_date_changed', now());

        return redirect()->back()->with(['success' => 'Age updated!']);
    }

    public function updateUsername(Request $request, $user_id){

        $data = $request->only(['username']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'username' => ['required', 'unique:users,username'],
            'user_id' => ['required', 'exists:users,id']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = User::query()->find($user_id);

        $user->update(['username' => $request->username, 'updated_username' => 0]);

        NotificationUtils::sendNotification($user->id, 'username_changed', now());

        return redirect()->back()->with(['success' => 'Username updated!']);
    }

    public function updateSpenderCategory(Request $request, $user_id){

        $data = $request->only(['category_id']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'category_id' => ['required', 'integer', "exists:$this->spenders_group_table,id"],
            'user_id' => ['required', 'integer', "exists:$this->leaders_table,id"]
        ], [
            'user_id.exists' => "Leader not found"
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = Leader::find($user_id);

        $user->update(['spender_group_id' => $request->category_id, 'spender_group_forced_by_admin' => true]);

        return redirect()->back()->with(['success' => 'Spender category updated!']);
    }

    public function updateStatus(Request $request, $user_id){

        $data = $request->only(['status', 'status_reason']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'status' => ['required', 'in:suspend,under_review,fraud'],
            'user_id' => ['required', 'integer', "exists:$this->leaders_table,id"],
            'status_reason' => ['required'],
        ], [
            'user_id.exists' => "Leader not found"
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $leader = Leader::query()
            ->find($user_id);

        $user = User::query()
            ->find($user_id);

        $user->createUserStatusHistory($request->status, Auth::user()->username, $request->status_reason);

        switch ($request->status) {
            case 'suspend':
            case 'under_review':

                $leader->update(['internal_status' => $request->status]);
                break;
            case 'fraud':
                $user->update(['status' => $request->status]);
            default:

                $leader->update(['internal_status' => null]);
                User::query()
                    ->find($user_id)->update(['status' => $request->status]);
                break;
        }

        return redirect()->back()->with(['success' => 'Status updated!']);
    }

    public function updateFirstNameAndLastName(Request $request, $user_id){

        $data = $request->only(['first_name', 'last_name']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'user_id' => ['required', 'integer', "exists:$this->rookies_table,id"]
        ], [
            'user_id.exists' => "Rookie not found"
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $rookie = Rookie::query()->find($user_id);

        if($rookie->first_name != $request->first_name){

            $rookie->update(['first_name' => $request->first_name]);
            UserNote::query()->create(['user_id' => $user_id, 'admin_id' => Auth::id(), 'note' => 'Admin update first name']);
            NotificationUtils::sendNotification($rookie->id, 'rookie_changed_first_name', now());
        }

        if($rookie->last_name != $request->last_name){

            $rookie->update(['last_name' => $request->last_name]);
            UserNote::query()->create(['user_id' => $user_id, 'admin_id' => Auth::id(), 'note' => 'Admin update last name']);
            NotificationUtils::sendNotification($rookie->id, 'rookie_changed_last_name', now());
        }

        return redirect()->back()->with(['success' => "Rookie's info updated!"]);
    }

    public function actionToUsername(Request $request, $user_id){
        $data = $request->only(['action']);
        $data['user_id'] = $user_id;

        $validator = Validator::make($data, [
            'action' => ['required', 'in:decline,approve'],
            'user_id' => ['required', 'integer', "exists:users,id"]
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = User::query()->find($user_id);

        switch ($request->action){

            case 'decline':

                $reason = "Username not valid";
                $user->createUserStatusHistory('blocked', Auth::user()->username, $reason);
                $user->update(['status' => 'blocked', 'updated_username' => 0]);
                $message = "Username not valid. User blocked";

                UserNote::create([
                    'user_id' => $user_id,
                    'admin_id' => Auth::id(),
                    'note' => 'User blocked because username was declined'
                ]);

                break;
            case 'approve':
                $user->update(['updated_username' => 0]);
                $message = "Username accepted";
                break;
        }

        return redirect()->back()->with(['success' => $message]);
    }
}
