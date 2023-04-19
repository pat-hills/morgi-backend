@extends('admin.layout')

@section('content')

    @include('admin.admin-pages.user_profile.search-user')


    @if(!empty($users))
        <h4 class="mt-5 mb-5">Users table</h4>

        <table class="table table-striped" id="table">
            <thead>
            <tr>
                <th scope="col">ID</th>
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
                    <td>{{$user->username}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->type}}</td>
                    <td><a style="color: #007bff" href="{{route('user.edit', $user->id)}}"><u>Details</u></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

@endsection


@section('js_after')

<script>
    $(document).ready(function () {
        $('#table').dataTable({
            "bSort": false
        });
    });
</script>
@endsection
