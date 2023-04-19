<span>Status: <span id="status">{{ucfirst(str_replace('_', ' ', $status))}}</span></span>
<div id="cancelledOrDeclinedBy" style="display: none">
</div>

<script>
    $(document).ready(function () {
        let status = '{{$status}}'.toLowerCase();
        let classColor;
        switch (status) {
            case 'proof_pending_approval':
                classColor = 'text-warning';
                break;
            case 'cancelled':
            case 'awaiting_proof':
                classColor = 'text-secondary';
                break;
            case 'active':
                classColor = 'text-primary';
                break;
            case 'successful':
                classColor = 'text-success';
                break;
            case 'proof_status_declined':
                classColor = 'text-danger';
                break;
        }

        $('#status').addClass(classColor);
    });
</script>
