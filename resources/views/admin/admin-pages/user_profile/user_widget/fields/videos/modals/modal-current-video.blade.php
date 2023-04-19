{{-- Modal for Video --}}
<div id="modalVideo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form action="" method="POST" id="form_decline_video">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <video width="450" height="300" controls id="vid_to_show">
                                <source src="" type="video/mp4" id="videoToShow">
                            </video>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="reasonVideoDecline" class="col-sm-4 col-form-label">REASON*</label>
                            </div>
                            <div class="col-9 mt-1">
                                <select class="form-control enable-select2" id="reasonVideoDecline"
                                        name="decline_reason">
                                    <option selected disabled>...</option>
                                    @foreach(\App\Utils\ReasonUtils::VIDEO_DECLINE[$user->type] as $key => $reason)
                                        <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for Video --}}
