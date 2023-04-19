@extends('admin.layout')


@section('content')
    @php
        $today = new DateTime();
    @endphp

    <h4 class="mt-5 mb-5">{{ucfirst($first_prefix)}}</h4>

    @include("admin.admin-pages.users_tables.$first_prefix-nav-tabs")
    <br>


    <table class="table table-striped">
        <thead>
        <form id="form">

            <tr>
                <th scope="col">
                    <select class="form-control" id="limit" name="limit"
                            style="width: 80px">

                    </select>
                </th>
                <th scope="col">
                    <input type="text" class="form-control" id="username" placeholder="Username..." name="username"
                           value="{{$data['username'] ?? ''}}"
                    >
                </th>
                <th scope="col">
                    <input type="text" class="form-control" id="email" placeholder="Email..." name="email"
                           value="{{$data['email'] ?? ''}}"
                    >
                </th>
                <th scope="col">
                    <select id="inputCreated" class="form-control selectKey" name="created">
                        <option selected disabled value="1">Key...</option>
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </th>
                <th scope="col">
                    <select id="inputHours" class="form-control selectKey" name="hours">
                        <option selected disabled value="1">Key...</option>
                        <option value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                </th>
                <th scope="col">

                </th>
                <th scope="col">
                    <input id="submitBtn" class="btn btn-info" value="Search" style="width: 100%">
                </th>
            </tr>
        </form>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Creation Date &nbsp; @if($data['key'] == 'users.created_at') @if($data['direction'] == 'ASC')<i class="fas fa-arrow-up"></i>@else<i class="fas fa-arrow-down"></i> @endif @endif</th>
            <th scope="col">Hours waiting &nbsp; @if($data['key'] != 'users.created_at') @if($data['direction'] == 'ASC')<i class="fas fa-arrow-up"></i>@else<i class="fas fa-arrow-down"></i> @endif @endif</th>
            <th scope="col">Account status</th>
            <th scope="col">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>#{{$user->id}}</td>
                <td>{{$user->username}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->created_at}}</td>
                <td>
                    {{ round($user->updated_awaiting/60) }}H
                </td>
                <td>
                    @if(isset($user->internal_status))
                        <button type="button"
                                class="{{\App\Utils\Utils::USERS_TABLES_BUTTON_STATUS_CLASS[$user->internal_status]}}">
                            {{strtoupper($user->internal_status)}}
                        </button>
                    @else
                        @switch($user->status)
                            @case('accepted')
                                @if($user->admin_check)
                                    @if($user->doc_status == 'pending')

                                        <button type="button" class="btn btn-warning"> PENDING ID <i
                                                class="fas fa-exclamation-triangle"></i>
                                        </button>
                                    @else

                                        <button type="button" class="btn btn-info">UPDATED</button>
                                    @endif
                                @else

                                    <button type="button" class="{{\App\Utils\Utils::USERS_TABLES_BUTTON_STATUS_CLASS[$user->status]}}">
                                        {{strtoupper($user->status)}}
                                    </button>
                                @endif
                            @break

                            @default
                            <button type="button" class="{{\App\Utils\Utils::USERS_TABLES_BUTTON_STATUS_CLASS[$user->status]}}">
                                {{strtoupper($user->status)}}
                            </button>
                            @break

                        @endswitch
                    @endif
                </td>
                <td><a style="color: #007bff" href="{{route('user.edit', $user->id)}}"><u>Details</u></a></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="row justify-content-end">
        <nav aria-label="Page navigation example">
            <ul class="pagination" id="pagination">

            </ul>
        </nav>
    </div>



@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            setDaysToShow();
            setPagination();

            let key = '{{$data['key']}}';
            let direction = '{{$data['direction']}}';

            if(key === 'users.created_at'){
                $('#inputCreated').val(direction).change();
            }else{
                $('#inputHours').val(direction).change();
            }

            $("#submitBtn").on("click", function(e) {
                var url = '{{  url()->current() }}?';
                var total = $(".form-control").length;
                $(".form-control").each(function (index) {
                    if($(this).val() != null) {
                        if ($(this).val().trim().length) {
                            if (index === total - 1) {
                                url += $(this).attr('name') + '=' + $(this).val();
                            } else {
                                url += $(this).attr('name') + '=' + $(this).val() + "&";
                            }
                        }
                    }
                });

                if(url.slice(-1) === "&"){
                    url = url.slice(0, -1);
                }
                window.location.href = url;


            });

        } );

        function changeKey(id){
            $("#"+id+"").val("1").change();
        }

        $('.selectKey').on('change', function() {
            if(this.id == 'inputHours'){
                $('#inputCreated').val("1").change;
            } else if(this.id == 'inputCreated'){
                $('#inputHours').val("1").change;
            }
        }).change();

        function setDaysToShow() {
            let limits = document.getElementById('limit');
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

            const max = "{{$max_pages}}";
            let url = window.location.href;
            let how_many = 2;

            url = new URL(url);
            let page = url.searchParams.get("page");


            if(!page){
                if(url.toString().includes('?')){
                    url = url + '&page=1';
                }else{
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
                a.setAttribute('href', url.replace('page='+page, 'page=' + to_show));
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
                a.setAttribute('href', current.replace('page='+page, 'page=1'));
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
                a.setAttribute('href', current.replace('page='+page, 'page=' + max));
                a.setAttribute('class', 'page-link')
                a.innerHTML = max;
                li.appendChild(a);
                li.setAttribute('class', 'page-item');
                pagination.appendChild(li);
            }

        }

    </script>

@endsection


