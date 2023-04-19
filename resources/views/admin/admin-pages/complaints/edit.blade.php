@extends('admin.layout')

@section('css_before')

    <style>
        .image--cover {
            width: 150px;
            height: 150px;
            border-radius: 50%;

            object-fit: cover;
            object-position: center right;
        }
    </style>

    @endsection

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

    <div class="row justify-content-end text-right">
        <div class="col-auto">
            @if($complaint->status == 'open')

                <form method="POST" action="#" class="form-inline">
                    @csrf
                    <div class="form-group mb-2">
                        <select class="form-control" id="complaint_status" name="new_status">
                            <option selected disabled>Select complaint status...</option>
                            <option value="closed">CLOSED</option>
                            @if(is_null($complaint->follow_up))
                                <option value="48h">48H FOLLOW UP</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group mx-sm-3 mb-2">
                    </div>
                    <button type="submit" class="btn btn-success mb-2">SAVE</button>
                </form>
            @endif
        </div>
    </div>

    <br>

    @if(!$complaint->by_system)
        <div class="row text-center">
            <div class="container">
                @include('admin.admin-pages.complaints.widgets.content-widget', ['reported_content' => $complaint->reported_content, 'note' => $complaint->notes, 'by_system' => 0, 'status' => $complaint->status, 'reported_by' => $reported_by, 'user_reported' => $user_reported])
            </div>
        </div>
        <br>
    @endif


    <div class="row">
{{--        <div class="col-lg-8 offset-lg-2 col-md-12 col-sm-12">--}}
        <div class="col-lg-5 col-md-12 col-sm-12">

            @if($complaint->by_system)
                @include('admin.admin-pages.complaints.widgets.content-widget', ['reported_content' => $complaint->reported_content, 'note' => null, 'by_system' => 1, 'status' => $complaint->status])
            @elseif($complaint->reported_by_type == 'leader')
                @include('admin.admin-pages.complaints.widgets.leader-widget', ['action' => 'user reporting', 'user' => $reported_by])
            @elseif($complaint->reported_by_type == 'rookie')
                @include('admin.admin-pages.complaints.widgets.rookie-widget', ['action' => 'user reporting', 'user' => $reported_by])
            @endif

        </div>
        <div class="col-lg-2">

        </div>
        <div class="col-lg-5 col-md-12 col-sm-12">
            @if($complaint->user_reported_type == 'leader')
                @include('admin.admin-pages.complaints.widgets.leader-widget', ['action' => 'user being reported on', 'user' => $user_reported])
            @elseif($complaint->user_reported_type == 'rookie')
                @include('admin.admin-pages.complaints.widgets.rookie-widget', ['action' => 'user being reported on', 'user' => $user_reported])
            @endif
        </div>
    </div>

    @include('admin.admin-pages.complaints.widgets.modal-add-note')

    {{-- Modal for Image --}}
    <div id="modalImage" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <img src="" alt="..."
                         class="img-thumbnail" id="valueToShow">
                </div>
            </div>
        </div>
    </div>
    {{-- END Modal for Image --}}


@endsection


@section('js_after')

    <script>
        function setImgToShow(url) {
            $("#valueToShow").attr("src", url);
        }
    </script>

    <script>
        function goTo(user_id){

            $.ajax({
                type: "POST",
                url: '{{route('getCustomerlyId.post')}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'user_id' : user_id,
                },
                success: function(data)
                {
                    if (data.includes('Error')){
                        alert(data);
                    } else {
                        window.open(data, '_blank');
                    }
                    // alert(data); // show response from the php script.
                },
                error: function (data)
                {
                    alert(data['responseJSON'].message);
                }
            });
        }
    </script>
@endsection


