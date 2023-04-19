<?php


namespace App\Http\Controllers\Admin;


use App\Ccbill\CcbillCurrencyCodes;
use App\Http\Controllers\Controller;
use App\Models\LeaderPayment;
use App\Models\Transaction;
use App\Models\User;
use App\Transactions\MicroMorgi\TransactionRookieFineMicromorgi;
use App\Transactions\Morgi\TransactionRookieFineMorgi;
use App\Utils\ReasonUtils;
use App\Utils\TransactionRefundUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{

    public function refundTransaction(Request $request){

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
            'transaction_id'=> 'required|exists:transactions,id',
        ]);

        if ($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $transaction = Transaction::find($request->transaction_id);
        if(!isset($transaction)){
            return redirect()->back()->with(['fail' => "Transaction not found"]);
        }

        DB::beginTransaction();
        try {

            if(isset($transaction->leader_payment_id)){
                TransactionRefundUtils::createTransactionRefund($transaction, ReasonUtils::ALL_REASON[$request->reason] ?? $request->reason, Auth::id());
            }else{
                TransactionRefundUtils::config($transaction)->refund(Auth::id(), $request->reason);
            }

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            return redirect()->back()->with(['fail' => $e->getMessage()]);
        }

        $success_message = "Refunded successfully";

        if(!is_null($transaction->leader_payment_id)){
            $success_message = "Refund request sent successfully!";
        }

        return redirect()->back()->with(['success' => $success_message]);
    }

    public function fineUser(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'gt:0', 'integer', 'exists:users,id'],
            'amount' => ['required', 'gt:0', 'integer'],
            'type' => ['required', 'in:morgi,micromorgi'],
            'reason' => ['required']
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $user = User::query()->find($request->user_id);
        if($user->type !== 'rookie'){
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        try {
            if($request->type === 'micromorgi'){
                TransactionRookieFineMicromorgi::create(
                    $user->id,
                    $request->amount,
                    Auth::id(),
                    $request->reason
                );
            }else{
                TransactionRookieFineMorgi::create(
                    $user->id,
                    $request->amount,
                    Auth::id(),
                    $request->reason
                );
            }
        }catch (\Exception $exception){
            return redirect()->back()->with(['fail' => "Couldn't make a fine to the user"]);
        }

        return redirect()->back()->with(['success' => "Created a fine to rookie"]);

    }

    public function search(Request $request){

        if($request->has('data') && !is_null($request->data)){

            $transaction = LeaderPayment::query()
                ->select(
                    'leaders_payments.id',
                    'leaders_payments.ccbill_subscriptionId',
                    'leaders_payments.ccbill_transactionId'
                )
                ->join('users as ul', 'ul.id', '=', 'leaders_payments.leader_id')
                ->join('leaders_ccbill_data', 'leaders_ccbill_data.id', '=', 'leaders_payments.leader_payment_method_id')
                ->join('transactions', 'transactions.leader_payment_id', '=', 'leaders_payments.id')
                ->leftjoin('users as ur', 'ur.id', '=', 'transactions.rookie_id')
                ->where('ccbill_subscriptionId', $request->data)
                ->orWhere('ccbill_transactionId', $request->data)
                ->first();

            if(isset($transaction)){
                return redirect()->to(route('transaction.show2', $transaction->id));
            }

            return redirect()->back()->with(['fail' => 'Transaction not found']);
        }

        return view('admin.admin-pages.transaction.search');
    }

    public function show($leader_payment_id){

        $validator = Validator::make([
            'leader_payment_id' => $leader_payment_id
        ], [
            'leader_payment_id' => ['required', 'exists:leaders_payments,id']
        ]);

        if ($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $select = [
            'transactions.id',
            'transactions.type',
            'transactions.dollars',
            'transactions.morgi',
            'transactions.micromorgi',
            'transactions.internal_id',
            'transactions.rookie_id',
            'transactions.leader_id',
            'transactions.refund_type',
            'transactions.refunded_at',
            'transactions.refunded_by',
            'transactions.referal_internal_id',
            'leaders_payments.ccbill_subscriptionId',
            'leaders_payments.ccbill_transactionId',
            'leaders_payments.ip_address',
            'leaders_payments.created_at',
            'leaders_ccbill_data.cardType',
            'leaders_ccbill_data.billedCurrencyCode',
            'ul.email as leader_email',
            'ur.email as rookie_email',
            'transactions_refunds.status',
            'transactions_refunds.error'
        ];

        $transaction = LeaderPayment::query()
            ->select($select)
            ->join('users as ul', 'ul.id', '=', 'leaders_payments.leader_id')
            ->join('leaders_ccbill_data', 'leaders_ccbill_data.id', '=', 'leaders_payments.leader_payment_method_id')
            ->join('transactions', 'transactions.leader_payment_id', '=', 'leaders_payments.id')
            ->leftjoin('users as ur', 'ur.id', '=', 'transactions.rookie_id')
            ->leftJoin('transactions_refunds', function($join) {
                $join->on('transactions_refunds.transaction_id', '=', 'transactions.id')
                    ->on('transactions_refunds.id', '=', DB::raw("(select max(id) from transactions_refunds WHERE transactions_refunds.transaction_id = transactions.id)"));
            })
            ->where('leaders_payments.id', $leader_payment_id)
            ->first();

        if(!isset($transaction)){
            return redirect()->back()->with(['fail' => "Unable to retrieve transaction"]);
        }

        if($transaction->refunded_by){
            $user = User::query()->find($transaction->refunded_by);
            $transaction->refunded_by_username = (isset($user)) ? $user->username : "Username of admin id $transaction->refunded_by no found";
        }

        $transaction->billedCurrencyCodeLabel = CcbillCurrencyCodes::CURRENCY[$transaction->billedCurrencyCode] ?? null;

        return view('admin.admin-pages.transaction.show2', compact('transaction'));
    }
}
