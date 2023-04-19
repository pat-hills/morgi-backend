<div class="modal fade" id="modalChangeStatus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.update-status', $user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-10 mt-1">
                            <div class="form-group row">
                                <label for="comments" class="col-sm-6 col-form-label">CURRENT STATUS</label>
                                <div class="col-6">
                                    <label class="col-form-label">{{str_replace('_', ' ', strtoupper($user->internal_status ?? $user->status))}}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-10 mt-1">
                            <div class="form-group row">
                                <label for="comments" class="col-sm-6 col-form-label">NEW STATUS*</label>
                                <div class="col-sm-6">
                                    <select class="form-control" name="status" id="foreach_status">
                                        <option disabled value="default" selected>Choose...</option>
                                        @foreach(\App\Enums\LeaderEnum::FORCE_STATUS_BY_ADMIN as $key => $status)
                                            @if($key == $user->internal_status || $key == $user->status)
                                                @continue
                                            @endif
                                            <option value="{{$key}}">
                                                {{strtoupper($status)}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-10 mt-1">
                            <div class="form-group row">
                                <label for="status_reason" class="col-sm-6 col-form-label">REASON*</label>
                                <div class="col-sm-6">
                                    <textarea class="form-control" id="status_reason" name="status_reason"
                                              rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-success" value="UPDATE">
                </div>
            </form>
        </div>
    </div>
</div>
