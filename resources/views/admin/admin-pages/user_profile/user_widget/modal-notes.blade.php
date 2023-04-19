{{-- Modal for Notes --}}
<div id="modalNotes" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">


                <div class="modal-title" style="text-align: center">
                    NOTES
                </div>


                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <div class="modal-body">

                <div class="form-group">
                    <form action="{{route('user.edit.add-note', $user->id)}}" method="POST">
                        @csrf
                        <textarea class="form-control" id="new_note" name="new_note"
                                  rows="3"></textarea>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                &nbsp;
                            </div>
                            <div class="col-6 mb-1" style="text-align: right">
                                <button type="button" class="btn" data-dismiss="modal">Cancel
                                </button>
                                <button type="submit" class="btn btn-success">ADD</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div
                    style="overflow-y: scroll; overflow-x: hidden; @if($notes->count() > 4)height: 400px @endif">
                    @foreach($notes as $note)
                        <br>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <p class="font-weight-normal"
                                           style="font-size: 100%">{{$note->note}}</p>
                                    </div>
                                    <div class="card-footer" style="height: 40px">
                                        <p class="font-weight-light" style="font-size: 80%">Note
                                            created
                                            on {{date_format(date_create("$note->created_at"), 'd M Y, h:i')}}
                                            &nbsp;
                                            by: {{$note->username}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
{{-- END Modal for Notes --}}
