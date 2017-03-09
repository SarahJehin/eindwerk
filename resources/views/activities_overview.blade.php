@extends('layouts.app')

@section('content')
    <div class="">

        <div class="block">
            <div class="heading">
                Activiteiten overzicht
            </div>
            <div class="content">
                Hierin komt een overzichtje met activiteiten

                @if (session('message'))
                    <div>
                        {{ session('message') }}
                    </div>
                @endif

                <ul>
                @foreach($activities as $activity)
                    <li>{{$activity->title}}</li>
                @endforeach
                </ul>

            </div>
        </div>
    </div>
@endsection