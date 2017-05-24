@extends('layouts.app')
@section('title', 'Nieuwe activiteit')

@section('custom_css')
<!--<link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">-->
<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
@endsection

@section('content')


    <div class="add_winterhour">

        <div class="block">
            <div class="heading">
                Nieuwe winteruur groep
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
                <div class="descriptive_info step_not_reachable">! Deze stap kan je pas bekijken wanneer je de groep hebt aangemaakt !</div>
                <div class="form_part">
                    <form id="add_winterhour" method="post" enctype="multipart/form-data" action="{{url('add_winterhour')}}" novalidate>
                        {{ csrf_field() }}
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
                                        <input type="text" name="groupname" id="groupname" value="{{old('groupname')}}">
                                    </div>

                                    <div class="date_and_time clearfix">
                                        <div class="day_and_time float">
                                            <div class="descriptive_info">
                                                Kies hieronder de dag waarop jullie zullen spelen.
                                                De data worden dan automatisch aangevuld op de kalender.
                                            </div>
                                            <div class="day_select apply_bootstrap">
                                                <select class="selectpicker" data-size="10" id="day" name="day">
                                                    <option value="">Selecteer dag</option>
                                                    <option value="maandag" {{ (old("day") == 'maandag' ? "selected":"") }}>Maandag</option>
                                                    <option value="dinsdag" {{ (old("day") == 'dinsdag' ? "selected":"") }}>Dinsdag</option>
                                                    <option value="woensdag" {{ (old("day") == 'woensdag' ? "selected":"") }}>Woensdag</option>
                                                    <option value="donderdag" {{ (old("day") == 'donderdag' ? "selected":"") }}>Donderdag</option>
                                                    <option value="vrijdag" {{ (old("day") == 'vrijdag' ? "selected":"") }}>Vrijdag</option>
                                                    <option value="zaterdag" {{ (old("day") == 'zaterdag' ? "selected":"") }}>Zaterdag</option>
                                                    <option value="zondag" {{ (old("day") == 'zondag' ? "selected":"") }}>Zondag</option>
                                                </select>
                                            </div>
                                            <div class="descriptive_info">
                                                Selecteer het uur waarop jullie zullen spelen.
                                            </div>
                                            <div class="hour_select apply_bootstrap">
                                                <select class="selectpicker" data-size="7" id="time" name="time">
                                                    <option value="">Selecteer uur</option>
                                                    @for($hour = 8; $hour < 24; $hour++)
                                                    <option value="{{sprintf('%02d', $hour)}}:00" {{ (old("time") == sprintf('%02d', $hour).':00' ? "selected":"") }}>{{sprintf("%02d", $hour)}}:00</option>
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
                                            <div class="inputs">
                                                @if (old('date'))
                                                    @foreach(old('date') as $date)
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
                                                <input type="text" class="search_participants name" name="participant[]" placeholder="+ Persoon toevoegen" autocomplete="off">
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
                                            <span class="float delete" title="Verwijderen"><i class="fa fa-times"></i></span>
                                        </div>
                                    </div>

                                    <div class="descriptive_info">
                                        Om naar stap 3 en 4 te kunnen gaan, moet je deze groep eerst aanmaken, daarna kan je het schema genereren.
                                    </div>
                                    <div>
                                        <input type="submit" value="Groep aanmaken">
                                    </div>

                                </div>
                            </div>
                            <div class="part03">
                                <div class="step_content">
                                    stap 3
                                </div>
                            </div>
                            <div class="part04">
                                <div class="step_content">
                                    stap 4
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
        var previous_clicked_step;
    </script>
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <!--<script src="{{ asset('js/custom_datepicker.js') }}"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>
@endsection