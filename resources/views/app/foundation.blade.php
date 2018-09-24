<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Tranquility CRM') }}</title>
    <meta name="application-name" content="{{ config('app.name', 'Tranquility CRM') }}"/>
    <script type="text/javascript">
        var runWhenTrue = function(condition, callback, timeout ) {
            if ('number' !== timeout ) {
                timeout = 100;
            }
            if ( true == eval(condition) ) {
                callback();
            } else {
                setTimeout(function(){
                    runWhenTrue(condition, callback, timeout);
                }, timeout);
            }
        }
    </script>
    <script type="text/javascript" src="{{ asset('js/app.js') }}" async defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset( 'img/favicon.png' ) }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset( 'img/favicon.png' ) }}" />
    <link rel="icon" type="image/png" href="{{ asset( 'img/favicon.png' ) }}" sizes="196x196" />
    <link rel="icon" type="image/png" href="{{ asset( 'img/favicon.png' ) }}" sizes="96x96" />
    <link rel="icon" type="image/png" href="{{ asset( 'img/favicon.png' ) }}" sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ asset( 'img/favicon.png' ) }}" sizes="16x16" />
    <link rel="icon" type="image/png" href="{{ asset( 'img/favicon.png' ) }}" sizes="128x128" />
    @yield('rbg')
</head>
    <body>
        @yield('blueprint')
        <audio class="d-none" id="loaded">
            <source src="{{ asset( 'sounds/loaded.wav' ) }}" type="audio/wav">
        </audio>
        <audio class="d-none" id="mt1">
            <source src="{{ asset( 'sounds/mt1.mp3' ) }}" type="audio/mpeg">
        </audio>
        <audio class="d-none" id="mt2">
            <source src="{{ asset( 'sounds/mt2.mp3' ) }}" type="audio/mpeg">
        </audio>
        <audio class="d-none" id="mt3">
            <source src="{{ asset( 'sounds/mt3.wav' ) }}" type="audio/wav">
        </audio>
        <audio class="d-none" id="ringtone">
            <source src="{{ asset( 'sounds/ringtone.mp3' ) }}" type="audio/mpeg">
        </audio>
        {{ \App\Http\Controllers\AuthenticatedSessionController::initializeRealtimeClient() }}
    </body>
</html>