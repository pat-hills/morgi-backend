<div class="row">
    <div class="col-2">
        Video:
    </div>
    <div class="col-10">
        <div class="row">
            @foreach($videos as $video)
                <div class="col-2 col-md-auto col-sm-auto my-3">
                    <video width="240" height="150" onclick="setVidToShow('{{$video->url}}', '{{$video->id}}')" data-toggle="modal" data-target="#modalVideo">
                        <source src="{{$video->url}}"
                                type="video/mp4">
                    </video>
                </div>
            @endforeach
        </div>
    </div>

</div>

@include('admin.admin-pages.user_profile.user_widget.fields.videos.modals.modal-current-video')

<script>
    function setVidToShow(path, id) {
        $("#videoToShow").attr("src", path);
        $('#vid_to_show')[0].load();

        let urlVideoToDecline = "{{route('videos.video.decline', [':video'])}}".replace(':video', id);
        $('#form_decline_video').attr('action', urlVideoToDecline);
    }
</script>
