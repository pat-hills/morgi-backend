{{-- Modal for New Video  --}}
<div id="modalUploadedVideo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-title">
                    <h5>Pending approval</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12 text-center">
                        <video width="450" height="300" controls id="video">
                            <source src="" type="video/mp4" id="video_src">
                            Not supported
                        </video>
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <div class="row">
                        <div class="col-3">
                            <label for="select_decline_reason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="select_decline_reason" name="decline_reason"
                                    form="form_decline_video_uploaded">
                                <option selected disabled>...</option>
                                @foreach(\App\Utils\ReasonUtils::VIDEO_DECLINE[$user->type] as $key => $reason)
                                    <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-right">
                    <form action="" method="POST" id="form_approve_video_uploaded">
                        @csrf
                        <input type="submit" class="btn btn-success" value="Approve">
                    </form>
                    &nbsp;
                    <form action="" method="POST" id="form_decline_video_uploaded">
                        @csrf
                        <input type="submit" class="btn btn-danger" value="Decline">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END Modal for New Videos--}}
