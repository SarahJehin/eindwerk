@extends('layouts.app')
@section('title', 'Oefeningen overzicht')

@section('custom_css')
@endsection

@section('content')


    <div class="exercises_overview">

        <div class="block">
            <div class="heading">
                Oefeningen overzicht
            </div>
            <div class="content clearfix">
                <div class="filters_block float">
                    In deze block komen all filter mogelijkheden (tags, nieuwst/oudst, most viewed)
                    @foreach($tag_types as $tag_type => $tags)
                    <div class="tag_type_block">
                        <h3>{{ucfirst($tag_type)}}</h3>
                        @foreach($tags as $tag)
                        <div class="tag">
                            <input id="{{$tag->id}}" type="checkbox" name="tag[]" hidden="">
                            <label for="{{$tag->id}}" ng-click="filter">
                                <span class="checkbox"><i class="fa fa-check"></i></span>
                                <span class="name">{{ucfirst($tag->name)}}</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                <div class="exercises_content float">
                    @if(Auth::user()->isHeadtrainer() && !$exercises_to_approve->isEmpty())
                    <div class="exercises_to_approve clearfix">
                        <div class="exclamation float">
                            <i class="fa fa-exclamation"></i>
                        </div>
                        <div class="overview float">
                            <div>Er zijn nieuwe oefeningen toegevoegd:</div>
                            <div class="exercise header clearfix">
                                <div class="title float">
                                    Titel
                                </div>
                                <div class="author float">
                                    Toegevoegd door
                                </div>
                                <div class="date">
                                    Toegevoegd op
                                </div>
                            </div>
                            @foreach($exercises_to_approve as $exercise)
                            <div class="exercise clearfix">
                                <div class="title float">
                                    <a class="link" href="{{url('exercise_details/' . $exercise->id)}}">{{$exercise->name}}</a>
                                </div>
                                <div class="author float">
                                    {{$exercise->user->first_name}} {{$exercise->user->last_name}}
                                </div>
                                <div class="created_at float">
                                    {{date('d/m/Y', strtotime($exercise->created_at))}}
                                </div>
                                <div class="view_details float">
                                    <a class="link" href="{{url('exercise_details/' . $exercise->id)}}">Bekijken</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if($exercises->isEmpty())
                    <div>
                        Er bestaan nog geen oefeningen...<br>
                        Wees nu de eerste om een <a class="link" href="{{url('add_exercise')}}">oefening te uploaden</a>.
                    </div>
                    @endif
                    @if($newest_exercise)
                    <div class="newest clearfix">
                        <div class="image float">
                            <img src="{{url('images/exercise_images/' . $newest_exercise->images[0]->path)}}" alt="{{$newest_exercise->name}}">
                        </div>
                        <div class="info float">
                            <h3>{{$newest_exercise->name}}</h3>
                            <div class="description">
                                {!!str_limit($newest_exercise->description, 299)!!}
                                <a class="link" href="{{url('exercise_details/' . $newest_exercise->id)}}">Lees meer</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="most_viewed">
                        <h3>Meest bekeken oefeningen</h3>
                        <div class="exercises">
                            @foreach($most_viewed_exercises as $exercise)
                            <div class="exercise">
                                <a href="{{url('exercise_details/' . $exercise->id)}}">
                                    <div class="image">
                                        <img src="{{url('images/exercise_images/' . $exercise->images[0]->path)}}">
                                    </div>
                                    <div class="views clearfix">
                                        <div class="float"><i class="fa fa-eye"></i></div>
                                        <div class="amount float">{{$exercise->views}}</div>
                                    </div>
                                    <div class="title">{{$exercise->name}}</div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="all">
                        <h3>Overzicht</h3>
                        <div class="exercises">
                            @foreach($exercises as $exercise)
                            <div class="exercise">
                                <a href="{{url('exercise_details/' . $exercise->id)}}">
                                    <div class="image">
                                        <img src="{{url('images/exercise_images/' . $exercise->images[0]->path)}}">
                                    </div>
                                    <div class="title">{{$exercise->name}}</div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
    <!--<script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>-->
@endsection