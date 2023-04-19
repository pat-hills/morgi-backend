<div class="table-responsive-xl table-striped">
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>TYPE</th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>TIME BETWEEN<br>ACCOUNT AND TRANSACTION</th>
            <th>FIRST SUBSCRIPTION</th>
            <th>FIRST MM EARNING</th>
            <th>TOT $ MICROMORGI EARNING</th>
            <th>SIGNUP IP ADDRESS</th>
            <th>ADDED PROFILE TEXT</th>
            <th>MESSAGES SENT</th>
            <th>COUNT OF LOGINS</th>
            <th></th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>#{{$user->id}}</td>
            <td>rookie</td>
            <td>{{$user->full_name}}</td>
            <td>{{$user->email}}</td>
            <td>
                @php
                    $date1 = new DateTime($user->created_at);
                    $date2 = new DateTime($transaction_created_at);
                    $interval = $date1->diff($date2);

                @endphp
                {{App\Utils\Utils::formatInterval($interval)}}
            </td>
            <td>
                @if(is_null($user->first_subscription))
                    none
                @else
                    {{date('F j, Y', strtotime($user->first_subscription))}}
                @endif
            </td>
            <td>
                @if(is_null($user->first_earn))
                    none
                @else
                    {{date('F j, Y', strtotime($user->first_earn))}}
                @endif
            </td>
            <td>$
                @if(empty($user->tot_micromorgi_earning))
                    0
                @else
                    {{$user->tot_micromorgi_earning}}
                @endif
            </td>
            <td>{{$user->signup_ip}}</td>
            @if(!empty($user->getOwnDescription()))
                <td>YES</td>
            @else
                <td>NO</td>
            @endif
            <td id="rookie_sent_message">
            </td>
            <td>{{$user->count_log}}</td>
            <td><a style="color: #007bff" href="{{route('user.edit', $user->id)}}" target="_blank"><u>Details</u></a></td>

        </tr>
        </tbody>

    </table>
</div>
