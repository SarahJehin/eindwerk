@extends('layouts.app')
@section('title')
{{$winterhour->title}}
@endsection

@section('custom_css')
<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
@endsection

@section('content')
    <div class="edit_winterhour" ng-controller="WinterhourController">
        <div class="block">
            <div class="heading">
                {{$winterhour->title}}
            </div>
            <div class="content">
                <div class="timeline">
                    <div class="line"></div>
                    <div class="filled_line"></div>
                    <div class="step1 reached">1</div>
                    <div class="step2">2</div>
                    <div class="step3">3</div>
                    <div class="step4">4</div>
                </div>

                @if (session('success_msg'))
                    <div class="success_msg">
                        {{ session('success_msg') }}
                    </div>
                @endif
                <div class="form_part">
                    <div class="not_editable_message">
                        <i class="fa fa-asterisk" aria-hidden="true"></i> Je kan dit winteruur niet meer aanpassen omdat het schema reeds geaccepteerd is.
                    </div>
                    <form id="add_winterhour" class="winterhour_form" method="post" enctype="multipart/form-data" action="{{url('edit_winterhour')}}" novalidate>
                        {{ csrf_field() }}
                        <input type="hidden" name="winterhour_id" value="{{$winterhour->id}}">
                        <div class="total">
                            <div class="part01">
                                <div class="step_content">

                                    @if (count($errors) > 0)
                                        <div class="error_msg">
                                            Niet alle velden werden correct ingevuld. Controleer de errors in elke stap.
                                            @if($errors->get('groupname') || $errors->get('day') || $errors->get('date') || $errors->get('time'))
                                            <ul>
                                                @foreach($errors->get('groupname') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach($errors->get('day') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach($errors->get('date') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach($errors->get('time') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="field_wrap groupname">
                                        <label>Groepsnaam</label>
                                        <input type="text" name="groupname" id="groupname" value="{{old('groupname', $winterhour->title)}}">
                                    </div>

                                    <div class="date_and_time clearfix">
                                        <div class="day_and_time float">
                                            <div class="descriptive_info">
                                                Kies hieronder de dag waarop jullie zullen spelen.
                                                De data worden dan automatisch aangevuld op de kalender.
                                            </div>
                                            <div class="day_select apply_bootstrap">
                                                <select class="selectpicker" data-size="10" id="day" name="day">
                                                    <option value="select_day">Selecteer dag</option>
                                                    <option value="maandag" {{ (old('day') ? (old("day") == 'maandag' ? "selected": "") : ($winterhour->day == 'maandag' ? "selected" : "")) }}>Maandag</option>
                                                    <option value="dinsdag" {{ (old('day') ? (old("day") == 'dinsdag' ? "selected": "") : ($winterhour->day == 'dinsdag' ? "selected" : "")) }}>Dinsdag</option>
                                                    <option value="woensdag" {{ (old('day') ? (old("day") == 'woensdag' ? "selected": "") : ($winterhour->day == 'woensdag' ? "selected" : "")) }}>Woensdag</option>
                                                    <option value="donderdag" {{ (old('day') ? (old("day") == 'donderdag' ? "selected": "") : ($winterhour->day == 'donderdag' ? "selected" : "")) }}>Donderdag</option>
                                                    <option value="vrijdag" {{ (old('day') ? (old("day") == 'vrijdag' ? "selected": "") : ($winterhour->day == 'vrijdag' ? "selected" : "")) }}>Vrijdag</option>
                                                    <option value="zaterdag" {{ (old('day') ? (old("day") == 'zaterdag' ? "selected": "") : ($winterhour->day == 'zaterdag' ? "selected" : "")) }}>Zaterdag</option>
                                                    <option value="zondag" {{ (old('day') ? (old("day") == 'zondag' ? "selected": "") : ($winterhour->day == 'zondag' ? "selected" : "")) }}>Zondag</option>
                                                </select>
                                            </div>
                                            <div class="descriptive_info">
                                                Selecteer het uur waarop jullie zullen spelen.
                                            </div>
                                            <div class="hour_select apply_bootstrap">
                                                <select class="selectpicker" data-size="7" id="time" name="time">
                                                    <option value="select_hour">Selecteer uur</option>
                                                    @for($hour = 8; $hour < 24; $hour++)
                                                    <option value="{{sprintf('%02d', $hour)}}:00" {{ (old("time") == (sprintf('%02d', $hour).':00') ? "selected":(substr($winterhour->time, 0, 5) == (sprintf('%02d', $hour).':00') ? "selected" : "")) }}>{{sprintf("%02d", $hour)}}:00</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                        <div class="date float">
                                            <div class="descriptive_info">
                                                Klik hieronder de dagen af waarop er niet gespeeld kan worden. 
                                                (bvb. tornooi, Kerstmis, Oudjaar, ...)
                                            </div>
                                            <div class="container_date">
                                                <div class="disable_datepicker"></div>
                                            </div>
                                            <div class="inputs">
                                                @if(count(old('date')))
                                                    @foreach(old('date') as $date)
                                                    <input type="text" name="date[]" value="{{$date}}" hidden>
                                                    @endforeach
                                                @else
                                                    @foreach($winterhour->dates->reverse() as $date)
                                                    <input type="text" name="date[]" value="{{$date->date}}" hidden>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="previous_next clearfix">
                                        <div class="previous link float">

                                        </div>
                                        <div class="next link" step="2">
                                            Volgende <i class="fa fa-angle-right" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="part02">
                                <div class="step_content">
                                    @if(count($errors->get('participant_id')) > 0)
                                    <div class="error_msg">
                                        <ul>
                                            @foreach ($errors->get('participant_id') as $error)
                                            <li>{{ ucfirst($error) }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    <div class="descriptive_info">
                                        Hieronder kan je alle groepsleden toevoegen.  Enkel personen die lid (winter- of zomerlid) zijn bij Sportiva kunnen toegevoegd worden.
                                    </div>

                                    <div class="add_participants">
                                        <div class="add_participant clearfix template">
                                            <div class="search_functionality float">
                                                <input type="text" class="search_participants name" name="participant[]" placeholder="Zoek een lid om toe te voegen">
                                                <input type="text" name="participant_name[]" class="participant_name" hidden="" disabled="">
                                                <input type="number" name="" class="id" hidden="" disabled="">
                                                <div class="search_results">
                                                    <ul>
                                                        <li>Sarah Jehin</li>
                                                        <li>Glass Sorenson</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <span class="float delete not_working" title="Verwijderen"><i class="fa fa-times"></i></span>
                                        </div>
                                        @if (count($errors) > 0)
                                            @for($i = 0; $i < count(old('participant_id')); $i++)
                                            <div class="add_participant clearfix">
                                                <div class="search_functionality float">
                                                    <input type="text" class="search_participants name" name="participant[]" placeholder="Zoek een lid om toe te voegen" autocomplete="off" readonly="" disabled="" value="{{old('participant_name')[$i]}}">
                                                    <input type="text" name="participant_name[]" class="participant_name" hidden="" value="{{old('participant_name')[$i]}}">
                                                    <input type="number" name="participant_id[]" class="id" hidden="" value="{{old('participant_id')[$i]}}">
                                                    <div class="search_results">
                                                        <ul>
                                                            <li>Sarah Jehin</li>
                                                            <li>Glass Sorenson</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                @if(old('participant_id')[$i] != Auth::user()->id)
                                                <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
                                                @endif
                                            </div>
                                            @endfor
                                        @else
                                            @for($i = 0; $i < count($winterhour->participants); $i++)
                                            <div class="add_participant clearfix">
                                                <div class="search_functionality float">
                                                    <input type="text" class="search_participants name" name="participant[]" placeholder="+ Persoon toevoegen" autocomplete="off" readonly="" disabled="" value="{{$winterhour->participants[$i]->first_name}} {{$winterhour->participants[$i]->last_name}}">
                                                    <input type="text" name="participant_name[]" class="participant_name" hidden="" value="{{$winterhour->participants[$i]->first_name}} {{$winterhour->participants[$i]->last_name}}">
                                                    <input type="number" name="participant_id[]" class="id" hidden="" value="{{$winterhour->participants[$i]->id}}">
                                                    <div class="search_results">
                                                        <ul>
                                                            <li>Sarah Jehin</li>
                                                            <li>Glass Sorenson</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                @if($winterhour->participants[$i]->id != Auth::user()->id)
                                                <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
                                                @endif
                                            </div>
                                            @endfor
                                        @endif
                                        <div class="add_participant clearfix">
                                            <div class="search_functionality float">
                                                <input type="text" class="search_participants name" name="participant[]" placeholder="Zoek een lid om toe te voegen" autocomplete="off">
                                                <input type="text" name="participant_name[]" class="participant_name" hidden="">
                                                <input type="number" name="" class="id" hidden="">
                                                <div class="search_results">
                                                    <ul>
                                                        <li>Sarah Jehin</li>
                                                        <li>Glass Sorenson</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <span class="float delete not_working" title="Verwijderen"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>
                                    @if($winterhour->status < 4)
                                    <div>
                                        <input type="submit" value="Groep updaten">
                                    </div>
                                    @endif
                                    <div class="previous_next clearfix">
                                        <div class="previous link float" step="1">
                                            <i class="fa fa-angle-left" aria-hidden="true"></i> Vorige
                                        </div>
                                        <div class="next link" step="3">
                                            Volgende <i class="fa fa-angle-right" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="part03">
                                <div class="step_content">
                                    <div class="availabilities">
                                        <div class="descriptive_info">
                                            Hieronder heb je een overzicht met alle deelnemers en of zij al dan niet hun beschikbaarheid hebben ingevuld. Als organisator van dit winteruur kan jij de beschikbaarheden van anderen ook aanpassen.<br>
                                            Pas als iedereen z'n beschikbaarheid heeft doorgegeven, kan je naar stap 4 gaan om het winteruur te genereren.
                                        </div>
                                        <div class="participants">
                                            <div class="participant header clearfix">
                                                <div class="name float">
                                                    Deelnemer
                                                </div>
                                                <div class="availability_ok float">
                                                    Beschikbaarheid
                                                </div>
                                            </div>
                                            @foreach($winterhour->participants as $participant)
                                            <div class="participant clearfix">
                                                <div class="name float">
                                                    {{$participant->first_name}} {{$participant->last_name}}
                                                </div>
                                                <div class="availability_ok float">
                                                    @if($winterhour->status < 4)
                                                        @if(count($participant->dates->where('winterhour_id', $winterhour->id)) > 0)
                                                        <i class="fa fa-check"></i> (<a class="link" href="{{url('availabilities/' . $winterhour->id . '/' . $participant->id)}}">Aanpassen</a>)
                                                        @else
                                                         (<a class="link" href="{{url('availabilities/' . $winterhour->id . '/' . $participant->id)}}">Aanpassen</a>)
                                                        @endif
                                                    @else
                                                        <a class="link" href="{{url('availabilities/' . $winterhour->id . '/' . $participant->id)}}">Bekijken</a>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="previous_next clearfix">
                                        <div class="previous link float" step="2">
                                            <i class="fa fa-angle-left" aria-hidden="true"></i> Vorige
                                        </div>
                                        <div class="next link" step="4">
                                            Volgende <i class="fa fa-angle-right" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="part04">
                                <div class="step_content scheme_generation">
                                    @if (session('success_msg'))
                                        <div class="success_msg">
                                            {{ session('success_msg') }}
                                        </div>
                                    @endif
                                    @if($winterhour->status >= 2)
                                        <div ng-if="(winterhour_status < 4)">
                                            <div class="descriptive_info">
                                                Iedereen heeft zijn beschikbaarheid doorgegeven.<br>
                                                Het winteruur kan nu willekeurig aangemaakt worden.  Als je niet tevreden bent met het schema klik dan nogmaals op onderstaande knop om het opnieuw te genereren. Of versleep de deelnemers van datum.<br>
                                                <span ng-if="(winterhour_status == 3)">
                                                    Wanneer je tevreden bent over het schema, <a class="link" href="{{url('save_scheme/' . $winterhour->id)}}">accepteer</a> het dan.
                                                </span>
                                            </div>
                                            <div class="submit">
                                                <!--<a href="{{url('generate_scheme/' . $winterhour->id)}}">Schema genereren</a>-->
                                                <div ng-if="(winterhour_status == 2)" class="generate_scheme">Schema genereren</div>
                                                <!--<a href="{{url('generate_scheme/' . $winterhour->id)}}">Schema opnieuw genereren</a>-->
                                                <div ng-if="(winterhour_status == 3)" class="generate_scheme">Schema opnieuw genereren</div>
                                            </div>
                                        </div>

                                        <div class="loader">
                                            <img src="{{url('images/site_images/loader01.gif')}}">
                                        </div>
                                        <div ng-if="scheme_exists">
                                            <div class="swap_message">
                                                Wissel bericht.
                                            </div>
                                            <div class="scheme clearfix">
                                                <div class="date float" ng-repeat="(date, info) in scheme">
                                                    <h3>@{{date | date : "dd/MM"}}<span class="year">@{{date | date : "/y"}}</span></h3>
                                                    <div class="participant dragdrop" user_id="@{{participant.id}}" date_id="@{{info['date_id']}}" ng-repeat="participant in info.participants">
                                                        @{{participant.first_name}} <span class="long_last_name">@{{participant.last_name}}</span><span class="short_last_name">@{{participant.last_name | limitTo : 1}}.</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="play_times">
                                                <div ng-repeat="(times, participants) in play_times">
                                                    <span class="title">Spelen <span class="times">@{{times}}</span> keer: </span>
                                                    <span ng-repeat="participant in participants">@{{participant.first_name}} @{{participant.last_name}}<span ng-if="!$last">, </span></span>
                                                    
                                                </div>
                                            </div>

                                            <div class="accept_scheme" ng-if="(winterhour_status < 4 && winterhour_status > 2)">
                                                <div class="descriptive_info">
                                                    Als je tevreden bent met het schema, klik dan op onderstaande knop om het zichtbaar te zetten voor anderen.
                                                </div>
                                                <div class="submit">
                                                    <a href="{{url('save_scheme/' . $winterhour->id)}}">Schema accepteren</a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="error_msg" ng-if="error_msg_exists">
                                            @{{error_msg}}
                                        </div>
                                        

                                    @elseif($winterhour->status < 2)
                                    <div class="descriptive_info">Je kan het schema pas genereren wanneer alle deelnemers hun beschikbaarheid hebben doorgegeven.</div>
                                    @endif
                                    <div class="previous_next clearfix">
                                        <div class="previous link float" step="3">
                                            <i class="fa fa-angle-left" aria-hidden="true"></i> Vorige
                                        </div>
                                        <div class="next link">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
    <script type="text/javascript">
        var winterhour_id = {{$winterhour->id}};
    </script>
    <script
              src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
              integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
              crossorigin="anonymous"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>
@endsection