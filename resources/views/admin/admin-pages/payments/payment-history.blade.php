@extends('admin.layout')

@section('content')

    <h4 class="mt-5 mb-5">Payments</h4>
    <h5 class="mt-5 mb-5">Payment History</h5>

    <br>

    <form class="form-inline" method="POST" action="#">
        @csrf
        <div class="form-group mb-2">
            <div class="form-group">
                <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                       @if(!empty($data['email']))
                       value="{{$data['email']}}"
                    @endif
                >
            </div>
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <div class="form-group">

                <select class="form-control" id="platform" name="payment_platform_id">
                    @if(array_key_exists('payment_platform_id', $data))

                        <option selected value="{{$data['payment_platform_id']}}">{{$data['payment_platform_name']}}</option>

                    @else
                        <option selected disabled>All</option>

                    @endif
                    <option value="">All</option>
                    @foreach($platforms as $platform)
                        @if(array_key_exists('payment_platform_id', $data) && $data['payment_platform_id'] == $platform->id)
                            @continue
                        @endif
                        <option value="{{$platform->id}}">{{$platform->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group mb-2">
            <span>From &nbsp;</span>
            <input class="form-control datepicker" type="text" id="fdate" name="start_date" autocomplete="off"
                   @if(!empty($data['start_date']))
                   value="{{$data['start_date']}}"
                @endif>
        </div>
        <div class="form-group mb-2">
            <span>&nbsp; To &nbsp;</span>
            <input class="form-control datepicker" type="text" id="tdate" name="end_date"  autocomplete="off"
                @if(!empty($data['end_date']))
                    value="{{$data['end_date']}}"
                    @endif
                >
        </div>
        &nbsp; &nbsp;
        <button type="submit" class="btn btn-primary mb-2">Search</button>
        &nbsp;
        <button type="reset" value="reset" class="btn btn-light mb-2" title="reset" onclick="resetSearch()">&nbsp;<i class="fas fa-redo-alt"></i>&nbsp;</button>
    </form>


    <table class="table table-striped" id="compliance">
        <thead>
        <tr>
            <th scope="col">PAYMENT METHOD</th>
            <th scope="col">DETAILS</th>
            <th scope="col">AMOUNT</th>
            <th scope="col">DATE</th>
            <th scope="col">CURRENCY</th>
            <th scope="col">PAYMENT ID</th>
            <th scope="col">COUNTRY</th>
            <th scope="col">PERIOD ID</th>
            <th scope="col">STATUS</th>
            <th scope="col">USERNAME</th>
            <th scope="col">EMAIL</th>

        </tr>
        </thead>
        <tbody>

        @foreach($payments as $payment)
            <tr>
                <td>{{$payment->platform_name}}</td>
                <td>{{$payment->reference}}</td>
                <td>{{$payment->total}}</td>
                <td>{{$payment->created_at}}</td>
                <td>USD</td>
                <td>{{$payment->id}}</td>

                <td>{{$payment->country_name}}</td>
                <td>{{$payment->payment_period_id}}</td>
                <td>{{$payment->rookie_status}}</td>


                <td>{{$payment->username}}</td>
                <td>{{$payment->email}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection


@section('js_after')
    <script>
        $(function () {

            $('#fdate').datepicker({
                // format: "dd-M-yy",
                format: "yyyy-mm-dd",
                todayHighlight: 'TRUE',
                autocomplete: false,
                autoclose: true,
                minDate: 0,
                maxDate: '+1Y+6M'
            }).on('changeDate', function (ev) {
                $('#tdate').datepicker('setStartDate', $("#fdate").val());
            });


            $('#tdate').datepicker({
                // format: "dd-M-yy",
                format: "yyyy-mm-dd",
                todayHighlight: 'TRUE',
                autocomplete: false,
                autoclose: true,
                minDate: '0',
                maxDate: '+1Y+6M'
            }).on('changeDate', function (ev) {
                var start = $("#fdate").val();
                var startD = new Date(start);
                var end = $("#tdate").val();
                var endD = new Date(end);
            });

        });


        function resetSearch(){
            $('#email').removeAttr('value');
            $('#platform').removeAttr('value');
            $('#fdate').removeAttr('value');
            $('#tdate').removeAttr('value');
        }

    </script>
    <script>
        $(document).ready( function () {
            $('#compliance').DataTable( {
                "order": [[ 3, "desc" ]]
            } );
        } );
    </script>
@endsection
