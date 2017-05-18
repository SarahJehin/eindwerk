<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TC Sportiva') }} - Pagina niet gevonden</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Amatic+SC:400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,700" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Sans:300,400,400i,700" rel="stylesheet"> 

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <!--<link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
    <link href="{{url('/css/main.css')}}" rel="stylesheet" type="text/css">
    <style type="text/css">
        .logged_in_content, nav {
            display: none;
        }
    </style>

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
                    <img src="{{url('images/profile_pictures/')}}">
                </div>
                <div class="name_total float">
                    <div class="name"><a href="{{url('/')}}"></a></div>
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
                <li><a class="{{ (Request::is('winterhours_overview') ? 'active' : '') }}" href="{{url('winterhours_overview')}}"><i class="fa fa-snowflake-o" aria-hidden="true"></i><span class="menu_name">Winteruren</span></a></li>
                <li><a class="{{ (Request::is('members_overview') ? 'active' : '') }}" href="{{url('members_overview')}}"><i class="fa fa-users" aria-hidden="true"></i><span class="menu_name">Leden</span></a></li>
            </ul>


        </nav>


        <div class="page_content">
            <div class="availabilities">

                <div class="block">
                    <div class="heading">
                        Pagina niet gevonden
                    </div>
                    <div class="content">
                        <div class="logged_in_content">
                            <div class="descriptive_info">
                                Oeps! Deze pagina lijkt niet te bestaan.<br>
                                Indien je de url manueel hebt ingegeven, kijk dan nog eens na of je geen foutje getypt hebt.<br>
                                Als je hier gekomen bent door op een link te klikken, hebben wij waarschijnlijk een foutje gemaakt.  Help ons dit foutje op te lossen door een mailtje te sturen naar <a href="mailto:sarah.jehin@belgacom.net?subject=Foutieve link">sarah.jehin@belgacom.net</a>.
                            </div>
                            <div>
                                Om verder te gaan kan je één van de volgende dingen doen:
                                <ul>
                                    <li>Ga naar de <a href="{{url('home')}}">homepagina</a>.</li>
                                    <li>Ga naar de <a href="{{URL::previous()}}">vorige pagina</a>.</li>
                                    <li>Zoek het gewenste onderwerp in de navigatie aan de linkerkant van het scherm.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="logged_out_content">
                            Om eender welke pagina op dit dashboard te bekijken moet je <a href="{{url('/')}}">inloggen</a>.
                        </div>
                        
                    </div>
                </div>


            </div>
        </div>

    </div>

    

    <!-- Scripts -->
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script type="text/javascript">
        (function ( window, document, $, undefined ) {
            //console.log(location.origin + "/get_authenticated_user");
            //get authenticated user through API call, because he/she is not known on error views
            $.get( location.origin + "/get_authenticated_user", function( data ) {
                //console.log(data);
                if(data) {
                    var original_source = $('.profile_pic img').attr('src');
                    $('.profile_pic img').attr('src', original_source + '/' + data.image);
                    $('.name_total .name a').text(data.first_name + ' ' + data.last_name);
                    $('.logged_out_content').hide();
                    $('.logged_in_content').show();
                    $('nav').show();
                }
                else {
                    //$('.logged_in_content').hide();
                    //$('nav').hide();
                }
                
            });

        })(window, window.document, window.jQuery);
    </script>
</body>
</html>

