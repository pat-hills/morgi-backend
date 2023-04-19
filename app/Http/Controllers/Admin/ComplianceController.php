<?php


namespace App\Http\Controllers\Admin;


use App\Enums\UserEnum;
use App\Http\Controllers\Controller;
use App\Models\Leader;
use App\Models\LeaderPayment;
use App\Models\SubscriptionEditHistory;
use App\Models\TransactionFailedHistory;
use App\Models\UserPath;
use App\Models\UserStatusHistory;
use App\Models\MicromorgiPackage;
use App\Models\Photo;
use App\Models\PubnubMessage;
use App\Models\SpenderGroup;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserIdentityDocumentHistory;
use App\Models\UserLoginHistory;
use App\Models\XLSXWriter;
use App\Utils\Admin\UserRelatedAccountUtils;
use App\Utils\NotificationUtils;
use App\Utils\ReasonUtils;
use App\Utils\TransactionRefundUtils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ComplianceController extends Controller
{
    private $users_table = 'users';
    private $rookies_table = 'rookies';
    private $leaders_table = 'leaders';
    private $transactions_table = 'transactions';
    private $leader_payments_table = 'leaders_payments';
    private $leaders_ccbill_data_table = 'leaders_ccbill_data';
    private $countries_table = 'countries';
    private $users_login_history_table = 'users_login_histories';

    public function getRefundReports()
    {

        $select = [
            "$this->transactions_table.*",
            "$this->users_table.*",
            "$this->users_table.type as user_type",
            "$this->users_table.id as user_id",
            "$this->users_table.description as user_description",
            "$this->countries_table.name as signup_country",
            "$this->leader_payments_table.ip_address",
            "$this->leader_payments_table.status as payment_status",
            "$this->leaders_ccbill_data_table.billingCountry"
        ];

        $reports = Transaction::where("$this->transactions_table.type", '=', 'refund')
            ->join($this->leader_payments_table, "$this->transactions_table.leader_payment_id", '=', "$this->leader_payments_table.id")
            ->join($this->leaders_ccbill_data_table, "$this->leader_payments_table.leader_payment_method_id", '=', "$this->leaders_ccbill_data_table.id")
            ->join("$this->users_table", "$this->users_table.id", '=', "$this->transactions_table.leader_id")
            ->leftJoin($this->countries_table, "$this->users_table.signup_country_id", '=', "$this->countries_table.id")
            ->select($select)
            ->groupBy('users.id')
            ->get()
            ->each(function ($query) {

                $query->sum_morgi = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->whereIn('refund_type', ['void', 'refund'])
                        ->get()
                        ->sum('morgi') ?? 0;
                $query->sum_micromorgi = Transaction::where($query->type . "_id", $query->user_id)
                        ->whereIn('refund_type', ['void', 'refund'])
                        ->get()
                        ->sum('micromorgi') ?? 0;
                $query->first_purchase = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->where("$this->transactions_table.type", 'bought_micromorgi')->first()->created_at ?? null;
                $query->last_purchase = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->where("$this->transactions_table.type", 'bought_micromorgi')->latest()->first()->created_at ?? null;
                $query->cgb_refund = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->where('type', 'refund')
                        ->get()
                        ->count() ?? 0;
                $query->count_login = UserLoginHistory::query()->where('user_id', $query->user_id)->count();
                $query->has_pic = Photo::query()->where('user_id', $query->user_id)->count();

            });

        return view('admin.admin-pages.compliance.refund-reports', compact('reports'));
    }

    public function getChargebackReports(){

        $select = [
            "$this->transactions_table.*",
            "$this->users_table.*",
            "$this->users_table.id as user_id",
            "$this->users_table.type as user_type",
            "$this->users_table.description as user_description",
            "$this->countries_table.name as signup_country",
            "$this->leader_payments_table.ip_address",
            "$this->leader_payments_table.status as payment_status",
            "$this->leaders_ccbill_data_table.billingCountry"
        ];

        $reports = Transaction::where("$this->transactions_table.refund_type", '=', 'chargeback')
            ->join($this->leader_payments_table, "$this->transactions_table.leader_payment_id", '=', "$this->leader_payments_table.id")
            ->join($this->leaders_ccbill_data_table, "$this->leader_payments_table.leader_payment_method_id", '=', "$this->leaders_ccbill_data_table.id")
            ->join("$this->users_table", "$this->users_table.id", '=', "$this->transactions_table.leader_id")
            ->leftJoin($this->countries_table, "$this->users_table.signup_country_id", '=', "$this->countries_table.id")
            ->select($select)
            ->groupBy('users.id')
            ->get()
            ->each(function ($query) {

                $transactions = Transaction::where($query->user_type . "_id", $query->user_id)
                    ->where('refund_type', 'chargeback')
                    ->get();
                $query->sum_morgi = $transactions->sum('morgi') ?? 0;
                $query->sum_micromorgi = $transactions->sum('micromorgi') ?? 0;

                $query->first_purchase = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->where("$this->transactions_table.type", 'bought_micromorgi')->first()->created_at ?? null;
                $query->last_purchase = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->where("$this->transactions_table.type", 'bought_micromorgi')->latest()->first()->created_at ?? null;
                $query->cgb_refund = Transaction::where($query->user_type . "_id", $query->user_id)
                        ->where('type', 'refund')
                        ->get()
                        ->count() ?? 0;

                $query->count_login = UserLoginHistory::query()->where('user_id', $query->user_id)->count();
                $query->has_pic = Photo::query()->where('user_id', $query->user_id)->count();
            });

        return view('admin.admin-pages.compliance.chargeback-reports', compact('reports'));
    }

    public function getBigSpenders(){

        $users = User::where('status', 'untrusted')
            ->get()
            ->each(function ($query){
                $document = UserIdentityDocumentHistory::where('user_id', $query->id)->latest()->first() ?? null;
                $query->document_id = (is_null($document)) ? $document : $document->created_at;
            });

        return view('admin.admin-pages.compliance.big_spenders', compact('users'));
    }

    public function getFirstThreeTransactions(){

        $query = LeaderPayment::query()
            ->select('leaders_payments.*',
                'leaders.has_approved_transaction',
                'users.username',
                'users.email',
                'leaders_payments.ccbill_transactionId',
                'leaders_payments.ccbill_subscriptionId',
                'transactions.internal_status'
            );

        $query->join('leaders', 'leaders.id', '=', 'leaders_payments.leader_id');
        $query->join('users', 'users.id', '=', 'leaders_payments.leader_id');
        $query->join('transactions', 'transactions.leader_payment_id', '=', 'leaders_payments.id');
        $query->where('transactions.internal_status', 'pending')
            ->where('leaders_payments.created_at', '>', Carbon::now()->subHours(24))
            ->where('leaders_payments.status', 'paid')
            ->where('users.status', UserEnum::STATUS_ACCEPTED)
            ->whereNull('leaders.internal_status');

        $transactions = $query->where('leaders.has_approved_transaction', 0)->limit(10)
            ->get();


        $new_transactions = [];
        foreach ($transactions as $transaction) {
            $new_transactions[$transaction->leader_id]['user_id'] = $transaction->leader_id;
            $new_transactions[$transaction->leader_id]['biller_transaction_id'] = $transaction->ccbill_transactionId ?? $transaction->ccbill_subscriptionId;
            $new_transactions[$transaction->leader_id]['username'] = $transaction->username;
            $new_transactions[$transaction->leader_id]['email'] = $transaction->email;
            $new_transactions[$transaction->leader_id]['last_amount'] = $transaction->amount;
            $new_transactions[$transaction->leader_id]['created_at'] = $transaction->created_at;
            $new_transactions[$transaction->leader_id]['ccbill_transactionId'] = $transaction->ccbill_transactionId;
            $new_transactions[$transaction->leader_id]['ccbill_subscriptionId'] = $transaction->ccbill_subscriptionId;

            if(array_key_exists('n_transactions', $new_transactions[$transaction->leader_id])){
                $new_transactions[$transaction->leader_id]['n_transactions'] += 1;
            }else{
                if($transaction->internal_status == 'pending'){
                    $new_transactions[$transaction->leader_id]['n_transactions'] = 1;
                }
            }

            if (!isset($new_transactions[$transaction->leader_id]['time_remaining'])) {

                $will_expire = Carbon::parse($transaction->created_at)->addHours(24);
                $time_remaining = $will_expire->diff(Carbon::now())->format('%H Hours %I Minutes');

                $new_transactions[$transaction->leader_id]['time_remaining'] = $time_remaining;
            }
        }

        foreach ($new_transactions as $user_id => $transaction){
            $new_transactions[$user_id]['checked_transactions'] = Transaction::query()->where('leader_id', $user_id)->where('internal_status', '=', 'approved')->count(). "/3";
        }

        $transactions = json_decode(json_encode($new_transactions));

        return view('admin.admin-pages.compliance.three_index', compact('transactions'));
    }

    public function getFirstThreeTransactionsById(Request $request, $user_id){

        $select = [
            "$this->leader_payments_table.ccbill_transactionId",
            "$this->leader_payments_table.ccbill_subscriptionId",
            "$this->leader_payments_table.created_at",
            "$this->leader_payments_table.ip_address",
            "$this->leaders_ccbill_data_table.cardType",
            "$this->leaders_ccbill_data_table.billingCountry",
            "$this->leaders_ccbill_data_table.firstName",
            "$this->leaders_ccbill_data_table.lastName",
            "$this->leaders_table.has_approved_transaction",
            "$this->leaders_table.internal_status as leader_internal_status",
            "$this->users_table.email",
            "$this->users_table.sent_first_message",
            "$this->users_table.username",
            "$this->users_table.id as user_id",
            "$this->users_table.created_at as user_created_at",
            "$this->users_table.description as user_description",
            "$this->transactions_table.internal_id",
            "$this->transactions_table.micromorgi",
            "$this->transactions_table.morgi",
            "$this->transactions_table.dollars",
            "$this->transactions_table.id as transaction_id",
            "$this->transactions_table.leader_id",
            "$this->transactions_table.internal_status",
            "$this->transactions_table.internal_status_reason",
        ];

        $count_login = UserLoginHistory::query()->where('user_id', '=', $user_id)->count();
        $has_pic = Photo::query()->where('user_id', $user_id)->exists();
        $transactions_query = Transaction::query()
            ->where("leader_id", $user_id)->get();
        $paid_mm_package = $transactions_query
                ->where('type', '=', 'bought_micromorgi')->sum('dollars') ?? 0;
        $first_purchase = $transactions_query
                ->where('type', '=', 'gift')->first()->dollars ?? 0;
        $country_name = User::join("$this->countries_table", "$this->users_table.signup_country_id", '=', "$this->countries_table.id")
                ->where("$this->users_table.id", '=', $user_id)
                ->first()->name ?? null;

        $transactions_approved = LeaderPayment::query()
            ->select($select)
            ->join("$this->users_table", "$this->users_table.id", '=', "$this->leader_payments_table.leader_id")
            ->join("$this->leaders_table", "$this->leaders_table.id", '=', "$this->leader_payments_table.leader_id")
            ->join("$this->leaders_ccbill_data_table", "$this->leader_payments_table.leader_payment_method_id", '=', "$this->leaders_ccbill_data_table.id")
            ->join("$this->transactions_table", "$this->transactions_table.leader_payment_id", '=', "$this->leader_payments_table.id")
            ->where("transactions.leader_id", $user_id)
            ->where("$this->transactions_table.type", '!=', 'refund')
            ->whereRaw("TIMESTAMPDIFF(minute, $this->transactions_table.created_at, NOW()) < 1440")
            ->where("$this->transactions_table.internal_status", '!=', 'pending')
            ->orderBy("$this->transactions_table.updated_at", 'DESC')
            ->limit(2)
            ->get();

        foreach ($transactions_approved as $transaction){
            $transaction->count_login = $count_login;
            $transaction->has_pic = $has_pic;
            $transaction->paid_mm_package = $paid_mm_package;
            $transaction->first_purchase = $first_purchase;
            $transaction->country_name = $country_name;
        }

        $transactions_pending = LeaderPayment::query()
            ->select($select)
            ->join("$this->users_table", "$this->users_table.id", '=', "$this->leader_payments_table.leader_id")
            ->join("$this->leaders_table", "$this->leaders_table.id", '=', "$this->leader_payments_table.leader_id")
            ->join("$this->leaders_ccbill_data_table", "$this->leader_payments_table.leader_payment_method_id", '=', "$this->leaders_ccbill_data_table.id")
            ->join("$this->transactions_table", "$this->transactions_table.leader_payment_id", '=', "$this->leader_payments_table.id")
            ->where("$this->leader_payments_table.leader_id", '=', $user_id)
            ->where("$this->transactions_table.type", '!=', 'refund')
            ->whereNull("$this->transactions_table.refund_type")
            ->where("$this->transactions_table.internal_status", '=', 'pending')
            ->whereRaw("TIMESTAMPDIFF(minute, $this->transactions_table.created_at, NOW()) < 1440")
            ->orderBy("$this->transactions_table.updated_at", 'DESC')
            ->limit(3)
            ->get();

        foreach ($transactions_pending as $transaction){
            $transaction->count_login = $count_login;
            $transaction->has_pic = $has_pic;
            $transaction->paid_mm_package = $paid_mm_package;
            $transaction->first_purchase = $first_purchase;
            $transaction->country_name = $country_name;
        }


        $user = User::find($user_id);
        $result = UserRelatedAccountUtils::getLeaderRelatedAccount($user);

        $user = $result['user'];
        $matched_users = $result['matched_users'];
        $user_signup = $result['user']->signup_login;
        $user_latest = $result['user']->latest_login;

        return view('admin.admin-pages.compliance.show-transactions', compact('user', 'matched_users', 'user_signup', 'user_latest', 'transactions_approved', 'transactions_pending'));
    }

    public function getTransactionById(Request $request, $transaction_id){

        $select = [
            "$this->transactions_table.id as transaction_id",
            "$this->transactions_table.admin_id",
            "$this->transactions_table.type",
            "$this->transactions_table.internal_id",
            "$this->transactions_table.created_at",
            "$this->transactions_table.dollars",
            "$this->transactions_table.morgi",
            "$this->transactions_table.micromorgi",
            "$this->transactions_table.rookie_id",
            "$this->transactions_table.leader_id",
            "$this->transactions_table.internal_status_by",
            "$this->transactions_table.internal_status",
            "$this->leader_payments_table.ccbill_transactionId",
            "$this->leader_payments_table.ccbill_subscriptionId"
        ];

        $transaction = Transaction::where("$this->transactions_table.id", '=', $transaction_id)
            ->join("$this->leader_payments_table", "$this->transactions_table.leader_payment_id", '=', "$this->leader_payments_table.id")
            ->select($select)
            ->first();

        $transaction->referal_transaction = Transaction::where('referal_internal_id', $transaction->internal_id)->first() ?? FALSE;


        $leader  = empty($transaction->leader_id) ? null : $this->getLeaderTransactionInfo($transaction->leader_id);
        $rookie  = empty($transaction->rookie_id) ? null : $this->getRookieTransactionInfo($transaction->rookie_id);

        return view('admin.admin-pages.transaction.show', compact('transaction', 'leader', 'rookie'));
    }

    private function getLeaderTransactionInfo($id){

        $query = User::query()
            ->where("$this->users_table.id", $id);

        $select = [
            "$this->users_table.id",
            "$this->users_table.type",
            "$this->users_table.username",
            "$this->users_table.email",
            "$this->users_table.sent_first_message",
            "$this->users_table.created_at",
            "$this->users_table.description as description",
            DB::raw("(SELECT COUNT(id) as count_log FROM $this->users_login_history_table WHERE $this->users_login_history_table.user_id = $id) as count_log"),
            DB::raw("(SELECT ip_address FROM $this->users_login_history_table WHERE $this->users_login_history_table.user_id = $this->users_table.id and is_signup_values = 1) as signup_ip"),
            DB::raw("(SELECT sum(dollars) as tot_micromorgi_packages FROM $this->transactions_table WHERE $this->transactions_table.leader_id = $id and $this->transactions_table.type = 'bought_micromorgi' and $this->transactions_table.refund_type IS null) as tot_micromorgi_packages"),
        ];

        $query->join("$this->leaders_table", "$this->users_table.id", '=', "$this->leaders_table.id");
        $query->select($select);
        $user = $query->first();

        $user->first_purchase = Transaction::query()
                ->select('created_at')
                ->where('leader_id', $user->id)
                ->where('type', '=','bought_micromorgi')
                ->first()->created_at ?? null;
        $user->first_subscription = Transaction::query()
                ->select('created_at')
                ->where('leader_id', $user->id)
                ->where('type', '=', 'gift')
                ->first()->created_at ?? null;

        return $user;
    }

    private function getRookieTransactionInfo($id){

        $query = User::query()->where("$this->users_table.id", $id);

        $select = [
            "$this->users_table.id",
            "$this->users_table.type",
            "$this->users_table.email",
            "$this->users_table.username",
            "$this->users_table.sent_first_message",
            "$this->users_table.created_at",
            "$this->users_table.description as description",
            DB::raw("(SELECT COUNT(id) as count_log FROM $this->users_login_history_table WHERE $this->users_login_history_table.user_id = $id) as count_log"),
            DB::raw("(SELECT ip_address FROM $this->users_login_history_table WHERE $this->users_login_history_table.user_id = $id and is_signup_values = 1) as signup_ip"),
            DB::raw("(SELECT sum(taxed_dollars) as tot_micromorgi_earning FROM $this->transactions_table WHERE $this->transactions_table.type = 'chat' and $this->transactions_table.rookie_id = $id and $this->transactions_table.refund_type IS null ) as tot_micromorgi_earning"),
        ];

        $query->join("$this->rookies_table", "$this->users_table.id", '=', "$this->rookies_table.id");
        $query->select($select);
        $user = $query->first();

        $user->first_earn = Transaction::query()
                ->select('created_at')
                ->where('rookie_id', $user->id)
                ->where('type', 'chat')
                ->whereNull('refund_type')
                ->first()->created_at ?? null;
        $user->first_subscription = Transaction::query()
                ->select('created_at')
                ->where('rookie_id', $user->id)
                ->where('type', 'gift')
                ->whereNull('refund_type')
                ->first()->created_at ?? null;

        return $user;
    }

    public function internalActionToTransaction(Request $request){

        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:approved,declined'],
            'decline_reason' => ['required_if:action,=,declined'],
            'transaction_id' => ['required', 'integer', 'exists:transactions,id'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $transaction = Transaction::find($request->transaction_id);
        if ($transaction->internal_status != 'pending') {
            return redirect()->back()->with(['fail' => 'Already checked by: ' . User::find($transaction->internal_status_by)->username ?? null . ' current status: ' . $transaction->internal_status]);
        }

        if (!is_null($transaction->refund_type)) {
            return redirect()->back()->with(['fail' => 'Already refunded. Refund type: ' . $transaction->refund_type]);
        }

        if($request->action === 'declined'){

            DB::beginTransaction();
            try {

                if(!is_null($transaction->leader_payment_id)){
                    TransactionRefundUtils::createTransactionRefund($transaction, ReasonUtils::ALL_REASON[$request->decline_reason] ?? $request->declline_reason, Auth::id());
                }else{
                    TransactionRefundUtils::config($transaction)->refund(Auth::id(), $request->reason);
                }

                $leader = Leader::query()->find($transaction->leader_id);
                $leader->createLeaderStatusHistory('under_review', Auth::user()->username, $request->decline_reason);
                $leader->update([
                    'internal_status' => 'under_review',
                    'admin_id' => Auth::id()
                ]);

                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                return redirect()->back()->with(['fail' => $e->getMessage()]);
            }

            $transaction->update([
                'internal_status_reason' => $request->decline_reason,
            ]);

            if($transaction->refund_type == 'void'){

                NotificationUtils::sendNotification($transaction->leader_id, 'leader_compliance_refund', now());

                if($transaction->rookie_id){
                    NotificationUtils::sendNotification($transaction->rookie_id, 'rookie_compliance_refund', now());
                }
            }
        }

        $transaction->update([
            'internal_status' => $request->action,
            'internal_status_by' => Auth::id(),
        ]);

        $message = "Transaction successfully $request->action";

        if(!is_null($transaction->leader_payment_id) && $request->action === 'declined'){

            $message = "Transaction internally declined. Refund request sent successfully.";
        }

        return redirect()->back()->with(['success' => $message]);
    }

    public function showTransactionsRefunds(){

        $type = request()->segment(count(request()->segments()));

        $header = [
            'Date of purchase' => 'string',
            'Date of cancellation' => 'string',
            'Username' => 'string',
            'ID number' => 'integer',
            'Biller' => 'string',
            'Type' => 'string',
            'Amount USD' => 'dollar',
            'If declined, decline reason' => 'string',
            'Rookie username' => 'string',
            'Transaction ID' => 'string',
            '1st purchase, rebill, micro morgi' => 'string',
            'Membership level' => 'string',
            'User status' => 'string',
            'count of other active rookies' => 'integer',
            'Details' => 'string',
        ];

        return view('admin.admin-pages.compliance.transactions_refunds', compact('type', 'header'));
    }

    public function getTransactionsRefunds(Request $request){

        $validator = Validator::make($request->all(), [
            'type' => ['in:all,chargebacks,refund_by_biller,refund_by_admin,void,rebill_declined'],
            'start' => ['integer'],
            'length' => ['integer'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'order' => ['array']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 400);
        }

        $request_type = $request->has('type') ? $request->get('type') : null;
        $offset = $request->has('start') ? $request->get('start') : 1;
        $limit = $request->has('length') ? $request->get('length') : 25;
        $order = ($request->order[0] && array_key_exists('dir', $request->order[0])) ? strtoupper($request->order[0]['dir']) : 'DESC';


        $response = $this->prepareTransactionRefundQuery($request_type, $request->from, $request->to, $order);

        $query = $response['query'];
        $all = $response['all'];

        $query->orderBy("$this->transactions_table.refunded_at", "DESC");
        $query->offset($offset)->limit($limit);

        $transactions = $query->get();


        $all_data = [];

        $this->transactionsRefundsForeach($transactions,$all_data);

        $max_pages = ceil($all / $limit);

        $data = [];
        $data["draw"] = intval($request->draw);
        $data['pages'] = $max_pages;
        $data['data'] = $all_data;
        $data['recordsTotal'] = $all;
        $data['recordsFiltered'] = $response['filtered'];

        return response()->json($data);
    }

    public function getReports(Request $request){

        $validator = Validator::make($request->all(), [
            'type' => ['in:daily,multiple_leaders,one_leader,inactive_communications,new_card,status_change'],
            'start' => ['integer'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'order' => ['nullable', 'array']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->getMessages(), 400);
        }

        $request_type = $request->has('type') ? $request->get('type') : null;
        $offset = $request->has('start') ? $request->get('start') : 1;
        $limit = $request->length ?? 25;

        $from = $request->from ?? null;
        $to = $request->to ?? null;
        $order = ($request->order && $request->order[0] && array_key_exists('dir', $request->order[0])) ? strtoupper($request->order[0]['dir']) : 'DESC';

        $all_data = [];

        switch ($request_type) {
            case 'multiple_leaders':

                $query = Subscription::query();

                $all = $query->count();

                $subscriptions = $query->select('rookie_id', DB::raw('count(id) as total'))
                    ->where('status', 'active')
                    ->havingRaw('total > 2')
                    ->groupBy('rookie_id')
                    ->get()
                    ->each(function ($query) {
                        $query->setAppends([]);
                    });

                $subs = [];
                foreach ($subscriptions as $sub) {
                    $leaders_id = Subscription::query()
                        ->select('leader_id')
                        ->where('rookie_id', $sub->rookie_id)
                        ->where('status', 'active')
                        ->groupBy('leader_id')
                        ->pluck('leader_id')
                        ->toArray();

                    $subs[$sub->rookie_id] = $leaders_id;
                }

                $leaders = Leader::query()->pluck('id');

                $matched = [];
                foreach ($leaders as $leader) {
                    $count = 0;
                    $array_id = [];
                    foreach ($subs as $key => $value) {

                        if (in_array($leader, $value)) {
                            $array_id[] = $key;
                            $count++;
                        }
                        if ($count > 2) {
                            $matched[] = $array_id;
                        }
                    }
                }

                $all_data = $matched;

                $filtered = count($all_data);

                break;

            case 'daily':
            case 'one_leader':
            case 'inactive_communications':
            case 'new_card':
            case 'status_change':

                $response = $this->prepareReportQuery($request->type, $from, $to, $order);

                $query = $response['query'];

                $all = $response['all'];

                $filtered = $query->count();

                $query->offset($offset)->limit($limit);

                $reports = $query->get();

                $this->reportForeach($request->type, $all_data, $reports);

                break;
        }

        $max_pages = ceil($all / $limit);

        $unindex_data = [];

        foreach ($all_data as &$data){
            $unindex_data[] = array_values($data);
        }

        $data = [];
        $data['draw'] = intval($request->draw);
        $data['pages'] = $max_pages;
        $data['data'] = $unindex_data;
        $data['recordsTotal'] = $all;
        $data['recordsFiltered'] = $filtered;

        return response()->json($data);
    }

    public function reportPage(){

        $type = request()->segment(count(request()->segments()));

        $header = $this->getReportHeader($type);

        return view('admin.admin-pages.compliance.reports', compact('type', 'header'));
    }

    private function getReportHeader($type){

        switch ($type){
            case 'daily':

                $header = [
                    'Date of purchase' => 'string',
                    'Username' => 'string',
                    'ID number' => 'integer',
                    'Biller' => 'string',
                    'Micro Morgi of Morgi' => 'integer',
                    'amount USD' => 'dollar',
                    'Rookie username if Morgi' => 'string',
                    'Transaction ID / Subscription ID' => 'string',
                    '1st purchase or rebill if Morgi' => 'integer',
                    'If rebill, count of messages sent in last 30 days'  => 'integer',
                    'Rebill with new card details'  => 'string',
                    'Count of total Micro Morgi Purchases' => 'integer',
                    'Membership level' => 'string',
                    'Status' => 'string',
                    'Count of other active rookies' => 'integer',
                    'Count of inactive subscriptions' => 'integer',
                    'Micro Morgi Balance USD' => 'dollar',
                    'Details' => 'string'
                ];

                break;

            case 'one_leader':

                $header = [
                    'Rookie username' => 'string',
                    'Rookie ID' => 'integer',
                    'Path' => 'string',
                    'Leader Username' => 'string',
                    'Leader ID' => 'integer',
                    'Amount of last payment' => 'dollar',
                    'Count of payments' => 'integer',
                    'Total amount sent to Rookie USD' => 'dollar',
                    'Total micro morgis sent to rookie' => 'string',
                    'Count Leader logins' => 'integer',
                    'Count of messages sent' => 'integer',
                    'Amount of times monthly subscription has been changed' => 'integer',
                ];

                break;

            case 'inactive_communications':

                $header = [
                    'Date of last rebill' => 'datetime',
                    'Amount of last rebill' => 'string',
                    'Username' => 'string',
                    'ID number' => 'integer',
                    'Biller' => 'string',
                    'Amount of next rebill' => 'string',
                    'Rookie username' => 'string',
                    'Membership level' => 'string',
                    'Count of other active rookies' => 'integer',
                    'Count messages sent from Leader' => 'integer',
                    'Count messages sent from Rookie' => 'integer',
                    'Last login leader' => 'string',
                    'Last login rookie' => 'string',

                ];

                break;

            case 'new_card':

                $header = [
                    'Username' => 'string',
                    'ID number' => 'integer',
                    'Biller' => 'string',
                    'Amount' => 'integer',
                    'Decline reason' => 'string',
                    'New card entered for new transaction' => 'string',
                    'Transaction successful' => 'string',
                    'Count of subscriptions at risk from declined card' => 'integer',
                    'Last login' => 'string',
                ];

                break;

            case 'status_change';

                $header = [
                    'Date' => 'string',
                    'Username' => 'string',
                    'ID number' => 'integer',
                    'Old status' => 'string',
                    'New Status' => 'string',
                    'Changed by' => 'string',
                    'Reason' => 'string'
                ];

                break;
        }

        return $header;
    }

    public function exportReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['in:daily,multiple_leaders,one_leader,inactive_communications,new_card,status_change'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $section = $request->type;

        $csv   = [];

        $header = $this->getReportHeader($request->type);

        $response = $this->prepareReportQuery($section, $request->start_date, $request->end_date);
        $query = $response['query'];

        $reports = $query->get();
        $this->reportForeach($section, $csv, $reports);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="reports-' . $section . '.xlsx"');

        $writer = new XLSXWriter();
        $writer->writeSheetHeader('Sheet1', $header);
        $writer->writeSheet($csv);

        echo $writer->writeToString();
    }

    private function reportForeach($section, &$array, $reports){

        switch ($section){

            case 'daily':

                foreach ($reports as $repo){
                    $data = [];
                    $data['date_of_purchase'] = $repo->purchase_at;
                    $data['username'] = $repo->leader_username;
                    $data['id_number'] = $repo->id_number;
                    $data['biller'] = 'ccbill';
                    $data['micro_or_morgi'] = $repo->currency_type;
                    $data['amount_usd'] = $repo->amount_usd;
                    $data['rookie_username'] = $repo->rookie_username;
                    $data['transaction_id_number_/_subscription_id'] = $repo->transactionId ?? $repo->subscriptionId;
                    $data['purchase_or_rebill'] = ($repo->currency_type == 'morgi') ? str_replace('_', ' ', ucfirst($repo->type)) : 'micro morgi';
                    $data['messages_last_30_days'] = null;
                    if($repo->currency_type == 'morgi' && $repo->subscription_id){
                        $data['messages_last_30_days'] = PubnubMessage::query()
                            ->where('channel_id', $repo->channel_id)
                            ->where('created_at', '<', now()->subDays(30))
                            ->count();
                    }
                    $data['is_new_card'] = 'no';
                    if($repo->currency_type == 'morgi' && $repo->subscription_id){
                        if($repo->isRebillNewCard()){
                            $data['is_new_card'] = 'yes';
                        }
                    }
                    $data['count_of_total_mm_purchases'] = LeaderPayment::query()
                        ->where('currency_type', 'micro_morgi')
                        ->where('leader_id', $repo->id_number)
                        ->count();
                    $data['membership_level'] = (SpenderGroup::find($repo->spender_group_forced_by_admin)->name) ?? SpenderGroup::find($repo->spender_group_id)->name;
                    $data['status'] = $repo->leader_status;
                    $data['active_rookies'] = Subscription::query()
                        ->where('leader_id', $repo->id_number)
                        ->where('status', 'active')
                        ->count();
                    $data['inactive_rookies'] = Subscription::query()
                        ->where('leader_id', $repo->id_number)
                        ->where('status', '!=', 'active')
                        ->count();
                    $data['balance_micromorgi_usd'] = MicromorgiPackage::getDollarAmount($repo->micro_morgi_balance);
                    $data['details'] = null;
                    if(Transaction::where('leader_payment_id', $repo->leader_payment_id)->exists()){
                        $data['details'] = route('transaction.show', ['transaction_id' => Transaction::where('leader_payment_id', $repo->leader_payment_id)->first()->id]);
                    }

                    $array[] = $data;
                }

                break;

            case 'one_leader':

                foreach ($reports as $repo){

                    $path = UserPath::query()
                        ->leftJoin('paths', 'users_paths.path_id', '=', 'paths.id')
                        ->where('users_paths.user_id', $repo->rookie_id)->first();

                    $last_payment = LeaderPayment::query()
                        ->where('subscription_id', $repo->id)->where('status', '!=', 'failed')->latest()->first();

                    $transactions = Transaction::query()
                        ->where('rookie_id', $repo->rookie_id)
                        ->where('leader_id', $repo->leader_id)
                        ->whereNull('refund_type')
                        ->get();

                    $data = [];
                    $data['rookie_username'] = $repo->rookie_username;
                    $data['rookie_id'] = $repo->rookie_id;
                    $data['path'] = $path->name ?? null;
                    $data['leader_username'] = $repo->leader_username;
                    $data['leader_id'] = $repo->leader_id;
                    $data['amount_last_payment'] = $last_payment->dollar_amount;
                    $data['count_payments'] = $repo->payments_count;
                    $data['total_amount_usd'] = $transactions->sum('dollars') ?? 0;
                    $data['total_micromorgi'] = $transactions->sum('micromorgi') ?? 0;
                    $data['leader_login'] = UserLoginHistory::query()->where('user_id', $repo->leader_id)->count();
                    $data['messages_sent'] = PubnubMessage::query()->where('sender_id', $repo->leader_id)->where('receiver_id', $repo->rookie_id)->count();
                    $data['changed_gift'] = SubscriptionEditHistory::query()
                        ->where('subscription_id', $repo->id)
                        ->whereDate('created_at', '>', Carbon::today()->subDays(30))
                        ->count();

                    $array[] = $data;
                }

                break;

            case 'inactive_communications':

                foreach ($reports as $repo){
                    $data = [];
                    $data['date_last_rebill'] = $repo->last_rebill;
                    $data['amount_last'] = $repo->last_amount_rebill;
                    $data['leader_username'] = $repo->leader_username;
                    $data['id_number'] = $repo->leader_id;
                    $data['biller'] = 'CCBill';
                    $data['amount_next'] = $repo->next_amount_rebill;
                    $data['rookie_username'] = $repo->rookie_username;
                    $data['membership'] = $repo->membership_level;
                    $data['count_active'] = $repo->active_rookies;
                    $data['count_messages_leader'] = $repo->leader_messages;
                    $data['count_messages_rookie'] = $repo->rookie_messages;
                    $data['leader_login'] = $repo->leader_last_login;
                    $data['rookie_login'] = $repo->rookie_last_login;

                    $array[] = $data;
                }

                break;

            case 'new_card':

                foreach ($reports as $repo){
                    $data = [];
                    $data['username'] = $repo->leader_username;
                    $data['id_number'] = $repo->user_id;
                    $data['biller'] = 'CCBill';
                    $data['amount'] = $repo->amount;
                    $data['decline_reason'] = $repo->reason;
                    $data['new_card_entered'] = ($repo->last_payment_method === $repo->payment_method_id) ? 'no' : 'yes';
                    $data['transaction_success'] = ($repo->real_status === 'paid') ? 'yes' : 'no';
                    $data['risky'] = $repo->counter_risky;
                    $data['last_login'] = $repo->leader_last_login;
                    $data['profile_link'] = route('user.edit', $repo->user_id);

                    $array[] = $data;
                }

                break;

            case 'status_change':

                foreach ($reports as $repo){
                    $data = [];
                    $data['date'] = date('Y-m-d H:i:s', strtotime($repo->created_at));
                    $data['username'] = $repo->username;
                    $data['id_number'] = $repo->user_id;
                    $data['old_status'] = $repo->old_status;
                    $data['new_status'] = $repo->new_status;
                    $data['changed_by'] = $repo->changed_by;
                    $data['reason'] = $repo->reason;

                    $array[] = $data;
                }

                break;
        }

    }

    private function prepareReportQuery($section, $date_from = null, $date_to = null, $order = 'DESC'){
        switch ($section){
            case 'daily':
                $query = LeaderPayment::query();

                $select = [
                    "leaders_payments.id as leader_payment_id",
                    "leaders_payments.type",
                    "leaders_payments.created_at as purchase_at",
                    "leaders_payments.created_at as created_at",
                    "leaders_payments.created_at as date_to_filter",
                    "leaders_payments.currency_type",
                    "leaders_payments.dollar_amount as amount_usd",
                    "leaders_payments.ccbill_transactionId as transactionId",
                    "leaders_payments.ccbill_subscriptionId as subscriptionId",
                    "leaders_payments.subscription_id",
                    "leaders.micro_morgi_balance",
                    "leaders.spender_group_forced_by_admin",
                    "leaders.spender_group_id",
                    "leaders_users.status as leader_status",
                    "leaders_users.username as leader_username",
                    "leaders_users.id as id_number",
                    "rookies_users.username as rookie_username",
                    "pubnub_channels.id as channel_id",
                ];

                $query->select($select);

                $query->join('users as leaders_users', 'leaders_users.id', '=', 'leaders_payments.leader_id')
                    ->join('leaders', 'leaders_users.id', '=', 'leaders.id')
                    ->join('transactions', 'transactions.leader_payment_id', '=', 'leaders_payments.id')
                    ->leftJoin('users as rookies_users', 'rookies_users.id', '=', 'transactions.rookie_id')
                    ->leftJoin('pubnub_channels', 'pubnub_channels.subscription_id', '=', 'transactions.subscription_id');

                $query->whereNotNull('leaders_payments.leader_payment_method_id')
                    ->where('leaders_payments.status', 'paid');

                if(!$date_from && !$date_to) {
                    $query->whereDate('leaders_payments.created_at', '=', Carbon::today());
                }else{
                    if ($date_from) {
                        $query->whereDate('leaders_payments.created_at', '>=', $date_from);
                    }

                    if ($date_to) {
                        $query->whereDate('leaders_payments.created_at', '<=', $date_to);
                    }
                }

                $query->orderBy('leaders_payments.created_at', $order);

                break;

            case 'one_leader':

                $query = Subscription::query()
                    ->selectRaw("subscriptions.id, subscriptions.rookie_id, subscriptions.leader_id,
                    rookie.username as rookie_username, leader.username as leader_username, COUNT(leader_sub.id) as have_rookie,
                    COUNT(rookie_sub.id) as have_leaders, (SELECT COUNT(leaders_payments.id) as payments_count FROM leaders_payments where leaders_payments.subscription_id = subscriptions.id and status != 'failed') as payments_count")
                    ->where('subscriptions.type', '=', 'paid')
                    ->leftJoin('subscriptions as rookie_sub', 'subscriptions.rookie_id', '=', 'rookie_sub.rookie_id')
                    ->leftJoin('users as rookie', 'rookie.id', '=', 'rookie_sub.rookie_id')
                    ->leftJoin('subscriptions as leader_sub', 'subscriptions.leader_id', '=', 'leader_sub.leader_id')
                    ->leftJoin('users as leader', 'leader.id', '=', 'leader_sub.leader_id')
                    ->groupBy('subscriptions.id')
                    ->havingRaw("COUNT(leader_sub.id) = 1 and COUNT(rookie_sub.id) = 1 and payments_count >= 3")
                    ->where('subscriptions.status', 'active');

                if ($date_from) {
                    $query->whereDate('subscriptions.created_at', '>=', $date_from);
                }

                if ($date_to) {
                    $query->whereDate('subscriptions.created_at', '<=', $date_to);
                }

                break;

            case 'inactive_communications':

                $select = [
                    "subscriptions.id",
                    "subscriptions.rookie_id",
                    "subscriptions.leader_id",
                    "subscriptions.last_subscription_at as last_rebill",
                    "subscriptions.amount as next_amount_rebill",
                    "leaders.username as leader_username",
                    "leaders.last_login_at as leader_last_login",
                    "rookies.username as rookie_username",
                    "rookies.last_login_at as rookie_last_login",
                    "spenders_groups.name as membership_level",
                    DB::raw("(SELECT COUNT(subscriptions.id) as active_rookies FROM subscriptions where leader_id = leaders.id and status = 'active') as active_rookies"),
                    DB::raw("(SELECT COUNT(pubnub_messages.id) as leader_messages FROM pubnub_messages where sender_id = leaders.id and receiver_id = rookies.id) as leader_messages"),
                    DB::raw("(SELECT COUNT(pubnub_messages.id) as rookie_messages FROM pubnub_messages where sender_id = rookies.id and receiver_id = leaders.id) as rookie_messages"),
                    DB::raw("COUNT(pubnub_messages.id) as messages"),
                    DB::raw('(DATE_ADD(last_subscription_at, INTERVAL +1 MONTH)) as next_subscription_at'),
                    DB::raw("DATEDIFF(DATE_ADD(last_subscription_at, INTERVAL +1 MONTH), NOW()) as date_diff"),
                    DB::raw("(SELECT morgi as last_amount_rebill FROM transactions where transactions.subscription_id = subscriptions.id ORDER BY 'created_at' DESC LIMIT 1) as last_amount_rebill"),

                ];

                $query = Subscription::query();

                $query->select($select)
                    ->join('pubnub_channels', 'subscriptions.id', '=', 'pubnub_channels.subscription_id')
                    ->leftJoin('pubnub_messages', 'pubnub_channels.id', '=', 'pubnub_messages.channel_id')
                    ->join('users as leaders', 'subscriptions.leader_id', '=', 'leaders.id')
                    ->join('leaders as l', 'leaders.id', '=', 'l.id')
                    ->leftjoin('spenders_groups', function ($join) {
                        $join->on('spenders_groups.id', '=', 'l.spender_group_forced_by_admin');
                        $join->orOn('spenders_groups.id', '=', 'l.spender_group_id');
                    })
                    ->join('users as rookies', 'subscriptions.rookie_id', '=', 'rookies.id')
                    ->where('subscriptions.status', 'active')
                    ->where('subscriptions.type', '=', 'paid');

                if ($date_from) {
                    $query->whereDate('subscriptions.last_subscription_at', '>=', $date_from);
                }

                if ($date_to) {
                    $query->whereDate('subscriptions.last_subscription_at', '<=', $date_to);
                }

                $query->havingRaw("date_diff < 2 and date_diff > 0 and messages <= 5")
                    ->groupBy('subscriptions.id');

                $query->orderBy('subscriptions.last_subscription_at', $order);

                break;

            case 'new_card':

                $select = [
                    "users.id as user_id",
                    "users.username as leader_username",
                    "users.last_login_at as leader_last_login",
                    "subscriptions.id as sub_id",
                    "transactions_failed_history.amount",
                    "transactions_failed_history.leader_payment_method_id as payment_method_id",
                    "transactions_failed_history.reason",
                    "transactions_failed_history.created_at as attempt_at",
                    DB::raw("(SELECT leader_payment_method_id as last_payment_method FROM leaders_payments WHERE leaders_payments.subscription_id = sub_id and created_at < DATE_SUB(transactions_failed_history.created_at, INTERVAL 20 day) ORDER BY created_at DESC LIMIT 1) as last_payment_method"),
                    DB::raw("(SELECT status FROM leaders_payments WHERE leaders_payments.subscription_id = sub_id and leaders_payments.created_at >= DATE_SUB(transactions_failed_history.created_at, INTERVAL 2 day) and leaders_payments.created_at <= DATE_ADD(transactions_failed_history.created_at, INTERVAL 2 day) ORDER BY created_at DESC LIMIT 1) as real_status"),
                    DB::raw("(SELECT COUNT(id) as counter_risky FROM subscriptions where id != sub_id and leader_payment_method_id = payment_method_id and status = 'active') as counter_risky"),
                ];

                $query = TransactionFailedHistory::query()
                    ->select($select)
                    ->join('subscriptions', 'subscriptions.id', '=', 'transactions_failed_history.subscription_id')
                    ->where('subscriptions.type','paid')
                    ->join('users', 'users.id', '=', 'subscriptions.leader_id')
                    ->orderBy('transactions_failed_history.created_at', 'DESC');


                $query->havingRaw('transactions_failed_history.created_at < DATE_SUB(NOW(), INTERVAL 24 hour)');

                $query->groupByRaw("MONTH(attempt_at)+'-'+YEAR(attempt_at), subscription_id");

                if(!$date_from && !$date_to) {

                    $query->whereDate('transactions_failed_history.created_at', '<', Carbon::today());
                }else{

                    if($date_from){
                        $query->whereDate('transactions_failed_history.created_at', '>=', $date_from);
                    }

                    if($date_to){
                        $query->whereDate('transactions_failed_history.created_at', '<=', $date_to);
                    }
                }

                break;

            case 'status_change':

                $select = [
                    "users_statues_histories.created_at",
                    "users.username",
                    "users_statues_histories.user_id",
                    "users_statues_histories.old_status",
                    "users_statues_histories.new_status",
                    "users_statues_histories.changed_by",
                    "users_statues_histories.reason",
                ];

                $query = UserStatusHistory::query()
                    ->select($select)
                    ->join('users', 'users_statues_histories.user_id', '=', 'users.id')
                    ->join('leaders', 'users_statues_histories.user_id', '=', 'leaders.id');

                if(!$date_from && !$date_to) {

                    $query->whereDate('users_statues_histories.created_at', '>', now()->subDays(7));
                }else{

                    if($date_from){
                        $query->whereDate('users_statues_histories.created_at', '>=', $date_from);
                    }

                    if($date_to){
                        $query->whereDate('users_statues_histories.created_at', '<=', $date_to);
                    }
                }

                $query->orderBy('users_statues_histories.created_at', $order);

                break;
        }

        $all = $query->count();


        return ['query' => $query, 'all' => $all];
    }

    private function prepareTransactionRefundQuery($section, $date_from = null, $date_to = null, $order = 'DESC'){

        $select = [
            "leaders_payments.created_at",
            "leaders_payments.dollar_amount",
            "leaders_payments.ccbill_failureReason",
            "leaders_payments.refund_reason",
            "leaders_payments.ccbill_transactionId",
            "leaders_payments.ccbill_subscriptionId",
            "leaders_payments.type",
            "leaders_payments.status",
            "leaders_users.username as leader_username",
            "leaders_users.id as leader_id",
            "leaders_users.status as leader_status",
            "transactions.refund_type",
            "subscriptions.canceled_at",
            "rookies_users.id as rookie_id",
            "rookies_users.username as rookie_username",
            "spenders_groups.name as membership_level",
            DB::raw("(SELECT COUNT(subscriptions.id) from subscriptions where subscriptions.leader_id = leaders_payments.leader_id and subscriptions.status = 'active' and subscriptions.rookie_id != transactions.rookie_id) as count_active_rookies")
        ];

        $query = LeaderPayment::query()
            ->select($select)
            ->join('users as leaders_users', 'leaders_users.id', '=', 'leaders_payments.leader_id')
            ->join('leaders', 'leaders_users.id', '=', 'leaders.id')
            ->leftJoin('subscriptions', 'subscriptions.id', '=', 'leaders_payments.subscription_id')
            ->leftJoin('users as rookies_users', 'rookies_users.id', '=', 'subscriptions.rookie_id')
            ->leftJoin('transactions', 'transactions.leader_payment_id', '=', 'leaders_payments.id')
            ->join('spenders_groups', 'spenders_groups.id', 'leaders.spender_group_id');

        switch ($section) {

            case 'rebill_declined':

                $where_date = 'refund_date';

                $query->where("leaders_payments.status", "failed")
                    ->where("leaders_payments.type", "rebill")
                    ->whereNotNull("leaders_payments.ccbill_failureReason");

                break;
            default:

                $where_date = 'refunded_at';

                $query->where("$this->transactions_table.type", 'refund');
                switch ($section) {
                    case 'chargebacks':
                        $query->where('transactions.refund_type', 'chargeback');
                        break;
                    case 'refund_by_biller':
                        $query->whereNull("$this->transactions_table.refund_type");
                        break;
                    case 'refund_by_admin':
                        $query->whereIn("$this->transactions_table.refund_type", ['refund', 'void']);
                        $query->whereNotNull("$this->transactions_table.refund_type");
                        break;
                    case 'void':
                        $query->where('transactions.refund_type', 'void');
                        break;

                }

                break;
        }

        $all = $query->count();

        if($date_from){
            $query->whereDate($where_date, '>=', $date_from);
        }

        if($date_to){
            $query->whereDate($where_date, '<=', $date_to);
        }


        $query->orderBy('leaders_payments.created_at',  $order);


        $filtered = $query->count();

        return ['query' => $query, 'all' => $all, 'filtered' => $filtered];

    }

    public function exportTransactionRefund(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => ['in:all,chargeback,refund_by_biller,refund_by_admin,void,rebill_declined'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $section = $request->type;

        $csv   = [];

        $header = [
            'Date of purchase' => 'string',
            'Date of cancellation' => 'string',
            'Username' => 'string',
            'ID number' => 'integer',
            'Biller' => 'string',
            'Type' => 'string',
            'Amount USD' => 'dollar',
            'If declined, decline reason' => 'string',
            'Rookie username' => 'string',
            'Transaction ID / Subscription ID' => 'string',
            '1st purchase, rebill, micro morgi' => 'string',
            'Membership level' => 'string',
            'User status' => 'string',
            'Count of other active rookies' => 'integer',
            'Details' => 'string',
        ];

        $response = $this->prepareTransactionRefundQuery($section, $request->from, $request->to);
        $query = $response['query'];

        $this->transactionsRefundsForeach($query->get(), $csv);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="transactions-refunds-' . $section . '.xlsx"');

        $writer = new XLSXWriter();
        $writer->writeSheetHeader('Sheet1', $header);
        $writer->writeSheet($csv);

        echo $writer->writeToString();
    }

    private function transactionsRefundsForeach($transactions, &$array_to_fill){

        $leader_payments_type_map = [
            "first_purchase" => "1st purchase",
            "rebill" => "rebill",
            "mm_purchase" => "micro morgi",
        ];

        foreach ($transactions as $transaction){
            $array_to_fill[] = [
                date('Y-m-d H:i:s', strtotime($transaction->created_at)),
                $transaction->canceled_at,
                $transaction->leader_username,
                $transaction->leader_id,
                "ccbill",
                $transaction->refund_type ?? $transaction->status ?? '',
                $transaction->dollar_amount,
                $transaction->refund_reason ?? $transaction->ccbill_failureReason,
                $transaction->rookie_username,
                $transaction->ccbill_transactionId ?? $transaction->ccbill_subscriptionId,
                $leader_payments_type_map[$transaction->type],
                $transaction->membership_level .' leader',
                $transaction->leader_status,
                $transaction->count_active_rookies,
                route('user.edit', ['id' => $transaction->leader_id]),
            ];
        }
    }

    public function getPendingRefund(Request $request){
        $validator = Validator::make($request->all(), [
            'leaders_ids' => ['sometimes', 'nullable', 'array'],
            'leaders_ids.*' => ['sometimes', 'integer', 'exists:leaders,id']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $leaders_ids = (array)$request->leaders_ids;

        return view('admin.admin-pages.compliance.refunds.pending', compact('leaders_ids'));
    }

    public function getApprovedRefund(Request $request){
        $validator = Validator::make($request->all(), [
            'leaders_ids' => ['sometimes', 'nullable', 'array'],
            'leaders_ids.*' => ['sometimes', 'integer', 'exists:leaders,id']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $leaders_ids = (array)$request->leaders_ids;

        return view('admin.admin-pages.compliance.refunds.approved', compact('leaders_ids'));
    }

    public function getFailedRefund(Request $request){
        $validator = Validator::make($request->all(), [
            'leaders_ids' => ['sometimes', 'nullable', 'array'],
            'leaders_ids.*' => ['sometimes', 'integer', 'exists:leaders,id']
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $leaders_ids = (array)$request->leaders_ids;

        return view('admin.admin-pages.compliance.refunds.failed', compact('leaders_ids'));
    }

}
