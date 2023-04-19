<div id="showVideo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>
            <form action="" method="POST" id="form_decline_video">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <video width="450" height="300" controls id="modalVideo">
                                <source src="" type="video/mp4">
                            </video>
                        </div>
                    </div>
                    <br>
                </div>
            </form>
        </div>
    </div>
</div>
