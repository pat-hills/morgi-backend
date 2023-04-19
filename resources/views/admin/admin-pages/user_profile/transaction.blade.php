@extends('admin.layout')

@section('content')

    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>


    <nav class="mt-4" style="text-align: center">
        <div class="nav nav-tabs">
            <a class="nav-item nav-link" id="profileInfo" href="{{route('user.edit', $user->id)}}" role="tab">Profile
                Info</a>
            <a class="nav-item nav-link" id="activeGifts" href="{{route('user.edit.active_gifts', $user->id)}}"
               role="tab">Monthly Active Gifts</a>
            <a class="nav-item nav-link" id="micromorgi" href="{{route('user.edit.micromorgi', $user->id)}}" role="tab">Micro Morgi</a>
            <a class="nav-item nav-link active" id="transaction" href="{{route('user.edit.transactions', $user->id)}}"
               role="tab">Transaction</a>
            <a class="nav-item nav-link" id="activitylog" href="{{route('user.edit.activity_log', $user->id)}}"
               role="tab">Activity Log</a>
            <a class="nav-item nav-link" id="complaints" href="{{route('user.edit.complaints', $user->id)}}" role="tab">Complaints</a>
            <a class="nav-item nav-link" id="relatedaccounts" href="{{route('user.edit.related_accounts', $user->id)}}"
               role="tab">Related Accounts</a>
            <a class="nav-item nav-link" id="cgbhistory" href="{{route('user.edit.cgb_history', $user->id)}}"
               role="tab">CGB History</a>
        </div>
    </nav>
    <br>
    <br>

    <div class="row">
        <div class="col-2">
            &nbsp;
        </div>

        <div class="col-8">
            Total Spend USD: {{rand(0, 2000)}} &nbsp; Total CGB USD {{rand(0,500)}}
        </div>

        <div class="col-2">
            &nbsp;
        </div>
    </div>

    <br>
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th scope="col">BILLER</th>
            <th scope="col">MORGI</th>
            <th scope="col">MICRO MORGI</th>
            <th scope="col">SUBSCRIPTION ID</th>
            <th scope="col">TRANSACTION ID</th>
            <th scope="col">INTERNAL ID</th>
            <th scope="col">EMAIL</th>
            <th scope="col">TRANSACTION IP</th>
            <th scope="col">PURCHASE DATE</th>
            <th scope="col">PURCHASE AMOUNT</th>
            <th scope="col">CURRENCY</th>
            <th scope="col">CARD TYPE</th>
            <th scope="col">REFUND BUTTON</th>
            <th scope="col">REFUND DATE</th>
            <th scope="col">REFUND TYPE</th>
            <th scope="col">ADMIN USER</th>
            <th scope="col">RECURRING TRANS</th>

        </tr>
        </thead>
        <tbody>
{{--        @for($i = 0; $i<10; $i++)--}}
            <tr>
                <td>Biller</td>
                <td>200</td>
                <td></td>
                <td>#{{rand(1,40000)}}</td>
                <td>#{{rand(1,40000)}}</td>
                <td>#{{rand(1,40000)}}</td>
                <td>abcs@cdf.com</td>
                <td>127.0.0.1</td>
                <td>04/06/2020</td>
                <td>{{rand(1, 50)}}</td>
                <td>USD</td>
                <td>VISA</td>
                <td><button type="button">Refund</button></td>
                <td>10/06/2020</td>
                <td>Void</td>
                <td>1</td>
                <td>Yes</td>
            </tr>
{{--        @endfor--}}

        </tbody>
    </table>

@endsection
