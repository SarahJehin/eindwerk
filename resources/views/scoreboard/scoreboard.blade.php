@extends('layouts.app')
@section('title', 'Scorebord')
@section('content')
    <div class="scoreboard">

        <div class="block">
            <div class="heading">
                Scorebord
            </div>
            <div class="content">

                <div class="tabs clearfix">
                    <div class="tab adults float active">
                        <div class="bg_helper"></div>
                        Volwassenen
                    </div>
                    <div class="tab youth float">
                        <div class="bg_helper"></div>
                        Jeugd
                    </div>
                </div>


                <div class="scoreboard adults">
                    @if($adult_top_3)
                    <div class="podium">
                        <div class="outer_arc">
                            <ul class='pie'>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content">
                                            <span class="ranking">1</span>
                                            <img src="{{url('images/profile_pictures/' . $adult_top_3[0]->image)}}">
                                            <span class="name">{{$adult_top_3[0]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content">
                                            <span class="ranking">2</span>
                                            <img src="{{url('images/profile_pictures/' . $adult_top_3[1]->image)}}">
                                            <span class="name">{{$adult_top_3[1]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content">
                                            <span class="ranking">3</span>
                                            <img src="{{url('images/profile_pictures/' . $adult_top_3[2]->image)}}">
                                            <span class="name">{{$adult_top_3[2]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="inner_arc">
                                <div class="trophy">
                                    <i class="fa fa-trophy" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!$adult_participants->isEmpty())
                    <div class="board">
                        <table class="table table-header-rotated">
                            <thead>
                                <tr>
                                    <th>Naam</th>
                                    @foreach($adult_activities as $activity)
                                    <th class="rotate"><div><span>{{$activity->title}}</span></div></th>
                                    @endforeach
                                    <th class="rotate"><div><span>Totale score</span></div></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adult_participants as $participant)
                                <tr>
                                    <td>{{$participant->last_name}} {{$participant->first_name}}</td>
                                    @foreach($adult_activities as $activity)
                                    <td data-toggle="tooltip" data-placement="left" data-container="body" title="{{$activity->title}}">
                                        <!-- has activities in the past for which he she has paid -->
                                        @if($participant->activities()->where('activities.id', $activity->id)->exists())
                                        {{1 + $participant->activities()->where('activities.id', $activity->id)->first()->pivot->extra_points}}
                                        @else
                                        
                                        @endif
                                    </td>
                                    @endforeach
                                    <td>{{$participant->total_score()}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="descriptive_info">
                        Het scorebord is nog niet beschikbaar omdat er nog geen activiteiten voorbij zijn waaraan personen hebben deelgenomen.
                    </div>
                    @endif
                </div>

                <div class="scoreboard youth">
                    @if($youth_top_3)
                    <div class="podium">
                        <div class="outer_arc">
                            <ul class='pie'>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content">
                                            <span class="ranking">1</span>
                                            <img src="{{url('images/profile_pictures/' . $youth_top_3[0]->image)}}">
                                            <span class="name">{{$youth_top_3[0]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content">
                                            <span class="ranking">2</span>
                                            <img src="{{url('images/profile_pictures/' . $youth_top_3[1]->image)}}">
                                            <span class="name">{{$youth_top_3[1]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content">
                                            <span class="ranking">3</span>
                                            <img src="{{url('images/profile_pictures/' . $youth_top_3[2]->image)}}">
                                            <span class="name">{{$youth_top_3[2]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="inner_arc">
                                <div class="trophy">
                                    <i class="fa fa-trophy" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(!$youth_participants->isEmpty())
                    <div class="board">
                        <table class="table table-header-rotated">
                            <thead>
                                <tr>
                                    <th>Naam</th>
                                    @foreach($youth_activities as $activity)
                                    <th class="rotate"><div><span>{{$activity->title}}</span></div></th>
                                    @endforeach
                                    <th class="rotate"><div><span>Totale score</span></div></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($youth_participants as $participant)
                                <tr>
                                    <td>{{$participant->last_name}} {{$participant->first_name}}</td>
                                    @foreach($youth_activities as $activity)
                                    <td data-toggle="tooltip" data-placement="left" data-container="body" title="{{$activity->title}}">
                                        <!-- has activities in the past for which he she has paid -->
                                        @if($participant->activities()->where('activities.id', $activity->id)->exists())
                                        <!-- dat hieronder moet dan + de extra score gedaan worden -->
                                        {{1 + $participant->activities()->where('activities.id', $activity->id)->first()->pivot->extra_points}}
                                        @else
                                        
                                        @endif
                                    </td>
                                    @endforeach
                                    <td>{{$participant->total_youth_score()}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="descriptive_info">
                        Het scorebord is nog niet beschikbaar omdat er nog geen jeugdactiviteiten voorbij zijn waaraan personen hebben deelgenomen.
                    </div>
                    @endif
                </div>

            </div>

        </div>

    </div>
@endsection
@section('custom_js')
<script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/modernizr-2.7.1.js"></script>
<script src="{{ asset('js/scoreboard.js') }}"></script>
@endsection
