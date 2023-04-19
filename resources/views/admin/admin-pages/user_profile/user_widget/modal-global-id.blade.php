<style>
    .modal-full-screen {
        max-width: 100%;
        margin: 0;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh;
        display: flex;
    }
</style>

<div id="modalGlobalId" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-full-screen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="card-title">Global ID # {{$global_id_data['id']}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="overflow: scroll">
                <table class="table">
                    <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">FIRST NAME</th>
                        <th scope="col">LAST NAME</th>
                        <th scope="col">UCID</th>
                        <th scope="col">LOCATION</th>
                        <th scope="col">BILLING EMAIL</th>
                        <th scope="col"></th>

                    </tr>
                    </thead>
                    <tbody>

                    @foreach($global_id_data['users'] as $user)

                        <tr>
                            <th>{{$user->id}}</th>
                            <td>{{$user->first_name}}</td>
                            <td>{{$user->last_name}}</td>
                            <td>{{$user->ucid}}</td>
                            <td>{{$user->location}}</td>
                            <td>{{$user->billing_email}}</td>
                            <td><a href="{{route('user.edit', $user->id)}}" target="_blank" style="color: dodgerblue"><u>Profile</u></a></td>

                        </tr>

                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
