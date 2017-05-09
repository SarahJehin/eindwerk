<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TC Sportiva') }} - @yield('title')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:300,400,400i,700" rel="stylesheet"> 

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    <link href="{{url('/css/main.css')}}" rel="stylesheet" type="text/css">
    @yield('custom_css')

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div class="page_container">

        <label class="hamburger" for="hamburger"><i class="fa fa-bars" aria-hidden="true"></i></label>
        <input type="checkbox" name="hamburger" id="hamburger">

        <nav>
            <div class="personal_details clearfix">
                <div class="profile_pic float">
                    <img src="{{url('images/profile_pictures/' . Auth::user()->image)}}">
                </div>
                <div class="name_total float">
                    <div class="name"><a href="{{url('/')}}">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</a></div>
                    <div class="logout">
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault();
                                                                 document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out" aria-hidden="true"></i> Uitloggen
                        </a>
                    </div>
                </div>
            </div>
            

            

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                {{ csrf_field() }}
            </form>

            <ul>
                <li><a class="{{ (Request::is('activities_overview') ? 'active' : '') }}" href="{{url('activities_overview')}}"><i class="fa fa-calendar" aria-hidden="true"></i><span class="menu_name">Activiteiten</span></a></li>
                <li><a class="{{ (Request::is('scoreboard') ? 'active' : '') }}" href="{{url('scoreboard')}}"><i class="fa fa-trophy" aria-hidden="true"></i><span class="menu_name">Scorebord</span></a></li>
                <li><a class="{{ (Request::is('winter_hours') ? 'active' : '') }}" href="{{url('#')}}"><i class="fa fa-snowflake-o" aria-hidden="true"></i><span class="menu_name">Winteruren</span></a></li>
                <li><a class="{{ (Request::is('members_overview') ? 'active' : '') }}" href="{{url('members_overview')}}"><i class="fa fa-users" aria-hidden="true"></i><span class="menu_name">Leden</span></a></li>
            </ul>


        </nav>


        <div class="page_content">
            @yield('content')
        </div>

    </div>

    

    <!-- Scripts -->
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('custom_js')
    <script src="{{ asset('js/main.js') }}"></script>

    <!--
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="{{url('/js/app.js')}}"></script>
    -->
</body>
</html>
