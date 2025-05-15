<!DOCTYPE html>
<html>

<head>
    <title>{{ __('Email Content') }}</title>
</head>

<body>
    <div>
        <p>
            {{ __('Hi,') }}<br>
            {{ __('You’ve been invited to view a Flowchart. You can view the Flowchart by clicking on the link below.') }}
        </p>

        <a href="{{ route('business.shared.link', ['id' => encrypt($business->id)]) }}">
            {{ __('Click here to access the shared link') }}
        </a>

        <p>{{ __('Many Thanks,') }}</p>
    </div>
</body>

</html>
