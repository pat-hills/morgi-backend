<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Logger\Logger;
use App\Models\ActivityLog;
use App\Models\Goal;
use App\Models\LeaderPayment;
use App\Models\Payment;
use App\Models\PaymentPlatform;
use App\Models\PaymentRookie;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Chat\Utils;
use App\Transactions\MicroMorgi\TransactionRookieMicromorgiBonus;
use App\Utils\Admin\UserRelatedAccountUtils;
use App\Transactions\MicroMorgi\TransactionLeaderMicromorgiBonus;
use App\Utils\NotificationUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserTabsController extends Controller
{
    private $users_table = 'users';
    private $rookies_table = 'rookies';
    private $leaders_table = 'leaders';
    private $transactions_table = 'transactions';
    private $payments_rookies_table = 'payments_rookies';
    private $countries_table = 'countries';

    public function getSubscriptionPaymentHistory($leader_payment_id){

        $sub = LeaderPayment::find($leader_payment_id);

        if(!isset($sub)){
            return response()->json(['status' => 404, 'message' => 'Subscription not found'], 404);
        }

        return response()->json($sub->subscription_history);
    }

    public function getMicromorgi($id)
    {

        $user = User::query()->find($id);

        if (!isset($user)) {
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        $bonus_micromorgi = Transaction::query()
            ->where('type', 'bonus')
            ->whereNotNull(['micromorgi'])
            ->whereNull(['coupon_id'])
            ->where(function ($query) use ($id) {
                $query->where('leader_id', $id)
                    ->orWhere('rookie_id', $id);
            })
            ->get();

        return view('admin.admin-pages.user_profile.' . $user->type . '.' . $user->type . '_micromorgi', compact('user', 'bonus_micromorgi'));

    }

    public function addBonusMicroMorgi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|gt:0|exists:users,id',
            'amount' => 'required|integer',
            'bonus_reason' => 'required',
            'comments' => 'nullable'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $user = User::query()->find($request->user_id);
        if(in_array($user->type, ['admin', 'operator'])){
            return redirect()->back()->with(['fail' => 'User not valid']);
        }

        DB::beginTransaction();
        try {
            switch ($user->type) {
                case 'rookie':
                    TransactionRookieMicromorgiBonus::create(
                        $request->user_id,
                        $request->amount,
                        Auth::id(),
                        $request->bonus_reason,
                        $request->comments
                    );
                    break;
                case 'leader':
                    TransactionLeaderMicromorgiBonus::create(
                        $request->user_id,
                        $request->amount,
                        Auth::id(),
                        $request->bonus_reason,
                        $request->comments
                    );
                    break;
                default:
                    return redirect()->back()->with(['fail' => 'User not valid']);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()->back()->with(['fail' => $exception->getMessage()]);
        }

        try {
            NotificationUtils::sendNotification($request->user_id, 'user_got_bonus', now(), [
                'amount_micromorgi' => $request->amount
            ]);
        }catch (\Exception $exception){
            Logger::logException($exception);
        }

        return \redirect()->back()->with(['success' => 'Micro Morgi bonus added to User']);
    }

    public function getTransactions($id)
    {

        $user = User::query()->find($id);

        return view('admin.admin-pages.user_profile.leader.leader_transactions', compact('user'));
    }

    public function getActivityLog($id)
    {
        $user = User::query()->find($id);

        if($user->type === 'rookie'){
            $user = User::join("$this->rookies_table", "$this->rookies_table.id", '=', "$this->users_table.id")
                ->where("$this->users_table.id", $id)
                ->first();
        }elseif($user->type === 'leader'){
            $user = User::join("$this->leaders_table", "$this->leaders_table.id", '=', "$this->users_table.id")
                ->where("$this->users_table.id", $id)
                ->first();
        }

        $query = ActivityLog::query()
            ->where($user->type."_id", $id);

        $activities = $query->get();

        return view('admin.admin-pages.user_profile.'. $user->type .'.'. $user->type .'_activity_log', compact('user', 'activities'));
    }

    public function getPaymentHistory($id){

        try {
            $user = User::findOrFail($id);
        }catch (\Exception $ex){
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        if($user->type == 'leader'){
            return redirect()->back()->with(['fail' => 'Use must be rookie']);
        }

        $payment_table = (new Payment())->getTable();
        $payment_platform_table = (new PaymentPlatform())->getTable();
        $payment_rookie_table = (new PaymentRookie())->getTable();

        $payments = PaymentRookie::query()->where("$payment_rookie_table.rookie_id", '=', $id)
            ->join($this->rookies_table, "$this->rookies_table.id", '=', "$this->payments_rookies_table.rookie_id")
            ->join($payment_table, "$payment_table.id", '=', "$payment_rookie_table.payment_id")
            ->join($payment_platform_table, "$payment_platform_table.id", "=", "$payment_table.payment_platform_id")
            ->leftJoin($this->countries_table, "$this->rookies_table.country_id", '=', "$this->countries_table.id")
            ->select("$payment_rookie_table.*",
                "$payment_platform_table.name as platform_name",
                "$payment_table.payment_period_id",
                "$this->countries_table.name as country_name"
            )
            ->get();


        return view('admin.admin-pages.user_profile.rookie.rookie_payment_history', compact('user', 'payments'));
    }

    public function getComplaints($id)
    {

        $data = array("user_id" => $id);

        $validation = Validator::make($data, [
            'user_id' => 'required'
        ]);

        if($validation->fails()){
            return redirect()->back()->with(['fails' => $validation->errors()->messages()]);
        }

        $user = User::find($id);

        return view('admin.admin-pages.user_profile.'. $user->type .'.' . $user->type . '_complaints', compact('user'));
    }

    public function getRelatedAccount($id){

        $user = User::find($id);

        if(in_array($user->type, ['rookie', 'leader'])){
            return redirect(route("user.edit.related_account.$user->type", $id));
        }

        return redirect('/')->back()->with(['fail' => 'Something went wrong']);
    }

    public function getLeaderRelatedAccounts($id){
        $user = User::find($id);
        $response = UserRelatedAccountUtils::getLeaderRelatedAccount($user);

        return view('admin.admin-pages.user_profile.leader.leader_related-accounts')->with([
            'user' => $response['user'],
            'matched_users' => $response['matched_users'],
            'user_signup' => $response['user']->signup_login,
            'user_latest' => $response['user']->latest_login
        ]);
    }

    public function getRookieRelatedAccounts($id){
        $user = User::find($id);
        try {
            $response = UserRelatedAccountUtils::getRookieRelatedAccount($user);
        } catch (\Exception $exception) {
            return redirect()->back()->with(['fail' => $exception->getMessage()]);
        }

        return view('admin.admin-pages.user_profile.rookie.rookie_related-accounts')->with([
            'user' => $response['user'],
            'matched_users' => $response['matched_users'],
            'user_signup' => $response['user']->signup_login,
            'user_latest' => $response['user']->latest_login
        ]);
    }

    public function getCGBHistory($id)
    {
        $user = User::query()->find($id);

        if(empty($user)){
            return redirect()->back()->with(['fail' => 'User not found']);
        }

        $select = [
            "$this->transactions_table.*"
        ];

        $query = Transaction::query();

        $query->where("$this->transactions_table.refund_type", '=', 'chargeback');
        $query->where($user->type."_id", $id);

        $query->select($select);

        $transactions = $query->groupByRaw("MONTH($this->transactions_table.created_at)")
            ->get()
            ->each(function ($query) use ($id, $user) {
                /*
                 * Payment period
                 */

                //Monthly
                $period = $query->created_at->format('m');
                $query->period = $query->created_at->format('Y-M');

                if ($user->type == 'rookie') {
                    $base_query = Transaction::query()
                        ->where($user->type."_id", $id);

                    /*
                     * Earnings this pp period USD
                     */

                    // Guadagni effettivi in questo periodo
                    $transactions_pp_query =  clone $base_query;
                    $transactions_pp = $transactions_pp_query
                        ->where('type', '!=', 'refund')
                        ->whereNull('refund_type')
                        ->get();
                    $query->earning_pp_usd = $transactions_pp->sum('taxed_dollars');

                    /*
                     * Chargeback USD made this period
                     */

                    //Chargeback fatti in questo periodo in USD
                    $cgb_pp_query = clone $base_query;
                    $cgb_pp = $cgb_pp_query
                        ->where('type', '=', 'refund')
                        ->where('refund_type', 'chargeback')
                        ->get();
                    $query->cgb_pp = $cgb_pp->sum('taxed_dollars');

                    //Tutte le transazioni effettive (non refundate) + chargeback
                    $all_transactions_query = clone $base_query;
                    $all_transactions = $all_transactions_query
                        ->where('type', '!=', 'refund')
                        ->where(function ($query) {
                            $query->whereNull('refund_type')
                                ->orWhere('refund_type', 'chargeback');
                        })
                        ->get();

                    //Transazioni di questo mese di tipo chargeback
                    $all_pp_chargeback_query = clone $base_query;
                    $all_pp_chargeback = $all_pp_chargeback_query
                        ->where('type', '=', 'refund')
                        ->where('refund_type', 'chargeback')
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();

                    //Transazioni di questo mese
                    $all_pp_transactions_query = clone $base_query;
                    $all_pp_transactions = $all_pp_transactions_query
                        ->where('type', '!=', 'refund')
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();

                    /*
                     * Percentage of USD earnings CGB
                     * Tutte le transazioni di questo mese diviso i cgb fatti in questo mese, in percentuale di USD
                     */

                    $percentage_usd_trans = ($all_pp_chargeback->sum('taxed_dollars') / $all_pp_transactions->sum('taxed_dollars')) * 100;
                    $query->percentage_usd_trans = number_format($percentage_usd_trans, 2, '.', ' ');

                    /*
                     * Total CGb percent for all earnings and chargebacks
                     * Quanti CGB fatti / Tutte le transazioni effettive + chargeback = %
                     */

                    $percentage_cgb_trans = (count($all_pp_chargeback) / count($all_transactions)) * 100;
                    $query->percentage_cgb_trans = number_format($percentage_cgb_trans, 2, '.', ' ');
                    /*
                     *  Micromorgis USD refunded in this pay period actual micro refunds giveback as micros
                     *  Micromorgi refundati in questo periodo in micromorgi
                     */

                    //Micromorgi refundati di tipo void/refund in USD
                    $mm_refunded_query = clone $base_query;
                    $mm_refunded = $mm_refunded_query
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->whereNotNull('refund_type')
                        ->where('refund_type', '!=', 'chargeback')
                        ->where('type', 'chat')
                        ->get();

                    $query->mm_usd_refunded = $mm_refunded->sum('taxed_dollars') ?? 0;

                    /*
                     * Percent of micros earnings refunded for this Pay period USD
                     * Micromorgi refundati in questo periodo = %
                     */

                    //Transazioni di micromorgi di questo periodo
                    $micromorgi_pp_query = clone $base_query;
                    $micromorgi_pp = $micromorgi_pp_query
                        ->where('type', '=', 'chat')
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();

                    //Micromorgi di questo periodo refundati / Micromorgi totali in questo periodo
                    $percentage_usd_mm_refunded = ($query->mm_usd_refunded / $micromorgi_pp->sum('taxed_dollars')) * 100;
                    $query->percentage_usd_mm_refunded = number_format($percentage_usd_mm_refunded, 2, '.', ' ');

                    /*
                     * Total morgi refund percent total for all earnings and chargeback USD
                     * Totale dei morgi refundati / tutte le transazioni effettive + quelle chargeback = % USD
                     */

                    //Totale morgi refundati
                    $morgi_refunded_query = clone $base_query;
                    $morgi_refunded = $morgi_refunded_query
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->whereNotNull('refund_type')
                        ->where('type', 'gift')
                        ->get();
                    $percentage_morgi_refund = ($morgi_refunded->sum('taxed_dollars') / $all_transactions->sum('taxed_dollars')) * 100;
                    $query->percentage_morgi_refund = number_format($percentage_morgi_refund, 2, '.', ' ');

                } elseif ($user->type == 'leader') {

                    $base_query = Transaction::query()
                        ->where($user->type."_id", $id);

                    /*
                     * Total transactions amount this PP USD
                     * Tutte le transazioni di questo periodo in USD [gift, chat, bought_micromorgi]
                     */
                    $transactions_pp_query = clone $base_query;
                    $transactions_pp = $transactions_pp_query
                        ->whereIn('type', ['bought_micromorgi', 'chat', 'gift'])
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();
                    $query->tot_transations_amount_usd = $transactions_pp->sum('dollars');

                    /*
                     * Chargebacks USD made this period
                     * Chargeback fatti in questo periodo
                     */

                    $chargeback_pp_query = clone $base_query;
                    $chargeback_pp = $chargeback_pp_query
                        ->where('type', 'refund')
                        ->where('refund_type', 'chargeback')
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();
                    $query->cgb_pp = $chargeback_pp->sum('dollars');


                    /*
                     * Percent of usd chargeback
                     * Tutte le transazioni di questo mese diviso i cgb fatti in questo mese, in percentuale di USD
                     */
                    $cgb_percent_pp = ($query->cgb_pp / $query->tot_transations_amount_usd) * 100;
                    $query->cgb_percent_pp = number_format($cgb_percent_pp, 2, '.', ' ');

                    /*
                     * Total CGB percent for all trans and chargebacks
                     * Totale dei cgb, tutte le transazioni effettive (non refund) + refund di tipo chargeback e ritornare la percentuale di quei cgb su quelle trans.
                    */

                    //Transazioni effettive + quelle refundate di tipo chargeback
                    $transactions_query = clone $base_query;
                    $transactions = $transactions_query
                        ->where(function ($query) {
                            $query->whereNull('refund_type')
                                ->orWhere('refund_type', 'chargeback');
                        })
                        ->whereIn('type', ['bought_micromorgi', 'chat', 'gift'])
                        ->get();


                    //Chargeback totali
                    $cgb_tot_query = clone $base_query;
                    $cgb_tot = $cgb_tot_query
                        ->where('refund_type', 'chargeback')
                        ->whereIn('type', ['bought_micromorgi', 'chat', 'gift'])
                        ->get();

                    //Count(cgb totali)/count(transazioni totali)
                    $tot_cgb = (count($cgb_tot) / count($transactions)) * 100;
                    $query->tot_cgb = number_format($tot_cgb, 2, '.', ' ');

                    /*
                     * Micromorgis USD refunded in this period actual micro refund giveback as micros
                     * Prendere l'amount dei micromorgi delle transazioni di questo periodo di tipo chat e con il refund_type non null o != chargeback
                     */

                    $micromorgi_refunded = Transaction::query()
                        ->where($user->type."_id", $id)
                        ->where('type', 'chat')
                        ->where(function ($query) {
                            $query->whereNotNull('refund_type')
                                ->orWhere('refund_type', '!=', 'chargeback');
                        })
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();

                    $query->micromorgi_refunded = $micromorgi_refunded->sum('dollars');


                    /*
                     * Percent of micros refunded for this PP USD
                     * Micromorgi refundati in questo periodo USD / Tutte le transazioni di Micromorgi in questo periodo USD
                    */

                    // Micromorgi refundati in questo periodo
                    $micromorgi_refund_pp_query = clone $base_query;
                    $micromorgi_refund_pp = $micromorgi_refund_pp_query
                        ->where(function ($query) {
                            $query->where('type', 'chat')
                                ->orWhere('type', 'bought_micromorgi');
                        })
                        ->whereNotNull('refund_type')
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->get();

                    // Transazioni di micromorgi in questo periodo
                    $micromorgi_pp_query = clone $base_query;
                    $micromorgi_pp = $micromorgi_pp_query
                        ->whereRaw("MONTH($this->transactions_table.created_at) = $period")
                        ->where(function ($query) {
                            $query->where('type', 'chat')
                                ->orWhere('type', 'bought_micromorgi');
                        })
                        ->get();
                    $mm_percent_pp_usd = ($micromorgi_refund_pp->sum('dollars') / $micromorgi_pp->sum('dollars')) * 100;
                    $query->mm_percent_pp_usd = number_format($mm_percent_pp_usd, 2, '.', ' ');

                    /*
                     * Total Morgi Refund Percent total for all trans and chargebacks USD
                     * Totale dei morgi refundati / tutte le transazioni effettive + quelle chargeback = % USD
                    */

                    //Morgi refundati
                    $tot_morgi_refund = Transaction::query()
                        ->where($user->type."_id", $id)
                        ->where('type', 'gift')
                        ->whereNotNull('refund_type')
                        ->get();

                    //Totale transazioni effettive + chargeback
                    $morgi_transactions_query = clone $base_query;
                    $morgi_transactions = $morgi_transactions_query
                        ->where('type', '!=', 'refund')
                        ->where(function ($query) {
                            $query->whereNull('refund_type')
                                ->orWhere('refund_type', '=', 'chargeback');
                        })
                        ->get();
                    $tot_morgi_transaction = ($tot_morgi_refund->sum('dollars') / $morgi_transactions->sum('dollars')) * 100;
                    $query->tot_morgi_transaction = number_format($tot_morgi_transaction, 2, '.', ' ');

                }
            });

        return view("admin.admin-pages.user_profile.". $user->type .".". $user->type ."_chargeback", compact('user', 'transactions'));
    }

    public function getMessages(Request $request){

        $validator = Validator::make($request->all(), [
            'leader_id' => ['required', 'integer', 'exists:leaders,id'],
            'rookie_id' => ['required', 'integer', 'exists:rookies,id'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $messages = Utils::getUsersChannelMessages($request->leader_id, $request->rookie_id, $request->limit, "F j, Y, g:i a");

        return response()->json($messages);
    }

    public function getFines(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'exists:rookies,id'],
            'type' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $select = [
            "users.username as given_by",
            "transactions.morgi",
            "transactions.notes as reason",
            "transactions.created_at"
        ];

        $transactions = Transaction::query()
            ->select($select)
            ->leftJoin('users', 'transactions.admin_id', '=', 'users.id')
            ->where('transactions.type', 'fine')
            ->where('rookie_id', $request->user_id)
            ->whereNotNull($request->type)
            ->get();

        return response()->json($transactions);
    }

}
