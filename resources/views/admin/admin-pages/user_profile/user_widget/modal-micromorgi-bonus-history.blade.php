{{-- Modal for Bonus History --}}
<div id="modalBonusHistory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Bonus History</div>
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
                            <th scope="col">Bonus Micro Morgi</th>
                            <th scope="col">Reason</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($bonus_micromorgi as $micros)
                            <tr>
                                <td>{{$micros->created_at}}</td>
                                <td>{{$micros->micromorgi}}</td>
                                <td>{{$micros->notes}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- END Modal for Bonus History --}}
