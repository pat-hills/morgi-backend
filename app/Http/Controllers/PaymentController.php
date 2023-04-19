<?php

namespace App\Http\Controllers;

use App\Logger\Logger;
use App\Models\Country;
use App\Models\PaymentPlatform;
use App\Models\PaymentPlatformRookie;
use App\Models\PaymentPlatformRookieHistory;
use App\Models\Rookie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $payments_rookies = PaymentPlatformRookie::where('rookie_id', $user->id)
            ->orderBy('id')
            ->get();

        return response()->json($payments_rookies);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $rookie = Rookie::query()->find($user->id);

        if(!isset($user->id_verified['card_id_status']) || $user->id_verified['card_id_status']!=='approved'){
            return response()->json(['message' => trans('rookie.id_not_verified')], 400);
        }

        $validator = Validator::make($request->all(), [
            'payment_platform_id' => ['required', 'exists:payments_platforms,id'],
            'payment_info' => ['required', 'array']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400 );
        }

        $payment_exists = PaymentPlatformRookie::where('rookie_id', $rookie->id)
            ->where('payment_info', json_encode($request->payment_info))
            ->where('payment_platform_id', $request->payment_platform_id)
            ->exists();

        if($payment_exists){
            return response()->json(['message' => 'This payment method already exist'], 400);
        }

        $platform = PaymentPlatform::query()->find($request->payment_platform_id);

        switch (strtolower($platform->name)) {
            case 'epay':

                $country = Country::find($rookie->country_id);
                if (isset($country) && $country->alpha_3_code === 'USA') {
                    try {
                        $this->validateUSAEpay($request->payment_info);
                    } catch (\Exception $exception) {
                        Logger::logException($exception);
                        return response()->json(json_decode($exception->getMessage()), 400);
                    }
                }
                break;
            case 'paxum':

                try {
                    $this->validatePaxum($request->payment_info);
                } catch (\Exception $exception) {
                    Logger::logException($exception);
                    return response()->json(json_decode($exception->getMessage()), 400);
                }
                break;
        }

        DB::beginTransaction();
        try {
            PaymentPlatformRookie::where('rookie_id', $rookie->id)->update(['main' => 0]);

            $payment_platform = PaymentPlatformRookie::create([
                'rookie_id' => $rookie->id,
                'payment_platform_id' => $request->payment_platform_id,
                'main' => true,
                'payment_info' => json_encode($request->payment_info)
            ]);

            PaymentPlatformRookieHistory::create([
                'rookie_id' => $rookie->id,
                'payments_platforms_rookies_id' => $payment_platform->id,
                'payment_platform_id' => $request->payment_platform_id
            ]);

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            Logger::logException($exception);
            return response()->json(['message' => 'Internal server error, try later!'], 500);
        }

        return response()->json($payment_platform, 201);
    }

    public function update(Request $request, PaymentPlatformRookie $paymentPlatformRookie)
    {
        $user = $request->user();
        if(!isset($user->id_verified['card_id_status']) || $user->id_verified['card_id_status']!=='approved'){
            return response()->json(['message' => trans('rookie.id_not_verified')], 400);
        }

        $validator = Validator::make($request->all(), [
            'main' => ['sometimes', 'boolean']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400 );
        }

        if(!isset($paymentPlatformRookie) || $paymentPlatformRookie->rookie_id !== $user->id){
            return response()->json(['message' => trans('payment.payment_not_found')], 404);
        }

        if($request->main){
            PaymentPlatformRookie::where('rookie_id', $user->id)->update(['main' => 0]);
            $paymentPlatformRookie->main = true;
        }

        $paymentPlatformRookie->save();
        $paymentPlatformRookie->refresh();

        return response()->json($paymentPlatformRookie);
    }

    public function show(PaymentPlatformRookie $paymentPlatformRookie)
    {
        if(!isset($paymentPlatformRookie) || $paymentPlatformRookie->rookie_id !== Auth::id()){
            return response()->json(['message' => trans('payment.payment_not_found')], 404);
        }

        return response()->json($paymentPlatformRookie);
    }

    public function delete(PaymentPlatformRookie $paymentPlatformRookie)
    {
        if(!isset($paymentPlatformRookie) || $paymentPlatformRookie->rookie_id !== Auth::id()){
            return response()->json(['message' => trans('payment.payment_not_found')], 404);
        }

        try {
            $paymentPlatformRookie->delete();
        }catch (\Exception $exception){
            Logger::logException($exception);
            return response()->json(['message' => 'Internal server error, try later!'], 500);
        }

        return response()->json([]);
    }

    public function platforms()
    {
        $platforms = Cache::get('platforms');
        if (isset($platforms)) {
            return response()->json($platforms);
        }

        $platforms = PaymentPlatform::all();

        Cache::put('platforms', $platforms, 86400);

        return response()->json($platforms);
    }

    public function validateUSAEpay($form)
    {
        $validator = Validator::make($form, [
            'account_holder' => ['required'],
            'beneficiary_type' => ['required', 'in:individual,business'],
            'email' => ['required', 'email'],
            'document_id' => ['required', 'string', 'max:4'],
            'city' => ['required'],
            'address' => ['required'],
            'state' => ['required'],
            'zip_code' => ['required', 'max:5'],
            'phone' => ['required'],
            'account_type' => ['required', 'in:savings,checking'],
            'bank_account_number' => ['required'],
            'bank_routing_number' => ['required', 'max:9'],
            'bank_name' => ['required'],
            'bank_city' => ['required'],
            'bank_address' => ['required'],
            'bank_state' => ['required'],
            'bank_zip_code' => ['required', 'max:5']
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors());
        }

    }

    public function validatePaxum($form)
    {
        $validator = Validator::make($form, [
            'account_type' => ['required', 'in:personal,business'],
            'email' => ['required', 'email'],
            'full_name' => ['required_if:account_type,personal'],
            'birth_date' => ['required_if:account_type,personal'],
            'company_name' => ['required_if:account_type,business'],
            'business_number' => ['required_if:account_type,business'],
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors());
        }
    }

}
