{{-- Modal for Reasons Blocked User --}}
<div id="modalSendPasswordLink" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Send reset password link</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.sent-password-reset', $user->id)}}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{$user->email}}">
                <div class="modal-body">
                    <h4>Are you sure?</h4>
                    <span><small>Will be send a password reset link to {{$user->email}}</small></span>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">SEND</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for Reasons Blocked User --}}
