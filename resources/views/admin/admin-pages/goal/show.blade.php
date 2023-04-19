@extends('admin.layout')

@section('content')

    <h4>GOAL #{{$goal->id}}</h4>

    @include('admin.admin-pages.goal.components.info-status', ['status' => $goal->status])
    <br>
    <br>
    <br>
    <div class="row">
        <div class="col-8">
            <div class="row justify-content-start">
                <div class="col-sm-12 col-md-6">
                    <span>Goal's Owner ID #{{$goal->rookie_id}}</span> <a
                        href="{{route('user.edit', $goal->rookie_id)}}" target="_blank" style="color: #007bff"><small>Go
                            to profile <i
                                class="fas fa-arrow-right"></i></small></a>

                </div>
                <div class="col-sm-12 col-md-6 text-right">
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-7">
        </div>

    </div>
    <br>
    @if($goal->status !== 'cancelled')
        @include('admin.admin-pages.goal.components.status-select', ['available_status' => $admin_available_statues, 'status' => $goal->status, 'goal_id' => $goal->id])
    @endif

    <br>

    <div class="row justify-content-center">
        <div class="col-2">
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row text-center mt-2">
                        <div class="col-12">
                            <h5>{{ucfirst($goal->name)}}</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <div class="container">
                            <div class="row justify-content-center" id="mediaGallery">
                            </div>
                            <hr>
                            <div class="row text-left">
                                <div class="col-12">
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            Details
                                        </div>
                                        <div class="col-4">
                                            {{$goal->details}}
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            Target {{ucfirst(str_replace('_', ' ', $goal->currency_type))}}
                                        </div>
                                        <div class="col-4">
                                            {{$goal->target_amount}}
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            Leaders Donations
                                        </div>
                                        <div class="col-4">
                                            <span id="leaders_donations">0</span>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            Progress
                                        </div>
                                        <div class="col-4">
                                            <label id="progressPercentage">0.00%</label>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            Start Date
                                        </div>
                                        <div class="col-4">
                                            {{date('Y-m-d H:i', strtotime($goal->start_date))}}
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            End Date
                                        </div>
                                        <div class="col-4">
                                            {{date('Y-m-d H:i', strtotime($goal->end_date))}}
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            The user will send an image proof
                                        </div>
                                        <div class="col-4">
                                            @if($goal->has_image_proof)
                                                YES
                                            @else
                                                NO
                                            @endif
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-around">
                                        <div class="col-6">
                                            The user will send a video proof
                                        </div>
                                        <div class="col-4">
                                            @if($goal->has_video_proof)
                                                YES
                                            @else
                                                NO
                                            @endif
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <a href="#" data-toggle="modal" data-target="#modalNotes">User Proof Note <i
                                                    class="fa fa-sticky-note"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2">

        </div>
    </div>

    <br>
    <div class="row justify-content-center" id="proofsCard">
        <div class="col-2">
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row text-center mt-2">
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            <h5>PROOFS</h5>
                        </div>
                        <div class="col-4">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <div class="container">
                            <div class="row text-center">
                                <div class="col-12">
                                    <small><strong>Note:</strong> If you decline a proof, new proofs will be
                                        automatically requested</small>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-12">
                                    <small>If you want approve the entire goal, please use the status box</small>
                                </div>
                            </div>
                            <br>
                            <br>
                            <br>
                            <div class="row text-left" id="imageProofsDiv">
                                <div class="col-12">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            PHOTOS
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row justify-content-center" id="proofImageGallery">
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <hr>
                            <br>
                            <div class="row text-left" id="videoProofsDiv">
                                <div class="col-12">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            VIDEOS
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-center" id="proofVideoGallery">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2">
        </div>
    </div>

    <div class="row justify-content-center" id="checkedProofsCard">
        <div class="col-2">
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">
                    <div class="row text-center mt-2">
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                            <h5>CHECKED PROOFS</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card-text">
                        <div class="container">
                            <br>
                            <div class="row text-left" id="imageCheckedProofsDiv">
                                <div class="col-12">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            CHECKED PHOTOS
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <div class="row justify-content-center" id="proofCheckedImageGallery">
                                    </div>
                                    <br>
                                </div>
                            </div>
                            <hr>
                            <br>
                            <div class="row text-left" id="videoCheckedProofsDiv">
                                <div class="col-12">
                                    <div class="row text-center">
                                        <div class="col-12">
                                            CHECKED VIDEOS
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row justify-content-center" id="proofCheckedVideoGallery">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-2">
        </div>
    </div>


    @include('admin.admin-pages.goal.components.modals.show-note')
    @include('admin.admin-pages.goal.components.modals.show-image')
    @include('admin.admin-pages.goal.components.modals.show-video')

    @include('admin.admin-pages.goal.components.modals.show-proof-image')
    @include('admin.admin-pages.goal.components.modals.show-proof-video')

