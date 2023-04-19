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

            <th>UCID</th>
            <th>EMAIL</th>
            <th>MORGI TOT</th>
            <th>MICROMORGI TOTAL</th>
            <th>1ST PURCHASE</th>
            <th>LAST PURCHASE</th>
            <th>LAST BILLER</th>
            <th>NAME</th>
            <th>BILLING COUNTRY</th>
            <th>SIGNUP COUNTRY</th>
            <th>SIGNUP IP ADDRESS</th>
            <th>LAST IP ADDRESS</th>
            <th>ACCOUNT STATUS</th>
            <th>UPLOADED A PIC</th>
            <th>ADDED PROFILE TEXT</th>
            <th>MESSAGES</th>
            <th>COUNT LOGIN</th>
            <th>CHARGEBACK/REFUND</th>

        </tr>
        </thead>

        <tbody>
        <tr>
            <td>{{$user->ucid}}</td>
            <td>{{$user->email}}</td>
            <td>{{$user->morgi_tot}}</td>
            <td>{{$user->micro_morgi_tot}}</td>
            <td>{{$user->first_purchase}}</td>
            <td>{{$user->last_purchase}}</td>
            @if($user->first_purchase == 'none')
                <td></td>
            @else
                <td>CCBILL</td>
            @endif
            <td>{{$user->username}}</td>
            <td>{{$user->billing_country}}</td>
            <td>{{$user->signup_country_name}}</td>
            <td>{{$user_signup->ip_address}}</td>
            <td>{{$user_latest->ip_address}}</td>
            <td>{{$user->status}}</td>
            <td>
                @if($user->has_pic > 0)
                    YES
                @else
                    NO
                @endif
            </td>
            @if(empty($user->getOwnDescription()))
                <td>NO</td>
            @else
                <td>YES</td>
            @endif
            <td>
                @if($user->sent_first_message)
                    YES
                @else
                    NO
                @endif
            </td>
            <td>{{$user->count_logins}}</td>
            <td>{{$user->cgb_refund}}</td>
        </tr>

        </tbody>
    </table>
    <br><br>
    <h5>Matches</h5>
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
