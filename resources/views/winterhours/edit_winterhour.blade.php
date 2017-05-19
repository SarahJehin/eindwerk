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
                    <form id="add_winterhour" class="winterhour_form" method="post" enctype="multipart/form-data" action="{{url('edit_winterhour')}}" novalidate>
                        {{ csrf_field() }}
                        <div class="total">
                            <div class="part01">
                                <div class="step_content">
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
                                                <input type="number" name="participant_id[]" class="id" hidden="">
                                                <div class="search_results">
                                                    <ul>
                                                        <li>Sarah Jehin</li>
                                                        <li>Glass Sorenson</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
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
                                            @for($i = 0; $i < (count($winterhour->participants)-1); $i++)
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
                                                <input type="text" class="search_participants name" name="participant[]" placeholder="+ Persoon toevoegen">
                                                <input type="number" name="participant_id[]" class="id" hidden="">
                                                <div class="search_results">
                                                    <ul>
                                                        <li>Sarah Jehin</li>
                                                        <li>Glass Sorenson</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>

                                    <div>
                                        <input type="submit" value="Groep updaten">
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
                                                    @if(count($participant->dates) > 0)
                                                    <i class="fa fa-check"></i> (<a class="link" href="{{url('availabilities/' . $winterhour->id . '/' . $participant->id)}}">Aanpassen</a>)
                                                    @else
                                                    <i class="fa fa-times"></i> (<a class="link" href="{{url('availabilities/' . $winterhour->id . '/' . $participant->id)}}">Aanpassen</a>)
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
                                    In deze stap kan je het schema genereren
                                    @if($winterhour->status >= 2)
                                    <div class="descriptive_info">
                                        Iedereen heeft zijn beschikbaarheid doorgegeven.<br>
                                        Het winteruur kan nu willekeurig aangemaakt worden.  Als je niet tevreden bent met het schema klik dan nogmaals op onderstaande knop om het opnieuw te genereren.
                                    </div>
                                    <div class="submit">
                                        <a href="{{url('generate_scheme/' . $winterhour->id)}}">Schema genereren</a>
                                    </div>
                                    @if($scheme)
                                    <div class="scheme clearfix">
                                        @foreach($scheme as $date => $participants)
                                        <div class="date float">
                                            <h3>{{$date}}</h3>
                                            @foreach($participants as $participant)
                                            <div class="participant">
                                                {{$participant->first_name}} {{$participant->last_name}}
                                            </div>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
                                    @else
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
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>
@endsection