@php
    $current_uri = url()->current();
    $admin = New \App\Http\Controllers\Admin\UserController();
    $data = $admin->getCounterForTablesTabs('leaders');
@endphp

<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="leaders_nav_all_accounts_item" href="{{route('leaders.get')}}"
           role="tab">All Accounts ( {{$data['all']}} )</a>
        <a class="nav-item nav-link" id="leaders_nav_accepted_item" href="{{route('leaders.accepted')}}"
           role="tab">Accepted Accounts ( {{$data['accepted']}} )</a>
        <a class="nav-item nav-link" id="leaders_nav_pending_id_item" href="{{route('leaders.id_verification')}}"
           role="tab">ID to Verify ( {{$data['id']}} )</a>
        <a class="nav-item nav-link" id="leaders_nav_updated_accounts" href="{{route('leaders.updated')}}"
           role="tab">Updated Accounts ( {{$data['updated']}} )</a>
        <a class="nav-item nav-link" id="leaders_nav_blocked_id_item" href="{{route('leaders.blocked')}}"
           role="tab">Blocked Accounts ( {{$data['blocked']}} )</a>
        <a class="nav-item nav-link" id="leaders_nav_rejected_accounts" href="{{route('leaders.rejected')}}"
           role="tab">Rejected Accounts ( {{$data['reject']}} )</a>
        <a class="nav-item nav-link" id="leaders_nav_updated_account" href="{{route('leaders.updated_username')}}"
           role="tab">Updated Usernames ( {{$data['updated_username']}} )</a>
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
