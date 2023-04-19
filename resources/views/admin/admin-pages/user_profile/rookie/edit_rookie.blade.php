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
                    <a href="#" data-toggle="modal" data-target="#modalChangePrimaryData" style="color: blue">
                        First Name:
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" data-toggle="modal" data-target="#modalChangePrimaryData">
                        {{$user->first_name}} &nbsp;&nbsp;<i class="fas fa-edit"></i>
                    </a>
                </div>
                <div class="col-2 col-md-auto">
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
                    <a href="#" data-toggle="modal" data-target="#modalChangePrimaryData" style="color: blue">
                        Last Name:
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" data-toggle="modal" data-target="#modalChangePrimaryData">
                        {{$user->last_name}} &nbsp;&nbsp;<i class="fas fa-edit"></i>
                    </a>
                </div>

                <div class="col-2">
                    <a href="#" data-toggle="modal" data-target="#modalEditBirthDate" style="color: blue">
                        Birth date:
                    </a>
                </div>

                <div class="col-md-4">
                    <a href="#" data-toggle="modal" data-target="#modalEditBirthDate">
                        {{$user->birth_date}} &nbsp;
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-calendar" viewBox="0 0 16 16">
                            <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                    </a>

                </div>
            </div>


            <br>
            <div class="row">
                <div class="col-2">
                    <a href="#" data-toggle="modal" data-target="#modalChangeUsername"
                       @if($user->updated_username) style="color: orange" @else style="color: blue" @endif>
                        Username:
                    </a>
                </div>
                <div class="col-md-4">
                    <a href="#" data-toggle="modal" data-target="#modalChangeUsername">
                        {{$user->username ?? NULL}} &nbsp;&nbsp;@if($user->updated_username)
                            <i class="fas fa-exclamation-triangle" style="color: orange"></i>
                        @else
                            <i class="fas fa-edit"></i>
                        @endif
                    </a>
                </div>
                <div class="col-2">
                    Age:
                </div>

                <div class="col-md-4">
                    {{$user->age}}
                </div>

            </div>

            <br>
            <div class="row">
                <div class="col-2">
                    Email:
                </div>
                <div class="col-md-4">
                    {{$user->email}}
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
            </div>

            <br>

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

            <div class="row">
                <div class="col-2">
                    Country:
                </div>
                <div class="col-md-4">
                    {{$user->country_name}}
                </div>
                <div class="col-2">
                    Region:
                    <br><br>
                    City:
                </div>
                <div class="col-md-4">
                    @if(is_object(App\Models\Rookie::find($user->id)->getRegion()))
                        {{App\Models\Rookie::find($user->id)->getRegion()->name}}
                        {{--@elseif(is_array(App\Models\Rookie::find($user->id)->region['name']))
                            {{App\Models\Rookie::find($user->id)->region['name']}}--}}
                    @else
                        {{App\Models\Rookie::find($user->id)->region_name}}
                    @endif
                    <br><br>
                    {{App\Models\Rookie::find($user->id)->getCity()->name ?? null}}
                </div>
            </div>


            <br/>

            <div class="row">
                <div class="col-2">
                    Converter
                </div>
                <div class="col-md-4">
                    <input type="checkbox" id="converter_checkbox" onclick="showConverterOption()">
                    &nbsp;&nbsp;&nbsp;
                    <span id="converter_message" style="display: none"></span>

                    <br>
                    <button id="converter_btn" class="btn btn-success btn-sm" style="display: none"
                            onclick="updateConverter()">Save!
                    </button>
                </div>
            </div>

            <br/>

            @include('admin.admin-pages.user_profile.user_widget.fields.description.field-description')

            <br>

            @if($user->status == 'accepted')

                <br>
                <div class="row">
                    <div class="col-2">
                        Is Favourite:
                    </div>

                    <div class="col-4">
                        <form action="{{route('user.edit.edit-is-favourite', $user->id)}}" method="POST">
                            @csrf
                            <select id="is_favourite" name="is_favourite">
                                <option value="true" @if($user->is_favourite) selected @endif>True</option>
                                <option value="false" @if(!$user->is_favourite) selected @endif>False</option>
                            </select>
                            <input type="submit" value="Submit">
                        </form>
                    </div>
                </div>

                <br/>

                <div class="row">

                    <div class="col-2">
                        <a href="#" data-toggle="modal" data-target="#scoreModal">Beauty score:</a>
                    </div>
                    <div class="col-4">
                        {{$user->beauty_score}}
                    </div>

                    <div class="col-2">
                        <a href="#" data-toggle="modal" data-target="#scoreModal">Intelligence score:</a>
                    </div>

                    <div class="col-4">
                        {{$user->intelligence_score}}
                    </div>

                </div>
                <br>
                <div class="row">
                    <div class="col-2">
                        <a href="#" data-toggle="modal" data-target="#scoreModal">Likely score:</a>
                    </div>
                    <div class="col-4">
                        {{ucfirst(\App\Enums\RookieEnum::LIKELY_SCORE[$user->likely_receive_score])}}
                    </div>
                    <div class="col-2">
                    </div>
                    <div class="col-4">
                        <a href="#" data-toggle="modal" data-target="#scoreModal" style="color: blue">Edit Scores</a>
                    </div>
                </div>

                @include('admin.admin-pages.user_profile.user_widget.modal-edit-score')

                <br>
            @endif


            <div class="row">
                <div class="col-2">
                    Path:
                </div>
                <div class="col-md-4">
                    {{implode(', ', $paths)}}
                </div>
            </div>
            @if(!empty($subpaths))
                <br>
                <div class="row">
                    <div class="col-2">
                        Subpath:
                    </div>
                    <div class="col-md-4">
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

            @if(!empty($videos) && count($videos) > 0)
                <br>

                @include('admin.admin-pages.user_profile.user_widget.fields.videos.field-current-videos')

            @endif

            @if(!empty($video_uploaded) && count($video_uploaded) > 0)
                <br>

                @include('admin.admin-pages.user_profile.user_widget.fields.videos.field-videos-histories')

            @endif
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
                    @if(!empty($signup))
                        {{$signup->ip_address}}
                    @else
                        0.0.0.0
                    @endif
                </div>
            </div>

            <br/>

            <div class="row">
                <div class="col-8">
                    Last login/platform
                </div>
                <div class="col-4">
                    @if(!empty($last_login))
                        {{$last_login->ip_address}} / {{$last_login->user_agent}}
                    @else
                        None /
                        None
                    @endif
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

            <div class="row">
                <div class="col-8">
                    Payment method:
                </div>
                <div class="col-4">
                    @if(!empty($main_payment_method))
                        {{$main_payment_method->payment_platform_name}}
                        <div class="row" style="text-align: right">
                            <div class="col-12">
                                <small><a href="#" data-toggle="modal" data-target="#modalResetPayment"
                                          style="color: blue">Reset Payment</a></small>
                                <br>
                                <small><a href="#" data-toggle="modal" data-target="#modalPaymentHistory"
                                          style="color: blue">History</a></small>
                            </div>
                        </div>
                    @else
                        NONE
                    @endif
                </div>
            </div>

            <br/>
            <br/>
            <div class="container">

                <div class="row" style="background: whitesmoke; border: solid 1px; ">
                    <div class="col-6">
                        CURRENT BALANCE
                    </div>
                    <div class="col-6" style="text-align: right">
                        {{$user->untaxed_morgi_balance}}M &nbsp; &nbsp; {{$user->untaxed_micro_morgi_balance}}MM
                    </div>

                </div>

                <br/>

                <div class="row" style="background: whitesmoke; border: solid 1px; ">
                    <div class="col-6">
                        TOTAL EARNED IN MORGI:
                    </div>
                    <div class="col-6" style="text-align: right">
                        {{$morgi_tot}}M {{$micromorgi_tot}}MM
                    </div>
                    <div class="col-6">
                        TOTAL EARNED IN DOLLARS:
                    </div>
                    <div class="col-6" style="text-align: right">
                        ${{$dollars_tot}}
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


            </div>

            <br/>

            <div class="row">
                <div class="col text-center mt-1">
                    <button type="button" class="btn btn-primary" onclick="goTo('{{$user->id}}')">CHAT &nbsp;
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-chat-fill" viewBox="0 0 16 16">
                            <path
                                    d="M8 15c4.418 0 8-3.134 8-7s-3.582-7-8-7-8 3.134-8 7c0 1.76.743 3.37 1.97 4.6-.097 1.016-.417 2.13-.771 2.966-.079.186.074.394.273.362 2.256-.37 3.597-.938 4.18-1.234A9.06 9.06 0 0 0 8 15z"/>
                        </svg>
                    </button>
                </div>
                <div class="col text-center mt-1">
                    <a href="#" data-toggle="modal" data-target="#modalFine" class="btn btn-warning">
                        FINE ROOKIE
                        <i class="fas fa-money-bill"></i>
                    </a>
                </div>
                <div class="col text-center mt-1">
                    <a href="#" data-toggle="modal" data-target="#modalBlockUser" class="btn btn-danger">
                        BLOCK &nbsp;
                        <i class="fa fa-ban" aria-hidden="true"></i></a>
                </div>
            </div>

            <br/>


        </div>
    </div>

    <div class="row">
        <div class="col-8">
            <hr>
            <div class="row">
                <div class="col-4">
                    ACTIVE LEADERS: {{$active_leaders}}
                </div>
                <div class="col-4">
                    PAST LEADERS: {{$past_leaders}}
                </div>

            </div>
        </div>
    </div>

    @include('admin.admin-pages.user_profile.user_widget.modal-notes')

    @include('admin.admin-pages.user_profile.user_widget.modal-send-password-reset-link')

    @include('admin.admin-pages.user_profile.user_widget.modal-block-user')

    @include('admin.admin-pages.user_profile.user_widget.modal-edit-birth-date')

    @include('admin.admin-pages.user_profile.user_widget.modal-edit-username')


    @if(!empty($main_payment_method))
        {{-- Modal for Reset Payment History --}}
        <div id="modalResetPayment" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-title">Reset Payment</div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure?

                    </div>
                    <div class="modal-footer">
                        <form method="POST" action="{{route('user.edit.reset-payment', $user->id)}}">
                            @csrf
                            <input type="hidden" name="id_payment" value="{{$main_payment_method->id}}">
                            <input type="submit" class="btn btn-danger" value="Reset">
                        </form>
                        <button type="button" class="btn" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- END Modal for Reset Payment History --}}
    @endif

    {{-- Modal for Reset Payment History --}}
    <div id="modalPaymentHistory" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title">Payment History</div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-striped header-fixed">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Method</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(!empty($payments_history))
                            @foreach($payments_history as $payment)
                                <tr>
                                    <td>{{date('y-M-d, h:i A', strtotime($payment->updated_at))}}</td>
                                    @if($payment->is_reset)
                                        <td>RESET</td>
                                    @else
                                        <td>{{$payment->name}}</td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif

                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    {{-- END Modal for Reset Payment History --}}

    @include('admin.admin-pages.user_profile.user_widget.modal-fine')

    @include('admin.admin-pages.user_profile.user_widget.modal-ID-document')

    @include('admin.admin-pages.user_profile.user_widget.modal-login-history')

    @include('admin.admin-pages.user_profile.user_widget.modal-edit-first-last-name')

    @include('admin.admin-pages.user_profile.user_widget.modal-password-history')

