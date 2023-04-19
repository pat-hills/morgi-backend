@extends('admin.layout')


@section('content')
    <h4 class="mt-5 mb-5">PAYMENTS</h4>
    <br>
    <div class="row">
        <div class="col-8">
            <form action="" method="POST" class="form-inline" autocomplete="off">
                @csrf
                <div class="form-group mb-2">
                    <span>From &nbsp;</span>
                    <input class="form-control datepicker" type="text" id="fdate" name="start_date">
                </div>
                <div class="form-group mb-2">
                    <span>&nbsp; To &nbsp;</span>
                    <input class="form-control datepicker" type="text" id="tdate" name="end_date">
                </div>
                &nbsp;
                <button type="submit" class="btn btn-primary mb-2">Search between the dates</button>
            </form>
        </div>
    </div>

    <br>
    <br>
    <br>

    @include('admin.admin-pages.payments.nav-tabs')

    <br>
    <br>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped" id="paymentsTable">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">P. PERIOD ID</th>
                    <th scope="col">PERIOD</th>
                    <th scope="col">N° ROOKIES</th>
                    <th scope="col">PAYMENT METHOD</th>
                    <th scope="col">DATE <small>(DATE LAST ACTION)</small></th>
                    <th scope="col">TOTAL</th>
                    <th style="width: 15%">STATUS</th>
                    <th scope="col"></th>


                </tr>
                </thead>
                <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>#{{$payment->id}}</td>
                        <td>#{{$payment->payment_period_id}}</td>
                        <td>{{date("d", strtotime($payment->period_start_date))}}/{{date("d M", strtotime($payment->period_end_date))}}</td>
                        <td>{{$payment->count_rookies}}</td>
                        <td>{{$payment->platform_name}}</td>
                        <td>{{$payment->updated_at}}</td>
                        <td>${{$payment->total}}</td>
                        <td>
                            @if($payment->status == 'completed')
                                Completed
                            @else

                                <form action="{{route('payments.update')}}" method="POST" id="form_{{$payment->id}}">
                                    <input type="hidden" name="payment_id" value="{{$payment->id}}">
                                    @csrf
                                    <div class="form-group">
                                        <select class="form-control" name="action" id="payment_status_{{$payment->id}}"
                                                onchange="updatePaymentStatus('{{$payment->id}}')">
                                            <option selected>{{ucfirst($payment->status)}}</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </form>

                            @endif

                        </td>

                        {{--                        <td style="color: orange">{{$payment->count_pendings}} PENDING</td>--}}
                        <td><a class="custom-unvisited-link" href="{{route('payments.show', $payment->id)}}">Details</a></td>
                    </tr>


                @endforeach
                </tbody>
            </table>
        </div>
    </div>

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


        function updatePaymentStatus(id){
            if(document.getElementById('payment_status_'+id).value == 'completed') {

                document.getElementById("form_" + id).submit();
            }
        }

    </script>

    <script>
        $(document).ready( function () {
            $('#paymentsTable').DataTable({
                "order": [[ 2, "desc" ]]
            });
        } );
    </script>
@endsection

