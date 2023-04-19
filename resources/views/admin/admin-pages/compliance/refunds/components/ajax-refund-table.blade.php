<script>

    $(document).ready(function (){

        const leaders_ids = [];
        @foreach($leaders_ids as $leader_id)
        leaders_ids.push({{$leader_id}})
        @endforeach

        $('#transactions').DataTable({
            "bProcessing": true,
            "serverSide": true,
            'ordering': false,
            "autoWidth": false,
            "bDestroy": true,
            "bFilter": false,
            "searching": true,
            'ajax': {
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                url: "{{route('api.admin.compliance.refunds')}}",
                type: "GET",
                data: {
                    status: '{{$status ?? ''}}',
                    leaders_ids: leaders_ids,
                },
            },
            'columns': [
                {data: 'biller'},
                {data: 'type'},
                {data: 'morgi'},
                {data: 'micromorgi'},
                {
                    data: "ccbill_subscriptionId",
                    render: function (data, type, row) {
                        if (data !== undefined && data !== null) {
                            return '#' + data;
                        }
                    },
                    defaultContent: ''
                },
                {
                    data: "ccbill_transactionId",
                    render: function (data, type, row) {
                        if (data !== undefined && data !== null) {
                            return '#' + data;
                        }
                    },
                    defaultContent: ''
                },
                {
                    data: "internal_id",
                    render: function (data, type, row) {
                        if (data !== undefined && data !== null) {
                            return '#' + data;
                        }
                    },
                    defaultContent: ''
                },
                {data: null, defaultContent: ''},
                {data: 'ipAddress'},
                {
                    data: "created_at",
                    render: function (data, type, row) {
                        if (data !== undefined && data !== null) {
                            let date = new Date(data);
                            return date.toDateString().split(' ').slice(1).join(' ');
                        }
                    },
                    defaultContent: ''
                },
                {
                    data: "dollars",
                    render: function (data, type, row) {
                        if (data !== undefined && data !== null) {
                            return '$' + data;
                        }
                    },
                    defaultContent: ''
                },
                {data: 'billedCurrencyCodeLabel'},
                {data: 'cardType'},
                {data: null, defaultContent: '<a href="#" data-toggle="modal" data-target="#modalRefund" type="button" class="btn">REFUND</a>'},
                {data: 'error', defaultContent: ''},
                {data: 'refunded_at', defaultContent: ''},
                {data: 'refund_type', defaultContent: ''},
                {data: 'admin.username', defaultContent: ''},
                {data: 'is_recurring'},
                {data: 'referal_internal_id'},
            ],
            "language": {
                "infoFiltered": ""
            },
            createdRow: function (row, data, dataIndex) {

                let email_column = $(row).find('td').eq(7);

                if (data['rookie'] !== null) {
                    email_column.html(data['rookie'].email);
                }

                if (data['type'] === 'bought_micromorgi') {
                    email_column.html(data['leader'].email);
                }

                const button_rules = {
                    pending: {
                        class: "btn-warning", is_disabled: true, value: 'PENDING', textColorClass: 'text-white'
                    },
                    approved: {
                        class: "btn-secondary  disabled text-white", is_disabled: true, value: 'REFUNDED', textColorClass: 'text-white'
                    },
                    failed: {
                        class: "btn-info", is_disabled: false, value: 'REFUND', textColorClass: 'text-white'
                    },
                    default: {
                        class: "btn-info", is_disabled: false, value: 'REFUND', textColorClass: 'text-black'
                    }
                }

                let rules = button_rules[data.status] ?? button_rules.default;
                let button = $(row).find('td')
                    .eq(13)
                    .find('a')
                    .attr('onClick', 'setTransactionId("' + data["id"] + '")')
                    .addClass(rules.class)
                    .html(rules.value);

                if(rules.is_disabled){
                    button.removeAttr("data-target")
                        .removeAttr("href")
                        .removeAttr('data-toggle');
                }

                $(row).find('td')
                    .eq(14)
                    .addClass('text-danger');
            }
        });

    });

    function setTransactionId(transaction_id) {
        let form_transaction_id = $('#transaction_id');
        if (!form_transaction_id.val()){
            form_transaction_id.val(transaction_id);
        }
    }
</script>
