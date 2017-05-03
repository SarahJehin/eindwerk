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
                <div class="podium">
                    <div class="outer_arc">
                        <ul class='pie'>
                            <li class='slice'>
                                <div class='slice-contents'>
                                    <div class="real_content"><span>plaats 1</span></div>
                                </div>
                            </li>
                            <li class='slice'>
                                <div class='slice-contents'>
                                    <div class="real_content"><span>plaats 2</span></div>
                                </div>
                            </li>
                            <li class='slice'>
                                <div class='slice-contents'>
                                    <div class="real_content"><span>plaats 3</span></div>
                                </div>
                            </li>
                            <!-- you can add more slices here -->
                        </ul>
                        <div class="inner_arc">
                            <div class="trophy">
                                <i class="fa fa-trophy" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
<!--
                <svg class="pie">
                  <circle cx="115" cy="115" r="110"></circle>
                  <path d="M115,115 L115,5 A110,110 1 0,1 190,35 z"><span>test</span></path>
                </svg>
-->
<!--
<ul class='pie'>
    <li class='slice'>
        <div class='slice-contents'>
            <div class="content">plaats 1</div>
        </div>
    </li>
    <li class='slice'>
        <div class='slice-contents'>
            <div class="content">plaats 2</div>
        </div>
    </li>
    <li class='slice'>
        <div class='slice-contents'>
            <div class="content">plaats 3</div>
        </div>
    </li>
</ul>
-->



                <div class="board">
                    <table>
                        <thead>
                            <tr>
                                <td>Naam</td>
                                @foreach($activities as $activity)
                                <td>{{$activity->title}}</td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{$user->first_name}} {{$user->last_name}}</td>
                                @foreach($activities as $activity)
                                <td>
                                    @if($user->activities()->where('activities.id', $activity->id)->exists())
                                    <!-- dat hieronder moet dan + de extra score gedaan worden -->
                                    {{1 + $user->activities()->where('activities.id', $activity->id)->first()->pivot->status}}
                                    @else
                                    no
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('custom_js')

@endsection
