@extends('admin.layout')

@section('css_before')
    <style>
        @include('admin.css.custom')
    </style>

@endsection

@section('content')

    @include('admin.admin-pages.user_profile.search-user')

    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>

    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')

    <br/>

    @include('admin.admin-pages.user_profile.user_widget.fields.status_actions.field-status-actions')


    <div class="row mt-3" style="text-align: center">
        <div class="col-8">
            <h4>{{strtoupper($user->type)}} INFO</h4>
        </div>


        <div class="col-4">
            <h4>ACCOUNT INFO</h4>

        </div>
    </div>
    <br/>
    <br/>

    <div class="row">
        <div class="col-8" style="border-right: solid">

            <div class="row">
                <div class="col-2">
                    Email:
                </div>
                <div class="col-md-4">
                    {{$user->email}}
                </div>
                <div class="col-2">
                    Password:
                </div>
                <div class="col-md-4">
                    <div class="row" style="text-align: right">
                        <div class="col-12">
                            <small><a href="#" data-toggle="modal" data-target="#modalSendPasswordLink"
                                      style="color: blue">Generate login link</a></small>


                        </div>
                    </div>
                    <div class="row" style="text-align: right">
                        <div class="col-12">
                            <small><a href="#" data-toggle="modal" data-target="#modalPassHistory"
                                      style="color: blue">Reset history</a></small>

                        </div>
                    </div>
                </div>

            </div>

            <br>

            <div class="row">
                <div class="col-2">
                    <a href="#" data-toggle="modal" data-target="#modalChangeUsername" @if($user->updated_username) style="color: orange" @else style="color: blue" @endif>
                        Username:
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" data-toggle="modal" data-target="#modalChangeUsername">
                        {{$user->username ?? NULL}} &nbsp;&nbsp;@if($user->updated_username) <i class="fas fa-exclamation-triangle" style="color: orange"></i> @else <i class="fas fa-edit"></i> @endif
                    </a>
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-2">
                    Gender:
                </div>
                <div class="col-md-4">

                    {{$user->getGender()->name ?? 'Not specified'}}

                </div>
                <div class="col-2">

                </div>
                <div class="col-md-4">

                </div>
            </div>

            <br/>

            @if(!is_null($user->group_id))
                <div class="row">
                    <div class="col-2">
                        Group:
                    </div>
                    <div class="col-md-4">
                        {{strtoupper($user->group_name)}}
                    </div>
                </div>
                <br>
            @endif

            <br>

            @include('admin.admin-pages.user_profile.user_widget.fields.description.field-description')

            <br>

            <div class="row">
                <div class="col-2">
                    <a href="#" data-toggle="modal" data-target="#modalChangeSpenderGroup" style="color: blue">
                        Spender Category:
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" data-toggle="modal" data-target="#modalChangeSpenderGroup">
                        {{ucfirst($user->spender_group_name)}} Leader &nbsp;&nbsp;<i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>

            @include('admin.admin-pages.user_profile.user_widget.modal-change-category-spender')

            <br>

            <div class="row">
                <div class="col-2">
                    Paths:
                </div>
                <div class="col-md-8">
                    {{implode(', ', $paths)}}
                </div>
            </div>
            @if(!empty($subpaths))
                <br>
                <div class="row">
                    <div class="col-2">
                        Subpaths:
                    </div>
                    <div class="col-md-8">
                        {{implode(', ', $subpaths)}}
                    </div>
                </div>
            @endif

            <br>


            @include('admin.admin-pages.user_profile.user_widget.fields.photos.field-current-photos')


        @if(!empty($photo_uploaded))
                <br>

                @include('admin.admin-pages.user_profile.user_widget.fields.photos.field-photos-histories')

            @endif

        <br>


        </div>


        <div class="col-4">
            <div class="row">
                <div class="col-8">
                    Account creation date:
                </div>
                <div class="col-4">
                    {{$user->created_at}}
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-8">
                    Account type:
                </div>
                <div class="col-4">

                    {{ucfirst($user->type)}}

                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-8">
                    SIGN UP IP
                </div>
                <div class="col-4">

                    @if(!empty($signup)){{$signup->ip_address ?? '0.0.0.0'}}@else 0.0.0.0 @endif

                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-8">
                    Last login/platform
                </div>
                <div class="col-4">
                    @if(!empty($last_login)){{$last_login->ip_address}} / {{$last_login->user_agent}} @else None / None @endif
                    <div class="row" style="text-align: right">
                        <div class="col-12">
                            <small><a href="#" data-toggle="modal" data-target="#modalLoginHistory" style="color: blue">Login
                                    History</a></small>
                        </div>
                    </div>
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-8">
                    Previous report on user:
                </div>
                <div class="col-4">
                    {{$reports->count()}}
                </div>
            </div>

            <br/>

            @include('admin.admin-pages.user_profile.user_widget.field-verified-id')

            <br/>
            <br/>
            <div class="container">

                <div class="row" style="background: whitesmoke; border: solid 1px; ">
                    <div class="col-6">
                        MICROMORGI BALANCE
                    </div>
                    <div class="col-6" style="text-align: right">
                        {{$user->micro_morgi_balance}}MM
                    </div>

                </div>

                <br/>

                <div class="row" style="background: whitesmoke; border: solid 1px; ">
                    <div class="col-6">
                        TOTAL PAID IN MORGI:
                    </div>
                    <div class="col-6" style="text-align: right">
                        {{$morgi_tot}}M {{$micromorgi_tot}}MM
                    </div>
                    <div class="col-6">
                        TOTAL PAID IN DOLLARS:
                    </div>
                    <div class="col-6" style="text-align: right">
                        ${{$dollars_tot}}
                    </div>
                </div>

                <br/>

                <div class="row" style="background: whitesmoke; border: solid 1px; ">
                    <div class="col-4">
                        GLOBAL ID:
                    </div>
                    <div class="col-8" style="text-align: right">
                        @if($user->global_id)
                            <a href="#" data-toggle="modal" data-target="#modalGlobalId" style="color: dodgerblue">
                                <u>#{{$global_id_data['id']}}  <small>( {{$global_id_data['count']}} RELATED ACCOUNTS )</small></u>
                            </a>
                        @else
                            <span class="muted"><i>NULL</i></span>
                        @endif
                    </div>
                    <div class="col-6">
                        TOTAL PURCHASED:
                    </div>
                    <div class="col-6" style="text-align: right">
                        @if($user->global_id)
                            ${{$global_id_data['dollars']}}
                        @else
                            <span class="muted"><i>NULL</i></span>
                        @endif
                    </div>
                </div>

                <br/>

                <div class="row" style="background: whitesmoke; border: solid 1px; text-align: center ">
                    <div class="col-12">
                        <a href="#" data-toggle="modal" data-target="#modalNotes">NOTES({{$notes->count()}})
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-newspaper" viewBox="0 0 16 16">
                                <path
                                    d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5v-11zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5H12z"/>
                                <path
                                    d="M2 3h10v2H2V3zm0 3h4v3H2V6zm0 4h4v1H2v-1zm0 2h4v1H2v-1zm5-6h2v1H7V6zm3 0h2v1h-2V6zM7 8h2v1H7V8zm3 0h2v1h-2V8zm-3 2h2v1H7v-1zm3 0h2v1h-2v-1zm-3 2h2v1H7v-1zm3 0h2v1h-2v-1z"/>
                            </svg>
                        </a>
                    </div>

                </div>
                    @include('admin.admin-pages.user_profile.user_widget.modal-notes')


            </div>

            <br/>

            <div class="row">
                <div class="col-5" style="text-align: center">
                        <button type="button" class="btn btn-primary" onclick="goTo('{{$user->id}}')">CHAT &nbsp; &nbsp;
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                 class="bi bi-chat-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                            </svg>
                        </button>
                </div>
                <div class="col-2">
                    &nbsp;
                </div>
                @if($user->status != 'blocked')
                    <div class="col-5">
                        <a href="#" data-toggle="modal" data-target="#modalBlockUser" class="btn btn-danger">BLOCK
                            &nbsp;
                            &nbsp; <i class="fa fa-ban" aria-hidden="true"></i></a>
                    </div>
                @endif

            </div>

            <br/>


        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <hr>
            <div class="row">
                <div class="col-4">
                    ACTIVE ROOKIES: {{$active_rookies}}
                </div>
                <div class="col-4">
                    PAST ROOKIES: {{$past_rookies}}
                </div>
                <div class="col-4">
                    TOTAL MICRO MORGI PACKAGES BOUGHT {{$tot_mm_packages_bought}}
                </div>

            </div>
        </div>
    </div>

    @include('admin.admin-pages.user_profile.user_widget.modal-notes')

    @if($user->global_id)
        @include('admin.admin-pages.user_profile.user_widget.modal-global-id')
    @endif

    @include('admin.admin-pages.user_profile.user_widget.modal-send-password-reset-link')

    @include('admin.admin-pages.user_profile.user_widget.modal-block-user')

    @include('admin.admin-pages.user_profile.user_widget.modal-password-history')

    @include('admin.admin-pages.user_profile.user_widget.modal-login-history')

    @include('admin.admin-pages.user_profile.user_widget.modal-ID-document')

    @include('admin.admin-pages.user_profile.user_widget.modal-edit-username')

@endsection



@section('js_after')

<script>
    function goTo(user_id){

        $.ajax({
            type: "POST",
            url: '{{route('getCustomerlyId.post')}}',
            data: {
                "_token": "{{ csrf_token() }}",
                'user_id' : user_id,
            },
            success: function(data)
            {
                if (data.includes('Error')){
                    alert(data);
                } else {
                    window.open(data, '_blank');
                }
                // alert(data); // show response from the php script.
            },
            error: function (data)
            {
                alert(data['responseJSON'].message);
            }

        });
    }
</script>
@endsection
