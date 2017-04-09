@extends('layouts.app')


@section('content')


    <div class="activity_details">

        <div class="block">
            <div class="heading">
                {{$activity->title}}
            </div>
            <div class="content">
                <div class="tabs clearfix">
                    <div class="details float active">
                        Details
                    </div>
                    <div class="sign_up float">
                        Inschrijven
                    </div>
                </div>

                <p>Terug naar overzicht</p>

                <div class="activity_info clearfix">
                    <div class="poster float">
                        <img src="{{url('images/activity_images/' . $activity->poster)}}" alt="{{$activity->title}}">
                    </div>
                    <div class="info float">
                        <div>
                            <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Beschrijving:</h4>
                            <p>{{$activity->description}}</p>
                        </div>
                        <div>
                            <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Algemene info:</h4>
                            <div>
                                <i class="fa fa-user" aria-hidden="true"></i>
                                {{$activity->min_participants}} - {{$activity->max_participants}} deelnemers
                            </div>
                            <div>
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                {{date('d-m-Y', strtotime($activity->startdate))}} (inschrijven tot {{date('d-m-Y', strtotime($activity->deadline))}})
                            </div>
                            <div>
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                {{date('H:i', strtotime($activity->startdate))}}
                            </div>
                            @if($activity->price > 0)
                            <div>
                                <i class="fa fa-eur" aria-hidden="true"></i>
                                {{ $activity->price }}
                            </div>
                            @endif
                            @if($activity->url)
                            <div>
                                <i class="fa fa-link" aria-hidden="true"></i>
                                <a href="{{ $activity->url }}" target="_blank">{{ $activity->url }}</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="sign_up float">
                        <div>
                            <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Wie wil je inschrijven:</h4>
                            <p>Mezelf - anderen</p>
                        </div>
                        hierin kan je je inschrijven voor deze activiteit (+ ook optie om anderen in te schrijven)
                        Misschien in db dan ook bijhouden door wie ze ingeschreven werden
                    </div>

                </div>

            </div>
        </div>


    </div>
@endsection
@section('custom_js')
<script src="{{ asset('js/activities/activity_details.js') }}"></script>

@endsection
