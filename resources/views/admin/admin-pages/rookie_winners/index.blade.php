@extends('admin.layout')

@section('head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
@endsection


@section('content')

    <div class="container-lg">
        <div class="row text-center">
            <div class="col-12">
                <h2>Today's Winners</h2>
                @if(!empty($latest[0]))
                    <h6>{{date('Y-M-d', strtotime($latest[0]->win_at))}}</h6>
                @else
                    <h6>none</h6>
                @endif
            </div>
        </div>

        <br>
        <br>
        <br>

        <div class="row text-center">
            <div class="col"></div>
            @foreach($latest as $user)
                <div class="col-sm">
                    <img
                        @if(!empty($user->getOwnAvatar()->path_location))


                            src="{{$user->getOwnAvatar()->url}}"

                        @else

                            src=""
                        @endif
                        class="img-thumbnail"
                        height="180" width="180">
                    <br>
                    <br>
                    <span>{{$user->full_name}}</span>
                    <br>
                    <span><small><a href="{{route('user.edit', $user->id)}}" style="color: #007bff">Go to profile &nbsp;<i
                                    class="fas fa-arrow-right"></i></a></small></span>

                </div>
            @endforeach
            <div class="col"></div>
        </div>
        <br>
        <br>
        <br>


        <div class="row text-center">
            <div class="col-12">
                <h3>Winners Rookie</h3>
            </div>
        </div>
        <br>
        <br>
        <div class="container">
            <form action="{{route('show.rookies_ofd')}}" method="GET" autocomplete="on" id="search_Form">
                @csrf
                <div class="row">
                    <div class="col-12 form-inline">


                        <div class="form-group mb-2">
                            <span>From &nbsp;</span>
                            <input class="form-control datepicker" type="text" id="fdate" name="from_date"
                                   autocomplete="off" value="{{$request->from_date ?? ''}}">
                        </div>
                        <div class="form-group mb-2">
                            <span>&nbsp; To &nbsp;</span>
                            <input class="form-control datepicker" type="text" id="tdate" name="to_date"
                                   autocomplete="off" value="{{$request->to_date ?? ''}}">
                        </div>
                        <div class="col">

                        </div>
                        <div class="form-group mb-2 text-right">
                            <input class="form-control" type="text" id="by_first_name" name="by_first_name"
                                   value="{{$request->by_first_name ?? ''}}"
                                   placeholder="First name...">
                        </div>
                        &nbsp;
                        <div class="form-group mb-2 text-right">
                            <input class="form-control" type="text" id="by_last_name" name="by_last_name"
                                   value="{{$request->by_last_name ?? ''}}"
                                   placeholder="Last name...">
                        </div>

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-12 text-right">
                        <a href="{{route('show.rookies_ofd')}}" class="btn btn-light mb-2" title="reset">&nbsp;<i
                                class="fas fa-redo-alt"></i>&nbsp;</a>
                        <button type="submit" class="btn btn-primary mb-2">Search</button>
                    </div>
                </div>

                <br>

                <br>
                <div class="row">
                    <div class="row">
                        <div class="col-8">
                            <label for="days">Days to show</label>
                            <select class="form-control" id="days" name="limit" onchange="this.form.submit()"
                                    style="width: 80px">
                                {{--                                <option selected>{{count($winners)}}</option>--}}
                                {{--                                <option>5</option>--}}
                                {{--                                <option>10</option>--}}
                                {{--                                <option>25</option>--}}
                                {{--                                <option>50</option>--}}
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <br>
                        <div class="panel panel-default">
                            <div class="panel-body">

                                <table class="table table-condensed" style="border-collapse:collapse;">

                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($winners as $key => $winner)
                                        <tr data-toggle="collapse" data-target="#demo{{$key}}" class="accordion-toggle">
                                            <td>{{$key}}</td>
                                            <td class="text-right"><i class="fas fa-info-circle fa-1x"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="12" class="hiddenRow">
                                                <div class="accordian-body collapse" id="demo{{$key}}">
                                                    <table class="table table-condensed"
                                                           style="border-collapse:collapse;">
                                                        <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th>Full Name</th>

                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($winner as $user)
                                                            <tr>
                                                                <td></td>
                                                                <td>
                                                                    <a href="{{route('user.edit', $user->rookie_id)}}">{{$user->first_name}}</a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
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
                </div>
            </form>
            <div class="row">
                <div class="col-8">

                </div>
                <div class="col-4 text-right">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination" id="pagination">

                        </ul>
                    </nav>
                </div>
            </div>
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

        $(document).ready(function () {
            setDaysToShow();
            setPagination();
        });

        function setDaysToShow() {
            let limits = document.getElementById('days');
            const current_limit = "{{$limit}}"
            const limit_arr = ["5", "10", "25", "50"];

            for (let i = 0; i < 4; i++) {
                let opt = document.createElement('option');
                opt.setAttribute('value', limit_arr[i]);
                opt.innerHTML = limit_arr[i];
                if (current_limit === limit_arr[i]) {
                    opt.setAttribute('selected', true);
                }
                limits.appendChild(opt);
            }
        }

        function setPagination() {

            const max = "{!! $pages !!}";
            let url = window.location.href;
            let how_many = 2;

            url = new URL(url);
            let page = url.searchParams.get("page");


            if (!page) {
                if (url.toString().includes('?')) {
                    url = url + '&page=1';
                } else {
                    url = url + '?page=1';
                }
            }

            if (/page=([^&]+)/.exec(url) != null) {
                page = /page=([^&]+)/.exec(url)[1];
            }

            page = parseInt(page);


            url = url.toString()
            // let current_page = page ? page : 1;

            let pagination = document.getElementById('pagination');

            let bef = hasBeforePage(page);

            let li = document.createElement('li');
            let a = document.createElement('a');

            if (max > 0) {

                a.setAttribute('href', url.replace('page=' + page, 'page=' + (page - 1)));
                a.setAttribute('class', 'page-link')
                a.innerHTML = 'Previous';
                li.appendChild(a);
                li.setAttribute('class', getClassItem(bef));
                pagination.appendChild(li);
            }

            needFirstPage(url, page);


            let loop = (how_many * 2) + 1;

            for (let i = loop; i > 0; i--) {

                let to_subtract = -how_many + (i - 1);
                let to_show = page - to_subtract;


                if (to_show <= 0 || to_show > max) {
                    continue;
                }

                li = document.createElement('li');
                a = document.createElement('a');
                // a.setAttribute('href', url.replace'?page=' + to_show);
                // a.setAttribute('href', url.replace'?page=' + to_show);
                a.setAttribute('href', url.replace('page=' + page, 'page=' + to_show));
                a.setAttribute('class', 'page-link');
                a.innerHTML = to_show;
                li.appendChild(a);
                li.setAttribute('class', isActivePage(to_show, page));
                pagination.appendChild(li);
            }

            needLastPage(url, page, max);


            let next = hasNextPage(page, max);

            li = document.createElement('li');
            a = document.createElement('a');
            if (max > 0) {
                a.setAttribute('href', url.replace('page=' + page, 'page=' + (page + 1)));
                a.setAttribute('class', 'page-link')
                a.innerHTML = 'Next';
                li.appendChild(a);
                li.setAttribute('class', getClassItem(next));
                pagination.appendChild(li);
            }

        }

        function hasBeforePage(current) {
            if (current === 1) {
                return false;
            }

            return true;
        }

        function hasNextPage(current, max) {
            if (current === parseInt(max)) {
                return false;
            }
            return true;
        }

        function getClassItem(res) {
            if (res) {
                return 'page-item';
            }
            return 'page-item disabled';
        }

        function isActivePage(to_show, current) {
            if (to_show === current) {
                return 'page-item active';
            }
            return 'page-item';
        }

        function needFirstPage(current, page) {
            let pagination = document.getElementById('pagination');

            if (page > 3) {
                let li = document.createElement('li');
                let a = document.createElement('a');
                a.setAttribute('href', current.replace('page=' + page, 'page=1'));
                a.setAttribute('class', 'page-link')
                a.innerHTML = 1;
                li.appendChild(a);
                li.setAttribute('class', 'page-item');

                pagination.appendChild(li);
                li = document.createElement('li');
                a = document.createElement('a');
                a.setAttribute('href', '');
                a.setAttribute('class', 'page-link')
                a.innerHTML = '...';
                li.appendChild(a);
                li.setAttribute('class', 'page-item disabled');
                pagination.appendChild(li);
            }
        }

        function needLastPage(current, page, max) {
            let pagination = document.getElementById('pagination');
            let li;
            let a;
            if (page < (max - 2)) {
                if (page != max - 3) {
                    li = document.createElement('li');
                    a = document.createElement('a');
                    a.setAttribute('href', '');
                    a.setAttribute('class', 'page-link')
                    a.innerHTML = '...';
                    li.appendChild(a);
                    li.setAttribute('class', 'page-item disabled');
                    pagination.appendChild(li);
                }


                li = document.createElement('li');
                a = document.createElement('a');
                a.setAttribute('href', current.replace('page=' + page, 'page=' + max));
                a.setAttribute('class', 'page-link')
                a.innerHTML = max;
                li.appendChild(a);
                li.setAttribute('class', 'page-item');
                pagination.appendChild(li);
            }

        }

    </script>

@endsection
