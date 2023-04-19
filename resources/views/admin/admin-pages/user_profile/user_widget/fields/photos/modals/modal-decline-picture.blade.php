{{-- Modal for Decline Pictures  --}}
<div id="modalPicture" class="modal fade" tabindex="-1" role="dialog">
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
                <div class="text-center">
                    <img src="" alt="..."
                         class="img-thumbnail" id="imgToDecline">

                </div>
                <br>
                <div class="form-group">
                    <div class="row">
                        <div class="col-3">
                            <label for="declineReason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="declineReason" name="decline_reason" form="form_decline_photo_history">
                                <option selected disabled>...</option>
                                @foreach(\App\Utils\ReasonUtils::PHOTO_DECLINE[$user->type] as $key => $reason)
                                    <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                {{--    setDataModalNewPic() will set the action url    --}}
                <form action="" method="POST" id="form_approve_photo_history">
                    @csrf
                    <input type="submit" class="btn btn-success" value="Approve">
                </form>

                {{--    setDataModalNewPic() will set the action url    --}}
                <form action="" method="POST" id="form_decline_photo_history">
                    @csrf
                    <input type="submit" class="btn btn-danger" value="Decline">
                </form>

            </div>
        </div>
    </div>
</div>
{{-- END Modal for Decline Pictures--}}
