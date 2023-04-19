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
            <th scope="col">TRANSACTION ID</th>
            <th scope="col">TRANSACTION TYPE</th>
            <th scope="col">FROM EMAIL</th>
            <th scope="col">TO EMAIL</th>
            <th scope="col">AMOUNT $</th>
            <th scope="col"></th>
        </tr>

        </thead>
        <tbody>
        @foreach($transactions as $transaction)
            <tr>
                <td>{{$transaction->id}}</td>
                <td>{{$transaction->type}}</td>
                <td>{{\App\Models\User::find($transaction->leader_id)->email ?? ''}}</td>
                <td>{{\App\Models\User::find($transaction->rookie_id)->email ?? ''}}</td>
                <td>${{$transaction->dollars}}</td>
                <td><a style="color: #007bff" href="{{route('transaction.show', $transaction->id)}}" target="_blank"><u>Details</u></a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            $('#compliance').DataTable( {
                "order": [[ 1, "desc" ]]
            } );
        } );
    </script>
@endsection

