@extends('layouts.app')

@section('custom_css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.2/css/bootstrap-slider.min.css" rel="stylesheet" type="text/css">
@endsection

@section('content')


    <div class="">

        <div class="block">
            <div class="heading">
                Nieuwe activiteit
            </div>
            <div class="content">
                <div class="timeline">
                    tijdlijn
                    <div class="step1">1</div>
                    <div class="step2">2</div>
                    <div class="step3">3</div>
                    <div class="step4">4</div>
                </div>
                <div class="form_part">
                    <form id="add_activity" method="post" action="{{url('add_activity')}}">
                        {{ csrf_field() }}
                        <div class="total">
                            <div class="part01">
                                <div class="step_content">

                                    @if (count($errors) > 0)
                                        There were errors
                                        <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                        </ul>
                                    @endif

                                    categorie en poster enz
                                    <div class="categories">
                                        @foreach($categories as $category)
                                            <div class="category">
                                                <input type="radio" name="category" id="cat{{$category->id}}" value="{{$category->id}}">
                                                <label for="cat{{$category->id}}">
                                                    <img src="{{url('images/category_images/' . $category->image)}}" alt="{{$category->name}}">
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="category">
                                            <input type="radio" name="category" id="cat01">
                                            <label for="cat01"></label>
                                        </div>
                                        <div class="category">
                                            <input type="radio" name="category" id="cat02">
                                            <label for="cat02"></label>
                                        </div>
                                        <div class="category">
                                            <input type="radio" name="category" id="cat03">
                                            <label for="cat03"></label>
                                        </div>
                                    </div>
                                    <div class="poster">
                                        <input type="file" name="poster">
                                    </div>
                                </div>
                            </div>
                            <div class="part02">
                                <div class="step_content">
                                    <div class="field_wrap">
                                        <label>Titel</label>
                                        <input type="text" name="title" id="title">
                                    </div>
                                    <div class="field_wrap">
                                        <label>Beschrijving</label>
                                        <textarea name="description" id="description"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="part03">
                                <div class="step_content">
                                    <div>algemene info</div>

                                    <div class="date_info">

                                        <div class="datepicker_box">
                                            <div class="container_startdate front">

                                            </div>

                                            <div class="container_deadline">

                                            </div>
                                        </div>
                                        <div class="date_type startdate">
                                            <span class="radio">
                                                <span class="bullet selected"></span>
                                            </span>
                                            <span class="label">Datum</span>
                                        </div>
                                        <div class="date_type deadline">
                                            <span class="radio">
                                                <span class="bullet"></span>
                                            </span>
                                            <span class="label">Deadline</span>
                                        </div>

                                        <input type="date" id="startdate" name="startdate" hidden>
                                        <input type="date" id="deadline" name="deadline" hidden>

                                    </div>

                                    <div class="timepicker">
                                        <div class="arrow_up">
                                            <i class="fa fa-angle-up" aria-hidden="true"></i>
                                        </div>
                                        <div class="visible_container">
                                            <ul>
                                                @for($i = 8; $i < 23; $i++)
                                                    <li>
                                                        <input type="radio" name="time" id="{{$i}}_00" value="{{$i}}:00">
                                                        <label for="{{$i}}_00">{{$i}}:00</label>
                                                    </li>
                                                    <li>
                                                        <input type="radio" name="time" id="{{$i}}_30" value="{{$i}}:30">
                                                        <label for="{{$i}}_30">{{$i}}:30</label>
                                                    </li>
                                                @endfor
                                            </ul>
                                        </div>

                                        <div class="arrow_down">
                                            <i class="fa fa-angle-down" aria-hidden="true"></i>
                                        </div>
                                    </div>

                                    <div class="location_info">
                                        locatie info
                                        <input name="location" id="location" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="part04">
                                <div class="step_content">
                                    <div>
                                        extra info
                                    </div><br><br>
                                    <div class="participants slider_block">
                                        <div>Aantal deelnemers</div>
                                        <span class="min_participants">0</span><input id="participants_slider" name="participants" type="text" class="span2" value="" data-slider-min="0" data-slider-max="30" data-slider-step="1" data-slider-value="[0,30]" tooltip="hide"/><span class="max_participants">30</span>
                                    </div>

                                    <div class="price slider_block">
                                        <div>Prijs</div>
                                        <span class="min">0</span><input id="price_slider" name="price" type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="0"/><span class="price_amount">0</span>
                                    </div>

                                    <div class="helpers slider_block">
                                        <div>Aantal helpers</div>
                                        <span class="min">0</span><input id="helpers_slider" name="helpers" type="text" data-slider-min="0" data-slider-max="20" data-slider-step="1" data-slider-value="0"/><span class="helpers_amount">0</span>
                                    </div>

                                    <div class="owner">

                                        <div class="select_toggler">
                                            <span class="select_title">Selecteer een eigenaar</span> <i class="fa fa-sort-desc" aria-hidden="true"></i>
                                        </div>
                                        <ul>
                                            @foreach($owners as $owner)
                                                <li owner-id="{{$owner->id}}">{{$owner->first_name}}</li>
                                            @endforeach
                                        </ul>

                                        <input name="owner" type="number" id="owner" hidden>
                                    </div>

                                    <div class="field_wrap">
                                        <label>URL</label>
                                        <input type="text" name="extra_url" id="extra_url">
                                    </div>

                                    <div>
                                        <input type="checkbox" name="is_visible" id="is_visible">
                                        <label for="is_visible">Zichtbaar zetten</label>
                                    </div>

                                    <div>
                                        <input type="submit" name="submit" value="Aanmaken">
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
    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.datepicker-fork/1.3.0/js/locales/bootstrap-datepicker.nl-BE.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/9.7.2/bootstrap-slider.min.js"></script>
    <script src="{{ asset('js/custom_datepicker.js') }}"></script>
@endsection