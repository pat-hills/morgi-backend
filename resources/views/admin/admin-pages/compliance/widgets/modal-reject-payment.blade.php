{{-- Modal for Decline button --}}
<div id="modalRejectPayment" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Reject Payment</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('action-transaction')}}" method="POST">
                @csrf
                <input type="hidden" name="transaction_id" id="refund_transaction_id">
                <input type="hidden" name="action" value="declined">
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-3">
                                <label for="select_decline_reason" class="col-sm-4 col-form-label">REASON*</label>
                            </div>
                            <div class="col-9 mt-1">
                                <select class="form-control enable-select2" id="select_decline_reason" name="decline_reason">
                                    <option selected disabled>FREE TEXT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">REJECT</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for Decline button --}}
