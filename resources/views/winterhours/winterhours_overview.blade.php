@extends('layouts.app')
@section('title', 'Winteruren')
@section('content')
    <div class="winterhours_overview">

        <div class="block">
            <div class="heading">
                Winteruren
            </div>
            <div class="content">

                
                <div class="add_winterhour_link clearfix">
                    <a href="{{url('add_winterhour')}}" data-toggle="tooltip" data-placement="left" title="Nieuwe groep">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </a>
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
                            
                            @if(count(Auth::user()->dates) > 0 && $winterhour->status < 3)
                            Jij of de verantwoordelijke hebben je beschikbaarheid reeds doorgegeven. <a href="{{url('availabilities/' . $winterhour->id)}}">Beschikbaarheid aanpassen</a>.
                            @elseif(count(Auth::user()->dates) <= 0 && $winterhour->status != 3)
                            Je hebt nog geen beschikbaarheid doorgegeven.  De verantwoordelijke kan het schema pas aanmaken wanneer jij je beschikbaarheid hebt doorgegeven. <a href="{{url('availabilities/' . $winterhour->id)}}">Beschikbaarheid doorgeven</a>.
                            @endif
                            @if($winterhour->status == 4)
                            <div class="descriptive_info">
                                Hieronder vind je het schema terug.  Indien je wil wisselen met iemand, versleep dan je eigen naam naar de persoon waarmee je wil wisselen.
                            </div>
                            <div class="swap_message">
                                Wissel bericht.
                            </div>
                            @endif
                            <div class="scheme clearfix">
                                @if($winterhour->status != 4)
                                Het schema verschijnt hier zodra de verantwoordelijke het gegenereerd heeft.
                                @else
                                    @foreach($winterhour->scheme as $date => $info)
                                    <div class="date float">
                                        <h3>{{date('d/m', strtotime($date))}}<span class="year">{{date('/Y', strtotime($date))}}</span></h3>
                                        @foreach($info['participants'] as $participant)
                                        <div class="participant dragdrop {{ ($participant->id == Auth::user()->id ? 'active':'') }}" user_id="{{$participant->id}}" date_id="{{$info['date_id']}}">
                                            {{$participant->first_name}} <span class="last_name_desktop">{{$participant->last_name}}</span><span class="last_name_smartphone">{{substr($participant->last_name, 0, 1)}}.</span>
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
<script
              src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
              integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
              crossorigin="anonymous"></script>
<script src="{{ asset('js/winterhours/winterhour_overview.js') }}"></script>
@endsection
