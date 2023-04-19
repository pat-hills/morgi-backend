@if(!is_null($verified_data))
{{-- Modal for Show ID --}}
<div id="modalShowID" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-title">
                    <h5>Verified ID</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row justify-content-center text-center">
                    @if($verified_data->front_photo)
                        <div class="col-4">
                            <a href="#" class="pop">
                                <img src="{{$verified_data->front_photo->url}}" alt="..."
                                     class="img-thumbnail">
                            </a>
                        </div>
                    @endif
                    @if($verified_data->back_photo)
                        <div class="col-4">
                            <a href="#" class="pop">
                                <img src="{{$verified_data->back_photo->url}}" alt="..."
                                     class="img-thumbnail">
                            </a>

                        </div>
                    @endif
                    @if($verified_data->front_photo)
                        <div class="col-4">
                            <a href="#" class="pop">
                                <img src="{{$verified_data->selfie_photo->url}}" alt="..."
                                     class="img-thumbnail">
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END Modal for Show ID --}}

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <img src="" class="imagepreview" style="width: 100%;">
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('.pop').on('click', function () {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');
            $('#modalShowID').modal('hide');
        });
        $('#imagemodal').on('hidden.bs.modal', function () {
            $('#modalShowID').modal('show');
        })
    });

</script>
@endif
