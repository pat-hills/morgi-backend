{{-- Modal for Decline History ID --}}
<div id="modalDeclineHistory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Decline History ID</div>
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
                        @foreach($history_id as $his)
                            <tr>
                                <td>{{$his->username}}</td>
                                <td>{{date('y-M-d, h:i A', strtotime($his->updated_at))}}</td>
                                @if($his->status == 'approved')
                                    <td style="color: green">{{strtoupper($his->status)}}</td>
                                @elseif($his->status == 'rejected')
                                    <td style="color: red">{{strtoupper($his->status)}}</td>
                                @elseif($his->status == 'pending')
                                    <td>waiting...</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
