<div id="showProofVideo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="" method="POST" id="form_decline_video">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 text-center">
                                <video width="450" height="300" controls id="modalProofVideo">
                                    <source src="" type="video/mp4">
                                </video>
                            </div>
                        </div>
                        <br>
                    </div>
                </form>
                <div class="form-group" id="showVideoProofReason">
                    <div class="row">
                        <div class="col-3">
                            <label for="declineProofVideoReason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="declineProofVideoReason">
                                <option selected disabled>...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <input type="hidden" id="videoProofId">
                <input type="hidden" id="videoProofStatus">

                <button class="btn btn-success" onclick="approveProofVideo()" id="approveProofVideoButton" style="display: none">Approve</button>
                <button class="btn btn-danger" onclick="declineProofVideo()" id="declineProofVideoButton" style="display: none">Decline</button>
                <span id="statusProofVideo" style="display: none"></span>

            </div>
        </div>
    </div>
</div>

<script>

    function approveProofVideo(){
        $('#approveProofVideoButton').attr('disabled', true);
        $('#declineProofVideoButton').attr('disabled', true);

        const videoProofId = $('#videoProofId').val();
        const goal_id = '{{$goal->id}}'

        if (!videoProofId){

            $('#approveProofVideoButton').attr('disabled', false);
            $('#declineProofVideoButton').attr('disabled', false);
            $('#showProofVideo').modal('hide');

            $('#alert-message').html("Data of Proof Video not found");
            $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                $("#alert-danger").slideUp(500);
            });
            return;
        }

        const actionUrl = '{{route('api.admin.goals.proofs.approve', ['goal' => ':goal_id', 'goal_proof' => ':goal_proof_id'])}}'.replace(':goal_id', parseInt(goal_id)).replace(':goal_proof_id', parseInt(videoProofId));

        $.ajax({
            type: "PATCH",
            url: actionUrl,
            data: {
                '_token': '{{ csrf_token() }}',
            },
            success: function (data) {
                location.reload();
            },
            error: function (data) {
                let error = data.responseJSON.message;
                $('#approveProofVideoButton').attr('disabled', false);
                $('#declineProofVideoButton').attr('disabled', false);
                $('#showProofVideo').modal('hide');

                $('#alert-message').html(error);
                $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                    $("#alert-danger").slideUp(500);
                });
            }
        });
    }

    function declineProofVideo(){
        $('#approveProofVideoButton').attr('disabled', true);
        $('#declineProofVideoButton').attr('disabled', true);

        const videoProofId = $('#videoProofId').val();
        const goal_id = '{{$goal->id}}'
        const decline_reason = $('#declineProofVideoReason').val();

        if (!videoProofId){
            $('html, body').animate({
                scrollTop: $("#top").offset().top
            }, 500);

            $('#approveProofVideoButton').attr('disabled', false);
            $('#declineProofVideoButton').attr('disabled', false);
            $('#showProofVideo').modal('hide');

            $('#alert-message').html("Data of Proof Video not found");
            $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                $("#alert-danger").slideUp(500);
            });
            return;
        }

        const actionUrl = '{{route('api.admin.goals.proofs.decline', ['goal' => ':goal_id', 'goal_proof' => ':goal_proof_id'])}}'.replace(':goal_id', parseInt(goal_id)).replace(':goal_proof_id', parseInt(videoProofId));
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: {
                '_token': '{{ csrf_token() }}',
                'decline_reason' : decline_reason
            },
            success: function (data) {
                location.reload();
            },
            error: function (data) {
                $('html, body').animate({
                    scrollTop: $("#top").offset().top
                }, 500);

                let error = data.responseJSON.message;
                $('#approveProofVideoButton').attr('disabled', false);
                $('#declineProofVideoButton').attr('disabled', false);
                $('#showProofVideo').modal('hide');

                $('#alert-message').html(error);
                $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                    $("#alert-danger").slideUp(500);
                });
            }
        });
    }

</script>
