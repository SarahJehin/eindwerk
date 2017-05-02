@extends('layouts.app')
@section('title', 'Scorebord')
@section('content')
    <div class="scoreboard">

        <div class="block">
            <div class="heading">
                Scorebord
            </div>
            <div class="content">

                <div class="tabs">
                    Hierin moeten nog 2 tabs komen: 1 voor de jeugd en 1 voor de volwassenen
                </div>
                <div class="podium">
                    Hierin komt de top 3 of top 5
                </div>

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
