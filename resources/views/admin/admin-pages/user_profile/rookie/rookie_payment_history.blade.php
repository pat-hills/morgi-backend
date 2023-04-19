@extends('admin.layout')

@section('content')

    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>


    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')

    <br>
    <br>


    <br>

    <table class="table" id="payment_history_table">
        <thead class="thead-light">
            <tr>
                <th scope="col">PAYMENT PERIOD ID</th>
                <th scope="col">DATE</th>
                <th scope="col">PAYMENT METHOD</th>
                <th scope="col">AMOUNT IN USD</th>
                <th scope="col">STATUS</th>
            </tr>
        </thead>
        <tbody>
        @foreach($payments as $payment)
            <tr>
                <td>{{$payment->payment_period_id}}</td>
                <td>{{$payment->created_at}}</td>
                <td>{{$payment->platform_name}}</td>
                <td>${{$payment->amount}}</td>
                @if($payment->status == 'pending')

                    <td style="color: orange">{{$payment->status}}</td>

                @elseif($payment->status == 'successful')
                    <td style="color: limegreen">{{$payment->status}}</td>

                @elseif($payment->status == 'declined')
                    <td style="color: red">{{$payment->status}}</td>

                @elseif($payment->status == 'returned')
                    <td style="color: grey">{{$payment->status}}</td>
                @endif
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            $('#payment_history_table').DataTable( {
                "order": [[ 0, "desc" ]]
            } );
        } );
    </script>

@endsection
