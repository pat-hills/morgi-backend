<div class="card mb-3">
    <div class="card-header">
        <div class="row justify-content-between">
            <div class="col-4">
                <h5>Leader</h5>
            </div>
            <div class="col-4 text-right">
                <h6 style="color: red"> {{strtoupper($action)}} </h6>
            </div>
        </div>

    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-8">
                <h5 class="card-title">{{$user->username}}</h5>
            </div>
            <div class="col-4 text-right">
                <a href="{{route('user.edit', $user->id)}}" style="color: #007bff"><small>Go to profile <i class="fas fa-arrow-right"></i></small></a>
            </div>
        </div>
        <p>{{$user->email}}</p>
        <div class="card-text">
            <div align="center">
                <img alt="User Pic"
                     id="profile-image1" class="image--cover"
                     @if(empty($user->getOwnAvatar()))
                        src="{{asset('img/noAvatar.png')}}"

                     @else
                         src="{{$user->getOwnAvatar()->url}}"
                         data-toggle="modal"
                         data-target="#modalImage"
                         onclick="setImgToShow('{{$user->getOwnAvatar()->url}}')"
                    @endif
                >
            </div>
            <br>

            <div class="row justify-content-end text-right">
                <div class="col-8">
                    <button onclick="goTo('{{$user->id}}')" class="btn btn-info">CHAT</button>
                    @if($user->status != 'blocked')
                        <a href="#" data-toggle="modal" data-target="#modalBlockUser{{str_replace(' ', '_', $action)}}" class="btn btn-danger">BLOCK</a>
                    @else
                        <span style="color: red">USER BLOCKED</span>
                    @endif
                </div>
            </div>
            <hr>


            <div class="container">
                <div class="row text-left">
                    <div class="col-12">
                        <div class="row justify-content-around">
                            <div class="col-6">
                                Sign up date
                            </div>
                            <div class="col-4">
                                {{$user->created_at}}
                            </div>
                        </div>
                        <br>
                        <div class="row justify-content-around">
                            <div class="col-6">
                                Last login
                            </div>
                            <div class="col-4">
                                {{$user->last_login_at}}
                            </div>
                        </div>
                        <br>
                        <div class="row justify-content-around">
                            <div class="col-6">
                                Gender
                            </div>
                            <div class="col-4">
                                {{(empty($user->getGender())) ? '' : ucfirst($user->getGender()->name)}}
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <div class="row text-left">
                    <div class="col-12">
                        <div class="row justify-content-around">
                            <div class="col-6">
                                Platform
                            </div>
                            <div class="col-4">
                                @if(!empty($user->platform))
                                    {{$user->platform}}
                                @endif
                            </div>
                        </div>
                        <br>

                        <div class="row justify-content-around">
                            <div class="col-6">
                                Previous reports on user:
                            </div>
                            <div class="col-4">
                                {{$user->counter_report}}
                            </div>
                        </div>
                        <br>

                        <div class="row justify-content-around">
                            <div class="col-6">
                                Total Micro packages:
                            </div>
                            <div class="col-4">
                                {{$user->packages_bought}}
                            </div>
                        </div>
                        <br>

                        <div class="row justify-content-around">
                            <div class="col-6">
                                Total Morgi spent:
                            </div>
                            <div class="col-4">
                                {{$user->tot_morgi}}
                            </div>
                        </div>
                        <br>

{{--                        <div class="row justify-content-around">--}}
{{--                            <div class="col-6">--}}
{{--                                Active gift between users:--}}
{{--                            </div>--}}
{{--                            <div class="col-4">--}}
{{--                                200--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>

            </div>


        </div>

        {{--                    <a href="#" class="btn btn-primary">Go to profile</a>--}}
    </div>
{{--    <div class="card-footer">--}}
{{--    </div>--}}
</div>

{{-- Modal for Block User --}}
<div id="modalBlockUser{{str_replace(' ', '_', $action)}}" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Block Profile</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('user.edit.block', $user->id)}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label>Reason</label>
                        <select class="form-control enable-select2" name="reasonBlockUser">
                            <option selected disabled>FREE TEXT</option>
                        </select>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Block</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- END Modal for Block User --}}

