@extends('layouts.app')
@section('title', 'Welkom')
@section('content')
<div class="">
    <div class="block">
        <div class="heading">
            Welkom {{Auth::user()->first_name}}
        </div>
        <div class="content">
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
        </div>
    </div>
</div>
@endsection
