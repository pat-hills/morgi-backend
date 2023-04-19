{{-- Modal for Block User --}}
<div id="modalBlockUser" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Block Profile</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.block', $user->id)}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="reasonBlockUser" class="col-sm-4 col-form-label">REASON*</label>
                            </div>
                            <div class="col-9 mt-1">
                                <select class="form-control enable-select2" id="reasonBlockUser" name="reasonBlockUser">
                                    <option selected disabled>FREE TEXT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- END Modal for Block User --}}
