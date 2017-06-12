@extends('layouts.app')
@section('title', $activity->title)
@section('content')

    <div class="activity_details">
        <div class="block">
            <div class="heading">
                {{$activity->title}}
            </div>
            <div class="content">
                <div class="tabs clearfix">
                    <div class="tab details float active">
                        <div class="bg_helper"></div>
                        Details
                    </div>
                    <div class="tab sign_up float">
                        <div class="bg_helper"></div>
                        Inschrijven
                    </div>
                </div>

                <div class="buttons clearfix">
                    <div class="float back"><a href="{{url('activities_overview')}}"><i class="fa fa-angle-left" aria-hidden="true"></i> Terug naar overzicht</a></div>

                    @if($is_admin)
                    <div class="edit_button">
                        <a href="{{url('edit_activity/' . $activity->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Bewerken</a>
                    </div>
                    @endif
                </div>
                
                @if (session('success_msg'))
                    <div class="success_msg">
                        {{ session('success_msg') }}
                    </div>
                @endif

                <div class="activity_info">
                    <div class="activity_details clearfix">
                        <div class="poster float small_no_float">
                            <img src="{{url('images/activity_images/' . $activity->poster)}}" alt="{{$activity->title}}">
                        </div>
                        <div class="info float small_no_float">
                            <div>
                                <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Beschrijving:</h4>
                                <p>{!! $activity->description !!}</p>
                            </div>
                            <div>
                                <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Algemene info:</h4>
                                <div>
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    {{$activity->min_participants}} - {{($activity->max_participants > 30 ? "onbeperkt aantal" : $activity->max_participants)}} deelnemers
                                </div>
                                <div class="date">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    {{date('d/m/Y', strtotime($activity->start))}} 
                                    @if($activity->deadline)
                                    (inschrijven tot {{date('d/m', strtotime($activity->deadline))}}<span class="year">{{date('/Y', strtotime($activity->deadline))}}</span>)
                                    @endif
                                </div>
                                <div>
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    {{date('H:i', strtotime($activity->start))}} - {{date('H:i', strtotime($activity->end))}}
                                </div>
                                @if($activity->price > 0)
                                <div>
                                    <i class="fa fa-eur" aria-hidden="true"></i>
                                    {{ $activity->price }}
                                </div>
                                @endif
                                @if($activity->extra_url)
                                <div>
                                    <i class="fa fa-link" aria-hidden="true"></i>
                                    <a href="{{ $activity->extra_url }}" target="_blank">{{ $activity->extra_url }}</a>
                                </div>
                                @endif
                                <div>
                                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                                    {{ $activity->location }}
                                </div>
                            </div>
                            <div class="google_maps">
                                <div id="map">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="sign_up clearfix">
                        <div class="sign_up_list float">
                            <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Inschrijvingslijst:</h4>
                            
                            <div class="people clearfix">
                                @if($is_admin)
                                <div class="edit_button">
                                    <a href="{{url('activity_participants/' . $activity->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Bewerken</a>
                                </div>
                                @endif
                                <div class="amount_participants">
                                    <span class="current_amount @if(count($activity->participants) >= $activity->min_participants){{'okay'}}@endif">{{count($activity->participants)}}</span>
                                     / <span>{{($activity->max_participants > 30 ? "&infin;" : $activity->max_participants)}}</span>
                                </div>
                                @foreach($activity->participants as $participant)
                                <div class="person float" user_id="{{$participant->id}}">
                                    <figure>
                                        <img src="{{url('images/profile_pictures/' . $participant->image)}}" alt="{{$participant->first_name}} {{$participant->last_name}}">
                                        <figcaption>
                                            {{$participant->first_name}}
                                            @if($activity->price > 0 && Auth::user()->id == $participant->id && $participant->pivot->status == 2)
                                            <i class="fa fa-check" aria-hidden="true" data-toggle="tooltip" data-placement="bottom" title="Betaald"></i>
                                            @endif
                                        </figcaption>
                                    </figure>
                                </div>
                                @endforeach
                                @if(!count($activity->participants))
                                <div class="empty">
                                    Er is nog niemand ingeschreven voor deze activiteit. <br>
                                    Schrijf je nu als eerste in !
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="sign_up_info float">
                            @if( strtotime($activity->start) < strtotime('now') || (date('Y', strtotime($activity->deadline)) != 1970 && strtotime($activity->deadline) < strtotime('now')))
                            <div>
                                <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Deelnemen:</h4>
                                <p>Inschrijven voor deze activiteit is niet meer mogelijk omdat de deadline of de activiteit zelf reeds voorbij is...</p>
                            </div>
                            @elseif(count($activity->participants) >= $activity->max_participants)
                            <div>
                                <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Deelnemen:</h4>
                                <p>Inschrijven voor deze activiteit is niet meer mogelijk omdat het maximum aantal inschrijvingen bereikt is...</p>
                            </div>
                            @else
                            <div>
                                <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Deelnemen:</h4>
                                <p>Wil je deelnemen aan deze activiteit? Schrijf jezelf en/of anderen dan hieronder in!</p>
                                @if($activity->price > 0)
                                <p>Vergeet het inschrijvingsgeld niet over te schrijven naar BE66 7333 2013 0443.</p>
                                @endif
                                <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Wie wil je inschrijven:</h4>
                                <form id="activity_sign_up" class="sign_up_form" method="post" action="{{url('sign_up_for_activity')}}">
                                {{ csrf_field() }}
                                    <input type="number" name="activity_id" id="activity_id" value="{{$activity->id}}" hidden="">
                                    <div class="sign_up_for">
                                        @if($user_signed_up)
                                        <div class="me">Je bent reeds ingeschreven. (<a id="sign_out" href="#">Uitschrijven?</a>)</div>
                                        @else
                                        <div class="me">
                                            <input type="checkbox" name="sign_up_me" id="sign_up_me" hidden>
                                            <label for="sign_up_me">
                                                <span class="checkbox float {{str_replace(' ', '_', strtolower($activity->category->name))}}">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                                <span>Mezelf inschrijven</span>
                                            </label>
                                        </div>
                                        @endif
                                        <div class="others">
                                            <input type="checkbox" name="sign_up_others" id="sign_up_others" hidden>
                                            <label for="sign_up_others">
                                                <span class="checkbox float {{str_replace(' ', '_', strtolower($activity->category->name))}}">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </span>
                                                <span>Anderen inschrijven</span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="sign_up_others">
                                        <p class="descriptive_info">Hieronder kan je leden zoeken om in te schrijven voor deze activiteit.<br>
                                        Enkel personen die lid zijn bij Sportiva kunnen ingeschreven worden.</p>
                                        <div class="search_participants">
                                            <input id="search_participants" type="text" name="search_participants" placeholder="Zoek leden" autocomplete="off">
                                            <div class="search_results">
                                                <ul>
                                                    <li>Sarah Jehin</li>
                                                    <li>Glass Sorenson</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="added_participants">
                                            <p class="descriptive_info">Volgende personen worden ingeschreven wanneer je op inschrijven klikt:</p>
                                            <div class="participant template">
                                                <input type="number" name="participant[]" value="0" hidden="">
                                                <i class="fa fa-dot-circle-o" aria-hidden="true"></i> <span>Sarah Jehin</span>
                                                <i class="fa fa-times remove" aria-hidden="true"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="error_msg">
                                        test
                                    </div>
                                </form>
                                <div>
                                    <input type="submit" name="submit" value="Inschrijven">
                                </div>
                                
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="poster_modal" class="lightbox_modal">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    <img src="{{url('images/activity_images/' . $activity->poster)}}" alt="{{$activity->title}}">
                </div>
                <div class="modal_footer">
                    {{$activity->title}}
                </div>
            </div>
        </div>

        <div id="sign_out_modal" class="lightbox_modal light">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    Zeker dat je wil uitschrijven?
                </div>
                <div class="modal_footer">
                    <form method="post" action="{{url('sign_out_for_activity')}}">
                        {{ csrf_field() }}
                        <input type="number" name="user_id" value="{{Auth::user()->id}}" hidden="">
                        <input type="number" name="activity_id" value="{{$activity->id}}" hidden="">
                        <input type="submit" name="submit" value="Ja, nu uitschrijven">
                    </form>
                </div>
            </div>
        </div>

        <div id="member_modal" class="lightbox_modal light">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    <div class="clearfix">
                        <div class="image float">
                            <img src="{{url('images/profile_pictures/' . 'sarah_jehin.jpg')}}" alt="Sarah">
                        </div>
                        <div class="info float">
                            <div class="name"><h2>Sarah Jehin</h2></div>
                            <div class="birth_date clearfix"><span class="float"><i class="fa fa-birthday-cake" aria-hidden="true"></span></i><span class="float">24/04/2017</span></div>
                            <div class="ranking_singles clearfix"><span class="float">E:</span><span class="float">C+30/3</span></div>
                            <div class="ranking_doubles clearfix"><span class="float">D:</span><span class="float">C+30/3</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal_footer">
                </div>
            </div>
        </div>

    </div>
@endsection
@section('custom_js')
<script type="text/javascript">
    var latitude = {{$activity->latitude}}
    var longitude = {{$activity->longitude}};
</script>
<script src="{{ asset('js/activities/activity_details.js') }}"></script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA69WeWJnH4qyNdwyjEjAc9YAOXA1Ooi-c&callback=initMap">
    </script>
@endsection
