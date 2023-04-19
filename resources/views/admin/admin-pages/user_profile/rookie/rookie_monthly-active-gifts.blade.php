@extends('admin.layout')


@section('content')

    @include('admin.admin-pages.user_profile.search-user')


    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>

    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')
    <br>

    <div class="row justify-content-center">
        <div class="col-4" style="text-align: right">
            <a href="#" type="button" class="btn btn-primary active" style="color: white"> <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" fill="currentColor" class="bi bi-circle-fill" viewBox="0 0 16 16" style="color: limegreen">
                    <circle cx="8" cy="8" r="8"/>
                </svg> Active subscribers ({{count($subscriptions)}})</a>
        </div>
        <div class="col-4" style="text-align: left">
            <a href="{{route('user.edit.active_gifts.not-active-gifts', $user->id)}}" type="button" class="btn btn-primary">Inactive subscribers ({{$opposite_subscriptions_counter}})</a>
        </div>
    </div>

    <br>
    <br>

    <div class="row">
        <div class="col-12 text-right">
            <a href="#" style="color: #007bff" data-toggle="modal" data-target="#modalShowFines" id="a-fine-modal"><u>Show Morgi's Fines</u></a>
        </div>
    </div>
    @include('admin.admin-pages.user_profile.user_widget.modal-show-fines', ['fine_type' => 'morgi'])

    <div class="row justify-content-center" style="background: whitesmoke">
        <div class="col-4">
            TOTAL ACTIVE MONTHLY SUBSCRIBERS: <span style="color: green">{{$total_monthly}} MORGI</span>
        </div>
    </div>
    <br>
        <div class="panel panel-default">
            <div class="panel-body">

                    <table id="activeSubsTable" class="table table-condensed" style="border-collapse:collapse;">
                    <thead>
                    <tr>
                        <th>Leader</th>
                        <th>Email</th>
                        <th>Last Subscription</th>
                        <th>Status</th>
                        <th>Morgi</th>
                        <th>&nbsp;</th>

                    </tr>
                    </thead>

                    <tbody>

                        @foreach($subscriptions as $sub)
                            <tr data-toggle="collapse" data-target="#demo{{$sub['id']}}" class="accordion-toggle" onclick="setSubTable('{{$sub['id']}}')">
                                <td>{{$sub['username']}}</td>
                                <td>{{$sub['email']}}</td>
                                <td>{{$sub['last_subscription_at']}}</td>
                                <td>{{strtoupper($sub['status'])}}</td>
                                <td>{{$sub['morgi']}}</td>

                                <td class="text-right"><a href="#"> <i class="fas fa-info-circle fa-1x"></i></a></td>

                            </tr>
                            <tr>
                                <td colspan="12" class="hiddenRow">
                                    <div class="accordian-body collapse" id="demo{{$sub['id']}}">
                                        <br>
                                        <br>
                                        <div class="row">
                                            <div class="col-6">
                                                &nbsp;
                                            </div>
                                            <div class="col-5" style="text-align: right">
                                                TOTAL RECEIVED:
                                                <span style="color: limegreen" id="tot{{$sub['id']}}"></span>
                                                MORGI
                                            </div>
                                            <div class="col-1">
                                                &nbsp;
                                            </div>

                                        </div>
                                        <br>
                                        <table class="table table-condensed" style="border-collapse:collapse;">
                                            <thead>
                                            <tr>
                                                <th style="width: 60%">&nbsp;</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


            </div>

        </div>

    </div>

    <script>
        $(document).ready(function () {
            $('#a-fine-modal').one('click', function () {
                setFines();
            });
        });

        function setSubTable(id){
            let demo = $('#demo' + id);
            demo.css('pointer-events', 'none');
            let first_tr = demo.find('tbody').find('tr');
            if (demo.find('tr').length === 1) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    type: "GET",
                    url: '/api/admin/subscriptions/' + id + '/payments-history',
                    success: function (response) {
                        $('#tot' + id).text(response.total);
                        let table = $('#demo'+ id).find('tbody');
                        let color;
                        Object.keys(response.data).forEach(key => {

                            switch (response.data[key].status) {
                                case 'paid':
                                case 'coupon':
                                    color = 'limegreen';
                                    break;
                                default:
                                    color = 'red';
                                    break;
                            }

                            table.append(
                                "<tr><td></td><td>" + response.data[key].created_at + "</td><td>" + response.data[key].status.toUpperCase() + "</td><td style='color: "+color+"'>" + response.data[key].amount + "</td></tr>"
                            );
                        });
                        $('body').css('pointer-events', 'all')
                    },
                    error: function (response) {
                        let table = demo.find('tbody');
                        table.append(
                            "<tr data-result='error'><td style='color: red'>Something went wrong</td><td></td><td></td><td></td></tr>"
                        );
                        demo.css('pointer-events', 'all')
                    }
                });
            }else{
                if(first_tr.attr('data-result') === 'error'){
                    first_tr.remove();
                }
                demo.css('pointer-events', 'all')
            }

        }
    </script>

@endsection

