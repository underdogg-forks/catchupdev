<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
<head>
    <meta charset="utf-8">
</head>
<body>
@if ($company->emailMarkupEnabled())
    @include('emails.partials.client_view_action', ['link' => $link])
@endif
{!! $body !!}
</body>
</html>
