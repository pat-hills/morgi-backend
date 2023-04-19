@extends('admin.layout')


@section('content')


    <h4 class="mt-5 mb-5"><a href="{{route('payments')}}"><i class="fas fa-arrow-left"></i> Payments</a></h4>
    <br>


    @include('admin.admin-pages.payments.nav-tabs')
    <div class="row">
        <div class="col-4">
            <h5>Details</h5>
            <div class="row">
                <div class="col-5">
                    ID
                </div>
                <div class="col-6">
                    <strong>{{$payment->id}}</strong>
                </div>
            </div>
            <div class="row">
                <div class="col-5">
                    Platform
                </div>
                <div class="col-6">
                    <strong>{{$payment->platform_name}}</strong>
                </div>

            </div>
            <div class="row">
                <div class="col-5">
                    Period
                </div>
                <div class="col-6">
                    <strong>{{$payment->period_name}}</strong>
                </div>

            </div>
        </div>
        <div class="col-4">

        </div>
        <div class="col-4" style="text-align: right">
            <a href="{{route('payments.show.download', ['id' => $payment->id, 'payment_platform_id' => $payment->payment_platform_id])}}" class="btn btn-secondary" >Download</a>
        </div>
    </div>

    <br>
    <br>
    <br>
    <div class="container-lg">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="col-10">

                    </div>
                    <div class="col-2">
                        <div class="form-group">
                            <label for="status_update">Status update</label>
                            <select class="form-control" id="status_update" form="payment_form" name="status">
                                <option selected disabled>Choose...</option>
                                <option value="successful">Successful</option>
                                <option value="declined">Declined</option>
                                <option value="returned">Returned</option>
                            </select>
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary" form="payment_form">Update</button>
                        </div>
                    </div>
                </div>
                <form action="#" method="POST" id="payment_form">
                    @csrf
                    <table class="table table-striped" id="payment">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">ID ROOKIE</th>
                            <th scope="col">NAME</th>
                            <th scope="col">REFERENCE</th>
                            <th scope="col">AMOUNT</th>
                            <th scope="col">STATUS</th>
                            {{--                        <th scope="col"></th>--}}


                        </tr>
                        </thead>

                        <tbody>
                        @csrf
                        @foreach($rookies as $rookie)
                            <tr>
                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{$rookie->payment_id}}"
                                               name="payments[]">
                                    </div>
                                </td>
                                <td>#{{$rookie->rookie_id}}</td>
                                <td>{{$rookie->first_name}} {{$rookie->last_name}}</td>
                                <td>{{$rookie->reference}}</td>
                                <td>${{$rookie->amount}}</td>
                                <td>
                                <span
                                    style="color: {{\App\Utils\Utils::getPaymentColor($rookie->status, true)}}">{{strtoupper($rookie->status)}}</span>
                                    <br>
                                    <small>{{$rookie->updated_at}}</small>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js_after')
    <script>
        $(document).ready(function () {
            $('#payment').DataTable({
                pageLength: 25,
            });
        });
    </script>
@endsection

