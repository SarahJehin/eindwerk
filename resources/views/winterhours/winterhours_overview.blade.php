@extends('layouts.app')
@section('title', 'Winteruren')
@section('content')
    <div class="winterhours_overview">

        <div class="block">
            <div class="heading">
                Winteruren
            </div>
            <div class="content">

                <h3>Mijn winteruurgroepen</h3>
                @if(!$winterhour_groups->isEmpty())
                    @foreach($winterhour_groups as $winterhour)
                    <div class="winterhour_group">
                        <h4>{{$winterhour->title}}</h4>
                        <div class="info">
                            <div class="day_time">
                                <div class="day">
                                    Dag: {{$winterhour->day}}
                                </div>
                                <div class="time">
                                    Uur: {{substr($winterhour->time, 0, 5)}}
                                </div>
                                <div class="author">
                                    Aangemaakt door: {{$winterhour->made_by_user->first_name}} {{$winterhour->made_by_user->last_name}}
                                    @if($winterhour->made_by == Auth::user()->id)
                                    (als auteur kan jij dit winteruur <a href="{{url('edit_winterhour/' . $winterhour->id)}}">beheren</a>)
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="participants">
                            <h4>Deelnemers</h4>
                            @foreach($winterhour->participants as $participant)
                            <div class="participant clearfix">
                                <div class="name float">{{$participant->first_name}} {{$participant->last_name}}</div>
                                <div class="gsm float">{{$participant->gsm}}</div>
                            </div>
                            @endforeach
                        </div>
                        @if(count(Auth::user()->dates) > 0)
                        Jij of de verantwoordelijke hebben je beschikbaarheid reeds doorgegeven. <a href="{{url('availabilities/' . $winterhour->id)}}">Beschikbaarheid aanpassen</a>.
                        @else
                        Je hebt nog geen beschikbaarheid doorgegeven.  De verantwoordelijke kan het schema pas aanmaken wanneer jij je beschikbaarheid hebt doorgegeven. <a href="{{url('availabilities/' . $winterhour->id)}}">Beschikbaarheid doorgeven</a>.
                        @endif
                        <div class="scheme clearfix">
                            @if($winterhour->status != 3)
                            Het schema verschijnt hier zodra de verantwoordelijke het gegenereerd heeft.
                            @else
                                @foreach($winterhour->scheme as $date => $participants)
                                <div class="date float">
                                    <h3>{{$date}}</h3>
                                    @foreach($participants as $participant)
                                    <div class="participant {{ ($participant->id == Auth::user()->id ? 'active':'') }}">
                                        {{$participant->first_name}} {{$participant->last_name}}
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="descriptive_info">
                    Je hebt nog geen winteruurgroepen.  <a class="link" href="{{url('add_winterhour')}}">Maak er nu één aan</a>.
                </div>
                @endif
            </div>

        </div>

    </div>
@endsection
@section('custom_js')
@endsection
