<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="modalEditBirthDate">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="card-title">
                    <h5>Update Birthday</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-4 mt-1">
                        Birth Date
                    </div>
                    <div class="col-4">
                        <input class="form-control datepicker" type="text" id="birthdate" name="birthdate"
                               autocomplete="off" value="{{$user->birth_date}}" form="birth_date_form">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-4">
                        Updated age
                    </div>
                    <div class="col-4">
                        <label id="updated-age">{{$user->age}}</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" class="btn" data-dismiss="modal">Cancel</button>
                <form action="{{route('user.edit.birth-date', $user->id)}}" method="POST"
                      id="birth_date_form">
                    @csrf
                    <button type="submit" class="btn btn-success"> UPDATE</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    $(function () {

        $('#birthdate').datepicker({
            format: "yyyy-mm-dd",
            todayHighlight: 'TRUE',
            autocomplete: false,
            autoclose: true,
            endDate: '-18y'
        }).on('changeDate', function (ev) {
            var start = $("#birthdate").val();
            var startD = new Date(start);
            var end = new Date();
            var age = calculateAge(startD, end);

            $('#updated-age').text(age);

        });

    });

    function calculateAge(birthDate, otherDate) {
        birthDate = new Date(birthDate);
        otherDate = new Date(otherDate);

        var years = (otherDate.getFullYear() - birthDate.getFullYear());

        if (otherDate.getMonth() < birthDate.getMonth() ||
            otherDate.getMonth() === birthDate.getMonth() && otherDate.getDate() < birthDate.getDate()) {
            years--;
        }

        return years;
    }

</script>

