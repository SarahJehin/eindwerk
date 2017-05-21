@extends('layouts.app')
@section('title', 'Beschikbaarheid')

@section('custom_css')

@endsection

@section('content')


    <div class="availabilities">

        <div class="block">
            <div class="heading">
                {{$winterhour->title}}
            </div>
            <div class="content">

                @if($user->id != Auth::user()->id)
                <div class="back link">
                    <a href="{{url('edit_winterhour/' . $winterhour->id)}}">Terug naar overzicht</a>
                </div>
                @endif

                @if (session('success_msg'))
                    <div class="success_msg">
                        {{ session('success_msg') }}
                    </div>
                @endif
                

                <div class="descriptive_info">
                    Geef hieronder je beschikbare datums door.<br>
                    Het schema kan pas gemaakt worden wanneer iedereen zijn beschikbaarheid heeft doorgegeven<br>
                    @if($user->id != Auth::user()->id)
                    Beschikbaarheid aanpassen voor <strong>{{$user->first_name}} {{$user->last_name}}</strong>
                    @endif
                </div>
                <div class="select_all">
                    <input id="select_all" type="checkbox" name="select_all">
                    <label for="select_all">Alle data selecteren</label>
                </div>
                <form method="post" action="{{url('update_availability')}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="winterhour_id" value="{{$winterhour->id}}">
                    <input type="hidden" name="user_id" value="{{$user->id}}">
                    <div class="dates_overview clearfix">
                        @foreach($dates_by_month as $key => $month)
                        <div class="monthly_dates float">
                            <h4>{{ucfirst(trans('datetime.' . date("F", strtotime($key))))}}</h4>
                            <div class="dates">
                                @foreach($month as $date)
                                <div class="date clearfix">
                                    <div class="date_date float">{{date('d/m/Y', strtotime($date->date))}}</div>
                                    @if(array_key_exists($date->id, $user_dates_array))
                                        @if($user_dates_array[$date->id]->pivot->available == 1)
                                        <input class="float" type="hidden" name="date[{{$date->id}}]" value="0">
                                        <input class="float" type="checkbox" name="date[{{$date->id}}]" checked="">
                                        @else
                                        <input class="float" type="hidden" name="date[{{$date->id}}]" value="0">
                                        <input class="float" type="checkbox" name="date[{{$date->id}}]">
                                        @endif
                                    @else
                                    <input class="float" type="hidden" name="date[{{$date->id}}]" value="0">
                                    <input class="float" type="checkbox" name="date[{{$date->id}}]">
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="submit_btn">
                        <input type="submit" value="Beschikbaarheid bewaren">
                    </div>
                </form>
                <div>
                    {{ $test or "Default Message" }}
                </div>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
<script type="text/javascript" src="{{ asset('js/winterhours/availabilities.js') }}"></script>
@endsection