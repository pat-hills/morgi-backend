<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class AdminApiComplaintController extends Controller
{

    public function getAllComplaints(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'start' => 'string|nullable',
            'length' => 'string|nullable',
            'search' => 'nullable',
            'draw' => 'nullable',
            'status' => 'nullable',
            'about_user_id' => ['sometimes'],
            'from_user_id' => ['sometimes']
        ]);

        if($validator->fails()){
            return response()->json(['status' => 404, 'message' => $validator->errors()->getMessages()], 404);
        }

        $offset = $request->has('start') ? $request->get('start') : 1;
        $limit = $request->has('length') ? $request->get('length') : 25;
        $search = $request->has('search') ? $request->search['value'] : null;

        $select = [
            'complaints.*',
            'user_reported_by.email as reported_by_email',
            'user_reported_by.username as reported_by_username',
            'user_reported_by.type as reported_by_type',
            'user_reported.username as user_reported_username'
        ];

        /*
         * 48H follow up is basically 'Show this complaint again after 48h'
         * LEVEL PRIORITY
         *  3 - Open Complaints without follow up but it was in follow up
         *  2 - Open Complaints without follow up but it never be in follow up
         *  1 - Open complaints with follow up active
         *  0 - Closed complaints
         */

        $query = Complaint::query()
            ->select($select)
            ->selectRaw(
                "(CASE
                WHEN complaints.status = 'open' and complaints.counter_follow_up > 0 and complaints.follow_up IS null THEN 3
                WHEN complaints.status = 'open' and complaints.counter_follow_up = 0 and complaints.follow_up IS null THEN 2
                WHEN complaints.status = 'open' and complaints.follow_up IS NOT null THEN 1
                ELSE 0 END) AS priority")
            ->leftJoin('users as user_reported_by', 'user_reported_by.id', '=', 'complaints.reported_by')
            ->join('users as user_reported', 'user_reported.id', '=', 'complaints.user_reported')
            ->orderByDesc('id');

        if ($request->has('about_user_id') && !empty($request->about_user_id)){
            $query->where('complaints.user_reported', $request->about_user_id);
        }

        if ($request->has('from_user_id') && !empty($request->from_user_id)){
            $query->where('complaints.reported_by', $request->from_user_id);
        }

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

        $query->orderBy('priority', 'DESC');
        switch ($request->status) {
            case 'follow_up':
                $query->orderBy('complaints.follow_up');
                break;
            default:
                $query->orderByRaw(
                    "CASE
                            WHEN complaints.follow_up IS NOT NULL THEN complaints.follow_up
                            ELSE complaints.created_at END
                            ASC");
                break;
        }

        $filtered = $query->count();

        $complaint_types = ComplaintType::all()->pluck('name', 'id');

        $query->offset($offset)->limit($limit);
        $complaints = $query->get()->append([]);

        $max_pages = ceil($all/$limit);

        $complaints_data = [];
        foreach ($complaints as $complaint){
            $complaints_data[] = array(
                'id' => $complaint->id,
                'made_by_username' => ($complaint->by_system) ? 'BY SYSTEM' : $complaint->reported_by_username,
                'made_by_email' => ($complaint->by_system) ? '' : $complaint->reported_by_email ?? null,
                'made_by_type' => ($complaint->by_system) ? 'SYSTEM' : $complaint->reported_by_type,
                'made_at' => $complaint->created_at->format('d/m/Y H:i:s'),
                'on_username' => $complaint->on_user->username,
                'on_email' => $complaint->on_user->email,
                'type' => $complaint_types[$complaint->type_id],
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
}
