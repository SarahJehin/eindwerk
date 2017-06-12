@extends('layouts.app')
@section('title')
{{$exercise->name}}
@endsection

@section('custom_css')
@endsection

@section('content')


    <div class="exercise_details">

        <div class="block">
            <div class="heading">
                {{$exercise->name}}
            </div>
            <div class="content">
                @if(Auth::user()->is_headtrainer || Auth::user()->id == $exercise->made_by)
                <div class="edit_button">
                    <a href="{{url('edit_exercise/' . $exercise->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i> Bewerken</a>
                </div>
                @endif
                <div class="back_link">
                    <a class="link" href="{{url('exercises_overview')}}">Terug naar overzicht</a>
                </div>
                <h3>{{$exercise->name}}</h3>
                <div class="clearfix">
                    <div class="float">
                        <div class="made_by descriptive_info">
                            Auteur: {{$exercise->user->first_name}} {{$exercise->user->last_name}}
                        </div>
                        <div class="tags clearfix">
                            @foreach($exercise->tags as $tag)
                            <div class="tag float">
                                {{$tag->name}}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="views float clearfix">
                        <div class="float"><i class="fa fa-eye"></i></div>
                        <div class="amount float">{{$exercise->views}}</div>
                    </div>
                </div>
                

                <div class="exercise clearfix">
                    <div class="images_block float small_no_float">
                        <div class="main_image">
                            <img src="{{url('images/exercise_images/' . $exercise->images[0]->path)}}" alt="{{$exercise->images[0]->title}}">
                            <iframe width="420" height="345" src="{{$exercise->video_url}}" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>
                        </div>
                        <div class="other_images clearfix">
                            @foreach($exercise->images as $image)
                            <div class="image float">
                                <img src="{{url('images/exercise_images/' . $image->path)}}" alt="{{$image->title}}">
                            </div>
                            @endforeach
                            @if($exercise->video_url)
                            <div class="image float video">
                                <img src="{{$video_thumbnail}}" alt="{{$exercise->name}}">
                                <span><i class="fa fa-play" aria-hidden="true"></i></span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="description_block float small_no_float">
                        {!!$exercise->description!!}
                    </div>
                </div>

                @if($exercise->video_url)
                <!--
                <div class="video_block">
                    <iframe width="420" height="345" src="{{$exercise->video_url}}" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen"></iframe>
                </div>
                -->
                @endif

                @if($exercise->approved == 0)
                <div class="approve_deny clearfix">
                    <div class="deny float">
                        <a href="{{url('deny_exercise/' . $exercise->id)}}">Oefening weigeren</a>
                    </div>
                    <div class="approve float">
                        <a href="{{url('approve_exercise/' . $exercise->id)}}">Oefening goedkeuren</a>
                    </div>
                </div>
                @endif
            </div>
        </div>


        <div id="exercise_image_modal" class="lightbox_modal">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    <img src="{{url('images/exercise_images/' . $exercise->images[0]->path)}}" alt="{{$exercise->name}}">
                </div>
                <div class="modal_footer">
                    {{$exercise->name}}
                </div>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
    <script src="{{ asset('js/exercises/exercise_details.js') }}"></script>
@endsection