{{-- Modal for Reasons Blocked User --}}
<div id="modalRefund" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">REFUND</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('refund-transaction')}}" id="form_refund">
                    @csrf

                <div class="form-group">
                    <div class="row">
                        <div class="col-3">
                            <label for="refund_reason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="refund_reason" name="reason">
                                <option selected disabled>...</option>
                                @foreach(\App\Utils\ReasonUtils::MICROMORGI_TAB_REFUND_REASON[$user->type] as $key => $reason)
                                    <option value="{{$key}}">{{strtoupper($reason)}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>
                </div>

                    <input type="hidden" id="transaction_id" name="transaction_id">
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" form="form_refund">Refund</button>
            </div>
        </div>
    </div>
</div>
{{-- END Modal for Reasons Blocked User --}}
