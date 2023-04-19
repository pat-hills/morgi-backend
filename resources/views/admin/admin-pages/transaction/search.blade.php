@extends('admin.layout')

@section('content')

    <br>
    <div class="row">
        <div class="col-4 offset-4">
            <h3>Search Transaction</h3>
        </div>
    </div>
    <br>
    <div class="row text-center">
        <div class="col-8 offset-2" >
            <form action="{{route('transaction.search')}}" method="GET" id="formSearchBar">
                <div class="row">
                    <div class="col-8">
                        <input type="text" class="form-control" id="data" name="data" placeholder="Search by ccbill subscriptionID or transactionID">
                    </div>
                    <div class="col-4">
                        <input type="submit" class="btn btn-primary mb-2" value="Search">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $('#formSearch input').keyup(function(event) {
            if (event.code === "Enter")
            {
                console.log($(this).val())
                $('#formSearch').submit();
            }
        });

    </script>

@endsection

@section('js_after')

    <script>
        $( document ).ready(function() {
            console.log( $('#data').val() );
        });
    </script>

@endsection


