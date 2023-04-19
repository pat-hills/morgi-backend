@php
    $current_uri = url()->current();
@endphp

<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="report_info_nav_item" href="{{route('complaints.edit.get', $complaint->id)}}" role="tab" >Report Info</a>
        <a class="nav-item nav-link" id="complaint_history_nav_item" href="{{route('complaints.show-history.get', $complaint->id)}}" role="tab" >Complaint History</a>
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
