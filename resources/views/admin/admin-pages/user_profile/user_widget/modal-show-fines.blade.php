<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     aria-hidden="true" id="modalShowFines">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4>FINES</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="fine_table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Given By</th>
                        <th scope="col">Reason</th>
                        <th scope="col">Type</th>
                        <th scope="col">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function setFines(){
        $.ajax({
            url : '{{route('api.get-fines')}}',
            type : 'GET',
            data : {
                'user_id' : {{$user->id}},
                'type' : "{{ $fine_type }}"
            },
            dataType:'json',
            success : function(data) {
                let loop = 1;
                data.forEach((element) => {
                    $('#fine_table tbody').append("<tr><td>" + loop + "</td><td>" + element.given_by + "</td><td>" + element.reason + "</td><td>{{ucfirst($fine_type)}}</td><td> -" + element.morgi + "</td></tr>");
                    loop++;
                })
            },
            error : function(request,error)
            {
                console.log(request)
            }
        });
    }
</script>
