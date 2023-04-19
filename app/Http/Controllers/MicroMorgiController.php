<?php

namespace App\Http\Controllers;

use App\Ccbill\CcbillUtils;
use App\Http\Resources\MicromorgiPackagesResource;
use App\LeaderPackages\SpendMicromorgi;
use App\Logger\Logger;
use App\Mixpanel\Events\EventBuyMicromorgiSuccess;
use App\Mixpanel\Events\EventGiftMicromorgiSuccess;
use App\Models\Leader;
use App\Models\MicromorgiPackage;
use App\Models\Rookie;
use App\Models\Transaction;
use App\Transactions\MicroMorgi\TransactionBoughtMicromorgi;
use App\Transactions\MicroMorgi\TransactionChat;
use App\Utils\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MicroMorgiController extends Controller
{
    public function indexPackages(Request $request)
    {
        $packages = Cache::get("micro_morgi_packages:{$request->response_type}");
        if (isset($packages)) {
            return response()->json($packages);
        }

        $packages = MicromorgiPackage::query()
            ->get()
            ->sortBy('sort_order')
            ->values();

        $packages = MicromorgiPackagesResource::compute($request, $packages)->get();

        Cache::put("micro_morgi_packages:{$request->response_type}", $packages, 86400);

        return response()->json($packages);
    }

    public function buyMicromorgi(Request $request, MicromorgiPackage $micromorgiPackage)
    {
        $leader_user = $request->user();
        $leader = Leader::find($leader_user->id);

        try {
            $leader->canBuyMicroMorgi($micromorgiPackage->price);
        }catch (\Exception $exception){
            return response()->json(['message' => 'You cant buy micromorgi', 'type' => $exception->getMessage()], 400);
        }

        try {
            $result = $leader->attemptPaymentWithPaymentMethods($micromorgiPackage->price, $micromorgiPackage->micromorgi_count, Utils::getRealIp($request));
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => $exception->getMessage(), 'type' => 'generic'], 400);
        }

        if($result['status']===false){
            $url = CcbillUtils::jpostMicromorgi($leader, $micromorgiPackage->micromorgi_count, $micromorgiPackage->price);
            return response()->json(['url' => $url, 'type' => 'credit_card'], 303);
        }

        DB::beginTransaction();
        try {
            TransactionBoughtMicromorgi::create(
                $leader->id,
                $micromorgiPackage->price,
                $result['payment_method_id'],
                $result['subscriptionId'],
                Utils::getRealIp($request)
            );
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => "Error during the creation of the transactions!", 'exception' => $exception->getMessage(), 'type' => 'generic'], 400);
        }

        try {
            EventBuyMicromorgiSuccess::config($leader->id, $micromorgiPackage->micromorgi_count, true);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json(['message' => trans('leader.micromorgi_transaction_successful')]);
    }

    public function sendMicromorgi(Request $request, Rookie $rookie, int $micromorgi_amount)
    {
        $leader_user = $request->user();
        $leader = Leader::find($leader_user->id);

        if (!$rookie->active){
            return response()->json(['message' => trans('auth.account_not_active')], 400);
        }

        if ($rookie->hasBlockedLeader($leader->id)){
            return response()->json(['message' => 'This rookie has blocked you'], 403);
        }

        if ($leader->micro_morgi_balance < $micromorgi_amount) {
            return response()->json(['message' => trans('leader.low_micromorgi')], 400);
        }

        DB::beginTransaction();
        try {
            $transaction = TransactionChat::create($rookie->id, $leader->id, $micromorgi_amount);
            SpendMicromorgi::spend($transaction);
            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json([
                'message' => 'Error occurred during the transaction',
                'error' => $exception->getMessage()
            ], 400);
        }

        $is_first_mm_transaction = Transaction::query()
            ->where('leader_id', $leader->id)
            ->where('type', 'chat')
            ->count() === 1;

        if($is_first_mm_transaction){
            $rookie->increment('first_micromorgi_gift_leaders');
        }

        try {
            EventGiftMicromorgiSuccess::config($leader->id, $transaction);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return response()->json(['message' => trans('leader.micromorgi_transaction_successful')]);
    }
}
