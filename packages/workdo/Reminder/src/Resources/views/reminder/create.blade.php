@extends('layouts.main')
@section('page-title')
    {{ __('Create Reminder ') }}
@endsection
@section('page-breadcrumb')
    {{ __('Reminder') }}
@endsection
@section('content')
    <div class="row">
        {{ Form::open(['url' => 'reminder', 'method' => 'post','class'=>'needs-validation','novalidate']) }}
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h3>{{ __('Create Reminder') }}</h3>
                    <div class="col-lg-8">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label class="form-label">{{ __('Select Type') }}</label>
                                <div>
                                    <div class="form-check form-check-inline default_date">
                                        <input type="radio" id="date_select1" name="date_select" class="form-check-input"
                                            value = "default" {{ old('date_select') == 'default' ? 'checked' : '' }} checked>
                                        <label class="form-check-label" for="date_select1">{{ __('Default') }}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" id="date_select2" name="date_select" class="form-check-input"
                                            value="manually" {{ old('date_select') == 'manually' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="date_select2">{{ __('Manually') }}</label>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="from-group ">
                            <div class="col-lg-12 row" id = "day-number">
                                <div class="col-lg-6 form-group">
                                    {{ Form::label('day_number', __('Number'), ['class' => 'form-label']) }}
                                    {{ Form::number('day_number', null, ['class' => 'form-control']) }}
                                </div>
                                <div class="col-lg-6 mt-2">
                                    {{ Form::label('', __(' '), ['class' => 'form-label']) }}
                                    {{ Form::text('day', 'Day', ['class' => 'form-control', 'id' => 'module', 'disabled' => 'disabled']) }}
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6 date" style = "display: none">
                                <div class="form-group">
                                    {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                                    <div class="form-icon-user">
                                        {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control ', 'required' => 'required', 'placeholder' => 'Select Issue Date']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-1">
                                    <div class="form-group">
                                        {{ Form::label('module', __('Module'), ['class' => 'form-label']) }}<x-required></x-required>
                                        <select class="form-select" id="selectmodule" name="module" required>
                                            <option value="0">{{ __('Select Module') }}</option>
                                            <option value="Invoice">{{ __('Invoice') }}</option>
                                            <option value="User">{{ __('User') }}</option>
                                            @stack('module_name')
                                        </select>
                                        <small class="text-danger has-validation"></small>
                                    </div>
                                </div>
                                <div class="col-md-6 mt-1 data-id" style="display:none">
                                    <div class="form-group ">
                                        <label class="form-label lable-name">{{__('Invoice')}}</label>
                                        <select class="form-select module-data" name="module_value"required >
                                        </select>
                                        <small class="text-danger data-validation"></small>

                                    </div>
                                </div>
                                <div class="col-md-6 mt-1 client" style="display:none">
                                    <div class="form-group ">
                                        <label class="form-label">{{ __('Client') }}</label>
                                        <select class="form-select" name="deal_client_id" id="dealclint-select" >
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-1">
                            <div class="form-group">
                                <h4>{{ __('Action') }}</h4>
                                <select class="form-control action" name="actions[]" id="choices-multiple-remove-button"
                                    multiple required>
                                    <option value="Email">{{ __('Email') }}</option>
                                    @stack('action_name')
                                </select>
                            </div>
                        </div>
                        <div class="notification-tab">

                        </div>
                    </div>
                </div>
                <div class="text-end mb-3" style="margin-right: 250px">
                    <button class="btn btn-print-invoice btn-primary" type="submit">{{ __('Save Changes') }}</button>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
@endsection


@push('scripts')
    <link href="{{  asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.css')  }}" rel="stylesheet">
    <script src="{{ asset('assets/js/plugins/summernote-0.8.18-dist/summernote-lite.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            $(document).on('click', '#date_select2', function() {
                $('#day').hide();
                $('#day-number').hide();
                $('.date').show();

            })
            $(document).on('click', '#date_select1', function() {
                $('#day').show();
                $('#day-number').show();
                $('.date').hide();
            });
        })

        $(document).on('change', '#selectmodule', function() {
            var module = $(this).val();
            $('.lable-name').text(module)
            $('.data-id').show();

            var date_select2 = $('#date_select2').val();
            if (module != 'Deal') {
                $('.client').hide();
            }
            // validation
            if(module != 0){
                $('.has-validation').text('');
            }else{
                $('.has-validation').text('Please select the Module');
            }
            if(module == 'User' || module == 'Deal' ){
                $('#day').hide();
                $('#day-number').hide();
                $('.date').show();
                $('.default_date').hide();
                $('#date_select2').prop('checked' ,true);
            }else{
                if(date_select2 != 'manually'){
                    $('#day').show();
                    $('#day-number').show();
                    $('.date').hide();
                    $('.default_date').show();
                    $('#date_select1').prop('checked' ,true);
                }else{
                    $('.default_date').show();
                    $('.date_select1').show();
                }

            }
            $.ajax({
                url: '{{ route('get.moduledata') }}',
                type: 'POST',
                data: {
                    "module": module,
                },
                success: function(response) {
                   if(response != false){
                    $('.module-data').empty();
                    $('.module-data').append('<option value="0">select ' + module + ' </option>');
                            $.each(response, function(key, value) {
                            $('.module-data').append('<option value="' + key + '">' +
                                value + '</option>');
                        });
                   }else{
                      $('.data-id').hide();
                   }

                }

            });
            $('.module-data').val(0);
            Action();


        });
        $(document).on('change', '.module-data', function() {
            var module = $('#selectmodule').val();
            var module_data = $(this).val();
            if (module_data != null) {
                $('.data-validation').text('');
            }

            if (module == 'Deal') {
                $.ajax({
                    url: '{{ route('deal.client') }}',
                    type: 'POST',
                    data: {
                        "module_data": module_data
                    },
                    success: function(response) {
                        $('.client').show();
                        $('#dealclint-select').empty();
                        $('#dealclint-select').append('<option value="0">select client </option>');
                        $.each(response, function(key, value) {
                            $('#dealclint-select').append('<option value="' + value.id + '">' +
                                value.name + '</option>');
                        });

                    }

                });
                $('#dealclint-select').val(0);

            }
            Action();

        });
        $(document).on('change', '.action', function() {
            var module = $('#selectmodule').val();
            var module_data = $('.module-data').val();
            if (module == 0) {
                $('.has-validation').text('Please select the Module');
            }else{
                $('.has-validation').text('');
            }
            if (module_data == 0) {
                $('.data-validation').text('Please select value')
            }else{
                $('.data-validation').text('');
            }
            Action();
        });
        $(document).on('change', '#dealclint-select', function() {
            Action();
        });

        function Action() {
            var action = $('.action').val();
            var module = $('#selectmodule').val();
            var module_data = $('.module-data').val();
            var deal_data = $('#dealclint-select').val();

            var module_data = $('.module-data').val();
            $.ajax({
                url: '{{ route('reminder.attribute') }}',
                type: 'POST',
                data: {
                    "module": module,
                    "action": action,
                    "module_data": module_data,
                    "deal_data": deal_data
                },
                success: function(response) {
                    if (response.is_success != false) {
                        $('.notification-tab').html(response.html);
                    } else {
                        $('.notification-tab').html('');

                    }
                }
            })
        }
        $(function() {
            var dtToday = new Date();
            var month = dtToday.getMonth() + 1;
            var day = dtToday.getDate();
            var year = dtToday.getFullYear();
            if (month < 10)
                month = '0' + month.toString();
            if (day < 10)
                day = '0' + day.toString();

            var minDate = year + '-' + month + '-' + day;

            $('#date').attr('min', minDate);
        });
        var multipleCancelButton = new Choices(
            '#choices-multiple-remove-button', {
                removeItemButton: true,
            }
        );
    </script>
@endpush
