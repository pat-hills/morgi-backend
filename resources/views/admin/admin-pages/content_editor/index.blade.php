@extends('admin.layout')

@section('css_before')

    <style>
        .funkyradio div {
            clear: both;
            overflow: hidden;
        }

        .funkyradio label {
            width: 100%;
            border-radius: 3px;
            border: 1px solid #D1D3D4;
            font-weight: normal;
        }

        .funkyradio input[type="radio"]:empty,
        .funkyradio input[type="checkbox"]:empty {
            display: none;
        }

        .funkyradio input[type="radio"]:empty ~ label,
        .funkyradio input[type="checkbox"]:empty ~ label {
            position: relative;
            line-height: 2.5em;
            text-indent: 3.25em;
            margin-top: 2em;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .funkyradio input[type="radio"]:empty ~ label:before,
        .funkyradio input[type="checkbox"]:empty ~ label:before {
            position: absolute;
            display: block;
            top: 0;
            bottom: 0;
            left: 0;
            content: '';
            width: 2.5em;
            background: #D1D3D4;
            border-radius: 3px 0 0 3px;
        }

        .funkyradio input[type="radio"]:hover:not(:checked) ~ label,
        .funkyradio input[type="checkbox"]:hover:not(:checked) ~ label {
            color: #888;
        }

        .funkyradio input[type="radio"]:hover:not(:checked) ~ label:before,
        .funkyradio input[type="checkbox"]:hover:not(:checked) ~ label:before {
            content: '\2714';
            text-indent: .9em;
            color: #C2C2C2;
        }

        .funkyradio input[type="radio"]:checked ~ label,
        .funkyradio input[type="checkbox"]:checked ~ label {
            color: #777;
        }

        .funkyradio input[type="radio"]:checked ~ label:before,
        .funkyradio input[type="checkbox"]:checked ~ label:before {
            content: '\2714';
            text-indent: .9em;
            color: #333;
            background-color: #ccc;
        }

        .funkyradio input[type="radio"]:focus ~ label:before,
        .funkyradio input[type="checkbox"]:focus ~ label:before {
            box-shadow: 0 0 0 3px #999;
        }

        .funkyradio-default input[type="radio"]:checked ~ label:before,
        .funkyradio-default input[type="checkbox"]:checked ~ label:before {
            color: #333;
            background-color: #ccc;
        }

        .funkyradio-primary input[type="radio"]:checked ~ label:before,
        .funkyradio-primary input[type="checkbox"]:checked ~ label:before {
            color: #fff;
            background-color: #337ab7;
        }

        .funkyradio-success input[type="radio"]:checked ~ label:before,
        .funkyradio-success input[type="checkbox"]:checked ~ label:before {
            color: #fff;
            background-color: #5cb85c;
        }

        .funkyradio-danger input[type="radio"]:checked ~ label:before,
        .funkyradio-danger input[type="checkbox"]:checked ~ label:before {
            color: #fff;
            background-color: #d9534f;
        }

        .funkyradio-warning input[type="radio"]:checked ~ label:before,
        .funkyradio-warning input[type="checkbox"]:checked ~ label:before {
            color: #fff;
            background-color: #f0ad4e;
        }

        .funkyradio-info input[type="radio"]:checked ~ label:before,
        .funkyradio-info input[type="checkbox"]:checked ~ label:before {
            color: #fff;
            background-color: #5bc0de;
        }


        .blockquote-custom {
            position: relative;
            font-size: 1.1rem;
        }

        .blockquote-custom-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -25px;
            left: 50px;
        }

        .blockquote-custom-icon-edit {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -25px;
            left: 80%;
            border-color: transparent;

        }

        .blockquote-custom-icon-delete {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: -25px;
            left: 90%;
            border-color: transparent;
        }
    </style>

@endsection

