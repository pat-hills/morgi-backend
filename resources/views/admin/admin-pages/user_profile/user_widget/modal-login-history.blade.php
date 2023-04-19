{{-- Modal for Login History --}}
<div id="modalLoginHistory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Last Login / Platform</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-wrapper">
                    <table class="table table-striped header-fixed">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Platform</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($login_history))
                            @foreach($login_history as $log)
                                <tr>
                                    <td>{{date_format(date_create("$log->created_at"), 'd M Y, h:i A')}}</td>
                                    <td>{{$log->user_agent}}</td>
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
{{-- END Modal for Login History --}}
