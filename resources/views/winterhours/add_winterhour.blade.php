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
                <div class="form_part">
                    <form id="add_winterhour" method="post" enctype="multipart/form-data" action="{{url('add_winterhour')}}" novalidate>
                        {{ csrf_field() }}
                        <div class="total">
                            <div class="part01">
                                <div class="step_content">
                                    <div class="field_wrap groupname">
                                        <label>Groepsnaam</label>
                                        <input type="text" name="groupname" id="groupname" value="{{old('groupname')}}">
                                    </div>

                                    <div class="date_and_time clearfix">
                                        <div class="day_and_time float">
                                            <div class="day_select apply_bootstrap">
                                                <select class="selectpicker" data-size="10" id="from_ranking" name="from_ranking">
                                                    <option value="select_day">Selecteer dag</option>
                                                    <option value="monday">Maandag</option>
                                                    <option value="tuesday">Dinsdag</option>
                                                    <option value="wednesday">Woensdag</option>
                                                    <option value="thursday">Donderdag</option>
                                                    <option value="friday">Vrijdag</option>
                                                    <option value="saturday">Zaterdag</option>
                                                    <option value="sunday">Zondag</option>
                                                </select>
                                            </div>
                                            timepicker
                                        </div>
                                        <div class="date float">
                                            <div class="container_date">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="part02">
                                <div class="step_content">
                                    stap 2
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
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <!--<script src="{{ asset('js/custom_datepicker.js') }}"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>
@endsection