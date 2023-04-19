@php
    $current_uri = url()->current();
@endphp

<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="transactions_refunds_item" href="{{route('transactions_refunds')}}"
           role="tab">All</a>
        <a class="nav-item nav-link" id="transactions_chargeback_item" href="{{route('transactions_refunds.chargeback')}}"
           role="tab">Chargebacks</a>
        <a class="nav-item nav-link" id="transactions_refund_biller_item" href="{{route('transactions_refunds.refund_biller')}}"
           role="tab">Refund by Biller</a>
        <a class="nav-item nav-link" id="transactions_refund_admin_item" href="{{route('transactions_refunds.refund_admin')}}"
           role="tab">Refund by Admin</a>
        <a class="nav-item nav-link" id="transactions_refund_void_item" href="{{route('transactions_refunds.void')}}"
           role="tab">Void</a>
        <a class="nav-item nav-link" id="transactions_rebill_declined_item" href="{{route('transactions_refunds.rebill_declined')}}"
           role="tab">Rebill declined</a>
    </div>
</nav>
<script>
    $(document).ready(function (){
        var current_uri = "{{$current_uri}}";
        var nav_items = $(".nav-item").get();
        nav_items.forEach(function(item) {
            if (item.getAttribute('href') == current_uri){
                $("#"+item.getAttribute('id')).addClass('active');
            }
        });
    })

</script>
