<?php

namespace App\Http\Controllers;

use App\Models\FakeLeaderQuotation;
use App\Models\LeaderQuotation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LeaderQuotationController extends Controller
{
    public function index(Request $request)
    {
        $leader_user = $request->user();
        $fake_leader_quotations = FakeLeaderQuotation::all()->map(function ($fake_leader_quotation){
            return [
                'username' => $fake_leader_quotation->username,
                'avatar' => [
                    'url' => $fake_leader_quotation->avatar_url
                ],
                'leader_since' => $fake_leader_quotation->leader_since,
                'dollar_amount_gifted' => $fake_leader_quotation->dollar_amount_gifted,
                'text' => $fake_leader_quotation->text
            ];
        });

        $leader_quotation = LeaderQuotation::query()->where('user_id', $leader_user->id)->first();
        if(isset($leader_quotation)){
            $fake_leader_quotations->prepend([
                'username' => $leader_user->username,
                'avatar' => [
                    'url' => $leader_user->getOwnAvatar()->url ?? null
                ],
                'leader_since' => $leader_user->created_at,
                'text' => $leader_quotation->text,
                'dollar_amount_gifted' => Transaction::query()->where('leader_id', $leader_user->id)
                    ->whereIn('type', ['gift', 'chat'])
                    ->whereNull('refund_type')
                    ->sum('dollars'),
            ]);
        }

        return response()->json($fake_leader_quotations);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => ['required', 'string', 'max:150'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader_user = $request->user();

        if(LeaderQuotation::query()->where('user_id', $leader_user->id)->exists()){
            return response()->json(['message' => 'Quotation already exist'], 400);
        }

        $leader_quotation = LeaderQuotation::query()->create([
            'user_id' => $leader_user->id,
            'text' => $request->text
        ]);

        return response()->json($leader_quotation, 201);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'text' => ['required', 'string', 'max:150'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $leader_user = $request->user();

        $leader_quotation = LeaderQuotation::query()->where('user_id', $leader_user->id)->first();
        if (!isset($leader_quotation)) {
            return response()->json([], 404);
        }

        $leader_quotation->update([
            'text' => $request->text
        ]);

        return response()->json($leader_quotation);
    }

}
