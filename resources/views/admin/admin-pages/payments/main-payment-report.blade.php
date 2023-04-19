@extends('admin.layout')

@section('content')

    <h4 class="mt-5 mb-5">Payments</h4>
    <h5 class="mt-5 mb-5">Main Payment Page</h5>

    <br>
    <form class="form-inline" method="POST" action="#">
        @csrf
        <div class="form-group mb-2">
            <div class="form-group">
                <label for="period">Period</label> &nbsp; &nbsp;
                <select class="form-control" id="period" name="period_id">
                    @if(array_key_exists('period_id', $data))

                        <option selected value="{{$data['period_id']}}">{{$data['period_id']}} - {{$data['period_name']}}</option>

                    @else
                        <option selected disabled>Choose...</option>

                    @endif

                    @foreach($periods as $period)
                        @if(array_key_exists('period_id', $data) && $data['period_id'] == $period->id)
                            @continue
                        @endif
                        <option value="{{$period->id}}">{{$period->id}} - {{$period->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        &nbsp;
        <button type="submit" class="btn btn-primary mb-2">Search</button>
    </form>
    <table class="table table-striped" id="compliance">
        <thead>
        <tr>
            <th scope="col">PAYMENT METHOD</th>
            <th scope="col">DATE</th>
            <th scope="col">CURRENCY</th>
            <th scope="col">AMOUNT TO PAY $</th>
            <th scope="col">COUNT OF PAYMENTS</th>
            <th scope="col">COUNT REJECTED</th>
            <th scope="col">TOTAL REJECTED $</th>
            <th scope="col">TOTAL PAID</th>
            <th scope="col">COUNT PAID</th>

        </tr>
        </thead>
        <tbody>

        @foreach($payments as $main)
            <tr>
                <td>{{$main->platform_name}}</td>
                <td>{{$main->updated_at}}</td>
                <td>USD</td>
                <td>${{$main->pending_total ?? 0}}</td>
                <td>{{$main->count_rookies}}</td>

                <td>{{$main->count_declined_rookies}}</td>
                <td>${{$main->count_declined ?? 0}}</td>

                <td>${{$main->count_paid ?? 0}}</td>
                <td>{{$main->count_paid_users}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            $('#compliance').DataTable( {
                "order": [[ 0, "desc" ]]
            } );
        } );
    </script>
@endsection
