@extends('admin.layout')

@php
    $today = new DateTime();
@endphp

@section('content')

    <h4 class="mt-5 mb-5">Compliance</h4>
    <h5 class="mt-5 mb-5">ID BIG SPENDER REPORT</h5>

    <br>

    <table class="table table-striped" id="compliance">
        <thead>
        <tr>
            <th scope="col">EMAIL</th>
            <th scope="col">TIME AWAITING</th>
            <th scope="col">ACTION</th>

        </tr>
        </thead>
        <tbody>
            @foreach($users as $user)

                <tr>
                    <td>{{$user->email}}</td>
                    <td>

                        @if(!is_null($user->document_id_at))
                            @php
                                $origin = new DateTime($user->document_id_at);
                                $interval = $origin->diff($today);
                            @endphp
                            {{App\Utils\Utils::formatInterval($interval)}}
                        @endif
                    </td>
                    <td><a class="btn btn-info" href="{{route('user.edit', $user->id)}}">ID APPROVAL REQUEST</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection


@section('js_after')

    <script>
        $(document).ready( function () {
            $('#compliance').DataTable( {
                "order": [[ 1, "desc" ]]
            } );
        } );
    </script>
@endsection