@endsection



@section('js_after')

    <script>

        $(document).ready(function () {

            @if($user->is_converter)
            $('#converter_checkbox').attr('checked', 'checked');
            @endif
        });


        function goTo(user_id) {

            $.ajax({
                type: "POST",
                url: '{{route('getCustomerlyId.post')}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'user_id': user_id,
                },
                success: function (data) {
                    if (data.includes('Error')) {
                        alert(data);
                    } else {
                        window.open(data, '_blank');
                    }
                    // alert(data); // show response from the php script.
                },
                error: function (data) {
                    alert(data['responseJSON'].message);
                }
            });
        }

        function showConverterOption() {
            let converter_checked = $('#converter_checkbox').prop('checked');
            let rookie_converter_field = {{($user->is_converter) ? 'true' : 'false'}};

            let message = '';
            if (converter_checked) {
                message = "The rookie will set as a <strong><i>converter</i></strong>";
                if (rookie_converter_field === converter_checked) {
                    message = "The rookie is already a <strong><i>converter</i></strong>";
                }
            }

            if (!converter_checked) {
                message = "The rookie will set as a <strong><i>non converter</i></strong>";
                if (rookie_converter_field === converter_checked) {
                    message = "The rookie is already a <strong><i>non converter</i></strong>";
                }
            }

            if (message !== '') {
                $('#converter_message').text('').append(message);
                $('#converter_message').show();
            }

            if (rookie_converter_field === converter_checked) {
                $('#converter_btn').hide();
            } else {
                $('#converter_btn').show();
            }
        }

        function updateConverter() {
            let converter_checked = $('#converter_checkbox').prop('checked');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'PATCH',
                url: '/api/admin/users/' + {{$user->id}},
                data: {
                    is_converter: converter_checked,
                },
                success: function (response) {
                    location.reload();
                },
                error: function (response) {
                    $('#converter_message').text('').append('<span class="text-danger">Something went wrong. Please try to reload the page</span>');
                    $('#converter_message').show();
                }
            });
        }
    </script>
@endsection
