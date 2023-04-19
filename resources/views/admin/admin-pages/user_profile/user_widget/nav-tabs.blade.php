@php
    $current_uri = url()->current();
@endphp

<nav class="mt-4" style="text-align: center">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="profileInfo" href="{{route('user.edit', $user->id)}}" role="tab">Profile
            Info</a>
        <a class="nav-item nav-link" id="activeGifts" href="{{route('user.edit.active_gifts', $user->id)}}"
           role="tab">Monthly Active <br> Gifts</a>
        <a class="nav-item nav-link" id="micromorgi" href="{{route('user.edit.micromorgi', $user->id)}}"
           role="tab">Micro <br>
            Morgi</a>

        @if($user->type == 'leader')
            <a class="nav-item nav-link" id="coupon" href="{{route('view.user.coupons', $user->id)}}"
               role="tab">Coupons</a>
            <a class="nav-item nav-link" id="transaction" href="{{route('user.edit.transactions', $user->id)}}"
               role="tab">Transactions</a>

        @endif

        <a class="nav-item nav-link" id="complaints" href="{{route('user.edit.complaints', $user->id)}}"
           role="tab">Complaints</a>
        <a class="nav-item nav-link" id="activity_log" href="{{route('user.edit.activity_log', $user->id)}}"
           role="tab">Activity <br> log</a>
        <a class="nav-item nav-link" id="cgbhistory" href="{{route('user.edit.cgb_history', $user->id)}}"
           role="tab">CGB History</a>
        @if($user->type == 'rookie')
            <a class="nav-item nav-link" id="payment_history" href="{{route('user.edit.payment-history', $user->id)}}"
               role="tab">Payment History</a>
        @endif

        <a class="nav-item nav-link" id="related_accounts"
           href="{{route('user.edit.related_account', $user->id)}}"
           role="tab">Related Accounts</a>

    </div>
</nav>

<script>
    $(document).ready(function () {
        var current_uri = "{{$current_uri}}";
        const myarr = current_uri.split("/");
        if (myarr[myarr.length - 1] === 'not_active_gifts' || myarr[myarr.length - 1] === 'active_gifts') {
            $("#activeGifts").addClass('active');
        }else if (myarr[myarr.length - 1] === 'rookie' || myarr[myarr.length - 1] === 'leader') {
            $("#related_accounts").addClass('active');
        } else {
            var nav_items = $(".nav-item").get();
            nav_items.forEach(function (item) {
                if (item.getAttribute('href') == current_uri) {
                    $("#" + item.getAttribute('id')).addClass('active');
                }
            });
        }
    })

</script>
