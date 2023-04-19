{{-- Modal for Decline button --}}
<div id="modalDecline" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Decline Profile</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('users.user.decline', $user->id)}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="decline_user_reason" class="col-sm-4 col-form-label">REASON*</label>
                            </div>
                            <div class="col-9 mt-1">
                                <select class="form-control enable-select2" id="decline_user_reason" name="decline_reason">
                                    <option selected disabled>...</option>
                                    @foreach(\App\Utils\ReasonUtils::FULL_DECLINE[$user->type] as $key => $reason)
                                        <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    @if($user->admin_check)
                        <small>! The User and the updates will be rejected</small>
                    @else
                        <small>! The User will be rejected</small>
                    @endif
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
