@extends('admin.layout')
@section('content')
    <br>
    <form method="POST" action="{{route('uploadMicromorgiBonus.post')}}"
          enctype="multipart/form-data">
        @csrf
            <div class="form-inline">
                <h4 class="mt-2">Upload CSV file</h4>
                &nbsp; &nbsp;
                <label for="file" class="btn btn-info">
                    Select file
                </label>
                <input id="file" type="file" name="file" style="display: none" onchange="checkFile()" accept=".csv"/>
                &nbsp; &nbsp;
                <input type="submit" class="btn btn-success" value="Upload">
                &nbsp; &nbsp;
                <p class="mt-3"><small id="upload-status"></small></p>
            </div>
    </form>
    <br>
    <br>
    <br>

    @if(isset($fails) && isset($good))

        <h5>{{$good}} BONUS APPROVED AND SEND</h5>
        <br>

        <h5>ALL {{count($fails)}} ERRORS</h5>
        @if(!empty($fails))

            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">EMAIL</th>
                    <th scope="col" style="width: 10%">AMOUNT</th>
                    <th scope="col">REASON</th>
                    <th scope="col" style="width: 20%">ERROR</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $counter = 1;
                @endphp
                @foreach($fails as $key => $user)
                    <tr>
                        <th scope="row">{{$counter}}</th>
                        <td>{{$user['email']}}</td>
                        <td>{{$user['amount']}}</td>
                        <td>{{$user['reason']}}</td>

                        @if(array_key_exists('extra_error', $user))
                            <td>{{$user['extra_error']}}</td>
                        @else
                            <td>{{$user['result']}}</td>
                        @endif
                    </tr>
                    @php
                        $counter++;
                    @endphp
                @endforeach
                </tbody>
            </table>

            @endif

    @elseif(!empty($users))
        <form action="{{route('test-route')}}" method="POST">
            @csrf
        <div class="row justify-content-end">
            <div class="col-4 text-right">
                <input class="btn btn-success" type="submit" value="SEND BONUS">
            </div>
        </div>
        <br>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">EMAIL</th>
                <th scope="col" style="width: 10%">AMOUNT</th>
                <th scope="col">REASON</th>
                <th scope="col" style="width: 20%">SUCCESS/FAIL</th>
            </tr>
            </thead>
            <tbody>
            @php
            $counter = 1;
            @endphp
            @foreach($users as $key => $user)
                <tr>
                    <th scope="row">{{$counter}}</th>
                    <td><input type="text" class="form-control" value="{{$user['email']}}" id="email{{$counter}}" name="users[{{$counter}}][email]" onchange="doubleCheck('{{$counter}}')"></td>
                    <td><input type="text" class="form-control" value="{{$user['amount']}}" id="amount{{$counter}}" name="users[{{$counter}}][amount]" onchange="doubleCheck('{{$counter}}')"></td>
                    <td>
                        <input type="hidden" name="users[{{$counter}}][reason]" value="{{$user['reason']}}">
                        {{$user['reason']}}
                    </td>
                    <input type="hidden" id="result{{$counter}}" name="users[{{$counter}}][result]" value="{{$user['result']}}">
                    <input type="hidden" id="user_id{{$counter}}" name="users[{{$counter}}][user_id]" value="{{$user['id']}}">
                    <td id="result{{$counter}}text">
                    @if($user['result'] === 200)
                            Success
                        @else
                            {{$user['result']}}
                        @endif
                    </td>
                </tr>
                @php
                    $counter++;
                @endphp
            @endforeach
            </tbody>
        </table>
        </form>
    @endif
@endsection

@section('js_after')

    <script>
        function doubleCheck(key){
            let email = $('#email'+key).val();
            let amount = $('#amount'+key).val();
            $.ajax({
                type: "POST",
                url: '{{route('double-check.post')}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'email' : email,
                    'amount' : amount
                },
                success: function (data) {
                    let parsed_data = JSON.parse(data);
                    $('#result' + key + 'text').text('Success');
                    $('#result' + key).val(200);
                    $('#user_id' + key).val(parsed_data.id);
                },
                error: function (data) {
                    $('#result' + key + 'text').text(data.responseJSON.message);
                    $('#result' + key).val(data.responseJSON.message);
                    $('#user_id' + key).val('');
                }
            });
        }

        function checkFile(){
            if ($('#file').get(0).files.length === 0) {
                console.log("No files selected.");
            }else {
               $('#upload-status').text('File selected: '+ $('#file').get(0).files[0].name);
            }
        }
    </script>

    @endsection
