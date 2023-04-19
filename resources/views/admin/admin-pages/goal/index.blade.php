@extends('admin.layout')

@section('content')

    <div class="row">
        <div class="col-2">

        </div>
        <div class="col">
            <h4 class="mt-5 mb-5">Goals</h4>
            <br>
            <table id="goalsTable" class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">GOAL ID</th>
                    <th scope="col">ROOKIE ID</th>
                    <th scope="col">NAME</th>
                    <th scope="col">CREATED AT</th>
                    <th scope="col">STATUS</th>
                    <th scope="col">HOURS WAITING</th>
                    <th scope="col">SLUG</th>
                    <th scope="col" class="text-right"></th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="col-2">

        </div>
    </div>

@endsection


@section('js_after')

    <script>
        $(document).ready(function () {

            let status;

            @if(isset($status))
                status = '{{$status}}';
            @endif

            $('#goalsTable thead tr')
                .clone(true)
                .addClass('filters')
                .appendTo('#goalsTable thead');

            let select = $('<select>');

            select.append($("<option>").attr('value', '').text('ALL'));

            let all_status = getStatusCounter();
            all_status.then(function (status_response) {
                let arrSelect = [];
                status_response.forEach(function (status) {
                    arrSelect.push({
                        value: status.status,
                        text: '' + status.status + ' (' + status.counter + ')'
                    })
                })

                arrSelect.map(function (arr) {
                    select.append($("<option>").attr('value', arr.value).text(arr.text.toUpperCase()))
                });
            })

            $.fn.dataTable.ext.errMode = 'none'
            $('#goalsTable')
                .on('error.dt', function (e, settings, techNote, message) {
                    $('#alert-message').html(message);
                    $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                        $("#alert-danger").slideUp(500);
                    });
                })
                .DataTable({
                    "searchDelay": 2000,
                    "bProcessing": true,
                    "serverSide": true,
                    "autoWidth": false,
                    'ajax': {
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        "url": "{{route('api.admin.goals.index')}}",
                        "data": {
                            'status': status
                        },
                    },
                    'order': [[2, "desc"]],
                    'columnDefs': [
                        {orderable: false, "targets": [0, 1, 3, 5]}
                    ],
                    'columns': [
                        {data: 'id'},
                        {data: 'rookie_id'},
                        {data: 'name'},
                        {data: 'created_at'},
                        {data: 'status'},
                        {data: 'time_waiting', defaultContent: '0H'},
                        {data: 'slug'},
                        {data: null, defaultContent: '<a style="color: #0000FF">Details</a>'},
                    ],
                    "language": {
                        "search": "Search by Rookie ID"
                    },
                    createdRow: function (row, data, dataIndex) {
                        $(row).find('td').eq(0).html('#' + data['id']);
                        $(row).find('td').eq(1).html('#' + data['rookie_id']);

                        $(row).find('td').eq(2).html(data['name']);

                        $(row).find('td').eq(3).html(new Date(data['created_at']).toDateString());

                        $(row).find('td').eq(4).html(data['status'].replaceAll('_', ' ').toUpperCase());

                        switch (data['status']) {
                            case 'proof_pending_approval':
                            case 'pending':
                            case 'review':

                                $(row).find('td').eq(4).addClass('text-warning')
                                $(row).find('td').eq(5).html(Math.round(data['time_waiting'] / 60) + "H");
                                break;
                            case 'cancelled':
                            case 'awaiting_proof':

                                $(row).find('td').eq(4).addClass('text-secondary')
                                break;
                            case 'active':
                                $(row).find('td').eq(4).addClass('text-primary')
                                break;
                            case 'successful':

                                $(row).find('td').eq(4).addClass('text-success')
                                break;
                            case 'proof_status_declined':
                                $(row).find('td').eq(4).addClass('text-danger')
                                break;
                        }

                        $(row).find('td').eq(6).html(data['slug']);

                        let link = "{{ route('goals.show', [':goal_id']) }}".replace(':goal_id', data['id']);
                        $(row).find('td').eq(7).addClass('text-right').find('a').attr('href', link);
                    },
                    initComplete: function () {
                        var api = this.api();
                        // For each column
                        api
                            .columns()
                            .eq(0)
                            .each(function (colIdx) {
                                // Set the header cell to contain the input element
                                var cell = $('.filters th').eq(
                                    $(api.column(colIdx).header()).index()
                                );
                                var title = $(cell).text();
                                if (title.toUpperCase() !== 'STATUS') {
                                    $(cell).html('')
                                    return;
                                }
                                $(cell).html(select);
                                // On every keypress in this input
                                $('select', $('.filters th').eq($(api.column(colIdx).header()).index()))
                                    .off('keyup change')
                                    .on('change', function (e) {
                                        // Get the search value
                                        $(this).attr('title', $(this).val());
                                        var regexr = '{search}'; //$(this).parents('th').find('select').val();

                                        var cursorPosition = this.selectionStart;
                                        // Search the column for that value
                                        api
                                            .column(colIdx)
                                            .search(
                                                this.value != ''
                                                    ? regexr.replace('{search}', this.value)
                                                    : '',
                                                this.value != '',
                                                this.value == ''
                                            )
                                            .draw();
                                    })
                                    .on('keyup', function (e) {
                                        e.stopPropagation();

                                        $(this).trigger('change');
                                        $(this)
                                            .focus()[0]
                                            .setSelectionRange(cursorPosition, cursorPosition);
                                    });
                            });
                    },
                });
        });

        async function getStatusCounter() {

            let result = {};
            await $.ajax({
                url: '{{route('api.admin.goals.status-counter')}}',
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    result = res;
                }
            });

            return result;
        }
    </script>
@endsection
