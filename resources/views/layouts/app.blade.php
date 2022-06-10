@php
    use App\Models\User;

        $user = User::getUser();

            $attrs = [
                'vuexData' => [
                    'user' => $user,
                  ],
            ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <meta property="og:type" content="website">

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <title>Система управления проектами Yeskela</title>
    </head>
    <body>
        <div id="app">
            <app v-bind="{{json_encode($attrs)}}">
                @yield('content')
            </app>
        </div>
    </body>
</html>