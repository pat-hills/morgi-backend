<style>
    .show-img {
        width: auto !important;
        height: auto !important;
        /* Magic! */
        max-width: 5vw !important;
        max-height: 7vw !important;
    }
</style>

<div class="row">
    <div class="col-2">
        Pictures uploaded:
    </div>
    <div class="col-10">
        <div class="row">
            @foreach($photo_uploaded as $photo)
                <div class="col-2 col-md-auto col-sm-auto my-3">
                    <img src="{{$photo->url}}" class="show-img"
                         data-toggle="modal"
                         style="height: 100px; width: 100px; border-color: red; border-style: solid"
                         data-target="#modalPicture"
                         onclick="setDataModalNewPic({{$photo}},'{{$photo->url}}')"
                    >
                </div>
            @endforeach
        </div>
    </div>

</div>

<script>
    function setDataModalNewPic(photo, url){
        let approve_url = "{{route('photos-histories.photo_history.approve', [':photo_history'])}}".replace(':photo_history', photo.id)
        $('#form_approve_photo_history').attr('action', approve_url);

        let decline_url = "{{route('photos-histories.photo_history.decline', [':photo_history'])}}".replace(':photo_history', photo.id)
        $('#form_decline_photo_history').attr('action', decline_url);

        $("#imgToDecline").attr("src", url);
    }
</script>


@include('admin.admin-pages.user_profile.user_widget.fields.photos.modals.modal-decline-picture')
