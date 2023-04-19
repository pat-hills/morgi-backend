@extends('admin.layout')

@section('content')
    <br>
    <h3>Channel's Settings</h3>
    <br>
    <br>
    <h4>Who can see Media Attachments</h4>
    <br>
    <div class="row" id="mediaAttachmentsOptions">
    </div>
    <br>
    <br>
    <h4>Converters Carousel Locations</h4>
    <br>
    <div>
        <div class="row">
            <div class="col-1">

            </div>
            <div class="col" id="convertersCarouselLocations">

            </div>
        </div>
        <div class="row">
            <div class="col-12 text-right" id="convertersCarouselLocationsDiv" style="display: none">
                <button class="btn btn-success" onclick="saveConverterCarouselLocations()">SAVE</button>
            </div>
        </div>
    </div>
    <br>
    <br>
    <h4>Converter's Order</h4>
    <br>
    <div class="row" id="converterOrder">
    </div>
    <div id="customCarousel" style="display: none">
        <div class="container">
            <div class="row">
                <div class="col-8" id="converterPositionList">
                    <div class="row">
                        <div class="col-6">
                            <label>CONVERTER</label>
                        </div>
                        <div class="col-4">
                            <label>CAROUSEL POSITION</label>
                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="display: none" id="saveConverterCarouselPositionDiv">
        <div class="col text-right">
            <button class="btn btn-success" onclick="saveConverterCarouselPosition()">SAVE</button>
        </div>
    </div>
    <br>
    <br>
    <h3>Carousel settings</h3>
    <br>
    <br>
    <h4>U/I for new Friends</h4>
    <br>
    <div class="row" id="carousel_settings">
    </div>
    <div class="row" style="display: none" id="saveCarouselSettingsDiv">
        <div class="col text-right">
            <button class="btn btn-success" onclick="saveCarouselSetting()">SAVE</button>
        </div></div>


@endsection

