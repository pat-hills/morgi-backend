{{-- Modal for Password History --}}
<div id="modalPassHistory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-title">
                    <h5>Password reset History</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div
                style="overflow-y: scroll; overflow-x: hidden; @if(!empty($psw_history) && count($psw_history) > 4)height: 400px @endif">
                <div class="modal-body">
                    <table class="table table-striped header-fixed">
                        <thead class="thead-light">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>IP Address</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($psw_history))
                            @foreach($psw_history as $psw)
                                <tr>
                                    <td>{{date('Y-m-d', strtotime($psw->created_at))}}</td>
                                    <td>{{date('H:i:s', strtotime($psw->created_at))}}</td>
                                    <td>{{$psw->ip_address}}</td>
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
{{-- END Modal for Password History --}}
