{{-- Modal for Reasons Blocked User --}}
<div id="modalReasonsBlock" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Block History</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped header-fixed">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Admin</th>
                        <th scope="col">Block Reason</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!empty($blocked_history))
                        @foreach($blocked_history as $blocked)
                            <tr>
                                <td>{{$blocked->created_at}}</td>
                                <td>{{$blocked->username}}</td>
                                <td>{{$blocked->reason}}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
{{-- END Modal for Reasons Blocked User --}}
