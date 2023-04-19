@extends('admin.layout')




@section('content')


    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id ?? ''}} Type <u>{{ucfirst($user->type)}}</u></h3>


    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')

    <br>
    <br>

    <h5>User Info</h5>
    <table id="account_info" class="table">
        <thead class="thead-light">
        <tr>
            <th>SURNAME</th>
            <th>EMAIL</th>
            <th>MORGI TOT</th>
            <th>DATE OF BIRTH</th>
            <th>LOCATION, COUNTRY</th>
            <th>PAYMENTS DETAILS</th>
            <th>ACCOUNT STATUS</th>
            <th>SIGNUP IP ADDRESS</th>
            <th>LAST IP ADDRESS</th>

        </tr>
        </thead>

        <tbody>
        <tr>
            <td>{{$user->last_name}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->morgi_tot}}</td>
            <td>{{$user->birth_date}}</td>
            <td>{{$user->country->name ?? ''}}, @if(is_object($user->getRegion()))
                    {{$user->getRegion()->name}}
                @else
                    {{$user->region_name}}
                @endif</td>
            <td>{{$user->getMainPaymentMethod()->payment_info ?? ''}}</td>
            <td>{{$user->status}}</td>
            <td>{{$user_signup->ip_address ?? ''}}</td>
            <td>{{$user_latest->ip_address ?? ''}}</td>
        </tr>

        </tbody>
    </table>
<br><br>
    <h5>Matches</h5>
    <table id="related_account_table" class="table">
        <thead class="thead-light">
        <tr>
            <th>MATCHES</th>
            <th>SURNAME</th>
            <th>EMAIL</th>
            <th>DATE OF BIRTH</th>
            <th>LOCATION, COUNTRY</th>
            <th>PAYMENTS DETAILS</th>
            <th>ACCOUNT STATUS</th>
            <th>IP ADDRESS</th>

        </tr>
        </thead>
            @foreach($matched_users as $match)
                <tr onclick="window.open('{{route('user.edit', $match->id)}}','_blank')">
                    <td>{{$match->found}}</td>
                    <td  @if($match->is_surname_match) style="background: #f3e97a" @endif>
                        {{$match->last_name}}
                    </td>
                    <td  @if($match->is_email_match) style="background: #f3e97a" @endif>
                        {{$match->email}}
                    </td>
                    <td  @if($match->is_bd_match) style="background: #f3e97a" @endif>
                        {{$match->birth_date}}
                    </td>
                    <td  @if($match->is_location_match) style="background: #f3e97a" @endif>
                        {{$match->location}}
                    </td>
                    <td  @if($match->is_payment_match) style="background: #f3e97a" @endif>
                        {{$match->payment_details}}
                    </td>
                    <td>{{$match->status}}</td>
                    <td  @if($match->is_ip_address_match) style="background: #f3e97a" @endif>
                        {{$match->ip_address}}
                    </td>
                </tr>
            @endforeach

        <tbody>


        </tbody>
    </table>


@endsection



@section('js_after')

    <script>
        $(document).ready( function () {
            $('#related_account_table').DataTable({
                "order": [[0, "desc"]]
            })
        });
    </script>


@endsection

