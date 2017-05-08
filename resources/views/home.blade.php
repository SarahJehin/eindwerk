@extends('layouts.app')
@section('title', 'Welkom')
@section('content')
<div class="">
    <div class="block">
        <div class="heading">
            Welkom {{Auth::user()->first_name}}
        </div>
        <div class="content">
            <div class="personal_info clearfix">
                <div class="profile_pic float">
                    <img src="{{url('images/profile_pictures/' . Auth::user()->image)}}" alt="{{Auth::user()->first_name}} {{Auth::user()->last_name}}">
                </div>
                <div class="data float">
                    <h2>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h2>
                    <div class="contact_info">
                        <div class="gsm">
                            <span class="title">GSM:</span>
                            <span class="value">{{substr(Auth::user()->gsm, 0, 4) . ' ' . chunk_split(substr(Auth::user()->gsm, 4), 2, ' ')}}</span>
                        </div>
                        <div class="tel">
                            <span class="title">Tel.:</span>
                            <span class="value">{{substr(Auth::user()->gsm, 0, 4) . ' ' . chunk_split(substr(Auth::user()->gsm, 4), 2, ' ')}}</span>
                        </div>
                        <div class="email">
                            <span class="title">E-mail:</span>
                            <span class="value">{{Auth::user()->email}}</span>
                        </div>
                        email, gsm enz
                    </div>
                    <div class="tennis_info">
                        <div class="singles">
                            <span>Enkel:</span>
                            <span>{{Auth::user()->ranking_singles}}</span>
                        </div>
                        <div class="doubles">
                            <span>Dubbel:</span>
                            <span>{{Auth::user()->ranking_doubles}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="badges">
                <h3>Badges</h3>
                <p class="descriptive_info">Je hebt nog geen badges verdiend.</p>
            </div>

            <div class="upcoming_activities">
                <h3>Mijn activiteiten</h3>
                @if(!$user->activities_as_participant_coming->isEmpty())
                <div class="list">
                    <div class="row header clearfix">
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($user->activities_as_participant_coming as $activity)
                    <div class="row activity clearfix">
                        <div class="date float">{{date('d-m-Y', strtotime($activity->start))}}</div>
                        <div class="title float">{{$activity->title}}</div>
                        <div class="paid float">
                            @if($activity->price > 0)
                                @if($activity->pivot->status == 2)
                                <i class="fa fa-check" aria-hidden="true"></i>
                                @endif
                            @else
                            n.v.t.
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="descriptive_info">Je hebt geen komende activiteiten.  Kijk op de <a class="link" href="{{url('activities_overview')}}">kalender</a> welke activiteiten je allemaal kan meedoen.</p>
                @endif
            </div>

            <div class="past_activities">
                <h3>Activiteiten waaraan ik heb deelgenomen</h3>
                @if(!$user->activities_as_participant_past->isEmpty())
                <div class="list">
                    <div class="row header clearfix">
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($user->activities_as_participant_past as $activity)
                    <div class="row activity clearfix">
                        <div class="date float">{{date('d-m-Y', strtotime($activity->start))}}</div>
                        <div class="title float">{{$activity->title}}</div>
                        <div class="paid float">
                            @if($activity->price > 0)
                                @if($activity->pivot->status == 2)
                                <i class="fa fa-check" aria-hidden="true"></i>
                                @endif
                            @else
                            n.v.t.
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="descriptive_info">Je hebt dit seizoen nog niet aan activiteiten deelgenomen.</p>
                @endif
            </div>



            <p>Welkom op het dashboard van TC Sportiva!</p>
            <p>Op deze persoonlijke pagina vind je een overzicht van activiteiten waarvoor je bent ingeschreven, je winteruurschema('s), ...</p>

            <div class="current_score">
                Je huidige score is {{$total_adult_score}}
            </div>

            <h4>Mijn activiteiten:</h4>
            <div>
            	@foreach($user->activities_as_participant_coming as $activity)
            	<div>{{date('d-m-Y', strtotime($activity->start))}} - {{$activity->title}}</div>
            	@endforeach
            </div>

            <h4>Activiteiten waaraan ik dit seizoen heb deelgenomen:</h4>
            <div>
                @foreach($user->activities_as_participant_past as $activity)
                <div>{{date('d-m-Y', strtotime($activity->start))}} - {{$activity->title}}</div>
                @endforeach
            </div>

            <div class="test">
                <form method="post" action="{{url('import_members')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="file" name="members_excel">
                    <input type="submit" name="submit" value="Leden importeren">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
