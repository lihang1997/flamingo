<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title> {{ $appName }} </title>
</head>

<body style="margin: 0">

    <div id="app"></div>

    <script src="{{ mix('js/app.js') }}"></script>

</body>
</html>