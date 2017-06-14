<!DOCTYPE html>
<html lang="{{ config('app.locale') }}" class="login">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>TCS - Dashboard</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Titillium+Web:300,300i,400,400i,600" rel="stylesheet">

        <!-- favicon -->
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/site_images/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/site_images/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/site_images/favicon-96x96.png') }}">

        <link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
        <link href="{{url('/css/main.css')}}" rel="stylesheet" type="text/css">
    </head>
    <body class="login">
        <div class="container">
            <div class="background_holder1"></div>
            <div class="background_holder2"></div>


            <div class="login_block">
                <div class="login-heading clearfix">
                    <div class="logo float">
                        <img src="{{url('images/site_images/logo_colored_light_bg01.png')}}" alt="Logo">
                    </div>
                    <div class="text float">
                        Welkom op TCS
                    </div>
                </div>
                @if (count($errors) > 0)
                    <div class="error_msg clearfix">
                        <div class="asterix float">*</div>
                        <div class="error float">{{ $errors->all()[0] }}</div>
                    </div>
                @endif
                @if (session('session_expired'))
                    <div class="error_msg">
                        {{ session('session_expired') }}
                    </div>
                @endif

                <div class="content">
                    <form id="login_form" method="post" action="{{ route('login') }}">
                        {{ csrf_field() }}
                        <div class="field_wrap">
                            <label class='{{ (old("vtv_nr") == null ? "" : "active") }}''>VTV-nr</label>
                            <input type="text" name="vtv_nr" id="vtv_nr" value="{{old('vtv_nr')}}">
                        </div>

                        <div class="field_wrap">
                            <label>Wachtwoord</label>
                            <input type="password" name="password" id="password">
                        </div>

                        <div class="field_wrap">
                            <input type="submit" name="submit" value="Inloggen">
                        </div>
                    </form>
                </div>

            </div>

        </div>
        <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.2/angular.min.js"></script>
        <script src="{{url('/js/main.js')}}"></script>
    </body>
</html>
