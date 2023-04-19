@extends('admin.layout')

@section('content')

    <h4 class="mt-5 mb-5">Complaints</h4>

    @include('admin.admin-pages.complaints.nav-tabs')
    <br>
    <table id="allComplaints"  class="table-striped">
        <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Complaint made by</th>
            <th scope="col">Email</th>
            <th scope="col">User type</th>
            <th scope="col">Report date</th>
            <th scope="col">Complaint on</th>
            <th scope="col">Complaint subject</th>
            <th scope="col" class="text-center">Status</th>
            <th scope="col">&nbsp;</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
@endsection


@section('js_after')

    <script>
        $(document).ready(function(){

            $('#allComplaints').DataTable({
                "bProcessing": true,
                "serverSide": true,
                'ordering':  false,
                "pageLength": 25,
                "autoWidth": false,
                'ajax': {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    "url": "{{route('api.admin.complaints')}}",
                    "type": "POST",
                    "data": {
                        'status' : "{{last(request()->segments())}}"
                    }
                },
                'columns': [
                    { data: 'id' },
                    { data: 'made_by_username' },
                    { data: 'made_by_email' },
                    { data: 'made_by_type' },
                    { data: 'made_at' },
                    { data: 'on_username' },
                    { data: 'type' },
                    { data: null, defaultContent: '<button class="btn"></button' },
                    { data: null, defaultContent: "<a><u style='color: dodgerblue'>Details</u></a>" },
                ],
                "language": {
                    "search": "Search by username, email:"
                },
                createdRow: function ( row, data, dataIndex) {
                    if (data['counter'] > 0 && data['follow_up'] === null && data['status'] === 'open') {
                        $('td:first-child', row).css('border-left', 'solid red 0.5em');
                    }
                    if (data['follow_up'] !== null) {
                        var timeRemaining = getTimeRemaining(data['follow_up'], '{{\Carbon\Carbon::now(config('app.timezone'))}}');
                        var stringTimeRemaining = timeRemaining.hours + ':' + timeRemaining.minutes + '';
                        var buttonText = '<span style="display: block">48H FOLLOW UP</span><span style="display: block; font-size: small">'+ stringTimeRemaining +'</span>';
                        $(row).find('td').eq(7).addClass('text-center').find('button').addClass('btn-danger').html(buttonText);
                    } else if (data['status'] === "open") {
                        $(row).find('td').eq(7).addClass('text-center').find('button').addClass('btn-success').html('OPEN')
                    } else if ( data['status'] === "closed"){
                        $(row).find('td').eq(7).addClass('text-center').find('button').addClass('btn-secondary').html('CLOSED')
                    }
                    $(row).find('td').eq(8).find('a').attr("href", data['url']);
                }
            });
        });

        function getTimeRemaining(startTime, endTime) {
            const total = Date.parse(startTime) - Date.parse(endTime);
            const seconds = Math.floor((total / 1000) % 60);
            const minutes = Math.floor((total / 1000 / 60) % 60);
            const hours = Math.floor((total / (1000 * 60 * 60)));
            const days = Math.floor(total / (1000 * 60 * 60 * 24));

            return {
                total,
                days,
                hours,
                minutes,
                seconds
            };
        }

    </script>
@endsection