@section('content')

    <h3>Rookie Panel / Content editor</h3>
    <br>
    <br>

    <div class="container">
        <form action="{{route('content_editor.create')}}" method="POST">
            @csrf
            <div class="form-row justify-content-center">
                <div class="col-5">
                    <div class="funkyradio">
                        @foreach(\App\Enums\ContentEditorEnum::TYPES as $key => $type)
                            <div class="funkyradio-primary">
                                <input type="radio" name="type" id="{{$key}}" value="{{$key}}"/>
                                <label for="{{$key}}">{{$type}}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <br>
            <div class="form-row justify-content-center">
                <div class="col-6">
                    <label for="contentTitle">Title</label>
                    <input type="text" class="form-control" id="contentTitle" name="title" placeholder="Title...">
                </div>
            </div>
            <br>
            <div class="form-row justify-content-center">
                <div class="col-8">
                    <label for="contentArea">Content</label>
                    <textarea class="form-control" id="contentArea" rows="3" name="content_text"
                              placeholder="Insert here the content..."></textarea>
                </div>

            </div>
            <br>
            <div class="row justify-content-end text-right">
                <div class="col-4">
                    <button type="reset" value="reset" class="btn btn-light" title="reset">&nbsp;<i
                            class="fas fa-redo-alt"></i>&nbsp;
                    </button>

                    <button type="submit" class="btn btn-success">Submit</button>

                </div>
            </div>
        </form>

    </div>

    <br>
    <br>
    <br>
    <div class="container">
        <hr>
        <form action="{{route('content_editor.post')}}" method="POST">
            @csrf
            <div class="row justify-content-md-center">
                <div class="col-md-auto">
                    <select class="form-control" id="search_type" name="search_type">
                        @if(!empty($old_input['search_type']))
                            <option selected
                                    value="{{$old_input['search_type']}}">{{ucfirst(\App\Enums\ContentEditorEnum::TYPES[$old_input['search_type']])}}</option>
                            <option value="">All</option>
                        @else
                            <option selected value="">All</option>
                        @endif

                        @foreach(\App\Enums\ContentEditorEnum::TYPES as $key => $value)
                            @if(!empty($old_input['search_type']))
                                @if($key != $old_input['search_type'])
                                    <option value="{{$key}}">{{ucfirst($value)}}</option>
                                @endif
                            @else
                                <option value="{{$key}}">{{ucfirst($value)}}</option>
                            @endif
                        @endforeach

                    </select>
                </div>
                <div class="col-md-auto">
                    <input class="form-control" name="search_text" id="search_text"
                           @if(!empty($old_input['search_text'])) value="{{$old_input['search_text']}}"
                           @endif placeholder="Text...">
                </div>
                <div class="col-md-auto">
                    <button type="reset" value="reset" class="btn btn-light" title="reset" onclick="resetSearch()">
                        &nbsp;<i class="fas fa-redo-alt"></i>&nbsp;
                    </button>
                    <button class="btn btn-info" name="submit">Search</button>
                </div>
            </div>
        </form>
        <br>
        <br>
        @foreach($contents as $content)
            <div class="row">
                <blockquote class="blockquote blockquote-custom bg-white p-5 shadow rounded" style="width: 100%;">
                    <div class="blockquote-custom-icon bg-info shadow-sm"><i class="fa fa-quote-left text-white"></i>
                    </div>
                    <small>{{ucfirst(\App\Enums\ContentEditorEnum::TYPES[$content->type])}}</small>
                    <button class="blockquote-custom-icon-edit bg-warning shadow-sm" data-toggle="modal"
                            data-target="#editContentEditor" onclick="setEditModal({{$content}})"><i
                            class="fas fa-pencil-alt text-white"></i></button>
                    <button class="blockquote-custom-icon-delete bg-danger shadow-sm" data-toggle="modal"
                            data-target="#editDelete" onclick="setDeleteModal({{$content->id}})"><i
                            class="fas fa-trash-alt text-white"></i></button>
                    <br>
                    <h5 class="font-italic mb-3">{{ucfirst($content->title)}}</h5>

                    <p class="mb-0 mt-2 font-italic">@php echo nl2br($content->content) @endphp</p>

                    <footer class="blockquote-footer pt-4 mt-4 border-top">Posted by
                        <cite title="Source Title">{{$content->admin_username ?? $content->admin_email}}</cite>
                        <div class="row">
                            <div class="col text-right">
                                updated {{date("d M, Y H:i", strtotime($content->updated_at))}}
                            </div>
                        </div>
                    </footer>
                </blockquote>
            </div>
            <br>
        @endforeach

        <div class="modal fade" id="editContentEditor" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Content</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('content_editor.update')}}" method="POST" id="editModal">
                        @csrf
                        <input type="hidden" name="contentID" id="contentID_update">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="editType">Type</label>
                                <select class="form-control" id="editType" name="editType">
                                    @foreach(\App\Enums\ContentEditorEnum::TYPES as $key => $value)
                                        <option value="{{$key}}">{{ucfirst($value)}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editTitle">Title</label>
                                <input type="text" class="form-control" id="editTitle" name="editTitle"
                                       placeholder="Title...">
                            </div>
                            <div class="form-group">
                                <label for="editContent">Content</label>
                                <textarea class="form-control" id="editContent" name="editContent" placeholder="Text..."
                                          rows="3"></textarea>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"
                                    onclick="this.form.reset();">Close
                            </button>
                            <button type="submit" class="btn btn-success">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Content</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('content_editor.delete')}}" method="POST">
                        @csrf
                        <input type="hidden" name="contentID" id="contentID_delete">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Are you sure?</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection



@section('js_after')

    <script>
        function setEditModal(content) {
            // document.getElementById("editModal").reset();
            $('#editModal')[0].reset()
            $('#contentID_update').val(content.id);
            $("#editType option").each(function () {
                if ($(this).val() == content.type) {
                    $(this).attr('selected', true);
                }
            });
            $('#editTitle').val(content.title);
            $("#editContent").text("" + content.content + "");
        }

        function setDeleteModal(id) {
            $('#contentID_delete').val(id);
        }

        function resetSearch() {
            $('#search_text').removeAttr('value');
            $("#search_type option").each(function () {
                if ($(this).val() == '') {
                    $(this).attr('selected', true);
                }
            });

        }

    </script>
@endsection

