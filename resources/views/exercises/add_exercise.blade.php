@extends('layouts.app')
@section('title', 'Nieuwe oefening')

@section('custom_css')
@endsection

@section('content')


    <div class="add_exercise">

        <div class="block">
            <div class="heading">
                Nieuwe oefening
            </div>
            <div class="content">
                <div class="descriptive_info">
                    Hieronder kan je een oefening toevoegen.  Wanneer de hoofdtrainer de oefening geaccepteerd heeft, verschijnt hij bij op het overzicht en kunnen andere trainers hem bekijken.
                </div>
                <form class="form_with_input_anims" method="post" action="{{url('add_exercise')}}">
                    <div class="field_wrap title">
                        <label>Titel</label>
                        <input type="text" name="title" id="title" value="{{old('title')}}">
                    </div>
                    <div class="description">
                        hierin komt de beschrijving met summernote
                    </div>
                    <div class="images">
                        hierin komen de afbeeldingen met croppie, -> hoe juist voor resultaat??? -> nog eens bekijken :p
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
                                    <input id="{{$tag->id}}" type="checkbox" name="tags[]" value="{{$tag->id}}" hidden>
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
                </form>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
    <!--<script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>-->
@endsection