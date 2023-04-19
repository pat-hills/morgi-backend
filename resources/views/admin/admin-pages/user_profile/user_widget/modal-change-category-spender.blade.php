<div class="modal fade" id="modalChangeSpenderGroup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Spender Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.update-spender-category', $user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col-4 mt-2">
                            {{ucfirst($user->spender_group_name)}} Leader
                        </div>
                        <div class="col-2 mt-2">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                        <div class="col-6">
                            <select class="form-control" name="category_id">
                                @foreach(\App\Models\SpenderGroup::all() as $category)
                                    <option value="{{$category->id}}" @if($user->spender_group_id == $category->id) selected @endif> {{ucfirst($category->name)}} Leader
                                    </option>
                                @endforeach
                            </select>
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
