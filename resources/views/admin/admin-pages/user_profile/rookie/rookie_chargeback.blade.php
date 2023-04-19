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
            <th>Earning this PP USD</th>
            <th>CGB USD made this period</th>
            <th>Percent of USD earnings CGB</th>
            <th>Tot CGB percent for all earnings</th>
            <th>MM USD refunded this PP actual micro refunds give back as micro</th>
            <th>% of Micros earnings refunded fot this PP USD</th>
            <th>tot Morgi refund % tot for earnings trans and CGB USD</th>


        </tr>
        </thead>

        @foreach($transactions as $transaction)
            <tr>
                <td>{{$transaction->period}}</td>
                <td>${{$transaction->earning_pp_usd}}</td>
                <td>${{$transaction->cgb_pp}}</td>
                <td>{{$transaction->percentage_usd_trans}}%</td>
                <td>{{$transaction->percentage_cgb_trans}}</td>
                <td>{{$transaction->mm_usd_refunded}}</td>
                <td>{{$transaction->percentage_usd_mm_refunded}}</td>
                <td>{{$transaction->percentage_morgi_refund}}</td>
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

