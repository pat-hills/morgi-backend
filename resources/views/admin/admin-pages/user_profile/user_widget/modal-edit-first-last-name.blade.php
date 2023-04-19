<div class="modal fade" id="modalChangePrimaryData" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.update-first-last-name', $user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <label class="control-label" for="first_name">
                                First name
                            </label>
                            <input class="form-control" id="first_name" name="first_name" value="{{$user->first_name}}" type="text"/>
                        </div>
                        <div class="col-6">
                            <label class="control-label" for="last_name">
                                Last name
                            </label>
                            <input class="form-control" id="last_name" name="last_name" value="{{$user->last_name}}" type="text"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-success" value="UPDATE">
                </div>
            </form>
        </div>
    </div>
</div>
