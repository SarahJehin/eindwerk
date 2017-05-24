@extends('layouts.app')
@section('title', 'Winteruren')
@section('content')
    <div class="winterhours_overview">

        <div class="block">
            <div class="heading">
                Winteruren
            </div>
            <div class="content">

                <div class="title clearfix">
                    <h3 class="float">Mijn winteruurgroepen</h3>
                    <div class="add_winterhour link float"><a href="{{url('add_winterhour')}}">+ Nieuwe groep</a></div>
                </div>
                @if(!$winterhour_groups->isEmpty())
                    @foreach($winterhour_groups as $winterhour)
                    <div class="winterhour_group">
                        <h3>{{$winterhour->title}} <i class="fa fa-angle-down" aria-hidden="true"></i></h3>
                        <div class="day_time">
                            ({{$winterhour->day}} - {{date('H:i', strtotime($winterhour->time))}})
                            @if($winterhour->made_by == Auth::user()->id)
                            (<a class="link" href="{{url('edit_winterhour/' . $winterhour->id)}}">Beheren</a>)
                            @endif
                        </div>
                        <div class="details open">
                            <div class="participants">
                                <h4>Deelnemers <i class="fa fa-angle-right" aria-hidden="true"></i></h4>
                                <div class="participants_block">
                                    @foreach($winterhour->participants as $participant)
                                    <div class="participant clearfix">
                                        <div class="name float">{{$participant->first_name}} {{$participant->last_name}}</div>
                                        <div class="gsm float">{{substr($participant->gsm, 0, 4) . ' ' . chunk_split(substr($participant->gsm, 4), 2, ' ')}}&nbsp;</div>
                                        <div class="email float">{{$participant->email}}</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            @if(count(Auth::user()->dates) > 0 && $winterhour->status != 3)
                            Jij of de verantwoordelijke hebben je beschikbaarheid reeds doorgegeven. <a href="{{url('availabilities/' . $winterhour->id)}}">Beschikbaarheid aanpassen</a>.
                            @elseif(count(Auth::user()->dates) <= 0 && $winterhour->status != 3)
                            Je hebt nog geen beschikbaarheid doorgegeven.  De verantwoordelijke kan het schema pas aanmaken wanneer jij je beschikbaarheid hebt doorgegeven. <a href="{{url('availabilities/' . $winterhour->id)}}">Beschikbaarheid doorgeven</a>.
                            @endif
                            <div class="scheme clearfix">
                                @if($winterhour->status != 4)
                                Het schema verschijnt hier zodra de verantwoordelijke het gegenereerd heeft.
                                @else
                                    @foreach($winterhour->scheme as $date => $participants)
                                    <div class="date float">
                                        <h3>{{date('d/m/Y', strtotime($date))}}</h3>
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
<script src="{{ asset('js/winterhours/winterhour_overview.js') }}"></script>
@endsection
