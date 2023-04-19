{{-- Modal for Reasons Blocked User --}}
<div id="modalReasonsRejected" class="modal fade" tabindex="-1" role="dialog">
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
                    @if(!empty($rejected_history))
                        @foreach($rejected_history as $rejected)
                            <tr>
                                <td>{{date('y-M-d, h:i A', strtotime($rejected->created_at))}}</td>
                                <td>{{$rejected->username}}</td>
                                <td>{{\App\Utils\ReasonUtils::ALL_REASON[$rejected->reason] ?? $rejected->reason}}</td>
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
