{{-- Modal for Give Micro Morgi --}}
<div id="modalStoreBonusCoupon" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">Give Morgi bonus coupon</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('leader.coupon.store', $user->id)}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="form-group row">
                        <label for="currency_value" class="col-sm-4 col-form-label">AMOUNT*</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="currency_value" name="subscription_packages_id">

                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reason" class="col-sm-4 col-form-label">BONUS REASON*</label>
                        <div class="col-sm-8">
                            <select class="form-control enable-select2" id="reason" name="reason">
                                <option selected disabled>
                                    FREE TEXT
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="comments" class="col-sm-4 col-form-label">COMMENT</label>
                        <div class="col-sm-8">
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="giveCoupon">Give Coupon</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
{{-- END Modal for Give Micro Morgi --}}

<script>

    $( "#modalStoreBonusCouponBtn" ).on('click', function(){
        fillAmountDropdown();
    });

    function fillAmountDropdown() {
        $.ajax({
            type: "GET",
            url: "{{route('api.subscription.packages.get')}}",
            success: function (data) {
                $.each(data, function () {
                    $("#currency_value").append($("<option/>").val(this.id).text(this.amount));
                });
            },
            error: function () {
                $('#giveCoupon').prop('disabled', true);
            }
        });
    }
</script>
