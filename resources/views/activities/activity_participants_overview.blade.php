@extends('layouts.app')

@section('content')
    <div class="activity_participants">

        <div class="block">
            <div class="heading" activity_id="{{$activity->id}}">
                {{$activity->title}}
            </div>
            <div class="content">
                <div>
                    <a class="link" href="{{url('activities_list')}}">Terug naar overzicht</a>
                </div>
                <p class="center">Hieronder vind je een overzicht van iedereen die zich ingeschreven heeft voor deze activiteit, alsook door wie ze ingeschreven zijn, en of ze betaald hebben.</p>
                
                @if($activity->participants->isEmpty())
                <div class="center">Er zijn nog geen inschrijvingen voor deze activiteit...</div>
                @else
                <div class="amount_participants">({{count($activity->participants)}}/{{$activity->max_participants}})</div>
                <div class="list">
                    <div class="participant_block header clearfix">
                        <div class="participant float">Deelnemer</div>
                        <div class="email float">E-mail</div>
                        <div class="signed_up_by float">Ingeschreven door</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($activity->participants as $participant)
                    <div class="participant_block clearfix">
                        <div class="participant float clearfix">
                            <div class="name float">{{$participant->last_name}} {{$participant->first_name}}</div>
                            <div class="age float">({{(date('Y')-date('Y', strtotime($participant->birth_date)))}}j.)</div>
                        </div>
                        <div class="email float">{{$participant->email}}</div>
                        <div class="signed_up_by float">{{$participant->pivot->signed_up_by_user->first_name}} {{$participant->pivot->signed_up_by_user->last_name}}</div>
                        <div class="paid float">
                            @if($participant->pivot->status == 1)
                            <input type="checkbox" name="paid{{$participant->id}}" is_checked='false'>
                            @elseif($participant->pivot->status == 2)
                            <input type="checkbox" name="paid{{$participant->id}}" checked="" is_checked='true'>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

    </div>
@endsection
@section('custom_js')
<script src="{{ asset('js/activities/activities_extra.js') }}"></script>
@endsection
