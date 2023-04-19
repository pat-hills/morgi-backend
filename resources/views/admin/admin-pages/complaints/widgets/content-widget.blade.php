<div class="card mb-2">
    <div class="card-body">
        @if($by_system)
        <h5 class="card-title text-center">REPORTED CONTENT <u>BY SYSTEM</u></h5>

        @else
            <h5 class="card-title text-center">REPORTED CONTENT</h5>

        @endif
        <br>
        <div class="card-text">

            <div class="container text-center">
                @switch($reported_content['type'])

                    @case('image')
                    <img src="{{$reported_content['content']}}" style="height: auto; width: 300px;" data-toggle="modal" data-target="#modalImage" onclick="setImgToShow('{{$reported_content['content']}}')">
                    @break

                    @case('photo')
                    <img src="{{$reported_content['content']}}" style="height: auto; width: 300px;" data-toggle="modal" data-target="#modalImage" onclick="setImgToShow('{{$reported_content['content']}}')">
                    @break

                    @case('video')
                        <video width="340" height="250" onclick="setVidToShow('{{$reported_content['content']}}')" data-toggle="modal" data-target="#modalVideo" controls>
                            <source src="{{$reported_content['content']}}" type="video/mp4">
                        </video>
                    @break

                    @case('message')
                        <span>{{$reported_content['content']}}</span>
                    @break

                    @case('error')
                    <span style="color: red">{{$reported_content['content']}}</span>
                    @break

                @endswitch
                    @if(!$by_system)
                        @if(!empty($note))
                            <br>
                            <br>
                            <hr>
                            <br>
                            <h5>User note</h5>
                            <br>

                            <span>{{$note}}</span>
                        @endif
                    @endif
            </div>

        </div>

        <br>
        <br>
            @if($status == 'open')
                {{--<div class="container">
                    <form action="" method="POST" id="chatForm">
                        @csrf
                        <div class="row">
                            <div class="col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3">
                                <div class="form-group text-center">
                                    <label for="exampleFormControlSelect1">Select user</label>
                                    <select class="form-control" id="exampleFormControlSelect1" name="user_id">
                                        <option selected disabled>...</option>
                                        @if(!$by_system)
                                        <option value="{{$reported_by->id}}">{{$reported_by->full_name}} - reporter
                                        </option>
                                        @endif
                                        <option value="{{$user_reported->id}}">{{$user_reported->full_name}} - user
                                            reported
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control" id="answer_to_user" name="message">
                                        <option selected disabled>Select answer to user...</option>
                                        <option value="Advise the user to delete the content">Advise the user to delete the content
                                        </option>
                                        <option value="Advise the user to block the user">Block the user
                                        </option>
                                        <option value="free">Free text...</option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-12">
                                <button type="submit" class="btn btn-info mb-2">OPEN CHAT</button>
                            </div>
                        </div>
                    </form>

                </div>--}}
            @endif

        {{--                    <a href="#" class="btn btn-primary">Go to profile</a>--}}
    </div>
</div>


{{--<script>--}}
{{--    $("#chatForm").submit(function(e) {--}}


{{--        var form = $(this);--}}
{{--        e.preventDefault();--}}

{{--        $.ajax({--}}
{{--            type: "POST",--}}
{{--            url: '{{route('sendMessage.post')}}',--}}
{{--            data: form.serialize(), // serializes the form's elements.--}}
{{--            success: function(data)--}}
{{--            {--}}
{{--                if (data.includes('Error')){--}}
{{--                    alert(data);--}}
{{--                } else {--}}
{{--                    window.open(data, '_blank');--}}
{{--                }--}}
{{--                // alert(data); // show response from the php script.--}}
{{--            }--}}
{{--        });--}}


{{--    });--}}
{{--</script>--}}


@include('admin.admin-pages.complaints.widgets.modal-show-img')

@include('admin.admin-pages.complaints.widgets.modal-show-video')

<script>
    function setImgToShow(url) {
        $("#valueToShow").attr("src", url);
    }

    function setVidToShow(url) {
        $("#videoToShow").attr("src", url);
        $('#vid_to_show')[0].load();

    }
</script>
