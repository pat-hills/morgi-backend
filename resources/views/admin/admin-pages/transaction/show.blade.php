@extends('admin.layout')

@php
    $user_id = (request()->route('user_id'))
@endphp

@section('content')

    <br>
    <h4>Transaction #{{$transaction->transaction_id}} info page</h4>
    <br>

    <div class="table-responsive-xl table-striped">
        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th>Biller Transaction ID /<br>Biller Subscription ID</th>
                    <th>INT. ID</th>
                    <th>TYPE</th>
                    <th>Datetime</th>
                    <th>Amount morgi</th>
                    <th>Amount micromorgi</th>
                    <th>$ amount</th>
                    <th>Biller</th>
                    <th>Action</th>
                    <th>Admin ID</th>

                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if(!empty($transaction->ccbill_transactionId))
                            #{{$transaction->ccbill_transactionId}}
                        @else
                            #{{$transaction->ccbill_subscriptionId}}
                        @endif
                    </td>
                    <td>#{{$transaction->internal_id}}</td>
                    <td>{{$transaction->type}}</td>
                    <td>{{date_format($transaction->created_at, 'F j, Y')}}</td>
                    <td>{{$transaction->morgi ?? 0}}</td>
                    <td>{{$transaction->micromorgi ?? 0}}</td>
                    <td>{{$transaction->dollars ?? 0}}</td>
                    <td>CCBILL</td>
                    <td>
                        @if($transaction->internal_status == 'pending')
                            <a href="#" data-toggle="modal" data-target="#modalRejectPayment" type="button" onclick="setTransactionId('{{$transaction->transaction_id}}')" class="btn btn-danger">REJECT</a>
                        @else
                            <label>Internally {{$transaction->internal_status}}</label>
                        @endif
                    </td>
                    <td>
                        @if(!is_null($transaction->internal_status_by))
                            {{$transaction->internal_status_by}}
                        @else
                            {{$transaction->admin_id}}
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <br>

    @if($transaction->leader_id)
        <h4>Leader #{{$transaction->leader_id}}</h4>
        <br>
        @include('admin.admin-pages.transaction.leader_info', ['user' => $leader, 'transaction_created_at' => $transaction->created_at])
    @endif

    @if($transaction->rookie_id)
        <br>
        <h4>Rookie #{{$transaction->rookie_id}}</h4>
        <br>
        @include('admin.admin-pages.transaction.rookie_info', ['user' => $rookie, 'transaction_created_at' => $transaction->created_at])
    @endif

    @include('admin.admin-pages.transaction.widget-reject-transaction', ['transaction' => $transaction])

    @include('admin.admin-pages.compliance.widgets.modal-reject-payment')

@endsection

@section('js_after')

    <script>
        $(document).ready(function () {
            $('#content').addClass('active');
            $('#sidebar').addClass('active');

            $('#leader_sent_message').text('NO');
            $('#rookie_sent_message').text('NO');

            @if(isset($transaction->rookie_id) && isset($transaction->leader_id))
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'GET',
                url: '{{route('api.admin.chats.find-by-users.get')}}',
                data: {
                    leader_id: '{{$transaction->leader_id}}',
                    rookie_id: '{{$transaction->rookie_id}}'
                },
                success: function (response) {

                    if(response.is_leader_sent_message){
                        $('#leader_sent_message').text('YES');
                    }

                    if(response.is_rookie_sent_message){
                        $('#rookie_sent_message').text('YES');
                    }
                },
                error: function() {
                    $('#rookie_sent_message').text('ERROR');
                    $('#leader_sent_message').text('ERROR');
                }
            });
            @endif
        });

        function setTransactionId(transaction_id){
            $('#refund_transaction_id').val(transaction_id);
        }
    </script>

@endsection
