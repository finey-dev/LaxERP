<!DOCTYPE html>
<html lang="en">
@php
$favicon = isset($company_settings['favicon']) ? $company_settings['favicon'] : (isset($admin_settings['favicon']) ? $admin_settings['favicon'] : 'uploads/logo/favicon.png');
@endphp
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="form-one">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <title>{{$form->name ?? ''}}</title>
    <meta name="description" content="form-one">
    <meta name="keywords" content="form-one">
    <link rel="icon" href="{{ check_file($favicon) ? get_file($favicon) : get_file('uploads/logo/favicon.png')  }}{{'?'.time()}}" type="image/x-icon" />

    <link rel="stylesheet" href="{{ asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/css/font-style.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/css/main-style.css') }}">
    <link rel="stylesheet" href="{{ asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/css/responsive.css') }}">
    <style>
        .step-container { display: none; }
        .step-container.active { display: block; }
    </style>
</head>
<body class="form-one {{$form->theme_color}}">
    <main>

        <section class="appointment-sec align-center pt pb">
            <div class="container">
                <h3 class="message">
                    @if(session('msg'))
                        {{ session('msg') }}
                    @endif
                </h3>
                <div class="row">
                    <div class="col-md-4 col-12">
                        @if($form->active == 'on')
                        <div class="form-steps-left">
                            <div class="section-title">
                                <h2> {{$form->name ?? ''}}</h2>
                            </div>
                            <div class="steps">
                                <ul>
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach ($objFields as $fileds)
                                    <li class="">
                                        <span>
                                         {{$i++}}
                                        </span>
                                     {{$fileds->name}}
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                    </div>
                    <div class="col-md-8 col-12">
                        <div class="myContainer">
                           @if ($form->active == "on")
                           <form class="appointment-form" action="{{ route('post.response', $form->code) }}" method="POST">
                            @foreach ($objFields as $index => $objField)
                            <div class="step-container {{ $objField->first ? 'active' : '' }}">
                                <div class="appointment-wrp">
                                    @csrf
                                    <h3>{{ __('Request ' . $objFields->count() . ' free Quotes for ' . $form->name) }}</h3>
                                    <div class="row">
                                        @if ($objField->type == 'Text')
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'col-form-label']) }}
                                                {{ Form::text('field[' . $objField->id . ']', null, ['class' => 'form-control required-field', 'required' => 'required', 'id' => 'field-' . $objField->id]) }}
                                            </div>
                                        </div>
                                        @elseif($objField->type == 'Email')
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'col-form-label']) }}
                                                {{ Form::email('field[' . $objField->id . ']', null, ['class' => 'form-control required-field', 'id' => 'field-' . $objField->id]) }}
                                            </div>
                                        </div>
                                        @elseif($objField->type == 'Number')
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'col-form-label']) }}
                                                {{ Form::number('field[' . $objField->id . ']', null, ['class' => 'form-control required-field','required' => 'required', 'id' => 'field-' . $objField->id]) }}
                                            </div>
                                        </div>
                                        @elseif($objField->type == 'Text Area')
                                        <div class="col-md-10">
                                            <div class="form-group">
                                                {{ Form::label('field-' . $objField->id, __($objField->name), ['class' => 'col-form-label']) }}
                                                {{ Form::textarea('field[' . $objField->id . ']', null, ['class' => 'form-control required-field', 'required' => 'required', 'style' => 'border-radius: 10px;', 'rows' => '3', 'id' => 'field-' . $objField->id]) }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="step-btns">
                                        @if ($index > 0)
                                        <button type="button" name="back" class="back btn btn-transparent">{{ __('Back') }}</button>
                                        @endif
                                        @if ($loop->last)
                                        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
                                        @endif
                                        @if (!$loop->last)
                                        <button type="button" name="next" class="next btn">{{ __('Next') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                           </form>
                            @else
                                <div class="page-title">
                                    <h1>{{ __('Form is not active.') }}</h1>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!--scripts start here-->
    <script src="{{ asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/js/jquery.min.js') }}"></script>
    <script src="{{ asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/js/custom.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.next').on('click', function() {
                var $currentStep = $(this).closest('.step-container');
                var $requiredFields = $currentStep.find('.required-field');
                var isValid = true;
                var isValidEmailField = true;

                $requiredFields.each(function() {
                    if (!$(this).val()) {
                        isValid = false;
                        return false;
                    }
                    // Additional check for email and number fields
                    if ($(this).attr('type') === 'email' && !isValidEmail($(this).val())) {
                        isValidEmailField = false;
                        return false;
                    }
                    if ($(this).attr('type') === 'number' && !isValidNumber($(this).val())) {
                        isValidEmailField = false;
                        return false;
                    }
                });

                if (isValid && isValidEmailField) {
                    $currentStep.removeClass('active').next('.step-container').addClass('active');
                } else {
                    if (!isValid) {
                        alert('Please fill all the required fields.');
                    } else {
                        alert('Please fill all the required fields correctly.');
                    }
                    return false;
                }
            });

            $('.back').on('click', function() {
                var $currentStep = $(this).closest('.step-container');
                $currentStep.removeClass('active').prev('.step-container').addClass('active');
            });
        });

        function isValidEmail(email) {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function isValidNumber(number) {
            var numberRegex = /^\d+$/;
            return numberRegex.test(number);
        }
    </script>
    <!--scripts end here-->
    <style>
        .message{
            margin-bottom: 69px;
            margin-left: 536px;
        }
    </style>


</body>

</html>
