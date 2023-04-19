@php
    $current_uri = url()->current();

@endphp

<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="all_complaints" href="{{route('complaints.get')}}"
           role="tab">All Complaints</a>
        <a class="nav-item nav-link" id="open_complaints" href="{{route('complaints.open.get')}}"
           role="tab">Open Complaints</a>
        <a class="nav-item nav-link" id="closed" href="{{route('complaints.closed.get')}}"
           role="tab">Closed Complaints</a>
        <a class="nav-item nav-link" id="follow_up" href="{{route('complaints.follow-up.get')}}"
           role="tab">Follow Up</a>
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
