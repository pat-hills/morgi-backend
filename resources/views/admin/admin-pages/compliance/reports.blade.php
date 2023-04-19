@extends('admin.layout')


@section('content')
    <h4 class="mt-5 mb-5">REPORTS</h4>
    <br>
    @include('admin.admin-pages.compliance.widgets.reports-nav-tabs')

    <br>
    <br>

    <div class="row">
        <div class="col-10  form-inline">
            <div class="form-group mb-2">
                <span>From &nbsp;</span>
                <input class="form-control datepicker" type="text" id="fdate" name="start_date" autocomplete="off"
                       @if(!empty($data['start_date']))
                       value="{{$data['start_date']}}"
                       @endif
                       onchange="dataTable()"
                       form="exportForm"
                >
            </div>
            <div class="form-group mb-2">
                <span>&nbsp; To &nbsp;</span>
                <input class="form-control datepicker" type="text" id="tdate" name="end_date" autocomplete="off"
                       @if(!empty($data['end_date']))
                       value="{{$data['end_date']}}"
                       @endif
                       onchange="dataTable()"
                       form="exportForm"
                >
            </div>
        </div>

        <div class="col-2 text-right">
            <form action="{{route('api.reports.export')}}" method="POST" id="exportForm">
                @csrf
                <input type="hidden" name="type" value="{{$type}}">
                <input type="submit" class="btn btn-secondary" value="Export">
            </form>
        </div>
    </div>

    <br>
    <table id="transactions" class="table-striped cell-border">
        <thead>
        <tr>
            @foreach($header as $key => $type_column)
                <th scope="col">{{strtoupper($key)}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>


@endsection

@section('js_after')

    <script>
        $(document).ready(function () {
            dataTable();
        });

        function dataTable() {

            $('#transactions').DataTable({
                "bProcessing": true,
                "serverSide": true,
                @if($type == 'one_leader' OR $type == 'new_card' )
                'ordering': false,
                @endif
                "autoWidth": false,
                "bDestroy": true,
                "bFilter": false,
                'ajax': {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    "url": "{{route('api.reports')}}",
                    "type": "POST",
                    "data": {
                        "type": '{{$type}}',
                        "from": $('#fdate').val(),
                        "to": $('#tdate').val()
                    },
                },
                @if($type != 'one_leader' && $type != 'new_card' )
                "columnDefs": [
                    {"orderable": true, "targets": [0]},
                    {"targets": '_all', "orderable": false}
                ],
                "order": [[0, 'desc']],
                @endif
                createdRow: function (row, data, dataIndex) {
                    @if($type == 'new_card' )
                    if (isValidHttpUrl(data[9])){
                        let profile_link = "<a href='" + data[9] + "' target='_blank'><u>" + data[0] + "</u></a>";
                        $(row).find('td').eq(0).html(profile_link);
                    }
                    @endif
                    if (isValidHttpUrl(data[17])) {
                        let link = "<a href='" + data[17] + "' target='_blank' style='color: blue'>See Transaction</a>"
                        $(row).find('td').eq(17).text('');
                        $(row).find('td').eq(17).append(link);
                    }
                },
                "language": {
                    "infoFiltered": ""
                }
            });
        }

        function isValidHttpUrl(string) {
            let url;

            try {
                url = new URL(string);
            } catch (_) {
                return false;
            }

            return url.protocol === "http:" || url.protocol === "https:";
        }

        $(function () {
            $('#fdate').datepicker({
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

        function loadDatatable() {
            dataTable();
        }
    </script>
@endsection
