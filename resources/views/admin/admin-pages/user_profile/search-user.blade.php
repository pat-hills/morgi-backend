<form action="{{route('user.index')}}" method="GET" id="formSearchBar">
    <div class="row">
        <div class="col-4">
            <input type="text" class="form-control" id="data" name="data" @if(!empty($data)) value="{{$data}}" @endif placeholder="Search by ID, Full Name, Username or Email">

        </div>
        <div class="col-4">
            <input type="submit" class="btn btn-primary mb-2" value="Search">
        </div>
    </div>
</form>

