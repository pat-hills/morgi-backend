{{-- Modal for Decline button --}}
<div id="modalDeclineDescription" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Decline User's description</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('users.user.updates.description.decline-current', $user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="select_decline_reason" class="col-sm-4 col-form-label">REASON*</label>
                            </div>
                            <div class="col-9 mt-1">
                                <select class="form-control enable-select2" id="select_decline_new_description_reason" name="decline_reason">
                                    <option selected disabled>...</option>
                                    @foreach(\App\Utils\ReasonUtils::DESCRIPTION_DECLINE[$user->type] as $key => $reason)
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
{{-- END Modal for Decline button --}}
