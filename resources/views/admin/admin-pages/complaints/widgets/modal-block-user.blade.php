{{-- Modal for Block User --}}
<div id="modalBlockUser" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Block Profile</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.block', $user->id)}}" method="POST">
                <input type="hidden" name="user_id">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label>Reason</label>
                        <select class="form-control" name="reasonBlockUser">
                            <option value="pervert">Pervert</option>
                            <option value="scammer">Scammer</option>
                            <option value="terrorist">Terrorist</option>
                            <option value="hacker">Hacker</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for Block User --}}
