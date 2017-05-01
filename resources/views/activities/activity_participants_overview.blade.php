@extends('layouts.app')

@section('content')
    <div class="activity_participants">

        <div class="block">
            <div class="heading" activity_id="{{$activity->id}}">
                {{$activity->title}}
            </div>
            <div class="content">
                <p>Hieronder vind je een overzicht van iedereen die zich ingeschreven heeft voor deze activiteit, alsook door wie ze ingeschreven zijn, en of ze betaald hebben.</p>
                <div class="list">
                    <div class="participant_block header clearfix">
                        <div class="participant float">Deelnemer</div>
                        <div class="signed_up_by float">Ingeschreven door</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($activity->participants as $participant)
                    <div class="participant_block clearfix">
                        <div class="participant float clearfix">
                            <div class="name float">{{$participant->first_name}} {{$participant->last_name}}</div>
                            <div class="age float">({{(date('Y')-date('Y', strtotime($participant->birth_date)))}}j.)</div>
                        </div>
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

            </div>
        </div>

    </div>
@endsection
@section('custom_js')
<script src="{{ asset('js/activities/activities_extra.js') }}"></script>
@endsection
