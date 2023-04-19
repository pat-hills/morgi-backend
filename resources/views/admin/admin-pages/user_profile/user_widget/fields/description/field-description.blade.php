<div class="row">
    <div class="col-2">
        Caption:
    </div>
    <div class="col-10">
        @if(empty($new_description))
            <textarea id="description" name="description" rows="4" style="width: 80%"
                      disabled>{{$user->user_description}}</textarea>
        @else
            <textarea id="description" name="description" rows="4" style="border-color: red; width: 80%"
                      disabled>{{$new_description->description}}</textarea>
        @endif

        <div class="row">
            <div class="col-3">
                &nbsp;
            </div>
            <div class="col-4">
                @if(empty($new_description))
                    @if(!empty($user->user_description))
                        <a href="#" data-toggle="modal" data-target="#modalDeclineDescription" type="submit"
                           class="btn btn-danger btn-circle"><i
                                class="fa fa-times"></i>
                        </a>
                    @endif
                @else
                    <div class="row">
                        <div class="col-6">
                            &nbsp;
                        </div>
                        <div class="col-6 form-inline">
                            <form action="{{route('users.user.updates.description.approve', $user->id)}}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-circle"><i
                                        class="fa fa-check"></i>
                                </button>
                                &nbsp;
                            </form>
                            &nbsp;
                            <a href="#" data-toggle="modal" data-target="#modalDeclineNewDescription"
                               type="submit" class="btn btn-danger btn-circle"><i
                                    class="fa fa-times"></i>
                            </a>
                        </div>

                    </div>
                @endif
            </div>
            <div class="col-3">
                            <span><small><a href="#" data-toggle="modal" data-target="#modalDescriptionHistory"
                                            style="color: blue">History</a></small></span>
            </div>
        </div>
    </div>
</div>

@if(empty($new_description))
    @include('admin.admin-pages.user_profile.user_widget.fields.description.modals.modal-decline-current-description')
@else
    @include('admin.admin-pages.user_profile.user_widget.fields.description.modals.modal-decline-description')
@endif

@include('admin.admin-pages.user_profile.user_widget.fields.description.modals.modal-description-history')
