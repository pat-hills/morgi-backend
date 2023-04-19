<div class="modal fade" id="scoreModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Scores</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.update-score', $user->id)}}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-3">
                            <label class="mt-2">Beauty Score:</label>
                        </div>
                        <div class="col-2">
                            <input type="number" class="form-control" min="0" max="10" name="beauty_score"
                                   value="{{$user->beauty_score}}">
                        </div>
                        <div class="col-3 offset-1">
                            <label class="mt-2">Intelligence Score:</label>
                        </div>
                        <div class="col-2">
                            <input type="number" class="form-control" min="0" max="10" name="intelligence_score"
                                   value="{{$user->intelligence_score}}">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-5">
                            <label class="mt-2">Likely to receive by leader Score:</label>
                        </div>
                        <div class="col">
                            <select class="form-control" id="likely_score" name="likely_score">
                                @foreach(\App\Enums\RookieEnum::LIKELY_SCORE as $key => $label)
                                    <option value="{{$key}}"
                                            @if($key === $user->likely_receive_score) selected @endif>{{strtoupper($label)}}</option>
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
