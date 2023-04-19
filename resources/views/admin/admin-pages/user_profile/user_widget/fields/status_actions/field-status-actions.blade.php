@php

    //set up user status
    $user_status = (isset($user->internal_status)) ? $user->internal_status : $user->status;

@endphp

<div class="row" style="border-bottom: solid">
    <div class="col-3">
        <h5>{{strtoupper($user->type)}} ACCOUNT</h5>

    </div>
    <div class="col-5">
        <div class="row">
            <div class="col-4">
                <strong>ACCOUNT STATUS:</strong>
            </div>
            <div class="col-8">
                @if($user->password_forced)
                    <span class="text-danger"><i class="fa fa-ban" aria-hidden="true"></i> &nbsp; BLOCKED ( Password forced )</span>
                @else
                    @switch($user_status)

                        @case('blocked')
                        <span class="text-danger">
                            <i class="fa fa-ban" aria-hidden="true"></i>
                            &nbsp; BLOCKED
                            <a href="#" data-toggle="modal" data-target="#modalReasonsBlock" class="text-primary">
                                <small> Block History</small>
                            </a>
                        </span>
                        @break

                        @case('rejected')
                        <span class="text-danger">REJECTED</span>
                        <a href="#" data-toggle="modal" class="text-primary" data-target="#modalReasonsRejected">
                            <small> Reject History</small>
                        </a>
                        @break

                        @case('untrusted')
                        <span class="text-danger">UNTRUSTED</span><span> (ID must be verified) </span>
                        @break

                        @case('new')
                        <span class="text-success">NEW</span>&nbsp;<span>(pending approval by admin)</span>
                        @break

                        @case('accepted')
                        @if($user->admin_check)
                            <span class="text-warning">UPDATED</span>&nbsp;<span>(updates awaiting approval)</span>
                        @else

                            <span class="text-success">ACCEPTED</span>
                        @endif
                        @break

                        @case('pending')
                        <span class="text-warning">PENDING</span>&nbsp;<span> @if(!isset($user->email_verified_at))(waiting email confirmation)@endif</span>
                        @break
                        @case('suspend')
                        <span class="text-danger">SUSPEND</span>

                        @break
                        @case('under_review')
                        <span class="text-secondary">UNDER REVIEW</span>

                        @break

                        @case('fraud')
                        <span class="text-danger">FRAUD</span>
                        @break

                        @default
                        <span class="text-secondary">{{$user->status}}</span>
                        @break
                    @endswitch
                @endif

                    @if($user->type === 'leader')
                        <a href="#" data-toggle="modal" data-target="#modalChangeStatus" class="ml-2">
                            <i class="fas fa-edit"></i>
                        </a>
                        @include('admin.admin-pages.user_profile.user_widget.modal-change-status')
                    @endif
            </div>
        </div>
    </div>
    <div class="col-4 mb-3" style="text-align: right">

        @switch($user_status)

            @case('blocked')
            @case('rejected')
            @case('untrusted')
            @case('suspend')
            @case('under_review')
            @case('fraud')
            <a type="button" href="{{route('user.edit.re-active', $user->id)}}"
               class="btn btn-success">Re-Active</a>
            @break

            @case('new')
            @case('pending')

            <div class="row justify-content-center text-center">
                <div class="col-4">
                    <form action="{{route('users.user.approve', $user->id)}}" method="POST">
                        @csrf
                        @if($user->admin_check )
                            <input type="checkbox" class="form-check-input" id="all_updates" name="all_updates">
                            <label class="form-check-label" for="all_updates">Approve all updates</label>
                            <br>
                        @endif
                        <input class="btn btn-success" type="submit" value="Approve">
                    </form>
                </div>
                <div class="col-4">
                    @if($user->admin_check) <br> @endif
                    <a href="#" data-toggle="modal" data-target="#modalDecline" type="button"
                       class="btn btn-danger">Reject</a>
                </div>
            </div>

            @break

            @case('accepted')
            @if($user->admin_check && !$only_id)

                <div class="row justify-content-center text-center">
                    <div class="col-4">
                        <form action="{{route('users.user.updates.approve', $user->id)}}" method="POST">
                            @csrf
                            <input type="checkbox" class="form-check-input" id="approve_all_updates" name="all_updates">
                            <label class="form-check-label" for="approve_all_updates">Approve all updates</label>
                            <br>
                            <input class="btn btn-success" type="submit" value="Approve">
                        </form>
                    </div>
                    <div class="col-4">
                        <br>
                        <a href="#" data-toggle="modal" data-target="#modalDeclineUpdates" type="button"
                           class="btn btn-danger">Decline</a>
                    </div>
                </div>

            @else
                <br>
            @endif
            @break

            @default
            <br>
            @break

        @endswitch
    </div>
</div>

@include('admin.admin-pages.user_profile.user_widget.fields.status_actions.modals.modal-block-history')
@include('admin.admin-pages.user_profile.user_widget.fields.status_actions.modals.modal-reject-history')
@include('admin.admin-pages.user_profile.user_widget.fields.status_actions.modals.modal-decline-user-button')
@include('admin.admin-pages.user_profile.user_widget.fields.status_actions.modals.modal-decline-updates')
