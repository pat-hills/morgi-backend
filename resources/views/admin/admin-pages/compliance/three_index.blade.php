@extends('admin.layout')

@php
$today = new DateTime();
@endphp

@section('content')

<h4 class="mt-5 mb-5">Compliance</h4>
<h5 class="mt-5 mb-5">FIRST 3 TRANSACTIONS MONITOR REPORT</h5>
<br>
<table class="table table-striped" id="compliance">
    <thead>
    <tr>
        <th scope="col">BILLER TRANS. ID</th>
        <th scope="col">USERNAME</th>
        <th scope="col">EMAIL</th>
        <th scope="col">LAST TRANSACTION AMOUNT</th>
        <th scope="col">TIME REMAIN</th>
        <th scope="col">N. TRANSACTIONS</th>
        <th scope="col">CHECKED TRANSACTIONS</th>
        <th scope="col"></th>

    </tr>

    </thead>
    <tbody>
    @foreach($transactions as $transaction)
        <tr>
            <td>#{{$transaction->ccbill_transactionId ?? $transaction->ccbill_subscriptionId}}</td>
            <td>{{$transaction->username}}</td>
            <td>{{$transaction->email}}</td>
            <td>${{$transaction->last_amount}}</td>
            <td>{{$transaction->time_remaining}}</td>
            <td>
                @if($transaction->n_transactions >= 3)
                    3
                @else
                    {{$transaction->n_transactions}}
                @endif
            </td>
            <td>{{$transaction->checked_transactions}}</td>
            <td><a style="color: #007bff" href="{{route('three_transactions_by_id', $transaction->user_id)}}"><u>Details</u></a></td>
        </tr>
    @endforeach
    </tbody>
</table>

@endsection


@section('js_after')

<script>
    $(document).ready( function () {
        $('#compliance').DataTable( {
            "ordering": false
        } );
    } );
</script>
@endsection

