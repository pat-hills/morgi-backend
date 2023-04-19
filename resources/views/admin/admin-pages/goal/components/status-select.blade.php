    <div class="row justify-content-end">
        <div class="form-group mb-2">
            <select class="form-control" name="status" id="new_status" onchange="enableUpdateButton(this)">
                <option selected disabled>Select Goal status...</option>
            </select>
        </div>
        <div class="form-group mx-sm-3 mb-2">
        </div>
        <button class="btn btn-success mb-2" onclick="updateStatus()" data-toggle="modal"
                data-target="#updateGoalStatus" id="updateButton" disabled="disabled">SAVE
        </button>
    </div>

    <script>

        let arrSelect = [];

        $(document).ready(function (){
            let select = $('#new_status');
            let current_status = '{{$status}}'.toLowerCase();

            let actions = getAllAvailableActions();
            actions.then(async function (actions_response) {
                await actions_response.forEach(await function (action) {
                    arrSelect[action.name] = {
                        value: action.name,
                        text: action.name.toUpperCase().replaceAll('_', ' '),
                        isSelected: current_status === action.name.toLowerCase(),
                        isReasonRequired: action.is_reason_required
                    }
                });

                let option;
                Object.entries(arrSelect).forEach(([key, action]) => {
                    option = $("<option>").attr('value', action.value).attr('selected', action.isSelected).text(action.text);
                    select.append(option)
                });
            })
        });

        async function getAllAvailableActions() {

            let result = {};
            await $.ajax({
                url: '{{route('api.admin.goals.available-actions')}}',
                type: 'GET',
                dataType: 'json',
                data : {
                    current_status : "{{$goal->status}}"
                },
                success: function (res) {
                    result = res;
                }
            });

            return result;
        }

        function updateStatus() {

            let action = $('#new_status').val();

            if(arrSelect[action].isReasonRequired){
                $('#updateStatusReason').show();
            }else{
                $('#updateStatusReason').hide();
                $('#declineReason').val("");
            }
        }
    </script>

    @include('admin.admin-pages.goal.components.modals.update-status', ['goal_id' => $goal->id])
