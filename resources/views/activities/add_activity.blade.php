@extends('layouts.app')
@section('title', 'Nieuwe activiteit')

@section('custom_css')
<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.2/css/bootstrap-slider.min.css" rel="stylesheet" type="text/css">
<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" type="text/css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.4.0/croppie.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
@endsection

@section('content')
    <div class="add_activity">
        <div class="block">
            <div class="heading">
                Nieuwe activiteit
            </div>
            <div class="content">
                <div class="timeline">
                    <div class="line"></div>
                    <div class="filled_line"></div>
                    <div class="step1 reached   ">1</div>
                    <div class="step2">2</div>
                    <div class="step3">3</div>
                    <div class="step4">4</div>
                </div>
                <div class="form_part">
                    <form id="add_activity" method="post" enctype="multipart/form-data" action="{{url('add_activity')}}" novalidate>
                        {{ csrf_field() }}
                        <div class="total">
                            <div class="part01">
                                <div class="step_content">

                                    @if (count($errors) > 0)
                                        <div class="error_msg">
                                            Niet alle velden werden correct ingevuld. Controleer de errors bij elke stap.
                                            <ul>
                                            @foreach ($errors->get('category') as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                            @foreach ($errors->get('poster') as $error)
                                                <li>{{ $error }}</li>@endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div class="category_poster clearfix">
                                        <div class="category_block float">
                                            <h3>Kies een categorie</h3>
                                            <div class="categories">
                                                @foreach($categories as $category)
                                                    <div class="category">
                                                        <input type="radio" name="category" id="cat{{$category->id}}" value="{{$category->id}}" <?php if($category->id == old('category')) {echo("checked");} ?>>
                                                        <label for="cat{{$category->id}}" title="{{$category->name}}">
                                                            <img src="{{url('images/category_images/' . $category->image)}}" alt="{{$category->name}}">
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="poster_block float">
                                            
                                            <h3>Upload poster</h3>
                                            <div class="poster">
                                                <label for="poster">
                                                    <img class="uploaded_poster" src="" alt="poster">
                                                    <span>Poster kiezen</span>
                                                </label>
                                                <input type="hidden" id="imagebase64" name="imagebase64">
                                                <input class="poster_input" type="text" name="poster" hidden="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="previous_next clearfix">
                                        <div class="previous link float">
                                            <!-- no previous button on the first step -->
                                        </div>
                                        <div class="next link" step="2">
                                            Volgende <i class="fa fa-angle-right" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="part02">
                                <div class="step_content">

                                    @if ($errors->get('title') || $errors->get('description'))
                                        <div class="error_msg">
                                            <ul>
                                                @foreach ($errors->get('title') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach ($errors->get('description') as $error)
                                                    <li>{{ $error }}</li>@endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="title description">
                                        <h3><label for="title">Titel</label></h3>
                                        <div class="field_wrap">
                                            <input type="text" name="title" id="title" value="{{ old('title') }}">
                                        </div>
                                        <h3><label for="description">Beschrijving</label></h3>
                                        <div class="field_wrap">
                                            <textarea name="description" id="description" hidden="hidden">{{ old('description') }}</textarea>
                                            <div id="summernote" class="apply_bootstrap"></div>
                                        </div>
                                    </div>

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
                                    <div class="clearfix">
                                        @if ($errors->get('startdate') || $errors->get('deadline') || $errors->get('time'))
                                            <div class="error_msg">
                                                <ul>
                                                    @foreach ($errors->get('startdate') as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                    @foreach ($errors->get('deadline') as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                    @foreach ($errors->get('starttime') as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                    @foreach ($errors->get('endtime') as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                    @foreach ($errors->get('location') as $error)
                                                        <li>Locatie op kaart is verplicht indien je 'Andere locatie' gekozen hebt.</li>
                                                    @endforeach
                                                    @if($errors->get('latitude') || $errors->get('longitude'))
                                                        <li>Zorg ervoor dat er een marker op het kaartje staat.</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="date_time_info float clearfix">
                                            <div class="date_info">
                                                <h3>Datum</h3>
                                                <div class="date_type clearfix">
                                                    <div class="startdate float">
                                                        <span class="radio">
                                                            <span class="bullet selected"></span>
                                                        </span>
                                                        <span class="label">Datum</span>
                                                    </div>
                                                    <div class="deadline float">
                                                        <span class="radio">
                                                            <span class="bullet"></span>
                                                        </span>
                                                        <span class="label">Deadline</span>
                                                    </div>
                                                </div>
                                                <div class="datepicker_box">
                                                    <div class="container_startdate front">

                                                    </div>

                                                    <div class="container_deadline">

                                                    </div>
                                                </div>
                                                
                                                <input type="date" id="startdate" name="startdate" hidden value="{{old('startdate')}}">
                                                <input type="date" id="deadline" name="deadline" hidden value="{{old('deadline')}}">
                                            </div>

                                            <div class="starttime">
                                                <h3><label for="starttime">Start</label></h3>
                                                <div class="field_wrap">
                                                    <input type="text" name="starttime" id="starttime" value="{{old('starttime', '14:00')}}">
                                                </div>
                                            </div>

                                            <div class="endtime">
                                                <h3><label for="endtime">Eind</label></h3>
                                                <div class="field_wrap">
                                                    <input type="text" name="endtime" id="endtime" value="{{old('endtime', '17:00')}}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="location_info float">
                                            <h3>Locatie</h3>
                                            <div class="location_type">
                                                <div class="loc_sportiva">
                                                    <input type="radio" id="loc_sportiva" name="location_type" value="sportiva" hidden <?php if(old('location_type') != 'else') { echo('checked'); } ?>>
                                                    <label for="loc_sportiva">
                                                        <span class="radio">
                                                            <span class="bullet <?php if(old('location_type') != 'else') { echo('selected'); } ?>"></span>
                                                        </span>
                                                        <span>Sportiva</span>
                                                    </label>
                                                </div>
                                                <div class="loc_else">
                                                    <input type="radio" id="loc_else" name="location_type" value="else" hidden <?php if(old('location_type') == 'else') { echo('checked'); } ?>>
                                                    <label for="loc_else">
                                                        <span class="radio">
                                                            <span class="bullet <?php if(old('location_type') == 'else') { echo('selected'); } ?>"></span>
                                                        </span>
                                                        <span>Andere locatie</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <div class="google_maps">
                                                <input id="place-input" class="controls" type="text" placeholder="Locatie zoeken ..." name="location">
                                                <div id="map"></div>
                                            </div>
                                            <input id="latitude" name="latitude" type="text" value="{{old('latitude')}}" required hidden>
                                            <input id="longitude" name="longitude" type="text" value="{{old('longitude')}}" required hidden>
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
                                <div class="step_content">

                                    @if ($errors->get('price') || $errors->get('helpers') || $errors->get('owner') || $errors->get('url'))
                                        <div class="error_msg">
                                            <ul>
                                                @foreach ($errors->get('price') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach ($errors->get('helpers') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach ($errors->get('owner') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                                @foreach ($errors->get('extra_url') as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="price slider_block">
                                        <div>Prijs</div>
                                        <span class="min">0</span><input id="price_slider" name="price" type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="<?php if(old('price') == null){echo('0'); } else {echo(old('price'));} ?>"/><span class="price_amount"><?php if(old('price') == null){echo('0'); } else {echo(old('price'));} ?></span>
                                    </div>

                                    <div class="participants slider_block">
                                        <div>Aantal deelnemers</div>
                                        <span class="min_participants"><?php if(old('participants') == null){echo('0'); } else {echo(explode(',',old('participants'))[0]);} ?></span><input id="participants_slider" name="participants" type="text" class="span2" value="" data-slider-min="0" data-slider-max="31" data-slider-step="1" data-slider-value="[<?php if(old('participants') == null){echo('0'); } else {echo(explode(',',old('participants'))[0]);} ?>,<?php if(old('participants') == null){echo('31'); } else {echo(explode(',',old('participants'))[1]);} ?>]" tooltip="hide"/><span class="max_participants"><?php if(old('participants') == null){echo('&infin;'); } else {echo(explode(',',old('participants'))[1]);} ?></span>
                                    </div>

                                    <div class="owner">                                    
                                        <label for="owner">Verantwoordelijke</label>
                                        <div class="apply_bootstrap">
                                            <select name="owner" class="selectpicker" title="Selecteer een verantwoordelijke">
                                                @foreach($owners as $owner)
                                                    <option value="{{$owner->id}}" {{ (old("owner") == $owner->id ? "selected":"") }}>{{$owner->first_name}} {{$owner->last_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="extra_url">
                                        <label for="extra_url">URL (optioneel)</label>
                                        <div class="field_wrap">
                                            <input id="extra_url" type="text" name="extra_url" id="extra_url" value="{{old('extra_url')}}">
                                        </div>
                                    </div>

                                    <div class="visibility">
                                        <input type="checkbox" name="is_visible" id="is_visible" <?php if(old('is_visible') == "on") {echo('checked');}?> hidden>
                                        <label for="is_visible">
                                            <span class="checkbox">
                                                <i class="fa fa-check" aria-hidden="true"></i>
                                            </span>
                                            <span>Zichtbaar zetten</span>
                                        </label>
                                    </div>

                                    <div class="submit">
                                        <button type="submit">
                                            Activiteit aanmaken
                                        </button>
                                    </div>

                                    <div class="previous_next clearfix">
                                        <div class="previous link float" step="3">
                                            <i class="fa fa-angle-left" aria-hidden="true"></i> Vorige
                                        </div>
                                        <div class="next link">
                                            <!-- no next button available -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="upload_activity_poster_modal">
            <div class="content">
                <div class="header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="body">
                    <div class="upload_poster">
                        <label for="upload">Upload poster (min 600x850)</label>
                        <input id="upload" value="Choose a file" accept="image/*" type="file" hidden="">

                        <div class="upload-wrap">
                            <div id="upload-container" class="croppie-container"></div>
                        </div>
                        <button class="save_poster submit">Poster bewaren</button>
                    </div>
                </div>
            </div>
            
        </div>

    </div>
@endsection
@section('custom_js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.2/bootstrap-slider.min.js"></script>
    <script src="{{ asset('js/custom_datepicker.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.js"></script>
    <script src="{{ asset('js/custom_google_maps.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initAutocomplete&key=AIzaSyA69WeWJnH4qyNdwyjEjAc9YAOXA1Ooi-c"
            async defer></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.4.0/croppie.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('js/activities/add_activity.js') }}"></script>
    </script>
@endsection