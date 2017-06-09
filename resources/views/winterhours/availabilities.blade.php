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
                @if($winterhour->made_by == Auth::user()->id)
                <div class="back link">
                    <a href="{{url('edit_winterhour/' . $winterhour->id . '?step=3')}}">Terug naar overzicht</a>
                </div>
                @else
                <div class="back link">
                    <a href="{{url('winterhours_overview')}}">Terug naar overzicht</a>
                </div>
                @endif

                @if (session('success_msg'))
                    <div class="success_msg">
                        {{ session('success_msg') }}
                    </div>
                @endif
                
                @if($winterhour->status < 4)
                <div class="descriptive_info">
                    Geef hieronder je beschikbare datums door.<br>
                    Het schema kan pas gemaakt worden wanneer iedereen zijn beschikbaarheid heeft doorgegeven<br>
                    Vink minstens 12 dagen aan waarop je beschikbaar bent.<br>
                    @if($user->id != Auth::user()->id)
                    Beschikbaarheid aanpassen voor <strong>{{$user->first_name}} {{$user->last_name}}</strong>
                    @endif
                </div>

                @if(count($errors) > 0)
                    <div class="error_msg">
                        {{$errors->get('date')[0]}}
                    </div>
                @endif

                <div class="select_all">
                    <input id="select_all" type="checkbox" name="select_all">
                    <label for="select_all">Alle data selecteren</label>
                </div>
                @else 
                <div class="descriptive_info">
                    Beschikbaarheid bekijken van <strong>{{$user->first_name}} {{$user->last_name}}</strong>.
                </div>
                @endif
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
                                    <div class="date_date float">{{date('d/m', strtotime($date->date))}}<span class="year">{{date('/Y', strtotime($date->date))}}</span></div>
                                    @if(array_key_exists($date->id, $user_dates_array))
                                        @if($winterhour->status < 4)
                                            @if($user_dates_array[$date->id]->pivot->available == 1)
                                            <input class="float" type="hidden" name="date[{{$date->id}}]" value="0">
                                            <input class="float" type="checkbox" name="date[{{$date->id}}]" checked="">
                                            @elseif(old('date'))
                                            <input class="float" type="hidden" name="date[{{$date->id}}]" value="0">
                                            <input class="float" type="checkbox" name="date[{{$date->id}}]" {{(old('date')[$date->id] == 'on' ? 'checked=""' : '')}}>
                                            @else
                                            <input class="float" type="hidden" name="date[{{$date->id}}]" value="0">
                                            <input class="float" type="checkbox" name="date[{{$date->id}}]">
                                            @endif
                                        @else
                                            @if($user_dates_array[$date->id]->pivot->available == 1)
                                            <i class="fa fa-check"></i>
                                            @else
                                            <i></i>
                                            @endif
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
                    @if($winterhour->status < 4)
                    <div class="submit_btn">
                        <input type="submit" value="Beschikbaarheid bewaren">
                    </div>
                    @endif
                </form>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
<script type="text/javascript" src="{{ asset('js/winterhours/availabilities.js') }}"></script>
@endsection