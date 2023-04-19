@extends('admin.layout')

@section('content')
    <h4 class="mt-5 mb-5">PAYMENTS</h4>
    <br>
    <h5>REJECTS REPORT</h5>
    <br>


    <br>
    <br>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped" id="paymentsTable">
                <thead>
                <tr>
                    <th scope="col">PAYMENT METHOD</th>
                    <th scope="col">USER EMAIL</th>
                    <th scope="col">REJECT AMOUNT</th>
                    <th scope="col">PAYMENT ID</th>
                    <th scope="col">REASON</th>
                    <th scope="col">ADMIN USER</th>
                    <th scope="col">DATE OF CANCEL</th>

                </tr>
                </thead>
                <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{$report->payment_method}}</td>
                        <td>{{$report->email}}</td>
                        <td>${{$report->amount}}</td>
                        <td>{{$report->id}}</td>
                        <td>{{$report->note}}</td>
                        <td>{{$report->admin}}</td>
                        <td>{{date('Y-m-d H:i:s', strtotime($report->updated_at))}}</td>
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
                "order": [[ 6, "desc" ]]
            });
        } );
    </script>
@endsection

