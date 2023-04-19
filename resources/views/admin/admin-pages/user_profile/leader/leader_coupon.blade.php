@extends('admin.layout')

@section('content')

    @include('admin.admin-pages.user_profile.search-user')

    <h3 class="mt-5 mb-4">User Profile #{{$user->id}} Type <u>{{ucfirst($user->type)}}</u></h3>

    @include('admin.admin-pages.user_profile.user_widget.nav-tabs')
    <br>
    <br>

    <div class="row">
        <div class="col">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalStoreBonusCoupon" id="modalStoreBonusCouponBtn">Give
                Coupon
            </button>
        </div>
    </div>
    <br/>
    <br/>

    <table class="table" id="couponTable">
        <thead class="thead-light">
        <tr>
            <th scope="col">Date</th>
            <th scope="col">For Transaction ID</th>
            <th scope="col">Name of Rookie revoked</th>
            <th scope="col">Morgi</th>
            <th scope="col">First Coupon?</th>
            <th scope="col">Coupon ID</th>
            <th scope="col">Given By</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>

    @include('admin.admin-pages.user_profile.user_widget.modal-store-bonus-coupon')
@endsection


@section('js_after')

    <script>
        $(document).ready(function () {

            $('#couponTable').DataTable({
                "bProcessing": true,
                "serverSide": true,
                "ordering": false,
                "autoWidth": false,
                "searching": false,
                'ajax': {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    "url": "{{route('api.leader.coupon.get', ['leader' => $user->id])}}",
                    "type": "GET"
                },
                'columns': [
                    {data: 'date'},
                    {data: 'for_transaction_id'},
                    {data: 'rookie_revoked', defaultContent: '---'},
                    {data: 'morgi'},
                    {data: 'first_coupon'},
                    {data: 'coupon_id'},
                    {data: 'given_by', defaultContent: '---'}
                ]
            });
        });
    </script>
@endsection
