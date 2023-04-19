@extends('admin.layout')

@section('content')

    <h4 class="mt-5 mb-5">Compliance</h4>
    <h5 class="mt-5 mb-5">Refunds</h5>

    <br>
    @include('admin.admin-pages.compliance.refunds.components.nav', ['status' => 'pending'])
    <br>
    <br>
    @include('admin.admin-pages.compliance.refunds.components.refund-table')
    @include('admin.admin-pages.compliance.refunds.components.ajax-refund-table', ['status' => 'pending'])

@endsection
