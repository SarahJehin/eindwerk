@extends('layouts.app')
@section('title', 'Nieuwe oefening')

@section('custom_css')

<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.css" rel="stylesheet">
@endsection

@section('content')


    <div class="edit_exercise">

        <div class="block">
            <div class="heading">
                Oefening bewerken: {{$exercise->name}}
            </div>
            <div class="content">
                <div class="descriptive_info">
                    Hieronder kan je een oefening toevoegen.  Wanneer de hoofdtrainer de oefening geaccepteerd heeft, verschijnt hij bij op het overzicht en kunnen andere trainers hem bekijken.
                </div>
                @if (count($errors) > 0)
                    <div class="error_msg">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="form_with_input_anims" method="post" action="{{url('edit_exercise')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="title_and_description">
                        <h3><label for="title">Titel</label></h3>
                        <div class="field_wrap title">
                            <input type="text" name="title" id="title" value="{{old('title', $exercise->name)}}">
                        </div>
                        <h3><label for="summernote">Beschrijving</label></h3>
                        <div class="descriptive_info">
                            * Deel de beschrijving best op in verschillende stappen.
                        </div>
                        <div class="description">
                            <div class="field_wrap">
                                <textarea name="description" id="description" hidden="hidden">{{ old('description', $exercise->description) }}</textarea>
                                <div id="summernote" class="apply_bootstrap"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="images_block">
                        <h3>Afbeeldingen</h3>
                        <div class="descriptive_info">
                            Hieronder kan je afbeeldingen/foto's uploaden om de oefening te verduidelijken.<br>
                            Upload best afbeeldingen met een 4:3 verhouding en een maximum grootte van 500kB.<br>
                            Je kan maximaal 6 afbeeldingen uploaden.
                        </div>
                        <div class="error_msg">
                            Error...
                        </div>
                        <div class="images clearfix">
                            <div class="template image float" identifier="">
                                <img src="{{url('images/exercise_images/default.jpg')}}">
                                <div class="delete"><i class="fa fa-times"></i></div>
                                <input type="hidden" name="name_and_size[]" value="" hidden="">
                            </div>
                            @foreach($exercise->images as $image)
                            <div class="image float" identifier="">
                                <img src="{{url('images/exercise_images/' . $image->path)}}">
                                <div class="delete"><i class="fa fa-times"></i></div>
                                <input type="hidden" name="name_and_size[]" value="" hidden="">
                            </div>
                            @endforeach

                            <div class="labelholder float first">
                                <input id="images" type="file" accept="image/*" name="image[]" multiple="" hidden="">
                                <label for="images">
                                    <i class="fa fa-plus"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="tags_block">
                        <h3>Tags</h3>
                        <div class="descriptive_info">
                            Hieronder kan je tags van verschillende types aanvinken zodat andere trainers je oefening makkelijker kunnen terugvinden.
                        </div>
                        <div class="tags clearfix">
                            @foreach($tag_types as $tag_type => $tags)
                            <div class="tag_type_block float">
                                <h4>{{ucfirst($tag_type)}}</h4>
                                @foreach($tags as $tag)
                                <div class="tag">
                                    <input id="{{$tag->id}}" type="checkbox" name="tags[]" value="{{$tag->id}}" hidden {{ (old('tags') ? (in_array($tag->id, old('tags')) ? "checked":"") : (in_array($tag->id, $exercise->tags->pluck('id')->toArray()) ? "checked" : "")) }}>
                                    <label for="{{$tag->id}}">
                                        <span class="checkbox"><i class="fa fa-check"></i></span>
                                        <span class="name">{{ucfirst($tag->name)}}</span>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="btns clearfix">
                        <div class="delete float">
                            <a href="{{url('delete_exercise/' . $exercise->id)}}">Oefening verwijderen</a>
                        </div>
                        <div class="submit float">
                            <input type="submit" name="" value="Oefening updaten">
                        </div>
                    </div>
                </form>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.3/summernote.js"></script>
    <script src="{{ asset('js/exercises/add_exercise.js') }}"></script>
@endsection