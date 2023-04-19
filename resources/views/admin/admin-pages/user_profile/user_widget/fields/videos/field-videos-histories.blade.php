<div class="row">
    <div class="col-2">
        Video uploaded:
    </div>
    <div class="col-10">
        <div class="row">
            @foreach($video_uploaded as $video)
                <div class="col-3 col-md-auto col-sm-auto mb-3">
                    <div class="form-group"
                         style="width: 245px; height: 155px; border-style: solid; border-color: red">
                        <video width="240" height="150" onclick="setDataModalNewVid({{$video->id}}, '{{$video->url}}')"
                               data-toggle="modal" data-target="#modalUploadedVideo">
                            <source src="{{$video->url}}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@include('admin.admin-pages.user_profile.user_widget.fields.videos.modals.modal-video-uploaded')

<script>
    function setDataModalNewVid(video_id, path) {
        $('#video_src').attr("src", path);

        $("#video_id").val(video_id);
        $('#video')[0].load();

        let urlVideoToApprove = "{{route('videos-histories.video_history.approve', [':video'])}}".replace(':video', video_id);
        $('#form_approve_video_uploaded').attr('action', urlVideoToApprove);

        let urlVideoToDecline = "{{route('videos-histories.video_history.decline', [':video'])}}".replace(':video', video_id);
        $('#form_decline_video_uploaded').attr('action', urlVideoToDecline);
    }
</script>
