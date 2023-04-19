@php
    $current_uri = url()->current();
@endphp

<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="reports_daily_transactions_item" href="{{route('reports.daily')}}"
           role="tab">Daily Transactions</a>
        <a class="nav-item nav-link" id="reports_one_to_one_item" href="{{route('reports.one_leader')}}"
           role="tab">Rookie with one Leader</a>
        <a class="nav-item nav-link" id="reports_inactive_rebill_item" href="{{route('reports.inactive_communications')}}"
           role="tab">Inactive com. for rebill</a>
        <a class="nav-item nav-link" id="reports_new_card_rebill_item" href="{{route('reports.new_card')}}"
           role="tab">New card details</a>
        <a class="nav-item nav-link" id="reports_status_change_rebill_item" href="{{route('reports.status_change')}}"
           role="tab">Leaders status change</a>
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
