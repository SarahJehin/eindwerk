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
                                        <div class="real_content" member_id="{{$adult_top_3[0]->id}}">
                                            <span class="ranking">1</span>
                                            <img src="{{url('images/profile_pictures/' . $adult_top_3[0]->image)}}" alt="{{$adult_top_3[0]->first_name}}">
                                            <span class="name">{{$adult_top_3[0]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content" member_id="{{$adult_top_3[1]->id}}">
                                            <span class="ranking">2</span>
                                            <img src="{{url('images/profile_pictures/' . $adult_top_3[1]->image)}}" alt="{{$adult_top_3[1]->first_name}}">
                                            <span class="name">{{$adult_top_3[1]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content" member_id="{{$adult_top_3[2]->id}}">
                                            <span class="ranking">3</span>
                                            <img src="{{url('images/profile_pictures/' . $adult_top_3[2]->image)}}" alt="{{$adult_top_3[2]->first_name}}">
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
                                    <td member_id="{{$participant->id}}">{{$participant->last_name}} {{$participant->first_name}}</td>
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
                    <div><a class="link" href="{{url('export_scoreboard/adult')}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Scorebord exporteren</a></div>
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
                                        <div class="real_content" member_id="{{$youth_top_3[0]->id}}">
                                            <span class="ranking">1</span>
                                            <img src="{{url('images/profile_pictures/' . $youth_top_3[0]->image)}}" alt="{{$youth_top_3[0]->first_name}}">
                                            <span class="name">{{$youth_top_3[0]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content" member_id="{{$youth_top_3[1]->id}}">
                                            <span class="ranking">2</span>
                                            <img src="{{url('images/profile_pictures/' . $youth_top_3[1]->image)}}" alt="{{$youth_top_3[1]->first_name}}">
                                            <span class="name">{{$youth_top_3[1]->first_name}}</span>
                                        </div>
                                    </div>
                                </li>
                                <li class='slice'>
                                    <div class='slice-contents'>
                                        <div class="real_content" member_id="{{$youth_top_3[2]->id}}">
                                            <span class="ranking">3</span>
                                            <img src="{{url('images/profile_pictures/' . $youth_top_3[2]->image)}}" alt="{{$youth_top_3[2]->first_name}}">
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
                                    <td member_id="{{$participant->id}}">{{$participant->last_name}} {{$participant->first_name}}</td>
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
                    <div><a class="link" href="{{url('export_scoreboard/youth')}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Scorebord exporteren</a></div>
                    @else
                    <div class="descriptive_info">
                        Het scorebord is nog niet beschikbaar omdat er nog geen jeugdactiviteiten voorbij zijn waaraan personen hebben deelgenomen.
                    </div>
                    @endif
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
<script type="text/javascript" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/3/modernizr-2.7.1.js"></script>
<script src="{{ asset('js/scoreboard.js') }}"></script>
@endsection
