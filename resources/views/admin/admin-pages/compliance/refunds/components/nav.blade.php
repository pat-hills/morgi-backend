<nav class="mt-4" style="text-align: center">
    <div class="nav nav-tabs">
        <a class="nav-item nav-link" id="active_pending" href="{{route('refunds.pending')}}"
           role="tab">Pending</a>
        <a class="nav-item nav-link" id="active_approved" href="{{route('refunds.approved')}}"
           role="tab">Success</a>
        <a class="nav-item nav-link" id="active_failed" href="{{route('refunds.failed')}}"
           role="tab">Failed</a>
    </div>
</nav>

<script>
    $(document).ready(function (){

        let active = '{{$status}}';
        $('#active_' + active).addClass('active');
    });
</script>
