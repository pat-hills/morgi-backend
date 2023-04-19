<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentPeriod;
use App\Models\PaymentPlatform;
use App\Models\PaymentRookie;
use App\Models\Rookie;
use App\Models\Transaction;
use App\Models\User;
use App\Models\XLSXWriter;
use App\Utils\NotificationUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{

    private $users_table = 'users';
    private $rookies_table = 'rookies';
    private $transactions_table = 'transactions';
    private $payments_table = 'payments';
    private $payments_rookies_table = 'payments_rookies';
    private $payments_platform_table = 'payments_platforms';
    private $countries_table = 'countries';
    private $payments_periods_table = 'payments_periods';
    private $payments_platform_rookie_table = 'payments_platforms_rookies';


    public function getAllPayments(Request $request){

        $validator = Validator::make($request->all(), [
            'payment_method' => 'string|nullable',
            'from_date' => 'date|nullable',
            'to_date' => 'date|nullable|after_or_equal:from_date'
        ]);

        if($validator->fails()){
            return \redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $query = Payment::query();

        $query->join((new PaymentPeriod())->getTable(), (new Payment())->getTable().'.payment_period_id', '=', (new PaymentPeriod())->getTable().'.id');
        $query->join((new PaymentPlatform())->getTable(), (new Payment())->getTable().'.payment_platform_id', '=', (new PaymentPlatform())->getTable().'.id');

        $query->select((new Payment())->getTable().'.*', (new PaymentPeriod())->getTable().'.name as period_name', (new PaymentPeriod())->getTable().'.start_date', (new PaymentPeriod())->getTable().'.end_date',
            (new PaymentPlatform())->getTable().'.name as payment_name'
        );

        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->where((new Payment())->getTable().'created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->where((new Payment())->getTable().'created_at', '<=', $request->to_date);
        }

        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where((new PaymentPlatform())->getTable().'.id', '=', $request->payment_method);
        }

        $payments = $query->get();

        $payment_method = PaymentPlatform::all();

        return view('admin.admin-pages.payments.payments_table', compact('payments', 'payment_method'), ['fix' => $request->all()]);
    }

    public function indexPayments(Request $request){

        $payments_table = (new Payment())->getTable();
        $payments_periods_table = (new PaymentPeriod())->getTable();
        $payments_platforms_table = (new PaymentPlatform())->getTable();
        $payments_rookies_table = (new PaymentRookie())->getTable();

        $select = [
            "$payments_table.id",
            "$payments_periods_table.id as payment_period_id",
            "$payments_platforms_table.name as platform_name",
            "$payments_periods_table.start_date as period_start_date",
            "$payments_periods_table.end_date as period_end_date",
            "$payments_table.created_at",
            "$payments_table.updated_at",
            "$payments_table.amount as total",
            "$payments_table.status",
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id) as count_rookies"),
        ];

        $query = Payment::query()
            ->select($select)
            ->join("$payments_periods_table", "$payments_periods_table.id", '=', "$payments_table.payment_period_id")
            ->join("$payments_platforms_table", "$payments_platforms_table.id", '=', "$payments_table.payment_platform_id");

        if($request->has('payment_platform_id') && !empty($request->payment_platform_id)){
            $query = $query->where("$payments_table.payment_platform_id", $request->payment_platform_id);
        }

        if($request->has('start_date') && !empty($request->start_date)){
            $query = $query->where("$payments_periods_table.start_date", '>=', $request->start_date);
        }

        if($request->has('end_date') && !empty($request->end_date)){
            $query = $query->where("$payments_periods_table.end_date", '<=', $request->end_date);
        }

        $payments = $query->orderBy("$payments_table.created_at", 'DESC')->get();

        $payment_method = PaymentPlatform::all();

        $final_query = $query->toSql();

        return view('admin.admin-pages.payments.payments_table', compact('payments', 'payment_method', 'final_query'), ['old_input' => $request->all()]);
    }

    public function updatePayment(Request $request){

        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|integer|gt:0',
            'action' => 'required|in:completed'
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $payment = Payment::find($request->payment_id);

        if(!$payment){
            return redirect()->back()->with(['fail' => 'Payment not found']);
        }

        $payment->update(['status' => $request->action]);

        return redirect()->back()->with(['success' => 'Payment updated']);
    }

    public function showPayment($id, Request $request){

        $validator = Validator::make($request->all(), [
            'status' => 'string|in:pending,successful,declined,returned',
            'payments' => 'array|min:1'
        ]);

        if($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->messages()]);
        }

        $payments_rookies_table = (new PaymentRookie())->getTable();

        if(!empty($request->payments) && !empty($request->status)){
            foreach ($request->payments as $payment_id){

                $payment_rookie = PaymentRookie::find($payment_id);
                $payment_rookie->update([
                    'status' => $request->status
                ]);

                if(in_array($request->status, ['declined', 'returned'])){
                    NotificationUtils::sendNotification($payment_rookie->rookie_id, 'rookie_rejected_payment_general', now());
                }
            }
        }

        $payments_table = (new Payment())->getTable();
        $payments_periods_table = (new PaymentPeriod())->getTable();
        $payments_platforms_table = (new PaymentPlatform())->getTable();
        $users_table = (new User())->getTable();
        $rookies_table = (new Rookie())->getTable();

        $select = [
            "$payments_table.id",
            "$payments_periods_table.id as payment_period_id",
            "$payments_platforms_table.id as payment_platform_id",
            "$payments_platforms_table.name as platform_name",
            "$payments_periods_table.name as period_name",
            "$payments_periods_table.start_date as period_start_date",
            "$payments_periods_table.end_date as period_end_date",
            "$payments_table.created_at",
            "$payments_table.updated_at",
            "$payments_table.amount as total",
            "$payments_table.status",
        ];

        $payment = Payment::select($select)
            ->join($payments_periods_table, "$payments_periods_table.id", '=', "$payments_table.payment_period_id")
            ->join($payments_platforms_table, "$payments_platforms_table.id", '=', "$payments_table.payment_platform_id")
            ->where("$payments_table.id", $id)->first();

        $select = [
            "$rookies_table.id as rookie_id",
            "$rookies_table.first_name",
            "$rookies_table.last_name",
            "$payments_rookies_table.reference",
            "$payments_rookies_table.amount",
            "$payments_rookies_table.status",
            "$payments_rookies_table.id as payment_id",
            "$payments_rookies_table.updated_at"
        ];

        $query = PaymentRookie::query()
            ->select($select)
            ->join($rookies_table, "$payments_rookies_table.rookie_id", '=', "$rookies_table.id")
            ->join($users_table, "$rookies_table.id", '=', "$users_table.id")
            ->where("$payments_rookies_table.payment_id", $id);

        $rookies = $query->get();

        return view('admin.admin-pages.payments.payments_table_by_payment', compact('payment', 'rookies'));
    }

    public function downloadPaymentFile($payment_id, $payment_platform_id){

        $payment = Payment::find($payment_id);
        if($payment->status == "new"){
            $payment->status = "pending";
            $payment->save();
        }

        $query = PaymentRookie::query()
            ->select(
                "$this->payments_rookies_table.*",
                "$this->payments_table.payment_period_id",
                'countries.alpha_3_code',
                'transactions.internal_id'
            )
            ->join('rookies', "$this->payments_rookies_table.rookie_id", '=', 'rookies.id')
            ->join('countries', 'countries.id', '=', 'rookies.country_id')
            ->join('transactions', 'payments_rookies.id', '=', 'transactions.payment_rookie_id')
            ->join($this->payments_table, "$this->payments_rookies_table.payment_id", '=', "$this->payments_table.id")
            ->where("$this->payments_rookies_table.payment_id", $payment_id)
            ->where("$this->payments_table.payment_platform_id", $payment_platform_id)
            ->where("$this->payments_rookies_table.status", 'pending');

        $users_payments = $query->get();

        if($payment_platform_id == 1) { //Paypal
            // For Paypal we only need the Email

            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment; filename="paypal.csv"' );
            $csv   = [];
            $csv[] = [ 'Paypal email', 'amount', 'currency', 'ref' ];

            foreach ( $users_payments as $user_payment ) {

                $payment_info = json_decode($user_payment->reference, true);

                if(is_null($payment_info)){
                    continue;
                }

                $paypal_email = array_key_exists('email', $payment_info) ? $payment_info['email'] : '';

                $csv[] = [ $paypal_email, $user_payment->amount, 'USD', $user_payment->rookie_id];
            }

            $fp = fopen( 'php://output', 'wb' );
            foreach ( $csv as $line ) {
                fputcsv( $fp, $line);
            }
            fclose( $fp );
        }

        if($payment_platform_id == 3){ //Paxum

            $data = [];

            $header = [
                'Paxum wallet' => 'string',
                'Amount' => 'price',
                'Currency' => 'string',
                'Information ' => 'string',
                'Internal payment id' => 'string'
            ];

            $errors = 0;
            foreach($users_payments as $user_payment){

                $payment_info = json_decode($user_payment->reference, true);

                if(is_null($payment_info)){
                    $errors++;
                    continue;
                }

                // At the moment we have only 2 type of account for Paxum: Personal and Business
                switch (strtolower($payment_info['account_type'])) {
                    case 'personal':

                        // For Personal Account, we must have these information: Full Name, Birth Date.
                        // The reason is always Payment for Morgi. No info about it
                        $information = [
                            'Profile Name: ' . $payment_info['full_name'] . ';',
                            'D.O.B: ' . date('Y/m/d', strtotime($payment_info['birth_date'])) . ';',
                            'Payment for Morgi'
                        ];
                        break;
                    case 'business':

                        // For Business Account, we must have these information: Company Name, Business Number.
                        // The reason is always Payment for Morgi. No info about it
                        $information = [
                            'Company Name: ' . $payment_info['company_name'] . ';',
                            'REG. NUMBER: ' . $payment_info['business_number'] . ';',
                            'Payment for Morgi'
                        ];
                        break;

                    default:

                        $errors++;
                        continue 2;
                }

                $data[] = [
                    $payment_info['email'],
                    $user_payment->amount,
                    'USD',
                    implode(' ', $information),
                    "#$user_payment->internal_id",
                ];

            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="paxum.xlsx"');

            $writer = new XLSXWriter();
            $writer->writeSheetHeader('Sheet1', $header );
            $writer->writeSheet($data);
            echo $writer->writeToString();

            if($errors){
                redirect()->back()->with(['message' => "There are $errors errors in PAXUM accounts."]);
            }

        }

        if($payment_platform_id == 2){ //epay
            /*
            For Epay we have 2 different Form.
            For USA accounts, we have a custom form on front-end side. You can find which values are required
            for them in function validateUSAEpay() in PaymentController.php
            For Other accounts user we ony need wallet_number.
            */

            $data_global = [];

            $header_global = [
                'p2p' => 'string',
                '|' => 'string',
                'US Wallet 1 ' => 'string',
                '| ' => 'string',
                'amount' => 'price',
                '|  ' => 'string',
                'currency' => 'string',
                '|   ' => 'string',
                'PP number' => 'integer',
                'Site' => 'string',
                'Type' => 'string',
                'User ID' => 'string',
                'Period Time' => 'string'
            ];

            $usa_payments = $users_payments->where('alpha_3_code', 'USA');
            $global_payments = $users_payments->where('alpha_3_code', '!=', 'USA');

            $global_errors = 0;
            foreach($global_payments as $user_payment){
                $payment_info = json_decode($user_payment->reference, true);

                if(is_null($payment_info)){
                    continue;
                }

                try {

                    $epay_wallet = array_key_exists('wallet_number', $payment_info) ? $payment_info['wallet_number'] : '';
                    $period_time = PaymentPeriod::query()->find($user_payment->payment_period_id)->start_date;
                    $period_time_to_print = (string)date('j - F - Y', strtotime($period_time));
                    $rookie = Rookie::query()->find($user_payment->rookie_id);
                }catch (\Exception $exception){
                    $global_errors++;
                    continue;
                }

                $data_global[] = ['p2p', '|', $epay_wallet, '|', $user_payment->amount, '|', 'USD', '|', $user_payment->payment_period_id, 'morgi', 'R', "#$user_payment->rookie_id", $period_time_to_print];
            }

            $data_usa = [];

            $header_usa = [
                'Beneficiary type (individual or entity)' => 'string',
                'Beneficiary name' => 'string',
                'Beneficiary email' => 'string',
                'Beneficiary document ID (SSN/DL/Tax ID) last 4 digits' => 'string',
                'Beneficiary Telephone' => 'string',
                'Beneficiary Bank Account number' => 'string',
                'Beneficiary Bank Account Type Checking or Saving' => 'string',
                'Beneficiary City' => 'string',
                'Beneficiary Address' => 'string',
                'Beneficiary State' => 'string',
                'Beneficiary Zip Code' => 'string',
                'Benificiary Bank Name' => 'string',
                'Beneficiary Bank Routing/ABA Number' => 'string',
                'Benificiary Bank State' => 'string',
                'Benificiary Bank City' => 'string',
                'Benificiary Bank Zip Code' => 'string',
                'Amount' => 'price',
                'Payments details' => 'string'
            ];

            $usa_errors = 0;
            foreach ($usa_payments as $user_payment) {
                $payment_info = json_decode($user_payment->reference);

                try {
                    $data_usa[] = [
                        'beneficiary_type' => $payment_info->beneficiary_type,
                        'account_holder' => $payment_info->account_holder,
                        'email' => $payment_info->email,
                        'document_id' => $payment_info->document_id,
                        'phone' => $payment_info->phone,
                        'bank_account_number' => $payment_info->bank_account_number,
                        'account_type' => $payment_info->account_type,
                        'city' => $payment_info->city,
                        'address' => $payment_info->address,
                        'state' => $payment_info->state,
                        'zip_code' => $payment_info->zip_code,
                        'bank_name' => $payment_info->bank_name,
                        'bank_routing_number' => $payment_info->bank_routing_number,
                        'bank_state' => $payment_info->bank_state,
                        'bank_city' => $payment_info->bank_city,
                        'bank_zip_code' => $payment_info->bank_zip_code,
                        'amount' => $user_payment->amount,
                        'payment_details' => "MORGI_Payment_" . $user_payment->payment_period_id . "_" . $user_payment->rookie_id
                    ];
                } catch (\Exception $exception) {
                    $usa_errors++;
                }
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="epay.xlsx"');

            $writer = new XLSXWriter();
            $writer->writeSheet($data_global, 'global_epay', $header_global);
            $writer->writeSheet($data_usa, 'usa_epay', $header_usa);
            echo $writer->writeToString();

            if($usa_errors || $global_errors){
                $string = '';

                if ($usa_errors && $global_errors){
                    $string = " There are $usa_errors errors in USA accounts and $global_errors errors in GLOBAL accounts";
                }elseif($usa_errors){
                    $string = " There are $usa_errors errors in USA accounts.";
                }elseif($global_errors){
                    $string = " There are $global_errors errors in GLOBAL accounts.";
                }

                redirect()->back()->with(['message' => $string]);
            }
        }
    }

    public function getSummaryPaymentReport(Request $request){

        $payments_table = (new Payment())->getTable();
        $payments_periods_table = (new PaymentPeriod())->getTable();
        $payments_platforms_table = (new PaymentPlatform())->getTable();
        $payments_rookies_table = (new PaymentRookie())->getTable();

        $select = [
            "$payments_table.id",
            "$payments_periods_table.id as payment_period_id",
            "$payments_periods_table.name as period_name",
            "$payments_platforms_table.name as platform_name",
            "$payments_periods_table.start_date as period_start_date",
            "$payments_periods_table.end_date as period_end_date",
            "$payments_table.created_at",
            "$payments_table.updated_at",
            "$payments_table.amount as total",
            "$payments_table.status",
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id) as count_rookies"),
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'declined' or status = 'returned') as count_declined_rookies"),
            DB::raw("(SELECT SUM(amount) as sum_declined FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'declined' or status = 'returned') as count_declined"),
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id and $payments_rookies_table.status = 'successful') as count_paid_users"),
            DB::raw("(SELECT SUM(amount) as sum_pay FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'successful') as count_paid")
        ];

        $data = [];
        $query = Payment::query();

        $query
            ->join("$payments_periods_table", "$payments_periods_table.id", '=', "$payments_table.payment_period_id")
            ->join("$payments_platforms_table", "$payments_platforms_table.id", '=', "$payments_table.payment_platform_id");

        if($request->has('payment_platform_id') && !empty($request->payment_platform_id)){
            $query = $query->where("$payments_table.payment_platform_id", $request->payment_platform_id);

            $data['payment_platform_id'] = $request->payment_platform_id;
            $data['payment_platform_name'] = PaymentPlatform::find($request->payment_platform_id)->name;
        }

        if($request->has('period_id') && !empty($request->period_id)){
            $query = $query->where("$payments_table.payment_period_id", '=', $request->period_id);

            $data['period_id'] = $request->period_id;
            $data['period_name'] = PaymentPeriod::find($request->period_id)->name;
        }

        $query->select($select);

        $payments = $query->orderBy("$payments_table.created_at", 'DESC')->get();

        $periods = PaymentPeriod::orderBy('id', 'DESC')->take(20)->get();
        $platforms = PaymentPlatform::all();


        return view('admin.admin-pages.payments.summary-payment', compact('payments', 'periods', 'platforms', 'data'));
    }

    public function getMainPaymentReport(Request $request){
        $payments_table = (new Payment())->getTable();
        $payments_periods_table = (new PaymentPeriod())->getTable();
        $payments_platforms_table = (new PaymentPlatform())->getTable();
        $payments_rookies_table = (new PaymentRookie())->getTable();


        $select = [
            "$payments_table.id",
            "$payments_periods_table.id as payment_period_id",
            "$payments_platforms_table.name as platform_name",
            "$payments_periods_table.start_date as period_start_date",
            "$payments_periods_table.end_date as period_end_date",
            "$payments_table.created_at",
            "$payments_table.updated_at",
            "$payments_table.amount as total",
            DB::raw("(SELECT SUM(amount) as sum_pay FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'pending') as pending_total"),
            "$payments_table.status",
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id) as count_rookies"),
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE $payments_rookies_table.payment_id = $payments_table.id and (status = 'declined' or status = 'returned')) as count_declined_rookies"),
            DB::raw("(SELECT SUM(amount) as sum_declined FROM $payments_rookies_table WHERE payment_id = $payments_table.id and (status = 'declined' or status = 'returned')) as count_declined"),
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id and $payments_rookies_table.status = 'successful') as count_paid_users"),
            DB::raw("(SELECT SUM(amount) as sum_pay FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'successful') as count_paid")
        ];

        $data = [];

        $query = Payment::query()
            ->select($select)
            ->join("$payments_periods_table", "$payments_periods_table.id", '=', "$payments_table.payment_period_id")
            ->join("$payments_platforms_table", "$payments_platforms_table.id", '=', "$payments_table.payment_platform_id");

        if($request->has('period_id') && !empty($request->period_id)){
            $query = $query->where("$payments_table.payment_period_id", '=', $request->period_id);

            $data['period_id'] = $request->period_id;
            $data['period_name'] = PaymentPeriod::find($request->period_id)->name;
        }

        $payments = $query->orderBy("$payments_table.created_at", 'DESC')->limit(3)->get();

        $periods = PaymentPeriod::orderBy('id', 'DESC')->take(20)->get();


        return view('admin.admin-pages.payments.main-payment-report', compact('payments', 'data', 'periods'));

    }

    public function getPaymentHistoryByData(Request $request){

        $payments_table = (new Payment())->getTable();
        $payments_periods_table = (new PaymentPeriod())->getTable();
        $payments_platforms_table = (new PaymentPlatform())->getTable();
        $payments_rookies_table = (new PaymentRookie())->getTable();
        $users_table = (new User())->getTable();

        $select = [
            "$payments_table.id",
            "$payments_periods_table.id as payment_period_id",
            "$payments_periods_table.name as period_name",
            "$payments_platforms_table.name as platform_name",
            "$payments_periods_table.start_date as period_start_date",
            "$payments_periods_table.end_date as period_end_date",
            "$payments_table.created_at",
            "$payments_table.updated_at",
            "$payments_table.amount as total",
            "$payments_table.status",
            "$payments_rookies_table.reference",
            "$payments_rookies_table.status as rookie_status",
            "$users_table.username",
            "$users_table.email",
            "$this->countries_table.name as country_name",
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id) as count_rookies"),
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'declined' or status = 'returned') as count_declined_rookies"),
            DB::raw("(SELECT SUM(amount) as sum_declined FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'declined' or status = 'returned') as count_declined"),
            DB::raw("(SELECT count(id) FROM $payments_rookies_table WHERE payment_id = $payments_table.id and $payments_rookies_table.status = 'successful') as count_paid_users"),
            DB::raw("(SELECT SUM(amount) as sum_pay FROM $payments_rookies_table WHERE payment_id = $payments_table.id and status = 'successful') as count_paid")
        ];

        $data = [];
        $query = Payment::query();

        $query
            ->join("$payments_periods_table", "$payments_periods_table.id", '=', "$payments_table.payment_period_id")
            ->join("$payments_platforms_table", "$payments_platforms_table.id", '=', "$payments_table.payment_platform_id")
            ->join("$payments_rookies_table", "$payments_rookies_table.payment_id", '=', "$payments_table.id")
            ->join("$users_table", "$users_table.id", '=', "$payments_rookies_table.rookie_id")
            ->join("$this->rookies_table", "$this->rookies_table.id", '=', "$payments_rookies_table.rookie_id")

            ->leftJoin($this->countries_table, "$this->rookies_table.country_id", '=', "$this->countries_table.id");


        if($request->has('email') && !empty($request->email)){

            $query = $query->where("$users_table.email", 'LIKE', '%'. $request->email . '%');
            $data['email'] = $request->email;
        }

        if($request->has('payment_platform_id') && !empty($request->payment_platform_id)){

            $query = $query->where("$payments_table.payment_platform_id", $request->payment_platform_id);
            $data['payment_platform_id'] = $request->payment_platform_id;
            $data['payment_platform_name'] = PaymentPlatform::find($request->payment_platform_id)->name;
        }

        if($request->has('start_date') && !empty($request->start_date)){

            $query = $query->where("$payments_table.created_at", '>=', $request->start_date);
            $data['start_date'] = $request->start_date;
        }

        if($request->has('end_date') && !empty($request->end_date)){

            $query = $query->where("$payments_table.created_at", '<=', $request->end_date);
            $data['end_date'] = $request->end_date;
        }

        $query->select($select);

        $payments = $query->orderBy("$payments_table.created_at", 'DESC')->get();

        $platforms = PaymentPlatform::all();

        return view('admin.admin-pages.payments.payment-history', compact('platforms', 'payments', 'data'));
    }

    public function getPaymentPrevPeriod(Request $request){

        $period = $request->has('period_id')
            ? PaymentPeriod::query()->find($request->period_id)
            : PaymentPeriod::query()->latest('end_date')->first();

        if(!is_null($period)){
            $from = $period->start_date;
            $to = $period->end_date;
        }else{
            $platforms = PaymentPlatform::all();
            $payments = [];
            $data = [];
            $periods = [];
            $period = null;
            $to_check = false;

            return view('admin.admin-pages.payments.payment-prev-period-socinv', compact('platforms', 'payments', 'data', 'period', 'periods', 'to_check'));
        }

        $data = [];
        $data['period_id'] = $period->id;
        $data['period_name'] = $period->name;
        $periods = PaymentPeriod::orderBy('id', 'DESC')->take(20)->get();

        $select = [
            "$this->payments_table.id",
            "$this->payments_table.created_at",
            "$this->payments_table.updated_at",
            "$this->payments_table.amount as total",
            "$this->payments_table.status",
            "$this->payments_periods_table.id as payment_period_id",
            "$this->payments_periods_table.name as period_name",
            "$this->payments_platform_table.name as platform_name",
            "$this->payments_periods_table.start_date as period_start_date",
            "$this->payments_periods_table.end_date as period_end_date",
            "$this->payments_rookies_table.reference",
            "$this->payments_rookies_table.amount as amount_to_pay",
            "$this->payments_rookies_table.id as payment_rookie_id",
            "$this->payments_rookies_table.status as rookie_status",
            "$this->users_table.id as user_id",
            "$this->users_table.username",
            "$this->users_table.email",
            "$this->users_table.status as user_status",
            "$this->transactions_table.id as transaction_id",
            "$this->transactions_table.type as transaction_status",
            "$this->transactions_table.created_at as transaction_created_at",
            "$this->transactions_table.referal_internal_id",
            "$this->transactions_table.internal_id",
            "$this->transactions_table.refunded_by",
            "$this->transactions_table.admin_id",
            "$this->transactions_table.notes",
            "admin_user.username as admin_username",
            DB::raw("(SELECT count(id) FROM $this->payments_rookies_table WHERE payment_id = $this->payments_table.id) as count_rookies"),
            DB::raw("(SELECT count(id) FROM $this->payments_rookies_table WHERE payment_id = $this->payments_table.id and status = 'declined' or status = 'returned' ) as count_declined_rookies"),
            DB::raw("(SELECT SUM(amount) as sum_payments FROM $this->payments_rookies_table WHERE $this->payments_rookies_table.rookie_id = $this->users_table.id) as count_payments"),
            DB::raw("(SELECT count(id) FROM $this->payments_rookies_table WHERE payment_id = $this->payments_table.id and $this->payments_rookies_table.status = 'successful') as count_paid_users"),
            DB::raw("(SELECT SUM(amount) as sum_pay FROM $this->payments_rookies_table WHERE payment_id = $this->payments_table.id and status = 'successful') as count_paid"),
            DB::raw("(SELECT SUM(taxed_dollars) as sum_payments_cgb FROM $this->transactions_table WHERE $this->transactions_table.refund_type = 'chargeback') as count_payments_cgb"),
            DB::raw("(SELECT created_at as last_created_at FROM $this->transactions_table WHERE $this->transactions_table.rookie_id = $this->users_table.id ORDER BY 'id' 'DESC' LIMIT 1) as last_created_at")
        ];

        $query = Payment::query();

        $query
            ->join("$this->payments_periods_table", "$this->payments_periods_table.id", '=', "$this->payments_table.payment_period_id")
            ->join("$this->payments_platform_table", "$this->payments_platform_table.id", '=', "$this->payments_table.payment_platform_id")
            ->join("$this->payments_rookies_table", "$this->payments_rookies_table.payment_id", '=', "$this->payments_table.id")
            ->join("$this->transactions_table", "$this->transactions_table.payment_rookie_id", '=', "$this->payments_rookies_table.id")
            ->join("$this->users_table", "$this->users_table.id", '=', "$this->payments_rookies_table.rookie_id")
            ->leftJoin("$this->users_table as admin_user", "$this->transactions_table.refunded_by", '=', "admin_user.id");

        $query = $query->where("$this->payments_table.payment_period_id", '=', $period->id);

        if($request->has('payment_platform_id') && !empty($request->payment_platform_id)){
            $query = $query->where("$this->payments_table.payment_platform_id", $request->payment_platform_id);

            $data['payment_platform_id'] = $request->payment_platform_id;
            $data['payment_platform_name'] = PaymentPlatform::find($request->payment_platform_id)->name;

        }

        $query->select($select);

        $payments = $query->get()->each(function ($query) use($from, $to){
            $query->count_leaders = Transaction::query()->where('rookie_id', $query->user_id)
                ->whereNotNull('leader_id')
                ->where('created_at', '>=', $from)
                ->where('created_at', '<', $to)
                ->count();
        });

        $to_check = false;
        $counter_declined = 0;
        foreach ($payments as $key => $payment){
            if(Transaction::query()->where('referal_internal_id', $payment->internal_id)->exists()){
                unset($payments[$key]);
            }

            if(!$to_check && $payment->rookie_status === 'pending'){
                $to_check = true;
            }

            if($payment->rookie_status == 'declined'){
                $counter_declined++;
            }

            $reference = json_decode($payment->reference, true);

            if(!is_string($payment->reference)){
                $payment->reference = null;
            }

            if (is_array($reference)){
                $payment->reference = implode(', ', $reference);
            }
        }

        $payments = $payments->filter()->values();

        $platforms = PaymentPlatform::all();

        return view('admin.admin-pages.payments.payment-prev-period-socinv', compact('platforms', 'payments', 'data', 'period', 'periods', 'to_check', 'counter_declined'));
    }

    public function rejectPayment(Request $request){

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
            'status' => 'required|in:declined',
            'transaction_ids' => 'required|array|min:1'
        ]);

        if ($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $count_fails = 0;
        $count_good = 0;

        $fails = [];
        foreach ($request->transaction_ids as $transaction_ids) {

            $transaction = Transaction::query()->find($transaction_ids);
            if (!isset($transaction)) {
                $fails[] = $transaction_ids;
                $count_fails++;
                continue;
            }

            $transaction->rejectRookiePayment(Auth::id(), $request->reason);
            $count_good++;
        }

        $message = "Payment successfully $request->status: $count_good. Fails: $count_fails.";
        if($count_fails){
            $message .= " Failed Transactions IDs: ". implode(', ', $fails);
        }

        return redirect()->back()->with(['success' => $message]);
    }

    public function getPaymentRejectsReports(){

        $query = PaymentRookie::where("$this->payments_rookies_table.status", '=', 'declined')
            ->orWhere("$this->payments_rookies_table.status", '=', 'returned')
            ->leftJoin($this->payments_table, "$this->payments_table.id", '=', "$this->payments_rookies_table.payment_id")
            ->leftJoin($this->payments_platform_table, "$this->payments_platform_table.id", '=', "$this->payments_table.payment_platform_id")
            ->leftJoin("$this->users_table as rookie", 'rookie.id', "=", "$this->payments_rookies_table.rookie_id");

        $select = [
            "rookie.email",
            "$this->payments_table.id",
            "$this->payments_rookies_table.amount",
            "$this->payments_rookies_table.updated_at",
            "$this->payments_rookies_table.note",
            "$this->payments_rookies_table.admin_id",
            "$this->payments_platform_table.name as payment_method"
        ];

        $reports = $query->select($select)
            ->get()
            ->each(function($query){

                $query->admin = User::find($query->admin_id)->username ?? null;
            });

        $data = [];
        $periods = PaymentPeriod::orderBy('id', 'DESC')->take(20)->get();
        $platforms = PaymentPlatform::all();

        return view('admin.admin-pages.payments.payment-reject-report', compact('reports', 'data', 'periods', 'platforms'));
    }


    public function approvePayments(Request $request, $payment_period_id){

        $data = [
            'payment_period_id' => $payment_period_id,
            'payment_platform_id' => $request->payment_platform_id ?? null
        ];

        $validator = Validator::make($data, [
            'payment_period_id' => ['required', 'integer', 'gt:0', 'exists:payments_periods,id'],
            'payment_platform_id' => ['nullable', 'integer', 'gt:0', 'exists:payments_platforms,id']
        ]);

        if ($validator->fails()){
            return redirect()->back()->with(['fail' => $validator->errors()->getMessages()]);
        }

        $query = Transaction::query()
            ->select('transactions.*')
            ->join('payments_rookies', 'transactions.payment_rookie_id', '=', 'payments_rookies.id')
            ->join('payments', 'payments.id', '=', 'payments_rookies.payment_id')
            ->where('payments_rookies.status', 'pending')
            ->where('payments.payment_period_id', $payment_period_id);

        if(isset($request->payment_platform_id) && !is_null($request->payment_platform_id)){
            $query->where('payments.payment_platform_id', $request->payment_platform_id);
        }

        $payments = $query->get();

        foreach ($payments as $payment){
            $payment->approveRookiePayment(Auth::id());
        }

        return redirect()->back()->with(['success' => count($payments) ." payments successfully approved!"]);


    }

    public function approvePayment($transaction_id){

        $transaction = Transaction::query()->find($transaction_id);

        if(!isset($transaction)){
            return redirect()->back()->with(['fail' => "Something went wrong."]);
        }

        $transaction->approveRookiePayment(Auth::id());

        return redirect()->back()->with(['success' => "Payment successfully approved!"]);
    }



}
