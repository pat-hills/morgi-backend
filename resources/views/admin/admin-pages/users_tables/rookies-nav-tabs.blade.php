@php
    $current_uri = url()->current();
    $admin = New \App\Http\Controllers\Admin\UserController();
    $data = $admin->getCounterForTablesTabs('rookies');
@endphp
<nav class="mt-4">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="rookies_nav_all_accounts_item" href="{{route('rookies.get')}}"
           role="tab">All Accounts ( {{$data['all']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_accepted_item" href="{{route('rookies.accepted')}}"
           role="tab">Accepted Accounts ( {{$data['accepted']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_pending_id_item" href="{{route('rookies.id_verification')}}"
           role="tab">ID to Verify ( {{$data['id']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_pending_accounts_item" href="{{route('rookies.pending')}}"
           role="tab">Pending accounts ( {{$data['pending']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_new_accounts_item" href="{{route('rookies.new_accounts')}}"
           role="tab">New Accounts ( {{$data['new']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_updated_accounts_item" href="{{route('rookies.updated_accounts')}}"
           role="tab">Updated Accounts ( {{$data['updated']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_rejected_accounts_item" href="{{route('rookies.rejected')}}"
           role="tab">Rejected Accounts ( {{$data['reject']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_blocked_accounts_item" href="{{route('rookies.blocked')}}"
           role="tab">Blocked Accounts ( {{$data['blocked']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_updated_account" href="{{route('rookies.updated_username')}}"
           role="tab">Updated Usernames ( {{$data['updated_username']}} )</a>
        <a class="nav-item nav-link" id="rookies_nav_favourite_rookies" href="{{route('rookies.favourite_rookies')}}"
           role="tab">Favourite Rookies ( {{$data['favourite_rookies']}} )</a>
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
