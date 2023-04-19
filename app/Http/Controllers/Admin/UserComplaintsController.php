<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintHistory;
use App\Models\ComplaintNote;
use App\Models\ComplaintType;
use App\Models\Customerly;
use App\Models\Leader;
use App\Models\Rookie;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLoginHistory;
use App\Utils\Utils;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserComplaintsController extends Controller
{
    private $complaints_table = 'complaints';

    public function getUserComplaintsApi(Request $request){
        $validator = Validator::make($request->all(), [
            "start" => 'string|nullable',
            "length" => 'string|nullable',
            "search" => 'nullable',
            "draw" => 'nullable',
            "status" => 'nullable',
        ]);

        if($validator->fails()){
            return response()->json(['status' => 404, 'message' => $validator->errors()->getMessages()], 404);
        }

        $offset = $request->has('start') ? $request->get('start') : 1;
        $limit = $request->has('length') ? $request->get('length') : 25;
        $search = $request->has('search') ? $request->search['value'] : null;


        $query = Complaint::leftJoin('users as user_reported_by', 'user_reported_by.id', '=', 'complaints.reported_by')

            ->join('users as user_reported', 'user_reported.id', '=', 'complaints.user_reported')
            ->select(
                'complaints.*',
                'user_reported_by.email as reported_by_email',
                'user_reported_by.username as reported_by_username',
                'user_reported_by.type as reported_by_type',
                'user_reported.username as user_reported_username'
            )
            ->selectRaw("(CASE WHEN $this->complaints_table.status = 'open' and $this->complaints_table.counter_follow_up > 0 and $this->complaints_table.follow_up IS null THEN 3 WHEN $this->complaints_table.status = 'open' and $this->complaints_table.counter_follow_up = 0 and $this->complaints_table.follow_up IS null THEN 2
             WHEN $this->complaints_table.status = 'open' and $this->complaints_table.follow_up IS NOT null THEN 1 ELSE 0 END) AS priority");

        $all = $query->count();

        switch ($request->status) {
            case 'closed':
            case 'open':
                $query->where('complaints.status', $request->status)
                    ->whereNull('follow_up');
                break;
            case 'follow_up':
                $query->whereNotNull('follow_up');
                break;
        }

        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('user_reported_by.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('user_reported_by.username', 'LIKE', '%' . $search . '%')
                    ->orWhere('user_reported.username', 'LIKE', '%' . $search . '%');
            });
        }


        $query->orderBy('priority', 'DESC')
            ->orderBy('complaints.created_at', 'DESC');

        $filtered = $query->count();

        $complaints = $query->offset($offset)
            ->limit($limit)
            ->get()
            ->each(function ($query){
                $query->type_name = ComplaintType::find($query->type_id);
            })
            ->append([]);

        $max_pages = ceil($all/$limit);

        $complaints_data = [];
        foreach ($complaints as $complaint){
            if($complaint->by_system){
                $complaint->reported_by_username = 'BY SYSTEM';
                $complaint->reported_by_type = 'SYSTEM';
            }

            $complaints_data[] = array(
                'id' => $complaint->id,
                'made_by_username' => $complaint->reported_by_username,
                'made_by_email' => $complaint->reported_by_email ?? null,
                'made_by_type' => $complaint->reported_by_type,
                'made_at' => $complaint->created_at->format('d/m/Y H:i:s'),
                'on_username' => $complaint->on_user->username,
                'type' => $complaint->type_name['name'],
                'status' => $complaint->status,
                'follow_up' => $complaint->follow_up,
                'url' => route('complaints.edit.get', $complaint->id),
                'counter' => $complaint->counter_follow_up,
                'priority' => $complaint->priority
            );
        }

        $data = [];
        $data["draw"] = intval($request->draw);
        $data['pages'] = $max_pages;
        $data['data'] = $complaints_data;
        $data['recordsTotal'] = $all;
        $data['recordsFiltered'] = $filtered;

        return response()->json($data);
    }

    public function getUserComplaints(){
        return view('admin.admin-pages.complaints.index');
    }

    public function editUserComplaint(request $request, $complaint_id){

        $validator = Validator::make($request->all(), [
            'new_status' => 'string|in:closed,48h'
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        if($request->has('new_status') && !empty($request->new_status)){

            $complaint = Complaint::query()->find($complaint_id);
            if($request->new_status == 'closed'){
                $complaint->update([
                    'status' => $request->new_status,
                    'follow_up' => null
                ]);

            }elseif($request->new_status == '48h'){
                $today = Carbon::now();
                $counter = $complaint->counter_follow_up + 1;

                $complaint->update([
                    'follow_up' => $today->addHours(48),
                    'counter_follow_up' => $counter
                ]);
            }

            $this->addActionToHistory($complaint_id, $request->new_status);
        }

        $rookies_table = (new Rookie())->getTable();
        $user_table = (new User())->getTable();
        $leader_table = (new Leader())->getTable();

        $complaint = Complaint::query()->where('complaints.id', $complaint_id)
            ->join('users as user_reported_by', 'user_reported_by.id', '=', 'complaints.reported_by')
            ->join('users as user_reported', 'user_reported.id', '=', 'complaints.user_reported')
            ->select('complaints.*', 'user_reported_by.type as reported_by_type', 'user_reported.type as user_reported_type')
            ->first();

        $type = ComplaintType::find($complaint->type_id);

        $type_name = $type->name;

        $user_reported_query = User::query()->where('users.id', $complaint->user_reported);

        if($user_reported_query->first()->type == 'rookie'){
            $user_reported_query->join($rookies_table, "$rookies_table.id", '=', "$user_table.id");
        }elseif($user_reported_query->first()->type == 'leader'){
            $user_reported_query->join($leader_table, "$leader_table.id", '=', "$user_table.id");
        }

        $user_reported = $user_reported_query->first();

        if($user_reported->type == 'leader'){
            $packages_bought = Transaction::query()->where('leader_id', $user_reported->id)->where('type', '=', 'bought_micromorgi')->count();
            $user_reported->packages_bought = $packages_bought;
        }

        $morgi_tot = Transaction::query()
            ->where($user_reported->type.'_id', $user_reported->id)
            ->whereNull('refund_type')
            ->where('type', 'gift')
            ->get();

        if($user_reported->type == 'leader'){
            $user_reported->tot_morgi = $morgi_tot->sum('morgi');
        }elseif($user_reported->type == 'rookie'){
            $user_reported->tot_morgi = $morgi_tot->sum('taxed_morgi');
        }

        $user_reported_plat = UserLoginHistory::query()->where('user_id', $user_reported->id)->where('is_signup_values', 1)->first();

        $user_reported->platform = $user_reported_plat->user_agent ?? 'User agent not found';

        $counter_reporting_reported = Complaint::query()->where('user_reported', $user_reported->id)->count();

        $user_reported->counter_report = $counter_reporting_reported;

        $reported_by_query = User::query()->where('users.id', $complaint->reported_by);

        if($reported_by_query->first()->type == 'rookie'){
            $reported_by_query->join($rookies_table, "$rookies_table.id", '=', "$user_table.id");
        }elseif($reported_by_query->first()->type == 'leader'){
            $reported_by_query->join($leader_table, "$leader_table.id", '=', "$user_table.id");
        }

        $reported_by = $reported_by_query->first();

        if($reported_by->type == 'leader'){
            $packages_bought = Transaction::query()->where('leader_id', $reported_by->id)->where('type', '=', 'bought_micromorgi')->count();
            $reported_by->packages_bought = $packages_bought;
        }

        unset($morgi_tot);
        $morgi_tot = Transaction::query()
            ->where($reported_by->type.'_id', $reported_by->id)
            ->whereNull('refund_type')
            ->where('type',  'gift')
            ->get();

        if($reported_by->type == 'leader'){
            $reported_by->tot_morgi = $morgi_tot->sum('morgi');
        }elseif($reported_by->type == 'rookie'){
            $reported_by->tot_morgi = $morgi_tot->sum('taxed_morgi');
        }

        $user_reported_by_plat = UserLoginHistory::query()->where('user_id', $reported_by->id)->where('is_signup_values', 1)->first();

        $reported_by->platform = $user_reported_by_plat->user_agent ?? 'User agent not found';

        $counter_reporting_by = Complaint::query()->where('user_reported', $reported_by->id)->count();

        $reported_by->counter_report = $counter_reporting_by;

        $notes = ComplaintNote::query()->where('complaint_id', $complaint_id)->join('users', 'users.id', '=', 'complaints_notes.admin_id')->select('complaints_notes.*', 'users.email as email')->latest()->get();

        return view('admin.admin-pages.complaints.edit', compact('notes', 'complaint', 'user_reported', 'reported_by', 'type_name'));
    }

    private function addActionToHistory($complaint_id, $action, $note = null){

        if($action == '48h'){
            $string = '48H follow up';
        }else if ($action == 'closed'){
            $string = 'closed';
        }else if ($action == 'sent'){
            $string = 'sent message';
        }else if($action == 'note'){
            $string = 'Note added';
            $note = Utils::truncateString($note, 20, false);
        }

        ComplaintHistory::create([
            'complaint_id' => $complaint_id,
            'admin_id' => Auth::id(),
            'action' => $string,
            'note' => $note
        ]);
    }

    public function createComplaintNote(request $request, $complaint_id){

        $data = $request->all();
        $data['complaint_id'] = $complaint_id;

        $validator = Validator::make($data, [
            'new_note' => 'required',
            'complaint_id' => 'required'
        ]);

        if ($validator->fails()) {
            return \redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }


        $created_note = ComplaintNote::create(['complaint_id' => $complaint_id, 'admin_id' => Auth::id(), 'note' => $request->new_note]);

        $this->addActionToHistory($complaint_id, 'note', $request->new_note);

        if (!empty($created_note)) {
            return \redirect()->back()->with(['success' => 'Note created']);
        }

        return \redirect()->back()->with(['fail' => 'Creation note failed']);
    }

    public function showUserComplaintHistory($id){

        $complaint = Complaint::find($id);

        $type = ComplaintType::find($complaint->type_id);

        $type_name = $type->name;

        $history = ComplaintHistory::query()->where('complaint_id', $id)->get();

        $notes = ComplaintNote::query()->where('complaint_id', $id)->join('users', 'users.id', '=', 'complaints_notes.admin_id')->select('complaints_notes.*', 'users.email as email')->get();

        return view('admin.admin-pages.complaints.show_history', compact('complaint', 'history', 'type_name', 'notes'));
    }

    public function getChat($reported, $reported_by){

        $user_reported = User::find($reported);
        $user_reported_by = User::find($reported_by);

        return view('admin.admin-pages.chat.show')->with(['user_reported' => $user_reported, 'user_reported_by' => $user_reported_by]);
    }

    public function getCustomerlyId(Request $request){

        $user = User::query()->find($request->user_id);

        $user_email = $user->email;
        $customerly = new Customerly();
        $user_data = $customerly->getUserData($user_email);

        if(!$user_data){
            $customerly->createUser($request->user_id, $user_email, $user->full_name);
            $user_data = $customerly->getUserData($user_email);
        }

        $crmhero_user_id = $user_data['data']['crmhero_user_id'];

        return "https://app.customerly.io/projects/45a1bcfa/conversations/1/".rand(3000, 5000)."/new/$crmhero_user_id";
    }
}
