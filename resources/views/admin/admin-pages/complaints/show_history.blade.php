@extends('admin.layout')



@section('content')

    <br>
    <h4>COMPLAINT DETAILS</h4>
    <span>Status: </span>
    @if($complaint->status == 'open')
        <span style="color: green">OPEN</span>
    @elseif($complaint->status == 'closed')
        <span style="color: red">CLOSED</span>
    @else
        <span>{{$complaint->status}}</span>
    @endif
    <br>
    @if(!is_null($complaint->follow_up))
        <button class="btn btn-danger btn-sm">48H FOLLOW UP</button>
    @endif
    <br>
    <br>
    <br>
    <div class="row">
        <div class="col-8">
            <div class="row justify-content-start">
                <div class="col-sm-12 col-md-6">
                    <h5>COMPLAINT OF {{strtoupper($type_name)}}</h5>
                </div>
                <div class="col-sm-12 col-md-6 text-right">
                    <div class="row">

                        <div class="col-6">
                            <button type="button" class="btn btn-light"><a href="#" data-toggle="modal" data-target="#modalNotes">NOTES({{$notes->count()}})
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                         class="bi bi-newspaper" viewBox="0 0 16 16">
                                        <path
                                            d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5v-11zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5H12z"/>
                                        <path
                                            d="M2 3h10v2H2V3zm0 3h4v3H2V6zm0 4h4v1H2v-1zm0 2h4v1H2v-1zm5-6h2v1H7V6zm3 0h2v1h-2V6zM7 8h2v1H7V8zm3 0h2v1h-2V8zm-3 2h2v1H7v-1zm3 0h2v1h-2v-1zm-3 2h2v1H7v-1zm3 0h2v1h-2v-1z"/>
                                    </svg>
                                </a></button>
                        </div>
                        <div class="col-6">

                            <button type="submit" class="btn btn-primary" onclick="openChat()">CHAT BETWEEN USERS</button>
                            <script>

                                function openChat(){
                                    win =top.consoleRef=window.open('{{route('chat', ['reported' => $complaint->user_reported, 'reported_by' => $complaint->reported_by])}}','myconsole',
                                        'width=700,height=900'
                                        +',menubar=0'
                                        +',toolbar=1'
                                        +',status=0'
                                        +',scrollbars=1'
                                        +',resizable=1')
                                }
                            </script>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-7">
            @include('admin.admin-pages.complaints.edit-nav')
        </div>

    </div>
    <br>

    <div class="row">
        <div class="col-4">

        </div>
        <div class="col-4 text-center">
            <h4 class="mt-5 mb-5">History of complaint</h4>
        </div>
        <div class="col-4">

        </div>
    </div>


    <table class="table table-striped" id="complaintHistory">
        <thead>
        <tr>
            <th scope="col">Admin</th>
            <th scope="col">Action</th>
            <th scope="col">date at</th>
            <th scope="col">note </th>
        </tr>
        </thead>
        <tbody>
        @foreach($history as $action)
            <tr>
                <td>{{\App\Models\User::find($action->admin_id)->email}}</td>
                <td>{{$action->action}}</td>
                <td>{{$action->created_at}}</td>
                <td>{{$action->note}}</td>

            </tr>
        @endforeach
        </tbody>
    </table>


    @include('admin.admin-pages.complaints.widgets.modal-add-note')

@endsection


@section('js_after')

    <script>
        $(document).ready(function () {
            $('#complaintHistory').DataTable({
                "order": [[2, "desc"]]
            });
        });
    </script>


@endsection
