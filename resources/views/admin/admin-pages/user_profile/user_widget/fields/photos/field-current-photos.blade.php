<div class="row">
    <div class="col-2">
        Pictures:
    </div>
    <div class="col-10">
        <div class="row">
            @foreach($photos as $photo)
                <div class="col-2 col-md-auto col-sm-auto my-3">
                    <img src="{{$photo->url}}" class="show-img"
                         data-toggle="modal"
                         style="height: 100px; width: 100px;"
                         data-target="#modalImage"
                         onclick="setImgToShow('{{$photo->url}}', '{{$photo->id}}')"
                    >
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
    function setImgToShow(url, id) {
        $("#valueToShow").attr("src", url);

        let url_photo_to_decline = "{{route('photos.photo.decline', [':photo'])}}".replace(':photo', id);
        $('#form_current_photo').attr('action', url_photo_to_decline);
    }
</script>

@include('admin.admin-pages.user_profile.user_widget.fields.photos.modals.modal-image')
