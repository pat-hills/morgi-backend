@extends('admin.layout')

@section('content')

    <h4 class="mt-5 mb-5">Compliance</h4>
    <h5 class="mt-5 mb-5">Refund reports</h5>

    <br>
    <table class="table table-striped" id="compliance">
        <thead>
        <tr>
            <th scope="col">UCID</th>
            <th scope="col">TYPE</th>
            <th scope="col">EMAIL</th>
            <th scope="col">MORGI TOT</th>
            <th scope="col">MICROMORGI TOT</th>
            <th scope="col">1st purchase</th>
            <th scope="col">LAST PURCHASE</th>
            <th scope="col">LAST BILLER</th>
            <th scope="col">NAME</th>
            <th scope="col">BILLING COUNTRY</th>
            <th scope="col">SIGNUP COUNTRY</th>
            <th scope="col">IP ADDRESS</th>
            <th scope="col">STATUS</th>
            <th scope="col">UPLOADED A PIC</th>
            <th scope="col">UPLOADED A TEXT</th>
            <th scope="col">COUNT OF LOGINS</th>
            <th scope="col">CGB/REFUND</th>
        </tr>
        </thead>
        <tbody>

        @foreach($reports as $report)
            <tr>
                <td>?</td>
                <td>{{$report->refund_type}}</td>
                <td>{{$report->email}}</td>
                <td>{{$report->sum_morgi}}</td>
                <td>{{$report->sum_micromorgi}}</td>
                <td>{{$report->first_purchase}}</td>
                <td>{{$report->last_purchase}}</td>
                <td>CCBILL</td>
                <td>{{$report->username}}</td>
                <td>{{$report->billingCountry}}</td>
                <td>{{$report->signup_country}}</td>
                <td>{{$report->ip_address}}</td>
                <td>{{$report->payment_status}}</td>
                <td>
                    @if($report->has_pic > 0)
                        YES
                    @else
                        NO
                    @endif
                </td>
                <td>
                    @if(!is_null($report->user_description))
                        YES
                    @else
                        NO
                    @endif

                </td>
                <td>{{$report->count_login}}</td>
                <td>{{$report->cgb_refund}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            $('#compliance').DataTable( {
                "order": [[ 0, "desc" ]],
                "language": {
                    "infoFiltered": ""
                }
            } );
        } );
    </script>
@endsection
