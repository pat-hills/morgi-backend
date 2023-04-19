<div class="table-responsive-xl table-striped">
    <table class="table">
        <thead class="thead-light">
        <tr>
            <th>ID</th>
            <th>TYPE</th>
            <th>USERNAME</th>
            <th>EMAIL</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>{{$user->id}}</td>
            <td>{{$user->type}}</td>
            <td>{{$user->username}}</td>
            <td>{{$user->email}}</td>
        </tr>
        </tbody>

    </table>
</div>
