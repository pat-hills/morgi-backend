<div id="showProofImage" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <img src="" alt="..." class="img-thumbnail" id="modalProofImage">
                </div>
                <br>
                <div class="form-group" id="showImageProofReason">
                    <div class="row">
                        <div class="col-3">
                            <label for="declineProofImageReason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="declineProofImageReason">
                                <option selected disabled>...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">

                <input type="hidden" id="imageProofId">
                <input type="hidden" id="imageProofStatus">

                <button class="btn btn-success" onclick="approveProofImage()" id="approveProofImageButton" style="display: none">Approve</button>
                <button class="btn btn-danger" onclick="declineProofImage()" id="declineProofImageButton" style="display: none">Decline</button>
                <span id="statusProofImage" style="display: none"></span>
            </div>
        </div>
    </div>
</div>

<script>

    function approveProofImage(){
        $('#approveProofImageButton').attr('disabled', true);
        $('#declineProofImageButton').attr('disabled', true);

        const imageProofId = $('#imageProofId').val();
        const goal_id = '{{$goal->id}}'

        if (!imageProofId){
            window.scrollTo(0, 0);

            $('#approveProofImageButton').attr('disabled', false);
            $('#declineProofImageButton').attr('disabled', false);
            $('#showProofImage').modal('hide');

            $('#alert-message').html("Data of Proof Image not found");
            $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                $("#alert-danger").slideUp(500);
            });
            return;
        }

        const actionUrl = '{{route('api.admin.goals.proofs.approve', ['goal' => ':goal_id', 'goal_proof' => ':goal_proof_id'])}}'.replace(':goal_id', parseInt(goal_id)).replace(':goal_proof_id', parseInt(imageProofId));

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
                $('#approveProofImageButton').attr('disabled', false);
                $('#declineProofImageButton').attr('disabled', false);
                $('#showProofImage').modal('hide');

                $('#alert-message').html(error);
                $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                    $("#alert-danger").slideUp(500);
                });
            }
        });
    }

    function declineProofImage(){
        $('#approveProofImageButton').attr('disabled', true);
        $('#declineProofImageButton').attr('disabled', true);

        const imageProofId = $('#imageProofId').val();
        const goal_id = '{{$goal->id}}'
        const decline_reason = $('#declineProofImageReason').val();

        if (!imageProofId){
            $('html, body').animate({
                scrollTop: $("#top").offset().top
            }, 500);

            $('#approveProofImageButton').attr('disabled', false);
            $('#declineProofImageButton').attr('disabled', false);
            $('#showProofImage').modal('hide');

            $('#alert-message').html("Data of Proof Image not found");
            $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                $("#alert-danger").slideUp(500);
            });
            return;
        }

        const actionUrl = '{{route('api.admin.goals.proofs.decline', ['goal' => ':goal_id', 'goal_proof' => ':goal_proof_id'])}}'.replace(':goal_id', parseInt(goal_id)).replace(':goal_proof_id', parseInt(imageProofId));

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
                $('#approveProofImageButton').attr('disabled', false);
                $('#declineProofImageButton').attr('disabled', false);
                $('#showProofImage').modal('hide');

                $('#alert-message').html(error);
                $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                    $("#alert-danger").slideUp(500);
                });
            }
        });
    }

</script>
