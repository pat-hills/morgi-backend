{{-- Modal for Description History --}}
<div id="modalDescriptionHistory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Description History</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-wrapper">
                    <table class="table table-striped header-fixed">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Admin</th>
                            <th scope="col">Date</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($descriptions_history))
                            @foreach($descriptions_history as $history)
                                <tr>
                                    @if(!empty($history->admin_id))
                                        <td>{{\App\Models\User::find($history->admin_id)->username}}</td>
                                    @else
                                        <td>N/D</td>
                                    @endif
                                    <td>{{$history->updated_at}}</td>
                                    <td>{{$history->status}}</td>
                                </tr>
                            @endforeach

                        @endif
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- END Modal for Description History --}}
