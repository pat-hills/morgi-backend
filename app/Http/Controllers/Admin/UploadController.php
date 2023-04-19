<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rookie;
use App\Models\User;
use App\Transactions\MicroMorgi\TransactionRookieMicromorgiBonus;
use App\Utils\NotificationUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function index(Request $request){
        $users = [];
        if ($request->hasFile('file')) {

            $validator = Validator::make($request->all(), [
                'file' => ['sometimes', 'mimes:csv,txt']
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
            }

            if (($handle = fopen($request->file('file'), "r")) !== FALSE) {

                $counter = 0;
                while (($data = fgetcsv($handle, 1000)) !== FALSE) {

                    if ($counter == 0 && count($data) != 3) {
                        return redirect()->back()->with(['fail' => 'Invalid CSV columns. Please fix it. [email, amount, reason]']);
                    }

                    $counter++;
                    if ($counter > env('MAX_INPUT_VARS', 190)) {
                        return redirect()->back()->with(['fail' => 'CSV File too large']);
                    }

                    list($email, $amount, $reason) = $data;

                    if ($email == 'email' || $amount == 'amount' || $reason == 'reason') {
                        continue;
                    }

                    $users[] = [
                        'email' => $email,
                        'amount' => trim($amount),
                        'reason' => $reason
                    ];

                }
                fclose($handle);
            }

            foreach ($users as $key => &$user){
                if (!filter_var($user['email'], FILTER_VALIDATE_EMAIL)) {
                    $users[$key]['result'] = "Email not valid";
                    $users[$key]['id'] = null;
                    continue;
                }

                $user_found = User::where('email', 'LIKE', '%' . $user['email'] . '%')
                    ->whereIn('type', ['rookie'])
                    ->first();

                if(!isset($user_found)){
                    $users[$key]['result'] = "Didn't find email";
                    $users[$key]['id'] = null;
                    continue;
                }

                if(!is_null($user_found->deleted_at)){
                    $users[$key]['result'] = "User deleted";
                    $users[$key]['id'] = null;
                    continue;
                }

                if(empty($user['amount']) || $user['amount'] < 0){
                    $users[$key]['result'] = 'Amount not valid';
                    $users[$key]['id'] = null;
                    continue;
                }

                $users[$key]['result'] = 200;
                $users[$key]['id'] = $user_found->id;
            }
        }

        return view('admin.admin-pages.micromorgi-bonus.index', compact('users'));
    }

    public function doubleCheck(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'amount' => ['required']
        ],
        [
            'email.required' => 'Email not valid',
            'amount.required' => 'Amount not valid'
        ]);

        if ($validator->fails()) {
            return json_encode(array('error' => $validator->errors()->first()), 400);
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email not valid");
        }

        $user_found = User::where('email', 'LIKE', '%' . $request->email . '%')
            ->whereIn('type', ['rookie'])
            ->first();
        if(!isset($user_found)){
            throw new \Exception("Didn't find email");
        }

        if(!is_null($user_found->deleted_at)){
            throw new \Exception("User deleted");
        }

        if(empty($request->amount) || $request->amount < 0){
            throw new \Exception("Amount not valid");
        }

        return json_encode(array('id' => $user_found->id), 200);
    }

    public function send(Request $request){

        $validator = Validator::make($request->all(), [
            'users' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return json_encode(array('error' => $validator->errors()->first()), 400);
        }

        $fails = [];
        $good = 0;
        foreach ($request->users as $key => $user){
            if((int)$user['result'] !== 200){
                $fails[$key] = $user;
                continue;
            }

            if(is_null($user['user_id'])){
                $fails[$key] = $user;
                $fails[$key]['extra_error'] = 'User not found';
                continue;
            }

            $user_found = Rookie::query()->find($user['user_id']);
            if(!isset($user_found)){
                $fails[$key] = $user;
                $fails[$key]['extra_error'] = 'User not found';
                continue;
            }

            DB::beginTransaction();
            try {
                TransactionRookieMicromorgiBonus::create(
                    $user['user_id'],
                    $user['amount'],
                    Auth::id(),
                    $user['reason']
                );

                NotificationUtils::sendNotification($user['user_id'], 'user_got_bonus', now(), [
                    'amount_micromorgi' => $user['amount']
                ]);
                DB::commit();
            }catch (\Exception $exception){
                DB::rollBack();
                $fails[$key] = $user;
                $fails[$key]['extra_error'] = $exception->getMessage();
            }

            $good++;
        }

        return view('admin.admin-pages.micromorgi-bonus.index', compact('fails', 'good'));
    }
}
