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
                        <div>
                            <input id="{{$tag->id}}" type="checkbox" name="tag[]">
                            <label for="{{$tag->id}}">{{ucfirst($tag->name)}}</label>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
                <div class="exercises_content float">
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
                                <a class="link" href="{{url('#')}}">Lees meer</a>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="most_viewed">
                        hierin komen de populairste / vaakst bekeken oefeningen
                    </div>
                    <div class="all">
                        hierin komen alle oefeningen in een willekeurige volgorde
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection
@section('custom_js')
    <!--<script src="{{ asset('js/winterhours/add_winterhour.js') }}"></script>-->
@endsection