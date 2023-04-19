@if(!empty($document_to_verify))
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true" id="modalID">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="card-title">
                        <h5>Verified ID</h5>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row justify-content-center text-center">
                        @if($document_to_verify->front_photo)
                            <div class="col-4">
                                <a href="#" class="pop">
                                    <img src="{{$document_to_verify->front_photo->url}}" alt="..."
                                         class="img-thumbnail">
                                </a>
                            </div>
                        @endif
                        @if($document_to_verify->back_photo)
                            <div class="col-4">
                                <a href="#" class="pop">
                                    <img src="{{$document_to_verify->back_photo->url}}" alt="..."
                                         class="img-thumbnail">
                                </a>
                            </div>
                        @endif
                        @if($document_to_verify->front_photo)
                            <div class="col-4">
                                <a href="#" class="pop">
                                    <img src="{{$document_to_verify->selfie_photo->url}}" alt="..."
                                         class="img-thumbnail">
                                </a>
                            </div>
                        @endif
                    </div>
                    <br>

                    <div class="row justify-content-center text-center">
                        @if($document_to_verify->front_photo)
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-10 offset-1">
                                        <div class="form-group">
                                            <label for="front_decline_reason">Decline Reason</label>
                                            <select class="form-control enable-select2" id="front_decline_reason"
                                                    name="front_decline_reason" form="id_document_form">
                                                <option selected disabled>Choose...</option>
                                                @foreach(\App\Utils\ReasonUtils::DECLINE_IDENTITY_DOCUMENT as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="front_action" id="front_action"
                                               form="id_document_form">
                                        <button type="submit" class="btn btn-danger btn-sm" name="front"
                                                value="rejected" id="front_rejected" onclick="setButtonId(this)">
                                            Decline
                                        </button>
                                        <br><br>
                                        <button type="submit" class="btn btn-success btn-sm" name="front"
                                                value="approved" id="front_approved" onclick="setButtonId(this)">
                                            Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($document_to_verify->back_photo)
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-10 offset-1">
                                        <div class="form-group">
                                            <label for="back_decline_reason">Decline Reason</label>
                                            <select class="form-control enable-select2" id="back_decline_reason"
                                                    name="back_decline_reason" form="id_document_form">
                                                <option selected disabled>Choose...</option>
                                                @foreach(\App\Utils\ReasonUtils::DECLINE_IDENTITY_DOCUMENT as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="back_action" id="back_action"
                                               form="id_document_form">
                                        <button type="submit" class="btn btn-danger btn-sm" name="back" value="rejected"
                                                id="back_rejected" onclick="setButtonId(this)"> Decline
                                        </button>
                                        <br><br>
                                        <button type="submit" class="btn btn-success btn-sm" name="back"
                                                value="approved" id="back_approved" onclick="setButtonId(this)"> Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($document_to_verify->front_photo)
                            <div class="col-4">
                                <div class="row">
                                    <div class="col-10 offset-1">
                                        <div class="form-group">
                                            <label for="selfie_decline_reason">Decline Reason</label>
                                            <select class="form-control enable-select2" id="selfie_decline_reason"
                                                    name="selfie_decline_reason" form="id_document_form">
                                                <option selected disabled>Choose...</option>
                                                @foreach(\App\Utils\ReasonUtils::DECLINE_IDENTITY_DOCUMENT as $key => $value)
                                                    <option value="{{$key}}">{{$value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="hidden" name="selfie_action" id="selfie_action"
                                               form="id_document_form">
                                        <button type="submit" class="btn btn-danger btn-sm" name="selfie"
                                                value="rejected" id="selfie_rejected" onclick="setButtonId(this)">
                                            Decline
                                        </button>
                                        <br><br>
                                        <button type="submit" class="btn btn-success btn-sm" name="selfie"
                                                value="approved" id="selfie_approved" onclick="setButtonId(this)">
                                            Approve
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <form action="{{route('user.edit.edit-verification-id', $user->id)}}" method="POST"
                          id="id_document_form">
                        @csrf
                        <input type="hidden" name="document_id" value="{{$document_to_verify->id}}">
                        <button type="submit" class="btn btn-secondary" id="finishBtn"> Finish</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#finishBtn').prop("disabled", true);
        })

        function setButtonId(input) {
            if (input.id.includes('rejected')) {
                $('#' + input.name + '_action').val(input.value);
                if ($('#' + input.name + '_rejected').hasClass('btn-secondary')) {
                    $('#' + input.name + '_rejected').toggleClass('btn-secondary').toggleClass('btn-danger')
                }
                if ($('#' + input.name + '_approved').hasClass('btn-success')) {
                    $('#' + input.name + '_approved').toggleClass('btn-success').toggleClass('btn-secondary');
                }
            } else if (input.id.includes('approved')) {
                $('#' + input.name + '_action').val(input.value);
                if ($('#' + input.name + '_approved').hasClass('btn-secondary')) {
                    $('#' + input.name + '_approved').toggleClass('btn-secondary').toggleClass('btn-success')
                }
                if ($('#' + input.name + '_rejected').hasClass('btn-danger')) {
                    $('#' + input.name + '_rejected').toggleClass('btn-danger').toggleClass('btn-secondary');
                }
            }

            if ($('#front_action').val() && $('#selfie_action').val() @if($document_to_verify->back_photo) && $('#back_action').val() @endif() ) {
                $('#finishBtn').addClass('btn-success').removeClass('btn-secondary').prop("disabled", false);
            }
        }
    </script>

    <div class="modal fade" id="imagemodalid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>
                    <img src="" class="imagepreview" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            $('.pop').on('click', function () {
                $('.imagepreview').attr('src', $(this).find('img').attr('src'));
                $('#imagemodalid').modal('show');
                $('#modalID').modal('hide');
            });
            $('#imagemodalid').on('hidden.bs.modal', function () {
                $('#modalID').modal('show');
            })
        });

    </script>
@endif
