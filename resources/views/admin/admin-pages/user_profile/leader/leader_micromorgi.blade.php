@extends('admin.layout')

@section('content')
    @include('admin.admin-pages.user_profile.search-user')

    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>

    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')
    <br>
    <br>

    <div class="row">
        <div class="col-1">
            &nbsp;
        </div>
        <div class="col-9">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalGiveMicroMorgi" >Give Micro Morgi Bonus</button> &nbsp;&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modalBonusHistory" style="color: blue">Bonus history</a>
        </div>
    </div>
    <br/>
    <br/>

    <table class="table" id="micromorgiTable">
        <thead class="thead-light">
        <tr>
            <th scope="col">INTERNAL ID FOR MICROMORGI</th>
            <th scope="col">DATE TIME</th>
            <th scope="col">ROOKIE GIVEN TO</th>
            <th scope="col">ACTIVITY</th>
            <th scope="col">MICROS GIVEN</th>
            <th scope="col">REFUND</th>
            <th scope="col">REASON</th>
            <th scope="col">REFUND BY</th>
            <th scope="col">REFUND DATE</th>
            <th scope="col">RELATED INTERNAL ID</th>
            <th scope="col">NOTES</th>
            <th scope="col">ADMIN NOTES</th>

        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

    @include('admin.admin-pages.user_profile.user_widget.modal-add-micromorgi')
    @include('admin.admin-pages.user_profile.user_widget.modal-refund-micromorgi')
    @include('admin.admin-pages.user_profile.user_widget.modal-micromorgi-bonus-history')
@endsection

@section('js_after')

    <script>
        $(document).ready(function () {
            $('#micromorgiTable').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "autoWidth": false,
                "order": [[1, "desc"]],
                'ajax': {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    "url": '{{route('api.admin.user.transaction.micromorgi.get', [$user->id])}}',
                },
                'columns': [
                    {
                        data: null, render: function (response) {
                            return '#'.concat('', response.internal_id)
                        }
                    },
                    {
                        data: null, render: function (response) {
                            return response.datetime;
                        }
                    },
                    {
                        data: null, render: function (response) {
                            let result = '';
                            if (response.rookie) {
                                result = response.rookie.username;
                            }
                            return result;
                        }
                    },
                    {
                        data: null, render: function (response) {
                            let activity;
                            activity = response.type.replace('_', ' ');
                            if (response.transaction_refunded) {
                                activity = activity.concat(" - ", response.transaction_refunded.type)
                            }
                            return ucFirst(activity);
                        }
                    },
                    {
                        data: null, render: function (response) {
                            return response.micromorgi;
                        }
                    },
                    {
                        data: null, render: function (response) {
                            let buttonStatus = null;
                            if (response.refund_type) {
                                buttonStatus = 'refunded';
                            }
                            if (response.type === 'refund') {
                                buttonStatus = 'refunded';
                            }
                            if (response.refund_history) {
                                buttonStatus = response.refund_history.status
                            }
                            return getTransactionRefundButton(response, buttonStatus).prop('outerHTML');
                        }
                    },
                    {
                        data: null, render: function (response) {
                            let notes = '';
                            if (response.type === 'refund') {
                                notes = response.notes;
                            }
                            return notes
                        }
                    },
                    {
                        data: null, render: function (response) {
                            let refunded_by = '';
                            if (response.refunded_by_admin) {
                                refunded_by = response.refunded_by_admin.username;
                            }
                            return refunded_by;
                        }
                    },
                    {
                        data: null, render: function (response) {
                            return response.refunded_datetime;
                        }
                    },
                    {
                        data: null, render: function (response) {
                            return (response.referal_internal_id) ? '#'.concat('', response.referal_internal_id) : '';
                        }
                    },
                    {
                        data: null, render: function (response) {
                            return (response.notes) ? response.notes : '';
                        }
                    },
                    {
                        data: null, render: function (response) {
                            return (response.admin_description) ? response.admin_description : '';
                        }
                    },
                ],
            });
        });

        function getButtonRule(transactionStatus) {
            const buttonRules = {
                pending: {
                    class: "btn-warning",
                    disabled: true,
                    value: 'PENDING',
                    textColorClass: 'text-white',
                    showRefundModal: false
                },
                approved: {
                    class: "btn-secondary  disabled text-white",
                    disabled: true,
                    value: 'REFUNDED',
                    textColorClass: 'text-white',
                    showRefundModal: false
                },
                failed: {
                    class: "btn-primary",
                    disabled: false,
                    value: 'REFUND',
                    textColorClass: 'text-white',
                    showRefundModal: true
                },
                refunded: {
                    class: "btn-light  disabled",
                    disabled: true,
                    value: 'REFUNDED',
                    textColorClass: 'text-black',
                    showRefundModal: false
                },
                default: {
                    class: "btn-primary",
                    disabled: false,
                    value: 'REFUND',
                    textColorClass: 'text-white',
                    showRefundModal: true
                },
            }

            return buttonRules[transactionStatus] ?? buttonRules.default
        }

        function setRefundTransactionId(tag) {
            $('#transaction_id').val($(tag).attr('data-transaction-id'));
        }

        function canBeRefunded(transaction_status) {
            switch (transaction_status) {
                case null:
                case undefined:
                case 'failed':
                    return true
                default:
                    return false;
            }
        }

        function getTransactionRefundButton(transaction, transactionStatus) {

            let overrideTextButton = null;
            if(transaction.refund_type === null && transaction.goal_id && transaction.goal && transaction.goal.status !== 'successful'){
                switch (transaction.goal.status) {
                    case 'cancelled':
                        transactionStatus = 'refunded';
                        overrideTextButton = 'Goal cancelled'
                        break;
                    default:
                        transactionStatus = null;
                        overrideTextButton = 'REFUND';
                        break;
                }
            }

            let refundButtonRule = getButtonRule(transactionStatus);
            let button = $('<a></a>').addClass('btn')
                .addClass(refundButtonRule.class)
                .addClass(refundButtonRule.textColorClass)
                .attr('data-transaction-id', transaction.id)
                .attr('data-transaction-status-refund', transactionStatus)
                .attr('role', 'button')
                .html(overrideTextButton ?? refundButtonRule.value);

            if (refundButtonRule.disabled) {
                button.addClass('disabled').attr('aria-disabled', refundButtonRule.disabled)
            }

            if (canBeRefunded(transactionStatus)) {
                button.attr('data-toggle', 'modal')
                    .attr('data-target', '#modalRefund')
                    .attr('onclick', 'setRefundTransactionId(this)')
            }
            return button;
        }

        function ucFirst(str) {
            let firstLetter = str.substr(0, 1);
            return firstLetter.toUpperCase() + str.substr(1);
        }
    </script>
@endsection

