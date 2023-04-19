{{-- Modal for Notes --}}
<div id="modalNotes" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" style="text-align: center">
                    USER PROOF NOTE
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if(!empty($goal->proof_note))
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="font-weight-normal"
                                       style="font-size: 100%">{{$goal->proof_note}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <span>No user proof note found</span>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
{{-- END Modal for Notes --}}
