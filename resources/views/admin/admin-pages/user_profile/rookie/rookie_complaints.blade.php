@extends('admin.layout')

@section('content')

    @include('admin.admin-pages.user_profile.search-user')

    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>

    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')
    <br>

    <div class="row justify-content-center">
        <div class="col-4" style="text-align: right">
            <button type="button" class="btn btn-primary active" onclick="hidetable('user_complaints')"
                    style="color: white">USER COMPLAINTS
            </button>
        </div>
        <div class="col-4" style="text-align: left">
            <button type="button" class="btn btn-primary active" onclick="hidetable('about_complaints')"
                    style="color: white">COMPLAINTS ABOUT USER
            </button>
        </div>
    </div>

    <br>
    <br>

    <div class="panel panel-default">
        <div class="panel-body">

            <div id="user_complaints">
                <table class="table table-condensed" id="user_complaints_table">

                    <thead class="thead-light">
                    <tr>
                        <th>COMPLAINT TO</th>
                        <th>EMAIL</th>
                        <th>COMPLAINT DATE</th>
                        <th>COMPLAINT SUBJECT</th>
                        <th>STATUS</th>
                        <th>DETAILS</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div id="about_complaints" style="display:none;">
                <table class="table table-condensed" id="about_complaints_table">

                    <thead class="thead-light">
                    <tr>
                        <th>COMPLAINT FROM</th>
                        <th>EMAIL</th>
                        <th>COMPLAINT DATE</th>
                        <th>COMPLAINT SUBJECT</th>
                        <th>STATUS</th>
                        <th>DETAILS</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@section('js_after')

    <script>
        var u_comp = document.getElementById("user_complaints");
        var about = document.getElementById("about_complaints");

        function hidetable(id) {
            u_comp.style.display = (id == "user_complaints") ? "block" : 'none';
            about.style.display = (id == "user_complaints") ? "none" : 'block';
        }

        $(document).ready(function () {
            // $('#table1').DataTable();
            // $('#table2').DataTable();

            getComplaint('user_complaints_table', 'from_user_id', '{{$user->id}}');
            getComplaint('about_complaints_table', 'about_user_id', '{{$user->id}}');

        });


        function getComplaint(table_id, type, user_id) {

            const data = [];
            data[type] = user_id;

            let columns = [];

            if (type === 'from_user_id') {
                columns = [
                    // {data: 'made_by_username'},
                    // {data: 'made_by_email'},
                    // {data: 'made_by_type'},
                    // {data: 'made_by_type'},
                    {data: 'on_username'},
                    {data: 'on_email'},
                    {data: 'made_at'},
                    {data: 'type'},
                    {data: null, defaultContent: '<button class="btn"></button'},
                    {data: null, defaultContent: "<a><u style='color: dodgerblue'>Details</u></a>"},
                ]
            }

            if (type === 'about_user_id') {
                columns = [
                    // {data: 'made_by_username'},
                    // {data: 'made_by_email'},
                    // {data: 'made_by_type'},
                    {data: 'made_by_username'},
                    {data: 'made_by_email'},
                    {data: 'made_at'},
                    {data: 'type'},
                    {data: null, defaultContent: '<button class="btn"></button>'},
                    {data: null, defaultContent: "<a><u style='color: dodgerblue'>Details</u></a>"},

                ]
            }


            $('#' + table_id).DataTable({
                "bProcessing": true,
                "serverSide": true,
                'ordering': false,
                "autoWidth": false,
                'ajax': {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    "url": "{{route('api.admin.complaints')}}",
                    "type": "POST",
                    "data": data
                },
                'columns': columns,
                "language": {
                    "search": "Search by username, email:"
                },
                createdRow: function (row, data, dataIndex) {
                    if (data['counter'] > 0 && data['follow_up'] === null && data['status'] === 'open') {
                        $('td:first-child', row).css('border-left', 'solid red 0.5em');
                    }
                    if (data['follow_up'] !== null) {
                        var timeRemaining = getTimeRemaining(data['follow_up'], '{{\Carbon\Carbon::now(config('app.timezone'))}}');
                        var stringTimeRemaining = timeRemaining.hours + ':' + timeRemaining.minutes + '';
                        var buttonText = '<span style="display: block">48H FOLLOW UP</span><span style="display: block; font-size: small">' + stringTimeRemaining + '</span>';
                        $(row).find('td').eq(4).find('button').addClass('btn-danger').html(buttonText);
                    } else if (data['status'] === "open") {
                        $(row).find('td').eq(4).find('button').addClass('btn-success').html('OPEN')
                    } else if (data['status'] === "closed") {
                        $(row).find('td').eq(4).find('button').addClass('btn-secondary').html('CLOSED')
                    }
                    $(row).find('td').eq(5).find('a').attr("href", data['url']);
                }
            });
        }

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

