<div class="row">
    <div class="col-4" style="background: whitesmoke">

        <span class="mt-3">Verified ID</span>
    </div>
    <div class="col-4">
        &nbsp;&nbsp;
    </div>
    <div class="col-4">

        @if($user->status === 'deleted')
            <span>DELETED</span>
        @else
            @if(!empty($user->id_verified))
                @switch($user->id_verified['card_id_status'])
                    @case('rejected')
                    <span class="text-danger">Rejected</span>
                    @break

                    @case('pending')
                    <a href="#" data-toggle="modal" data-target="#modalID" class="text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Pending...
                    </a>
                    @break

                    @case('approved')
                    <span class="text-success">YES</span>
                    @break
                @endswitch
            @else
                <span>NO</span>
            @endif
        @endif
    </div>
</div>
<div class="row">
    <div class="col-4 offset-8">
        @if($user->status !== 'deleted')
            @if(!empty($user->id_verified))
                @if($user->id_verified['card_id_status'] == 'approved')
                    <a href="#" data-toggle="modal" data-target="#modalShowID" style="color:blue">
                        <small>ID document</small>
                    </a>
                    <br>
                @endif
            @endif
        @endif
        <a href="#" data-toggle="modal" data-target="#modalDeclineHistory"
           style="color:blue;"><small>History</small></a>
    </div>
</div>

@if($user->status !== 'deleted')
    @include('admin.admin-pages.user_profile.user_widget.modal-show-ID')
@endif

@include('admin.admin-pages.user_profile.user_widget.modal-edit-ID-document')

