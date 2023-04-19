@extends('admin.layout')

@php
    $today = new DateTime();
@endphp

@section('content')

    <h4 class="mt-5 mb-5">Compliance</h4>
    <h6><a style="color: #007bff" href="{{route('three_transactions')}}"><i class="fas fa-arrow-left"></i> Return to compliance 1-3 transactions page</a></h6>

    <br>
    <h5 class="mt-5 mb-5">FIRST 3 TRANSACTIONS MONITOR REPORT</h5>
    <br>
    <br>
    <h5>PENDING TRANSACTIONS</h5>
    <table class="table table-striped" id="compliance">
        <thead>
        <tr>
            <th scope="col">BILLER TRANSACTION ID / BILLER SUBSCRIPTION ID</th>
            <th scope="col">INTERNAL ACTION ID</th>
            <th scope="col">EMAIL</th>
            <th scope="col">DATETIME</th>
            <th scope="col">TIME BETWEEN<br>ACCOUNT AND TRANSACTION</th>
            <th scope="col">PAID FOR MORGI 1ST PURCHASE</th>
            <th scope="col">PAID FOR MICROMORGI PACKAGES</th>
            <th scope="col">BILLER</th>
            <th scope="col">NAME</th>
            <th scope="col">CARD TYPE</th>
            <th scope="col">BILLING COUNTRY</th>
            <th scope="col">SIGNUP COUNTRY</th>
            <th scope="col">IP ADDRESS</th>
            <th scope="col">UPLOADED A PIC</th>
            <th scope="col">UPLOADED A PROFILE TEXT</th>
            <th scope="col">MESSAGES SENT</th>
            <th scope="col">COUNT OF LOGINS</th>
            <th scope="col" class="text-center">ACTION</th>
            <th scope="col"></th>

        </tr>
        </thead>
        <tbody>
        @foreach($transactions_pending as $transaction)
            <tr>
                <td>#{{$transaction->ccbill_transactionId ?? $transaction->ccbill_subscriptionId}}</td>
                <td>#{{$transaction->internal_id}}</td>
                <td>{{$transaction->email}}</td>
                <td>{{$transaction->created_at}}</td>

                @php
                    $date1 = new DateTime($transaction->user_created_at);
                    $date2 = new DateTime($transaction->created_at);
                    $interval = $date1->diff($date2);
                @endphp
                <td>{{App\Utils\Utils::formatInterval($interval)}}</td>
                <td>${{$transaction->first_purchase}}</td>
                <td>${{$transaction->paid_mm_package}}</td>
                <td>CCBILL</td>
                <td>{{$transaction->firstName}} {{$transaction->lastName}}</td>
                <td>{{$transaction->cardType}}</td>
                <td>{{$transaction->billingCountry}}</td>
                <td>{{$transaction->country_name}}</td>
                <td>{{$transaction->ip_address}}</td>
                <td>
                    @if($transaction->has_pic)
                        YES
                    @else
                        NO
                    @endif
                </td>
                <td>
                    @if(!is_null($transaction->user_description))
                        YES
                    @else
                        NO
                    @endif
                </td>
                <td>
                    @if($transaction->sent_first_message)
                        YES
                    @else
                        NO
                    @endif
                </td>
                <td>{{$transaction->count_login}}</td>
                <td style="white-space: nowrap">
                    @if($transaction->leader_internal_status != 'under_review')
                        <form method="POST" action="{{route('action-transaction')}}"
                              style="white-space: nowrap" id="approve-transaction">
                            @csrf
                            <input type="hidden" name="action" value="approved">
                            <input type="hidden" name="transaction_id" value="{{$transaction->transaction_id}}"
                                   form="approve-transaction">
                        </form>
                        <input type="submit" class="btn btn-success" value="APPROVE" form="approve-transaction">
                        <a href="#" data-toggle="modal" data-target="#modalRejectPayment" type="button"
                           onclick="setTransactionId('{{$transaction->transaction_id}}')"
                           class="btn btn-danger">REJECT</a>
                    @else
                        USER IS UNDER REVIEW
                    @endif
                </td>

                <td><a style="color: #007bff" href="{{route('transaction.show', ['transaction_id' => $transaction->transaction_id])}}" target="_blank">More details</a></td>

            </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <h5>PAST TRANSACTIONS</h5>
    <table class="table table-striped" id="compliance">
        <thead>
        <tr>
            <th scope="col">EMAIL</th>
            <th scope="col">DATETIME CREATED AT</th>
            <th scope="col">MORGI</th>
            <th scope="col">MICROMORGI PACKAGE</th>
            <th scope="col">DOLLARS</th>
            <th scope="col">BILLER</th>
            <th scope="col">NAME</th>
            <th scope="col">CARD TYPE</th>
            <th scope="col">SIGNUP COUNTRY</th>
            <th scope="col">IP ADDRESS</th>
            <th scope="col">APPROVE/DECLINE</th>
            <th scope="col">REASON</th>
            <th scope="col"></th>

        </tr>
        </thead>
        <tbody>
        @foreach($transactions_approved as $transaction)
            <tr>
                <td>{{$transaction->email}}</td>
                <td>{{$transaction->created_at}}</td>
                <td>{{$transaction->morgi ?? 0}}M</td>
                <td>{{$transaction->micromorgi ?? 0}}MM
                <td>${{$transaction->dollars ?? 0}}</td>
                <td>CCBILL</td>
                <td>{{$transaction->firstName}} {{$transaction->lastName}}</td>
                <td>{{$transaction->cardType}}</td>
                <td>{{$transaction->country_name}}</td>
                <td>{{$transaction->ip_address}}</td>
                <td>{{ucfirst($transaction->internal_status)}}</td>
                <td>{{ucfirst($transaction->internal_status_reason)}}</td>
                <td><a style="color: #007bff" href="{{route('transaction.show', ['transaction_id' => $transaction->transaction_id])}}" target="_blank">More details</a></td>

            </tr>
        @endforeach
        </tbody>
    </table>


    <br style="display: block; margin-top: 10%; content: ''">

    <h5>RELATED LEADERS ACCOUNTS</h5>
    <table id="related_account_table" class="table">
        <thead class="thead-light">
        <tr>
            <th>MATCH</th>
            <th>UCID</th>
            <th>EMAIL</th>
            <th>MORGI TOTAL</th>
            <th>MICROMORGI TOTAL</th>
            <th>1ST PURCHASE</th>
            <th>LAST PURCHASE</th>
            <th>LAST BILLER</th>
            <th>NAME</th>
            <th>BILLING COUNTRY</th>
            <th>SIGNUP COUNTRY</th>
            <th>IP ADDRESS</th>
            <th>ACCOUNT STATUS</th>
            <th>UPLOADED A PIC</th>
            <th>ADDED PROFILE TEXT</th>
            <th>MESSAGES</th>
            <th>COUNT LOGIN</th>
            <th>CHARGEBACK/REFUND</th>

        </tr>
        </thead>

        @foreach($matched_users as $match)
            <tr  onclick="window.open('{{route('user.edit', $match->id)}}','_blank')">
                <td>{{$match->found}}</td>
                @if($match->is_ucid_match)
                    <td style="background: #f3e97a"><a href="{{route('user.edit', $match->id)}}"  target="_blank">{{$match->ucid}}</a></td>

                @else
                    <td><a href="{{route('user.edit', $match->id)}}" target="_blank">{{$match->ucid}}</a></td>
                @endif
                @if($match->is_email_match)
                    <td style="background: #f3e97a"><a href="{{route('user.edit', $match->id)}}"  target="_blank">{{$match->email_match}}</a></td>

                @else
                    <td><a href="{{route('user.edit', $match->id)}}" target="_blank">{{$match->email}}</a></td>
                @endif
                <td>{{$match->morgi_tot}}</td>
                <td>{{$match->micro_morgi_tot}}</td>
                <td>{{$match->first_purchase}}</td>
                <td>{{$match->last_purchase}}</td>
                @if($match->first_purchase == 'none')
                    <td></td>
                @else
                    <td>CCBILL</td>
                @endif
                <td>{{$match->username}}</td>
                <td>{{$match->billing_country}}</td>
                <td>
                    @if(isset($match->signup_country_name))
                        {{$match->signup_country_name}}
                    @endif
                </td>
                @if($match->is_ip_address_match)
                    <td style="background: #f3e97a">{{$match->ip_address}}</td>

                @else
                    <td>{{$match->ip_address}}</td>
                @endif
                <td>{{$match->status}}</td>
                <td>
                    @if($match->has_pic > 0)
                        YES
                    @else
                        NO
                    @endif
                </td>
                @if(empty($match->description))
                    <td>NO</td>
                @else
                    <td>YES</td>
                @endif
                <td>
                    @if($match->sent_first_message)
                        YES
                    @else
                        NO
                    @endif
                </td>
                <td>{{$match->count_logins}}</td>
                <td>{{$match->cgb_refund}}</td>

            </tr>
        @endforeach

        <tbody>


        </tbody>
    </table>


    @include('admin.admin-pages.compliance.widgets.modal-reject-payment')

@endsection


@section('js_after')

    <script>

        $(document).ready( function () {

            $('#content').addClass('active');
            $('#sidebar').addClass('active');
            $('#related_account_table').DataTable({
            "order": [[0, "desc"]]
            });
        } );


        function setTransactionId(transaction_id){
            console.log(transaction_id);
            $('#refund_transaction_id').val(transaction_id);
        }
    </script>
@endsection

