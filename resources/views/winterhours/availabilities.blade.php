@extends('layouts.app')
@section('title', 'Beschikbaarheid')

@section('custom_css')

@endsection

@section('content')


    <div class="edit_winterhour">

        <div class="block">
            <div class="heading">
                {{$winterhour->title}}
            </div>
            <div class="content">
                <div>
                    Geef hieronder je beschikbare datums door.<br>
                    Het schema kan pas gemaakt worden wanneer iedereen zijn beschikbaarheid heeft doorgegeven
                </div>
                {{--
                <div>
                    @foreach($winterhour->dates as $date)
                    <div>{{$date->date}}</div>
                    @endforeach
                </div>
                --}}
                <div class="dates_overview">
                    @foreach($dates_by_month as $key => $month)
                    <div class="monthly_dates">
                        <h4>{{ucfirst(trans('datetime.' . date("F", strtotime($key))))}}</h4>
                        <div class="dates">
                            @foreach($month as $date)
                            <div class="date">
                                <div>{{$date->date}}</div>
                                @if(array_key_exists($date->id, $user_dates_array))
                                    @if($user_dates_array[$date->id]->pivot->available == 1)
                                    <input type="checkbox" name="available_{{$date->id}}" checked="">
                                    @else
                                    <input type="checkbox" name="available_{{$date->id}}">
                                    @endif
                                @else
                                <input type="checkbox" name="available_{{$date->id}}">
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <div>
                    {{ $test or "Default Message" }}
                </div>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')

@endsection