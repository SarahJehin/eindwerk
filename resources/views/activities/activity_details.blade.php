@extends('layouts.app')


@section('content')


    <div class="activity_details">

        <div class="block">
            <div class="heading">
                {{$activity->title}}
            </div>
            <div class="content">
                <div class="tabs clearfix">
                    <div class="tab details float active">
                        <div class="bg_helper"></div>
                        Details
                    </div>
                    <div class="tab sign_up float">
                        <div class="bg_helper"></div>
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
                            <p>{!! $activity->description !!}</p>
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
                            <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Deelnemen:</h4>
                            <p>Wil je deelnemen aan deze activiteit? Schrijf jezelf en/of anderen dan hieronder in!</p>
                            @if($activity->price > 0)
                            <p>Vergeet het inschrijvingsgeld niet over te schrijven naar BE66 7333 2013 0443.</p>
                            @endif
                            <h4 class="{{str_replace(' ', '_', strtolower($activity->category->name))}}">Wie wil je inschrijven:</h4>
                            <div class="sign_up_for">
                                <div>
                                    <input type="checkbox" name="sign_up_me" id="sign_up_me" hidden>
                                    <label for="sign_up_me">
                                        <span class="checkbox">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </span>
                                        <span>Mezelf inschrijven</span>
                                    </label>
                                </div>
                                <div>
                                    <input type="checkbox" name="sign_up_others" id="sign_up_others" hidden>
                                    <label for="sign_up_others">
                                        <span class="checkbox">
                                            <i class="fa fa-check" aria-hidden="true"></i>
                                        </span>
                                        <span>Anderen inschrijven</span>
                                    </label>
                                </div>
                            </div>
                            <div class="sign_up_others">
                                wordt pas getoond wanneer je de checkbox aanvinkt
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="lightbox_modal">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    <img src="{{url('images/activity_images/' . $activity->poster)}}" alt="{{$activity->title}}">
                </div>
                <div class="modal_footer">
                    {{$activity->title}}
                </div>
            </div>
        </div>

    </div>
@endsection
@section('custom_js')
<script src="{{ asset('js/activities/activity_details.js') }}"></script>

@endsection
