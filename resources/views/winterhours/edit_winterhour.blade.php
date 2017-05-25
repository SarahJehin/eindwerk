@extends('layouts.app')
@section('title')
{{$winterhour->title}}
@endsection

@section('custom_css')
<!--<link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">-->
<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
@endsection

@section('content')

    <div class="edit_winterhour">

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
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
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
                                                    <option value="maandag" {{ (old("day") == 'maandag' ? "selected":($winterhour->day == 'maandag' ? "selected" : "")) }}>Maandag</option>
                                                    <option value="dinsdag" {{ (old("day") == 'dinsdag' ? "selected":($winterhour->day == 'dinsdag' ? "selected" : "")) }}>Dinsdag</option>
                                                    <option value="woensdag" {{ (old("day") == 'woensdag' ? "selected":($winterhour->day == 'woensdag' ? "selected" : "")) }}>Woensdag</option>
                                                    <option value="donderdag" {{ (old("day") == 'donderdag' ? "selected":($winterhour->day == 'donderdag' ? "selected" : "")) }}>Donderdag</option>
                                                    <option value="vrijdag" {{ (old("day") == 'vrijdag' ? "selected":($winterhour->day == 'vrijdag' ? "selected" : "")) }}>Vrijdag</option>
                                                    <option value="zaterdag" {{ (old("day") == 'zaterdag' ? "selected":($winterhour->day == 'zaterdag' ? "selected" : "")) }}>Zaterdag</option>
                                                    <option value="zondag" {{ (old("day") == 'zondag' ? "selected":($winterhour->day == 'zondag' ? "selected" : "")) }}>Zondag</option>
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
                                                @if (count($errors) > 0)
                                                    @foreach(old('date') as $date)
                                                    <input type="text" name="date[]" value="{{$date}}" hidden>
                                                    @endforeach
                                                @else
                                                    @foreach($winterhour->dates as $date)
                                                    <input type="text" name="date[]" value="{{$date}}" hidden>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="part02">
                                <div class="step_content">
                                    <div class="descriptive_info">
                                        Hieronder kan je alle groepsleden toevoegen.  Enkel personen die lid (winter- of zomerlid) zijn bij Sportiva kunnen toegevoegd worden.
                                    </div>

                                    <div class="add_participants">
                                        <div class="add_participant clearfix template">
                                            <div class="search_functionality float">
                                                <input type="text" class="search_participants name" name="participant[]" placeholder="+ Persoon toevoegen">
                                                <input type="text" name="participant_name[]" class="participant_name" hidden="" disabled="">
                                                <input type="number" name="participant_id[]" class="id" hidden="" disabled="">
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
                                            @for($i = 0; $i < (count(old('participant_id'))-1); $i++)
                                            <div class="add_participant clearfix">
                                                <div class="search_functionality float">
                                                    <input type="text" class="search_participants name" name="participant[]" placeholder="+ Persoon toevoegen" autocomplete="off" readonly="" disabled="" value="{{old('participant_name')[$i]}}">
                                                    <input type="text" name="participant_name[]" class="participant_name" hidden="" value="{{old('participant_name')[$i]}}">
                                                    <input type="number" name="participant_id[]" class="id" hidden="" value="{{old('participant_id')[$i]}}">
                                                    <div class="search_results">
                                                        <ul>
                                                            <li>Sarah Jehin</li>
                                                            <li>Glass Sorenson</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
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
                                                <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
                                            </div>
                                            @endfor
                                        @endif
                                        <div class="add_participant clearfix">
                                            <div class="search_functionality float">
                                                <input type="text" class="search_participants name" name="participant[]" placeholder="+ Persoon toevoegen" autocomplete="off">
                                                <input type="text" name="participant_name[]" class="participant_name" hidden="">
                                                <input type="number" name="participant_id[]" class="id" hidden="">
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
                                                        @if(count($participant->dates) > 0)
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
                                        @if($winterhour->status < 4)
                                        <div class="descriptive_info">
                                            Iedereen heeft zijn beschikbaarheid doorgegeven.<br>
                                            Het winteruur kan nu willekeurig aangemaakt worden.  Als je niet tevreden bent met het schema klik dan nogmaals op onderstaande knop om het opnieuw te genereren. Of versleep de deelnemers van datum.<br>
                                            @if($winterhour->status >= 3)
                                            Wanneer je tevreden bent over het schema, <a class="link" href="{{url('save_scheme/' . $winterhour->id)}}">accepteer</a> het dan.
                                            @endif
                                        </div>
                                        <div class="submit">
                                            @if($winterhour->status == 2)
                                            <a href="{{url('generate_scheme/' . $winterhour->id)}}">Schema genereren</a>
                                            @elseif($winterhour->status == 3)
                                            <a href="{{url('generate_scheme/' . $winterhour->id)}}">Schema opnieuw genereren</a>
                                            @endif
                                        </div>
                                        @endif
                                        @if($scheme)
                                        <div class="swap_message">
                                            Wissel bericht.
                                        </div>
                                        <div class="scheme clearfix">
                                            @foreach($scheme as $date => $info)
                                            <div class="date float">
                                                <h3>{{date('d/m/Y', strtotime($date))}}</h3>
                                                @foreach($info['participants'] as $participant)
                                                <div class="participant dragdrop" user_id="{{$participant->id}}" date_id="{{$info['date_id']}}">
                                                    {{$participant->first_name}} {{$participant->last_name}}
                                                </div>
                                                @endforeach
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="play_times">
                                            @foreach($play_times as $times => $participants)
                                            <div>
                                                <span class="title">Spelen <span class="times">{{$times}}</span> keer: </span>
                                                @foreach($participants as $participant)
                                                <span>{{$participant->first_name}} {{$participant->last_name}}</span>
                                                @if($participant != end($participants))
                                                ,
                                                @endif
                                                @endforeach
                                            </div>
                                            @endforeach
                                        </div>
                                            @if($winterhour->status < 4)
                                            <div class="accept_scheme">
                                                <div class="descriptive_info">
                                                    Als je tevreden bent met het schema, klik dan op onderstaande knop om het zichtbaar te zetten voor anderen.
                                                </div>
                                                <div class="submit">
                                                    <a href="{{url('save_scheme/' . $winterhour->id)}}">Schema accepteren</a>
                                                </div>
                                            </div>
                                            @endif
                                        @endif
                                    @elseif($winterhour->status < 2)
                                    Je kan het schema pas genereren wanneer alle deelnemers hun beschikbaarheid hebben doorgegeven.
                                    @endif
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
        //console.log(winterhour_id);
    </script>
    <script
              src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
              integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
              crossorigin="anonymous"></script>
    <!--<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>-->
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>
@endsection