@endsection


@section('js_after')

    <script>
        function setImgToShow(url) {
            $("#valueToShow").attr("src", url);
        }
    </script>

    <script>

        $(document).ready(function () {
            $('#proofsCard').hide();
            $('#imageProofsDiv').hide()
            $('#videoProofsDiv').hide()

            $('#checkedProofsCard').hide();
            $('#imageCheckedProofsDiv').hide()
            $('#videoCheckedProofsDiv').hide()

            let url = '{{route('api.admin.goals.show', ['goal' => $goal->id])}}';
            $.ajax({
                url: url,
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function (data) {
                    let percentageProgress = (data.donations_count / data.target_amount) * 100;

                    let percentageProgressWithPercent = parseFloat(percentageProgress).toFixed(2) + '%';

                    let progressPercentage = $('#progressPercentage');
                    progressPercentage.html(percentageProgressWithPercent);


                    if (data['status'] === 'cancelled' || data['status'] === 'proof_status_declined') {
                        let cancelledOrDeclinedBy = $('#cancelledOrDeclinedBy');

                        let cancelledBy;
                        if(data['cancelled_by']){
                            console.log('inside')
                            cancelledBy = data['cancelled_by']['type'];
                            if (cancelledBy) {
                                cancelledBy = cancelledBy.charAt(0).toUpperCase() + cancelledBy.slice(1);
                                let cancelledByUsername = data['cancelled_by']['username'];
                                cancelledOrDeclinedBy.append('<span>Cancelled by ' + cancelledBy + ': ' + cancelledByUsername + ' </span>');
                            }
                        }

                        let cancelledReason;
                        if(data['cancelled_reason']){
                            cancelledReason = data['cancelled_reason'];
                            if (cancelledReason) {
                                cancelledReason = cancelledReason.charAt(0).toUpperCase() + cancelledReason.slice(1);
                                if(cancelledBy){
                                    cancelledOrDeclinedBy.append('<br>');
                                }
                                cancelledOrDeclinedBy.append('<span>Reason: ' + cancelledReason + ' </span>');
                            }
                        }

                        if (cancelledBy || cancelledReason) {
                            cancelledOrDeclinedBy.show();
                        }
                    }

                    $('#leaders_donations').html(data.donations_count);

                    if (data.media !== null) {
                        let gallery = $('#mediaGallery');
                        data.media.forEach((media) => {
                            let id;
                            let image;
                            let image_url;
                            let video;
                            let video_url;

                            switch (media.type) {
                                case 'image':
                                    image_url = media.url;
                                    id = media.id;
                                    image = buildImage(image_url, id);
                                    gallery.append(image);
                                    break;
                                case 'video':
                                    video_url = media.url;
                                    id = media.id;
                                    video = buildVideo(video_url, id);
                                    gallery.append(video);
                                    $(video)[0].load();
                                    break;
                            }
                        })
                    } else {
                        $('#mediaGallery').text('No Media founded');
                    }

                    let proofImageGallery = $('#proofImageGallery');
                    let proofCheckedImageGallery = $('#proofCheckedImageGallery');
                    let proofVideoGallery = $('#proofVideoGallery');
                    let proofCheckedVideoGallery = $('#proofCheckedVideoGallery');
                    let hasProofImage = false;
                    let hasProofVideo = false;
                    let hasCheckedProofImage = false;
                    let hasCheckedProofVideo = false;

                    if (data.proofs !== null) {
                        data.proofs.forEach((media) => {
                            let id;
                            let image;
                            let image_url;
                            let video;
                            let video_url;
                            let imageProofStatus;
                            let videoProofStatus;

                            switch (media.type) {
                                case 'image':
                                    image_url = media.url;
                                    id = media.id;
                                    imageProofStatus = media.status
                                    image = buildProofImage(image_url, id, imageProofStatus);
                                    switch (imageProofStatus) {
                                        case 'pending':
                                            hasProofImage = true;
                                            proofImageGallery.append(image);
                                            break;
                                        default:
                                            hasCheckedProofImage = true;
                                            proofCheckedImageGallery.append(image);
                                            break;
                                    }
                                    break;
                                case 'video':
                                    video_url = media.url;
                                    id = media.id;
                                    videoProofStatus = media.status
                                    video = buildProofVideo(video_url, id, videoProofStatus);
                                    switch (videoProofStatus) {
                                        case 'pending':
                                            hasProofVideo = true;
                                            proofVideoGallery.append(video);
                                            break;
                                        default:
                                            hasCheckedProofVideo = true;
                                            proofCheckedVideoGallery.append(video);
                                            break;
                                    }
                                    break;

                            }
                        })
                    }

                    if (hasProofImage || hasProofVideo) {
                        $('#proofsCard').show();
                        if (hasProofImage) {
                            $('#imageProofsDiv').show()
                        }
                        if (hasProofVideo) {
                            $('#videoProofsDiv').show()
                        }
                    }

                    if (hasCheckedProofImage || hasCheckedProofVideo) {
                        $('#checkedProofsCard').show();
                        if (hasCheckedProofImage) {
                            $('#imageCheckedProofsDiv').show()
                        }
                        if (hasCheckedProofVideo) {
                            $('#videoCheckedProofsDiv').show()
                        }
                    }
                }
            });


        });

        function showImage(img) {
            let url = $(img).attr('src');
            $('#modalImage').attr('src', url);
        }

        function showVideo(video) {
            let url = $(video).attr('src');
            $('#modalVideo source').each(function () {
                $(this).attr('src', url)
            });
        }

        function buildImage(url, id) {
            return '<div class="col-2 col-md-auto col-sm-auto my-3">' +
                '<img src="' + url + '" class="show-img" data-toggle="modal" style="height: 150px; width: 140px" data-id="' + id + '" data-target="#showImage" onclick="showImage(this)" >' +
                '</div>';
        }

        function buildVideo(url, id) {
            return '<div class="col-2 col-md-auto col-sm-auto my-3">' +
                '<video width="240" height="150" data-id="' + id + '" onclick="showVideo(this)" data-toggle="modal" data-target="#showVideo">' +
                '<source src="' + url + '" type="video/mp4">' +
                '</video>' +
                '</div>';
        }

        function showProofImage(img) {
            let image = $(img);
            let imageProofId = image.attr('data-id');
            let imageProofStatus = image.attr('data-status');
            let url = image.attr('src');

            $('#imageProofId').val(imageProofId)
            $('#modalProofImage').attr('src', url);

            switch (imageProofStatus) {
                case 'pending':
                    $('#approveProofImageButton').show();
                    $('#declineProofImageButton').show();
                    $('#showImageProofReason').show();
                    $('#statusProofImage').text('').hide();
                    break
                default:
                    $('#approveProofImageButton').hide();
                    $('#declineProofImageButton').hide();
                    $('#showImageProofReason').hide();
                    let imageProofStatusPrint = imageProofStatus.charAt(0).toUpperCase() + imageProofStatus.slice(1);

                    $('#statusProofImage').text(imageProofStatusPrint).show()
                    break;
            }
        }

        function showProofVideo(vid) {
            let video = $(vid);
            let videoProofId = video.attr('data-id');
            let videoProofStatus = video.attr('data-status');
            let url = video[0].currentSrc;

            $('#videoProofId').val(videoProofId)
            $('#modalProofVideo source').each(function () {
                $(this).attr('src', url)
            });
            $('#modalProofVideo')[0].load();

            switch (videoProofStatus) {
                case 'pending':
                    $('#approveProofVideoButton').show();
                    $('#declineProofVideoButton').show();
                    $('#showVideoProofReason').show();
                    $('#statusProofVideo').text('').hide();
                    break
                default:
                    $('#approveProofVideoButton').hide();
                    $('#declineProofVideoButton').hide();
                    $('#showVideoProofReason').hide();
                    let videoProofStatusPrint = videoProofStatus.charAt(0).toUpperCase() + videoProofStatus.slice(1);

                    $('#statusProofVideo').text(videoProofStatusPrint).show()
                    break;
            }
        }

        function buildProofImage(url, id, status) {
            let borderCss;

            switch (status) {
                case 'declined':
                    borderCss = 'border-color: red; border-style: solid"'
                    break;
                case 'approved':
                    borderCss = 'border-color: green; border-style: solid"'
                    break;
                default:
                    borderCss = 'border: none'
                    break;
            }

            return '<div class="col-2 col-md-auto col-sm-auto my-3">' +
                '<img src="' + url + '" class="show-img" data-toggle="modal" style="height: 150px; width: 140px; ' + borderCss + '" data-id="' + id + '" data-status="' + status + '" data-target="#showProofImage" onclick="showProofImage(this)" >' +
                '</div>';
        }

        function buildProofVideo(url, id, status) {
            let borderCss;

            switch (status) {
                case 'declined':
                    borderCss = 'border-color: red; border-style: solid"'
                    break;
                case 'approved':
                    borderCss = 'border-color: green; border-style: solid"'
                    break;
                default:
                    borderCss = 'border: none'
                    break;
            }
            return '<div class="col-2 col-md-auto col-sm-auto my-3">' +
                '<video style="' + borderCss + '" width="240" height="150" data-id="' + id + '" data-status="' + status + '" onclick="showProofVideo(this)" data-toggle="modal" data-target="#showProofVideo">' +
                '<source src="' + url + '" type="video/mp4">' +
                '</video>' +
                '</div>';
        }

        function enableUpdateButton(select) {
            let select_value = $(select).val();
            if (select_value !== undefined) {
                $('#updateButton').attr('disabled', false)
            }
        }
    </script>
@endsection


