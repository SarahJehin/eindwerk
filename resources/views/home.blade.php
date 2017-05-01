@extends('layouts.app')

@section('content')
<div class="">
    <div class="block">
        <div class="heading">
            Nieuwe activiteit
        </div>
        <div class="content">
            Welcome, you're logged in now!
            <h3>Upcoming activities for me:</h3>
            <div>
            	@foreach($user->activities as $activity)
            	<div>{{$activity->title}}</div>
            	@endforeach
            </div>
        </div>
    </div>
</div>
@endsection
