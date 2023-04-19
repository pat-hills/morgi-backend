{{-- Modal for Give Micro Morgi --}}
<div id="modalFine" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Fine {{ucfirst($user->type)}}</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.fine-user', $user->id)}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group row">
                        <label for="fineReason" class="col-sm-4 col-form-label">REASON*</label>
                        <div class="col-sm-8 mt-11">
                            <select class="form-control enable-select2" id="fineReason" name="reason">
                                <option selected disabled>
                                    FREE TEXT
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="bonusReason" class="col-sm-4 col-form-label">M/MM*</label>
                        <div class="col-sm-8 mt-1">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="morgi_radio" name="type" value="morgi">
                                <label class="form-check-label" for="morgi_radio">Morgi</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="type" id="micromorgi_radio" value="micromorgi">
                                <label class="form-check-label" for="micromorgi_radio">Micro Morgi</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="amountFine" class="col-sm-4 col-form-label">AMOUNT*</label>
                        <div class="col-sm-8">
                            <input type="text"  id="amountFine" name="amount"><br>
                            <small style="color: red">* Please use int numbers only</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Fine</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- END Modal for Give Micro Morgi --}}
