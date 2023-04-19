@extends('admin.layout')

@section('content')

    <h4 class="mt-5 mb-5">Payments</h4>
    <h5 class="mt-5 mb-5">Payment Previous Period</h5>

    <form class="form-inline" method="POST" action="#">
        @csrf
        <div class="form-group mx-sm-3 mb-2">
            <div class="form-group mr-4">
                <label for="period">Period</label> &nbsp; &nbsp;
                <select class="form-control" id="period" name="period_id">
                    @if(array_key_exists('period_id', $data))

                        <option selected value="{{$data['period_id']}}">{{$data['period_id']}} - {{$data['period_name']}}</option>

                    @else
                        <option selected disabled>Choose...</option>

                    @endif

                    @foreach($periods as $period)
                        @if(array_key_exists('period_id', $data) && $data['period_id'] == $period->id)
                            @continue
                        @endif
                        <option value="{{$period->id}}">{{$period->id}} - {{$period->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" id="platform" name="payment_platform_id">
                    @if(array_key_exists('payment_platform_id', $data))

                        <option selected value="{{$data['payment_platform_id']}}">{{$data['payment_platform_name']}}</option>

                    @else
                        <option selected disabled>All</option>

                    @endif
                    <option value="">All</option>
                    @foreach($platforms as $platform)
                        @if(array_key_exists('payment_platform_id', $data) && $data['payment_platform_id'] == $platform->id)
                            @continue
                        @endif
                        <option value="{{$platform->id}}">{{$platform->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        &nbsp; &nbsp;
        <button type="submit" class="btn btn-primary mb-2">Search</button>
        &nbsp; &nbsp;
        @if(isset($data['payment_platform_id']) && count($payments) >= 1)
            <a id="downloadBtn" href="{{route('payments.show.download', ['id' => $payments[0]->id, 'payment_platform_id' => $data['payment_platform_id']])}}" class="btn btn-secondary mb-2">Download</a>
            <p class="mb-2" style="display: none" id="reload_page"><small>&nbsp;&nbsp;&nbsp;&nbsp;!! Reload the page after the download is complete to see if errors appears !!</small></p>
        @endif
    </form>

    <br>
    <br>
    @if($to_check)
        <div class="form-inline">

            <div class="form-group mx-sm-3">
                <label for="status_update">Status update</label> &nbsp; &nbsp;
                <select class="form-control" id="status_update" form="form_payments" name="status">
                    <option selected disabled>Choose...</option>
                    <option value="declined">Declined</option>
                </select>
            </div>
            &nbsp; &nbsp;
            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#modalNotes" onclick="beforeUpdate()">Update</a>
            &nbsp; &nbsp;&nbsp;
            <a class="btn btn-success" href="#" data-toggle="modal" data-target="#approveAllPayments">Approve all
                payments</a>
        </div>
    @else
        @if(!empty($payments))
            <h6>! All payments was checked</h6>
            @if(isset($counter_declined))
                @if($counter_declined===1)
                    <h6>{{$counter_declined}} payment is declined</h6>
                @else
                    <h6>{{$counter_declined}} payments are declined</h6>
                @endif
            @endif
        @endif
    @endif
    <br>

    <form action="{{route('user-payment-prev.reject')}}" method="POST" id="form_payments">
        @csrf
        <input type="hidden" id="action" name="action">
    <table class="table table-striped" id="prevPeriod">
        <thead>
        <tr>
            <th scope="col">PAYMENT DETAILS</th>
            <th scope="col">AMOUNT</th>
            <th scope="col">DATE</th>
            <th scope="col">CURRENCY</th>
            <th scope="col">PAYMENT ID</th>
            <th scope="col">PERIOD ID</th>
            <th scope="col">ACCOUNT STATUS</th>
            <th scope="col">USERNAME</th>
            <th scope="col">EMAIL</th>
            <th scope="col">DATE LAST EARNINGS</th>
            <th scope="col">COUNTS OF LEADERS EARNING ARE FROM</th>
            <th scope="col">TOTAL HISTORY OF CGB USD</th>
            <th scope="col">AMOUNT OF TOTAL PAYMENTS USD</th>
            <th scope="col" class="text-center">PAYEMENT STATUS</th>
            <th scope="col" class="text-center">REJECT PAYMENT</th>
            <th scope="col" class="text-center">APPROVE PAYMENT</th>
            <th scope="col">ADMIN</th>
            <th scope="col">RELATED TO EXT.ID</th>
            <th scope="col">COMMENTS</th>
        </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{"MORGI_Payment_" . $payment->payment_period_id . "_" . $payment->user_id}}</td>
                    <td>{{$payment->amount_to_pay}}</td>
                    <td>{{date('Y-m-d', strtotime($payment->transaction_created_at))}}</td>
                    <td>USD</td>
                    <td>{{$payment->id}}</td>
                    <td>{{$payment->payment_period_id}}</td>
                    <td>{{$payment->user_status}}</td>
                    <td>{{$payment->username}}</td>
                    <td>{{$payment->email}}</td>
                    <td>{{$payment->last_created_at}}</td>
                    <td>{{$payment->count_leaders}}</td>
                    <td>{{$payment->count_payments_cgb ?? 0}}</td>
                    <td>{{$payment->count_payments ?? 0}}</td>
                    <td>{{$payment->rookie_status}}</td>
                    <td class="text-center">
                    @if($payment->rookie_status === 'pending')
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{$payment->transaction_id}}"
                                           name="transaction_ids[]">
                            </div>
                    @endif
                    </td>
                    <td class="text-center">
                    @if($payment->rookie_status !== 'successful')
                            <div class="form-check">
                                <a type="button" class="btn btn-success"
                                   @if($payment->rookie_status !== 'pending')
                                   href="#" data-toggle="modal" data-target="#modalConfirmPayment" onclick="confirmPayment({{$payment->transaction_id}})"
                                   @else
                                   href="{{route('payments.approve-single-payment', $payment->transaction_id)}}"
                                    @endif
                                >APPROVE</a>
                            </div>
                    @endif
                    </td>
                    <td>{{$payment->admin_username}}</td>
                    <td>{{$payment->referal_internal_id}}</td>
                    <td>{{$payment->notes}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </form>

    {{-- Modal for Notes --}}
    <div id="modalNotes" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" style="text-align: center">
                        REASON
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="new_note">Reason*</label>
                        <select class="form-control enable-select2" id="new_note" name="reason" form="form_payments">
                            <option selected disabled>Choose...</option>
                            @foreach(\App\Utils\ReasonUtils::ROOKIE_PAYMENT_REJECT as $key => $reason)
                                <option value="{{$key}}">{{strtoupper($reason)}}</option>
                            @endforeach
                        </select>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                &nbsp;
                            </div>
                            <div class="col-6 mb-1" style="text-align: right">
                                <button type="button" class="btn" data-dismiss="modal">Cancel
                                </button>
                                <input type="submit" class="btn btn-success"
                                       onclick="document.getElementById('form_payments').submit();">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END Modal for Notes --}}

    @if(!empty($data))
    {{-- Modal for all payments --}}
    <div id="approveAllPayments" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">


                    <div class="modal-title" style="text-align: center">
                        ARE YOU SURE?
                    </div>


                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>

                <div class="modal-body">
                    <div class="form-group">
                        By clicking submit, every Rookie that you didn't mark their transaction as 'Rejected' will have his 'Pending' transaction changed to 'Approved'
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel
                    </button>
                    <form method="POST" action="{{route('payments.approve-all-payments', $data['period_id'])}}">
                        @csrf
                        @if(array_key_exists('payment_platform_id', $data))
                            <input type="hidden" name="payment_platform_id" value="{{$data['payment_platform_id']}}">
                        @endif
                        <input type="submit" class="btn btn-success" value="Submit">
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- END Modal all payments --}}
    @endif

    {{-- Modal for single payment --}}
    <div id="modalConfirmPayment" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title" style="text-align: center">
                        ARE YOU SURE?
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        The payment was already declined, are you sure do you want approve it?
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-dismiss="modal">Cancel
                    </button>
                    <a class="btn btn-success" id="approveSinglePayment" href="">Approve</a>
                </div>
            </div>
        </div>
    </div>
    {{-- END Modal for single payment --}}
@endsection


@section('js_after')
    <script>
        $(document).ready( function () {
            $('#prevPeriod').DataTable( {
                "order": [[ 2, "desc" ]]
            } );
        } );


        function setTransId(id){
            $('#transaction_id').val(id);
        }

        function beforeUpdate(){
            let action = $('#status').val();
            $('#action').val(action);
        }

        function confirmPayment(transaction_id){
            let link = "{{ route('payments.approve-single-payment', [':transaction_id']) }}".replace(':transaction_id', transaction_id);
            $('#approveSinglePayment').attr('href', link);
        }

        $( "#downloadBtn" ).click(function() {
            $('#reload_page').show();
        });

    </script>
@endsection