@section('js_after')
    <script>

        let carousel_load = false;
        let current_setting_order;
        let available_carousel_position;
        let carousel_types;

        $(document).ready(function () {

            mediaAttachments();

            getCurrentCarouselConvertersLocations();

            convertersCarouselLocations();

            converterOrder();

            getCarouselSettingsTypes();

            carouselSettingsTypes();
        });

        function saveCarouselSetting() {
            let carouselSettingType = $('input[name=carouselSettingOption]:checked').val();

            $.ajax({
                url: '{{route('api.admin.carousel-settings.update.patch')}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    'carousel_type' : carouselSettingType
                },
                type: 'PATCH',
                success: function (response) {
                    showSuccessMessage('Channels settings updated!');
                },
                error: function (response) {
                    showErrorMessage(response.responseJSON.message);
                }
            });
        }

        function carouselSettingsTypes() {
            $('#carousel_settings').append('<div class="col"></div>')
            carousel_types.forEach(function (carousel_setting) {

                let input = '<input class="form-check-input" type="radio" name="carouselSettingOption" id=":carouselSettingId" value=":carouselSettingValue" >'.replace(':carouselSettingId', carousel_setting.id).replace(':carouselSettingValue', carousel_setting.type);
                input = $(input)

                if (carousel_setting.is_active === 1) {
                    input.attr('checked', 'checked')
                }

                let label = '<label class="form-check-label" for=":carouselSettingId">:label_value</label>'.replace(':carouselSettingId', carousel_setting.id).replace(':label_value', carousel_setting.type.toUpperCase());
                label = $(label)
                let col = $('<div class="col"></div>')
                    .append(input)
                    .append(label);
                $('#carousel_settings').append($(col));
            })
            $('#saveCarouselSettingsDiv').show();
        }

       function getCarouselSettingsTypes() {
            $.ajax({
                url: '{{route('api.admin.carousel-settings.index.get')}}',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "GET",
                success: function (data) {
                    carousel_types = data;
                }
            });
        }

        function mediaAttachments() {
            $.ajax({
                url: '{{route('api.admin.pubnub.channels-settings.get')}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    $('#mediaAttachmentsOptions').append('<div class="col"></div>')
                    data.forEach(function (element) {

                        let media_id = 'media_' + element.id;
                        let input = '<input class="form-check-input" type="radio" name="mediaAttachmentOption" id=":media_id" value=":media_value">'.replace(':media_id', media_id).replace(':media_value', element.id);
                        input = $(input)
                        if (element.is_active) {
                            input.attr('checked', 'checked')
                        }
                        let label = '<label class="form-check-label" for=":media_id">:label_value</label>'.replace(':media_id', media_id).replace(':label_value', element.type.toUpperCase().replace('_', ' '));
                        label = $(label)
                        let col = $('<div class="col"></div>')
                            .append(input)
                            .append(label);
                        $('#mediaAttachmentsOptions').append($(col));
                    })

                    let saveButton = '<button class="btn btn-success" onclick="saveMediaAttachmentOption()">SAVE</button>'
                    $('#mediaAttachmentsOptions').append($('<div class="col text-right"></div>').append($(saveButton)));
                }
            });
        }

        function saveMediaAttachmentOption() {
            let pubnub_channel_setting_id = $("input[name='mediaAttachmentOption']:checked").val();
            $.ajax({
                url: '{{route('api.admin.pubnub.channels-settings.update.post', [':pubnub_channel_setting_id'])}}'.replace(':pubnub_channel_setting_id', pubnub_channel_setting_id),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                success: function (response) {
                    showSuccessMessage('Channels settings updated!');
                },
                error: function (response) {
                    showErrorMessage(response.responseJSON.message);
                }
            });
        }

        function selectSystemOrder() {
            switch ($('input[name=converterOrderOption]:checked').val()) {
                case 'custom':
                    customSystemPosition();
                    break;
                case 'randomly':
                    randomlySystemOrder();
                    break;
            }
        }

        function customSystemPosition() {
            $('#customCarousel').show();
            if (carousel_load === true) {
                return;
            }

            $.ajax({
                url: '{{route('api.admin.rookie.converters.get')}}',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "GET",
                success: function (data) {
                    data.forEach(function (rookie) {
                        let converter = '<div class="col-6"><input type="hidden" name="converters_ids[]" value="' + rookie.id + '">:label_value</div>'.replace(':label_value', rookie['full_name']);
                        let select_id = 'select_' + rookie.id;
                        let select = '<select id=":select_id" onchange="checkCarouselPositions(this)" name="selectCarouselPosition"></select>'.replace(':select_id', select_id);
                        select = $(select);
                        select.append('<option value="" selected>NULL</option>');
                        available_carousel_position.forEach(function (position) {
                            let option = '<option value=":position_id">:label_value</option>'.replace(':position_id', position.id).replace(':label_value', position.position)
                            $(select).append($(option));
                        });
                        if (rookie.converter_carousel_position_id) {
                            $(select).val(rookie.converter_carousel_position_id).change();
                        }
                        let input_select = $('<div class="col-4"></div>').append(select);
                        let col = $('<div class="row"></div>')
                            .append($(converter))
                            .append(input_select)
                        $('#converterPositionList').append(col).append('<br>');
                    })

                    carousel_load = true;
                },
                error: function (response) {
                    showErrorMessage(response.responseJSON.message);
                    location.reload();
                }
            });
        }

        function saveConverterCarouselPosition() {
            let converterPositionType = $('input[name=converterOrderOption]:checked').val();

            $.ajax({
                url: '{{route('api.admin.system-settings.update-current.post', [':current-position-id'])}}'.replace(':current-position-id', current_setting_order.id),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "POST",
                data: {
                    converters_carousel_order: converterPositionType,
                },
                dataType: "JSON",
                success: function (data) {
                    showSuccessMessage('System order updated!');
                },
                error: function (data) {
                    showErrorMessage(data.responseJSON.message);
                }
            });

            switch (converterPositionType) {
                case 'custom':
                    var converters = $('input[name="converters_ids[]"]').map(function () {
                        return this.value;
                    }).get();

                    let convertersPositions = []
                    converters.forEach(function (converter_id) {
                        let position_value = $('#select_' + converter_id).val();
                        convertersPositions.push({
                            user_id: converter_id,
                            position_id: position_value
                        })
                    });

                    $.ajax({
                        url: '{{route('api.admin.rookie.converters.update-carousel-positions.post')}}',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        type: "POST",
                        data: {
                            converters_positions: convertersPositions,
                        },
                        dataType: "json",
                        success: function (data) {
                            showSuccessMessage('Converters positions updated!');
                        },
                        error: function (data) {
                            showErrorMessage(data.responseJSON.message);
                        }
                    });
                    break;
            }
        }

        function randomlySystemOrder() {
            $('#customCarousel').hide();
        }

        function checkCarouselPositions(input) {
            let currentSelect = $(input);
            $('select[name="selectCarouselPosition"]').each(function () {
                if ($(this).attr('id') !== currentSelect.attr('id')) {
                    if ($(this).val()) {
                        if ($(this).val() === currentSelect.val()) {
                            $(this).val('');
                        }
                    }
                }
            });
        }

        function converterOrder() {
            $.ajax({
                url: '{{route('api.admin.system-settings.current.get')}}',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    current_setting_order = data;
                }
            });

            $.ajax({
                url: '{{route('api.admin.system-settings.list.get')}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "GET",
                dataType: 'json',
                success: function (data) {
                    $('#converterOrder').append('<div class="col"></div>')
                    data.forEach(function (name) {

                        let input = '<input class="form-check-input" type="radio" name="converterOrderOption" id=":converterOrderId" value=":converterOrderValue" onclick="selectSystemOrder()">'.replace(':converterOrderId', name).replace(':converterOrderValue', name);
                        input = $(input)

                        if (current_setting_order.converters_carousel_order === name) {
                            input.attr('checked', 'checked')
                        }

                        let label = '<label class="form-check-label" for=":converterOrderId">:label_value</label>'.replace(':converterOrderId', name).replace(':label_value', name.toUpperCase().replace('_', ' '));
                        label = $(label)
                        let col = $('<div class="col"></div>')
                            .append(input)
                            .append(label);
                        $('#converterOrder').append($(col));
                    })
                    selectSystemOrder()
                    $('#saveConverterCarouselPositionDiv').show();
                }
            });
        }

        function convertersCarouselLocations() {
            let counter = 1;
            available_carousel_position.forEach(function (position) {
                let row = $('<div class="row"></div>');
                let counter_lable = $('<div class="col-1"><strong>:counter</strong>.</div>'.replace(':counter', counter));
                let label = $('<div class="col-4"><span id=":carousel_position_id"> Position n° :position_number move to position </span></div>'.replace(':carousel_position_id', position.id).replace(':position_number', position.position));
                let input = $('<div class="col-2"><input class="form-control" type="number" min="0" max="49" id=":carousel_position_id" name="carouselPositionsInput[]" value=":position_value"></div>'.replace(':position_value', position.position).replace(':carousel_position_id', position.id))
                counter += 1;
                row.append(counter_lable).append(label).append(input).append('<br>')

                $('#convertersCarouselLocations').append(row);
            })

            $('#convertersCarouselLocationsDiv').show()
        }

        function getCurrentCarouselConvertersLocations() {
            $.ajax({
                url: '{{route('api.admin.converters-carousel-positions.index.get')}}',
                async: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "GET",
                success: function (data) {
                    available_carousel_position = data;
                }
            });
        }

        function saveConverterCarouselLocations() {
            let positions = [];
            $('input[name^=carouselPositionsInput]').each(function () {
                positions.push({
                    id: $(this).attr('id'),
                    position: $(this).val()
                })
            })

            $.ajax({
                url: '{{route('api.admin.converters-carousel-positions.update.post')}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: "POST",
                data: {
                    positions: positions,
                },
                dataType: "json",
                success: async function (data) {
                    showSuccessMessage('Converters Carousel Locations updated!');
                    await sleep(2000);
                    location.reload();
                },
                error: async function (data) {
                    showErrorMessage(data.responseJSON.message);
                    await sleep(2000);
                    location.reload();
                }
            });
        }
    </script>
@endsection
