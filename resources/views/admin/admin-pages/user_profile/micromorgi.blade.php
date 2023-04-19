@extends('admin.layout')

@section('content')
    @include('admin.admin-pages.user_profile.search-user')

    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>


    <nav class="mt-4" style="text-align: center">
        <div class="nav nav-tabs">
            <a class="nav-item nav-link" id="profileInfo" href="{{route('user.edit', $user->id)}}" role="tab">Profile
                Info</a>
            <a class="nav-item nav-link" id="activeGifts" href="{{route('user.edit.active_gifts', $user->id)}}"
               role="tab">Monthly Active Gifts</a>
            <a class="nav-item nav-link active" id="micromorgi" href="{{route('user.edit.micromorgi', $user->id)}}" role="tab">Micro Morgi</a>
            <a class="nav-item nav-link" id="transaction" href="{{route('user.edit.transactions', $user->id)}}"
               role="tab">Transaction</a>
            <a class="nav-item nav-link" id="activitylog" href="{{route('user.edit.activity_log', $user->id)}}"
               role="tab">Activity Log</a>
            <a class="nav-item nav-link" id="complaints" href="{{route('user.edit.complaints', $user->id)}}" role="tab">Complaints</a>
            <a class="nav-item nav-link" id="relatedaccounts" href="{{route('user.edit.related_accounts', $user->id)}}"
               role="tab">Related Accounts</a>
            <a class="nav-item nav-link" id="cgbhistory" href="{{route('user.edit.cgb_history', $user->id)}}"
               role="tab">CGB History</a>
        </div>
    </nav>
    <br>
    <br>


    <div class="row">
        <div class="col-1">
            &nbsp;
        </div>
        <div class="col-9">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGiveMicroMorgi" >Give Micro Morgi Bonus</button> &nbsp;&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modalBonusHistory" style="color: blue">Bonus history</a>
        </div>
    </div>
    <br/>
    <br/>

    <table class="table" id="micromorgiTable">
        <thead class="thead-light">
        <tr>
            <th scope="col">INTERNAL ID FOR MICROMORGI</th>
            <th scope="col">DATE</th>
            <th scope="col">TIME</th>
            <th scope="col">ROOKIE GIVEN TO</th>
            <th scope="col">ACTIVITY</th>
            <th scope="col">MICROS GIVEN</th>
            <th scope="col">REFUND</th>
            <th scope="col">REASON</th>
            <th scope="col">REFUND DATE</th>
            <th scope="col">RELATED INTERNAL ID</th>
        </tr>
        </thead>
        <tbody>
        {{--        @for($i = 0; $i<10; $i++)--}}
        <tr>
            <td>#{{rand(0, 40000)}}</td>
            <td>04/05/2020</td>
            <td>{{rand(0,23)}}:{{rand(0,59)}}</td>
            <td>name of rookie</td>
            <td></td>
            <td>{{rand(0,60)}}</td>
            <td><a type="button" class="btn btn-info" style="color: white">Refund</a></td>
            <td>Technical</td>
            <td>05/05/2020 13:33</td>
            <td>#{{rand(1, 400000)}}</td>
        </tr>
        {{--        @endfor--}}

        </tbody>
    </table>




    {{-- Modal for Give Micro Morgi --}}
    <div id="modalGiveMicroMorgi" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Give Micro Morgi bonus to Leader</div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('user.edit.micromorgi.add_bonus', $user->id)}}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group row">
                            <label for="LeaderID" class="col-sm-4 col-form-label">LEADER ID</label>
                            <div class="col-sm-8">
                                <input type="text"  id="LeaderID" name="leader_id"
                                       value="{{$user->id}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="amount" class="col-sm-4 col-form-label">AMOUNT</label>
                            <div class="col-sm-8">
                                <input type="text"  id="amount" name="amount"><br>
                                <small style="color: red">* Please use int numbers only</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="admin" class="col-sm-4 col-form-label">ADMIN ID</label>
                            <div class="col-sm-8">
                                <input type="text"  id="admin" name="admin" value="{{\Illuminate\Support\Facades\Auth::id()}}"><br>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="bonusReason" class="col-sm-4 col-form-label">BONUS REASON</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="bonusReason" name="bonus_reason">
                                    <option value="" selected disabled hidden>
                                        Select an Option
                                    </option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="comments" class="col-sm-4 col-form-label">COMMENTS</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Give Bonus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    {{-- END Modal for Give Micro Morgi --}}

    {{-- Modal for Login History --}}
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
                                <th scope="col">Bonus Morgi</th>
                                <th scope="col">Reason</th>
                            </tr>
                            </thead>
                            <tbody>

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- END Modal for Login History --}}

@endsection

@section('js_after')

    <script>
        $(document).ready( function () {
            $('#micromorgiTable').DataTable();
        } );
    </script>
@endsection
