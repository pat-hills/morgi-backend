@extends('admin.layout')

@section('content')

    <br>
    <div class="row">
        <div class="col-4 offset-4">
            <h3>Search User</h3>
        </div>
    </div>
    <br>
    <div class="row text-center">
        <div class="col-8 offset-2" >
            <form action="{{route('user.index')}}" method="GET" id="formSearch">
                <div class="row">
                    <div class="col-8">
                        <input type="text" class="form-control" id="data" name="data" @if(!empty($data)) value="{{$data}}" @endif placeholder="Search by ID, First Name, Username or Email">

                    </div>
                    <div class="col-4">
                        <input type="submit" id="submitBtnSearch" class="btn btn-primary mb-2" value="Search">
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


