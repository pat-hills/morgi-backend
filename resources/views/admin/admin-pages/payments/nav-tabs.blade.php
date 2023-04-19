{{--
@php
    $current_uri = url()->current();
@endphp



<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="payments_nav_item" href="{{route('payments')}}" role="tab" >All</a>
        <a class="nav-item nav-link" id="merch_requests_to_sent_nav_item" href="" role="tab" >TEST</a>
        <a class="nav-item nav-link" id="merch_requests_pending_nav_item" href="" role="tab" >TEST</a>
        <a class="nav-item nav-link" id="merch_requests_in_elaboration_nav_item" href="" role="tab">TEST</a>
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
--}}
