@extends('layouts.app')
@section('title', 'Winteruren')
@section('content')
    <div class="scoreboard">

        <div class="block">
            <div class="heading">
                Winteruren
            </div>
            <div class="content">
                test

                <h3>Mijn winteruurgroepen</h3>
                @if(!$winterhour_groups->isEmpty())
                laat alle winteruren zien
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
