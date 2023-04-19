{{-- Modal for Reasons Blocked User --}}
<div id="modalRefund" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Refund reason</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{route('refund-transaction')}}" method="POST">
                    @csrf
                    <input type="hidden" name="transaction_id" id="transaction_id" value="{{$transaction->id ?? ''}}">
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="comments" class="col-sm-4 col-form-label">REASON*</label>
                            <div class="col-sm-8">
                                <select class="form-control enable-select2" id="comments" name="reason">
                                    <option selected disabled>FREE TEXT</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Refund</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
{{-- END Modal for Reasons Blocked User --}}
