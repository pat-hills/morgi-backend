<div id="updateGoalStatus" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" style="text-align: center">
                    ARE YOU SURE?
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group" id="updateStatusReason" style="display: none">
                    <div class="row">
                        <div class="col-3">
                            <label for="declineReason" class="col-sm-4 col-form-label">REASON*</label>
                        </div>
                        <div class="col-9 mt-1">
                            <select class="form-control enable-select2" id="declineGoalReason">
                                <option selected disabled>...</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" onclick="updateGoal()" id="goalSubmitButton">Update
                </button>
            </div>
        </div>
    </div>
</div>

<script>

    function updateGoal() {
        $('#goalSubmitButton').attr('disabled', true);

        const goal_id = '{{$goal_id}}';
        console.log(goal_id);
        const status = $('#new_status').val();
        const reason = $('#declineGoalReason').val();

        const actionUrl = '{{route('api.admin.goals.update-status', ['goal' => ':goal_id'])}}'.replace(':goal_id', goal_id);

        $.ajax({
            type: "POST",
            url: actionUrl,
            dataType: 'json',
            data: {
                '_token': '{{ csrf_token() }}',
                'action': status,
                'decline_reason': reason
            },
            success: function (data) {
                location.reload();
            },
            error: function (data) {
                let error = data.responseJSON.message;
                $('#goalSubmitButton').attr('disabled', false);
                $('#updateGoalStatus').modal('hide');

                $('#alert-message').html(error);
                $("#alert-danger").fadeTo(8000, 500).slideUp(500, function () {
                    $("#alert-danger").slideUp(500);
                });
            }
        });

    }

</script>
