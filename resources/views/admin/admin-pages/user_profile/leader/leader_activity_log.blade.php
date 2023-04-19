@extends('admin.layout')




@section('content')


    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>


    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')

    <br>
    <br>

    <div class="row">
        <div class="col-8  offset-2 text-center">
            <h5>CURRENT LEADER MICROMORGI BALANCE: {{$user->micro_morgi_balance}}</h5>
        </div>
    </div>

    <br>


    <table id="activity_log_table" class="table">
        <thead class="thead-light">
        <tr>
            <th style="width: 13%;">INTERNAL ACTION ID (issued by Morgi)</th>
            <th>ROOKIE ID</th>
            <th>TYPE</th>
            <th>MORGI</th>
            <th>MICRO MORGI</th>
            <th>INITIATED BY</th>
            <th>DATE</th>
            <th>REFUND TYPE</th>
            <th style="width: 18%;">AGAINST WHAT TRANSACTION (INT. ID)</th>

        </tr>
        </thead>

        <tbody>
        @foreach($activities as $log)
            <tr
                @if($log->refund_type == 'refund')
                style="color: red"
                @elseif($log->refund_type == 'withdrawal')
                style="color: blue"
                @elseif($log->refund_type == 'withdrawal_reject')
                style="color: red"
                @endif
            >
                <td>
                    #{{$log->internal_id}}
                </td>
                <td>
                    @if($log->rookie_id) #{{$log->rookie_id}} @else @endif
                </td>
                <td>
                    {{$log->type}}
                </td>
                <td>
                    {{$log->morgi}}
                </td>
                <td>
                    {{$log->micromorgi}}
                </td>
                <td>
                    {{$log->real_initiated_by}}
                </td>
                <td>
                    {{$log->created_at}}
                </td>
                <td>
                    @if(!is_null($log->refund_type))
                        @if(str_contains($log->refund_type, 'void') && $log->admin_id)
                        @elseif(str_contains($log->refund_type, 'refund') && $log->admin_id)
                            REFUND BY ADMIN
                        @elseif(str_contains($log->refund_type, 'void'))
                            VOID DECLINE
                        @elseif(str_contains($log->refund_type, 'refund'))
                            REFUND BY MORGI
                        @elseif(str_contains($log->refund_type, 'chargeback'))
                            CHARGEBACK BY BILLER
                        @else
                            {{$log->refund_type}}
                        @endif
                    @endif
                </td>
                <td>
                    @if(!is_null($log->transaction_internal_id) && $log->refund_type != 'withdrawal')
                        #{{$log->transaction_internal_id}}@endif
                </td>

            </tr>

        @endforeach
        </tbody>
    </table>


@endsection


@section('js_after')

    <script>
        $(document).ready(function () {
            $('#activity_log_table').DataTable({
                "order": [[6, "desc"]]
            });
        });
    </script>

@endsection
