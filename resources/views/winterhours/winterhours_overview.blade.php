@extends('layouts.app')
@section('title', 'Winteruren')
@section('content')
    <div class="winterhours_overview">

        <div class="block">
            <div class="heading">
                Winteruren
            </div>
            <div class="content">
                <h3>Mijn winteruurgroepen</h3>
                @if(!$winterhour_groups->isEmpty())
                    @foreach($winterhour_groups as $winterhour)
                    <div class="winterhour_group">
                        <h4>{{$winterhour->title}}</h4>
                        <div class="info">
                            <div class="day_time">
                                Dag en uur: {{$winterhour->day}} om {{substr($winterhour->time, 0, 5)}}
                            </div>
                        </div>
                        <div class="scheme">
                            hierin komt het schema wanneer dat gegenereerd is :)
                        </div>
                        <div class="participants">
                            <h4>Deelnemers</h4>
                            @foreach($winterhour->participants as $participant)
                            <div class="participant clearfix">
                                <div class="name float">{{$participant->first_name}} {{$participant->last_name}}</div>
                                <div class="gsm float">{{$participant->gsm}}</div>
                            </div>
                            @endforeach
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
@endsection
