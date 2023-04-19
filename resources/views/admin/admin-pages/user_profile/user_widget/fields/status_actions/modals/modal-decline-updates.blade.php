{{-- Modal for Decline button --}}
<div id="modalDeclineUpdates" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Decline user's updates</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('users.user.updates.decline', $user->id)}}" method="POST">
                <div class="modal-body">
                    <span><strong>Are you sure?</strong></span>
                    <br>
                    <br>
                    @csrf
                    <div class="form-inline">
                        <label for="decline_all_updates">Decline all updates</label> &nbsp;
                        <input type="checkbox" class="form-check" id="decline_all_updates" name="all_updates">
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="select_decline_reason" class="col-sm-4 col-form-label">REASON*</label>
                            </div>
                            <div class="col-9 mt-1">
                                <select class="form-control enable-select2" id="select_decline_updates_reason" name="decline_reason">
                                    <option selected disabled>...</option>
                                    @foreach(\App\Utils\ReasonUtils::FULL_DECLINE[$user->type] as $key => $reason)
                                        <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <small>Please, check the checkbox and choose a reason before proceed</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for Decline button --}}
