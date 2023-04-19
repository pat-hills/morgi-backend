{{-- Modal for Give Micro Morgi --}}
<div id="modalGiveMicroMorgi" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Give Micro Morgi bonus to Leader</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.micromorgi.add_bonus', $user->id)}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group row">
                        <label for="amount" class="col-sm-4 col-form-label">AMOUNT*</label>
                        <div class="col-sm-8">
                            <input type="text"  id="amount" name="amount"><br>
                            <small style="color: red">* Please use int numbers only</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="bonusReason" class="col-sm-4 col-form-label">BONUS REASON*</label>
                        <div class="col-sm-8">
                            <select class="form-control enable-select2" id="bonusReason" name="bonus_reason">
                                <option selected disabled>
                                    FREE TEXT
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="comments" class="col-sm-4 col-form-label">COMMENTS</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Give Bonus</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- END Modal for Give Micro Morgi --}}
