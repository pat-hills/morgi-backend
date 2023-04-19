@extends('admin.layout')




@section('content')


    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id ?? ''}} Type <u>{{ucfirst($user->type)}}</u></h3>


    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')

    <br>
    <br>
    <table id="cgbHistory_table" class="table table-responsive-lg">
        <thead class="thead-light">
        <tr>
            <th>MONTH</th>
            <th>TOT TRANSACTIONS AMOUNT THIS PP USD</th>
            <th>CGB USD MADE THIS PERIOD</th>
            <th>PERCENT OF USD CHARGEBACK</th>
            <th>TOT CGB PERCENT FOR ALL TRANS AND CHARGEBACK</th>
            <th>MICROMORGI USD REFUNDED THIS P.PERIOD ACTUAL MICRO REFUNDS GIVE BACK AS MICRO</th>
            <th>PERCENT OF MICROS REFUNDED FOR THIS PP USD</th>
            <th>TOT MORGI REFUND PERCENT TOTAL FOR ALL TRANS AND CHARGEBACK USD</th>


        </tr>
        </thead>

        @foreach($transactions as $transaction)
            <tr>
                <td>{{$transaction->period}}</td>
                <td>${{$transaction->tot_transations_amount_usd}}</td>
                <td>${{$transaction->cgb_pp}}</td>
                <td>{{$transaction->cgb_percent_pp}}%</td>
                <td>{{$transaction->tot_cgb}}</td>
                <td>{{$transaction->micromorgi_refunded}}</td>
                <td>{{$transaction->mm_percent_pp_usd}}</td>
                <td>{{$transaction->tot_morgi_transaction}}</td>
            </tr>


        @endforeach



        <tbody>


        </tbody>
    </table>


@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            $('#cgbHistory_table').DataTable( {
                "order": [[ 0, "desc" ]]
            } );
        } );
    </script>
@endsection

