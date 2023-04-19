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
            <a class="nav-item nav-link" id="transaction" href="{{route('user.edit.transactions', $user->id)}}"
               role="tab">Transaction</a>
            <a class="nav-item nav-link active" id="activitylog" href="{{route('user.edit.activity_log', $user->id)}}"
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
        <div class="col-6">
            Current {{$user->type}} balance: <span>{{$user->micro_morgi_balance}}</span> MICRO MORGI
        </div>
        <div class="col-4">
            &nbsp;
        </div>
    </div>

    <br>

    <table class="table">
        <thead class="thead-light">
        <tr>
            <th scope="col">Internal action ID</th>
            <th scope="col">Morgi</th>
            <th scope="col">Micro Morgi</th>
            <th scope="col">Initiated by</th>
            <th scope="col">Date</th>
            <th scope="col">Refund Type</th>
            <th scope="col">Original Transaction (internal ID)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            <td>Mark</td>
        </tr>
        <tr>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            <td>Mark</td>
        </tr>
        <tr>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            <td>Mark</td>
            <td>Otto</td>
            <td>@mdo</td>
            <td>Mark</td>
        </tr>
        </tbody>
    </table>

@endsection
