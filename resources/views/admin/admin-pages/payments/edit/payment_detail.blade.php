@extends('admin.layout')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
@endsection

@section('content')
{{--    <div class="row">--}}
{{--        <div class="col-8">--}}
{{--            <form action="" method="POST" class="form-inline" autocomplete="off">--}}
{{--                @csrf--}}
{{--                <div class="form-group mb-2">--}}
{{--                    <span>From &nbsp;</span>--}}
{{--                    <input class="form-control datepicker" type="text" id="fdate" name="from_date">--}}
{{--                </div>--}}
{{--                <div class="form-group mb-2">--}}
{{--                    <span>&nbsp; To &nbsp;</span>--}}
{{--                    <input class="form-control datepicker" type="text" id="tdate" name="to_date">--}}
{{--                </div>--}}
{{--                &nbsp;--}}
{{--                <button type="submit" class="btn btn-primary mb-2">Search between the dates</button>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--        --}}{{--        <div class="col-4" style="text-align: right">--}}
{{--        --}}{{--            <a href="{{route('export.excel', $final_query)}}" class="btn btn-secondary">Export to Excel</a>--}}
{{--        --}}{{--        </div>--}}
{{--    </div>--}}

    <br>

    <br>
    <br>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped" id="merchRequest">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">P. PERIOD ID</th>
                    <th scope="col">PERIOD</th>
                    <th scope="col">ID ADMIN</th>
                    <th scope="col">PAYMENT METHOD</th>
                    <th scope="col">DATE <small>(DATE LAST ACTION)</small></th>
                    <th scope="col">TOTAL</th>
                    <th scope="col">STATUS</th>
                    <th scope="col"></th>


                </tr>
                </thead>
                <tbody>
{{--                @foreach($payments as $payment)--}}
{{--                    <tr>--}}
{{--                        <td>#{{$payment->id}}</td>--}}
{{--                        <td>#{{$payment->payment_period_id}}</td>--}}
{{--                        <td>{{$payment->period_name}}</td>--}}
{{--                        @if(!empty($payment->admin_id))--}}
{{--                            <td>#{{$payment->admin_id}}</td>--}}
{{--                        @else--}}
{{--                            <td></td>--}}
{{--                        @endif--}}
{{--                        <td>{{$payment->payment_name}}</td>--}}
{{--                        <td>{{$payment->created_at}}</td>--}}
{{--                        <td>${{$payment->amount}}</td>--}}
{{--                        <td>{{$payment->status}}</td>--}}
{{--                        <td><a href="">Details</a></td>--}}
{{--                    </tr>--}}


{{--                @endforeach--}}
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section('js_after')
@endsection

