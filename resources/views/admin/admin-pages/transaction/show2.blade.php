@extends('admin.layout')

@section('content')
    <br>
    <br>
    <h2>Transaction #{{$transaction->id}} info</h2>
    <br>
    <br>
    <table class="table" id="transactions">
        <thead class="thead-light">
        <tr>
            <th scope="col">BILLER</th>
            <th scope="col">TYPE</th>
            <th scope="col">MORGI</th>
            <th scope="col">MICRO MORGI</th>
            <th scope="col">SUBSCRIPTION ID<br>(issued by biller)</th>
            <th scope="col">TRANSACTION ID<br>(issued by biller)</th>
            <th scope="col">INTERNAL ID</th>
            <th scope="col">EMAIL</th>
            <th scope="col">ROOKIE ID</th>
            <th scope="col">LEADER ID</th>
            <th scope="col">TRANSACTION IP</th>
            <th scope="col">PURCHASE DATE</th>
            <th scope="col">PURCHASE $ AMOUNT</th>
            <th scope="col">CURRENCY</th>
            <th scope="col">CARD TYPE</th>
            <th scope="col">REFUND BUTTON</th>
            <th scope="col"></th>
            <th scope="col">REFUND DATE</th>
            <th scope="col">REFUND TYPE</th>
            <th scope="col">ADMIN USER</th>
            <th scope="col">RECURRING TRANS</th>
            <th scope="col">REF. INT. ID</th>
        </tr>
        </thead>
        <tbody>

            <tr>
                <td>CCBILL</td>
                <td>{{$transaction->type}}</td>
                <td>{{$transaction->morgi}}</td>
                <td>{{$transaction->micromorgi}}</td>
                <td>
                    @if($transaction->ccbill_subscriptionId)
                        #{{$transaction->ccbill_subscriptionId}}
                    @endif
                </td>
                <td>
                    @if($transaction->ccbill_transactionId)
                        #{{$transaction->ccbill_transactionId}}
                    @endif
                </td>
                <td>#{{$transaction->internal_id}}</td>
                <td>{{$transaction->rookie_email ?? $transaction->leader_email}}</td>
                <td>
                    @if($transaction->rookie_id)
                        #{{$transaction->rookie_id}}
                    @endif
                </td>
                <td>#{{$transaction->leader_id}}</td>
                <td>{{$transaction->ip_address}}</td>
                <td>{{$transaction->created_at}}</td>
                <td>${{$transaction->dollars}}</td>
                <td>{{$transaction->billedCurrencyCodeLabel}}</td>
                <td>{{$transaction->cardType}}</td>
                <td>

                    @switch($transaction->status)

                        @case('pending')
                            <a type="button" class="btn btn-warning">REFUND</a>
                        @break

                        @case('approved')
                            REFUNDED
                        @break

                        @case('failed')
                            <a href="#" data-toggle="modal" class="btn btn-info" data-target="#modalRefund" onclick="setTransactionId('{{$transaction->id}}')">REFUND</a>
                        @break

                        @default
                            @if($transaction->refund_type)
                                Refunded
                            @else
                                <a href="#" data-toggle="modal" class="btn btn-info" data-target="#modalRefund" onclick="setTransactionId('{{$transaction->id}}')">Refund</a>
                            @endif
                        @break

                    @endswitch
                </td>
                <td>
                    <span style="color: red">{{$transaction->error}}</span>
                </td>
                <td>
                    @if(isset($transaction->refunded_at))
                        {{date('F j, Y', strtotime($transaction->refunded_at))}}
                    @endif
                </td>
                <td>{{$transaction->refund_type}}</td>
                <td>{{$transaction->refunded_by_username}}</td>
                @if($transaction->type==='gift')
                    <td>YES</td>
                @else
                    <td>NO</td>
                @endif
                <td>
                    @if($transaction->referal_internal_id)
                        #{{$transaction->referal_internal_id}}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>


    @include('admin.admin-pages.transaction.components.modal-refund-transaction')
@endsection

@section('js_after')

    <script>
        function setTransactionId(transaction_id) {
            let form_transaction_id = $('#transaction_id');
            if (!form_transaction_id.val()){
                form_transaction_id.val(transaction_id);
            }
        }
    </script>
@endsection
