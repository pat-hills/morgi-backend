<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalChangeUsername">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-title">
                    <h5>Change username</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-4 mt-1">
                        Username
                    </div>
                    <div id="attention-mark">
                        <i class="fa fa-exclamation-circle" aria-hidden="true" style="color: red"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" id="username" name="username" value="{{$user->username}}" onchange="checkUsername(this, '{{$user->updated_username}}')" form="update_username">
                        @if($user->updated_username)
                            <small class="text-muted">NOTE: Declining the username will block the user</small>
                        @else
                            <small class="text-muted">Username must not contain spaces and must be unique</small>
                        @endif
                    </div>
                </div>
                <br>
            </div>
            <div class="modal-footer text-center" id="div_action_username"
                 @if(!$user->updated_username) style="display: none" @endif>
                <form action="{{route('user.edit.action-to-username', $user->id)}}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" value="decline" name="action">DECLINE</button>
                    <button type="submit" class="btn btn-success" value="approve" name="action">APPROVE</button>
                </form>
            </div>
            <div class="modal-footer text-center" id="div_update_username"
                 @if($user->updated_username) style="display: none" @endif>
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                <form action="{{route('user.edit.username', $user->id)}}" method="POST"
                      id="update_username">
                    @csrf
                    <button type="submit" class="btn btn-success" id="submitButton">UPDATE</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#attention-mark').hide();
    });

    function checkUsername(input, updated) {
        if (input.value !== null) {
            let string = "" + input.value + "";
            if(input.value !== '{{$user->username}}') {
                if (/\s/g.test(string)) {
                    $('#username').css('border-color', 'red');
                    $('#attention-mark').show();
                    $("#submitButton").prop('disabled', true);
                } else {
                    $('#attention-mark').hide();
                    var formData = new FormData();
                    formData.append('username', string);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': "{{csrf_token()}}"
                        },
                        type: 'POST',
                        url: '{{route('check-username')}}',
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            if (data.status === 200) {
                                $('#username').css('border-color', 'green');
                                $("#submitButton").prop('disabled', false);
                            }
                        }, error: function (data) {
                            $('#username').css('border-color', 'red');
                            $("#submitButton").prop('disabled', true);
                        }
                    });
                }
            }

            if (updated == 1){
                console.log(updated);
                if(input.value === '{{$user->username}}'){
                    $('#div_action_username').show();
                    $('#div_update_username').hide();
                }else{
                    $('#div_action_username').hide();
                    $('#div_update_username').show();
                }
            }

        }
    }
</script>
