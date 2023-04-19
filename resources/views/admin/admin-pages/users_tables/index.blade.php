@extends('admin.layout')

@section('content')

    @include('admin.admin-pages.user_profile.search-user')

    @if(!empty($users))
        <h4 class="mt-5 mb-5">Users table</h4>

        <table class="table table-striped" id="table">
            <thead>
            <form id="formFilters">
                <tr>
                    <th scope="col">
                        <select class="form-control" id="limit" name="limit"
                                style="width: 80px">

                        </select>
                    </th>
                    <th scope="col">
                    </th>
                    <th scope="col">
                        <input type="text" class="form-control" id="username" placeholder="Username..." name="username"
                               value="{{$filters['username'] ?? ''}}"
                        >
                    </th>
                    <th scope="col">
                        <input type="text" class="form-control" id="email" placeholder="Email..." name="email"
                               value="{{$filters['email'] ?? ''}}"
                        >
                    </th>
                    <th scope="col">
                        <select id="inputType" class="form-control" name="user_type">
                            <option value="">All</option>
                            <option value="rookie">Rookie</option>
                            <option value="leader">Leader</option>
                        </select>
                    </th>
                    <th scope="col" style="width: 10%">
                        <button type="button" id="submitBtn" class="btn btn-info" style="width: 100%">Search</button>
                    </th>
                </tr>
            </form>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Full Name</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Type</th>
                <th scope="col">Details</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->id}}</td>
                    <td>
                        @if($user->type === 'rookie')
                            {{$user->full_name}}
                        @endif
                    </td>
                    <td>{{$user->username}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->type}}</td>
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
    @endif

@endsection

@section('js_after')

    <script>
        $(document).ready( function () {
            setDaysToShow();
            setPagination();

            let type = '{{$filters['type'] ?? ''}}';

            $("#submitBtn").on("click", function(e) {
                var url = '{{  url()->current() }}?';
                var total = $(".form-control").length;
                let value;
                $(".form-control").each(function (index) {
                    if($(this).val() != null) {
                        if ($(this).val().trim().length) {
                            if (index === total - 1) {
                                value = $(this).attr('name') + '=' + encodeURIComponent( $(this).val());
                            } else {
                                value = $(this).attr('name') + '=' + encodeURIComponent($(this).val()) + "&";
                            }
                            url += value;
                        }
                    }
                });

                if(url.slice(-1) === "&"){
                    url = url.slice(0, -1);
                }
                window.location.href = url;
            });

            $("#inputType option").each(function()
            {
                if( $(this).val() === type){
                    $(this).attr("selected","selected");
                }
            });


        } );

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
