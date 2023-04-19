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
                <div class="text-center">
                    <img src="" alt="..."
                         class="img-thumbnail" id="valueToShow">
                </div>
                <br>
                <div class="form-group">
                    <div class="row">
                        <div class="col-3">
                            <label for="decline_reason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="decline_reason" name="decline_reason" form="form_current_photo">
                                <option selected disabled>...</option>
                                @foreach(\App\Utils\ReasonUtils::PHOTO_DECLINE[$user->type] as $key => $reason)
                                    <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <form action="" method="POST" id="form_current_photo">
                        @csrf
                        <input type="submit" class="btn btn-danger" value="Decline">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END Modal for Image --}}
