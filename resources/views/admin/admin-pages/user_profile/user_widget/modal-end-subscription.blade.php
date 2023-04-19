{{-- Modal for end subscription --}}
<div id="modalEndSubscription" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Are you sure?</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('subscriptions.end')}}" method="POST">
                @csrf
                <input type="hidden" id="subscription_to_end" name="subscription_id">
                <div class="modal-body">
                   <span>By clicking 'cancel' the subscription will be canceled and changed to inactive.</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for end subscription --}}
