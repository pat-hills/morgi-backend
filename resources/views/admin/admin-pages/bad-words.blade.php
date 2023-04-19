@extends('admin.layout')

@section('content')

    <h3>Bad Words</h3>
    <br>
    <br>


    <div class="row">
        <div class="col-1">

        </div>
        <div class="col">
            <form class="form-inline" method="POST" action="{{route('add.bad.words.post')}}">
                @csrf
                <div class="form-group mx-sm-3 mb-2">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Bad Word">
                </div>
                <button type="submit" class="btn btn-success mb-2">Add</button>
            </form>
        </div>

    </div>
    <br>
    <br>
    <br>
    <div class="container">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Word</th>
                <th style="width: 30%"></th>

            </tr>
            </thead>
            <tbody>
            @php($counter = 1)
            @foreach($bad_words as $word)
                <tr>
                    <th scope="row">{{$counter}}</th>
                    <td>{{$word->name}}</td>
                    <th>
                        <form method="POST" action="{{route('add.bad.words.delete')}}">
                            @csrf
                            <input type="hidden" name="word_id" value="{{$word->id}}">
                            <input type="submit" class="btn btn-sm btn-danger" value="Remove">
                        </form>

                    </th>
                </tr>
                @php($counter++)
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
