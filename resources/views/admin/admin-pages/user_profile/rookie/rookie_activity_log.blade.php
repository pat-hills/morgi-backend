@extends('admin.layout')


@section('content')


    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>


    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')

    <br>
    <br>

    <div class="row">
        <div class="col-8 text-center offset-2">
            <h5>CURRENT ROOKIE BALANCE: &nbsp;&nbsp;&nbsp; MORGI {{$user->untaxed_morgi_balance}} &nbsp;&nbsp;
                MICROMORGI {{$user->untaxed_micro_morgi_balance}}</h5>
        </div>
    </div>

    <br>


    <table id="activity_log_table" class="table">
        <thead class="thead-light">
        <tr>
            <th style="width: 13%;">INTERNAL ACTION ID (issued by Morgi)</th>
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
                @switch($log->refund_type)
                @case('refund')
                style="color: red"

                @break
                @case('withdrawal_pending')
                style="color: orange"
                @break
                @case('withdrawal')
                style="color: blue"
                @break
                @case('withdrawal_rejected')
                style="color: red"
                @break
                @endswitch
            >
                <td>
                    #{{$log->internal_id}}
                </td>
                <td>
                    @if(strstr($log->refund_type, 'withdrawal'))
                        {{strtoupper(str_replace('_', ' ', $log->refund_type))}}
                    @else
                        {{$log->type}}
                    @endif
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
                            @switch($log->refund_type)
                                @case('withdrawal')
                                PAYMENT
                                @break
                                @case('withdrawal_pending')
                                PAYMENT PENDING
                                @break
                                @case('withdrawal_rejected')
                                PAYMENT REJECTED
                                @break
                                @default
                                {{$log->refund_type}}
                                @break
                            @endswitch
                        @endif
                    @endif
                </td>
                <td>
                    @if(!is_null($log->transaction_internal_id))#{{$log->transaction_internal_id}}@endif
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
                "order": [[5, "desc"]]
            });
        });
    </script>

@endsection
