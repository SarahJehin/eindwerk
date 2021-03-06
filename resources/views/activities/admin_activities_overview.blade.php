@extends('layouts.app')
@section('title', 'Activiteitenoverzicht')

@section('content')
    <div class="activities_list">
        <div class="block">
            <div class="heading">
                Activiteitenoverzicht
            </div>
            <div class="content">

                <div class="add_activity_link clearfix"><a href="{{url('add_activity')}}" data-toggle="tooltip" data-placement="left" title="Activiteit toevoegen"><i class="fa fa-plus" aria-hidden="true"></i></a></div>

                <h3 class="first">Komende activiteiten</h3>
                <div class="list">
                    <div class="activity header clearfix">
                        <div class="is_visible float"><i class="fa fa-eye" aria-hidden="true"></i></div>
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="participants float">Inschrijvingen</div>
                        <div class="edit float"><i class="fa fa-pencil" aria-hidden="true"></i></div>
                        <div class="delete float"><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    @if(!$activities->isEmpty())
                    @foreach($activities as $activity)
                    <div class="activity clearfix">
                        <div class="is_visible float">
                            <input type="checkbox" id="visible{{$activity->id}}" name="visible{{$activity->id}}"  @if($activity->is_visible) {{'checked'}}@endif hidden>
                            <label for="visible{{$activity->id}}" activity_id="{{$activity->id}}"><i class="fa fa-eye" aria-hidden="true"></i></label>
                        </div>
                        <div class="date float">{{date('d/m', strtotime($activity->start))}}<span class="year">{{date('/Y', strtotime($activity->start))}}</span></div>
                        <div class="title float"><a href="{{url('activity_details/' . $activity->id)}}">{{$activity->title}}</a></div>
                        <div class="participants float"><a href="{{url('activity_participants/' . $activity->id)}}">{{count($activity->participants)}}/{{($activity->max_participants > 30 ? "&infin;" : $activity->max_participants)}}</a></div>
                        <div class="edit float"><a href="{{url('edit_activity/' . $activity->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                        <div class="delete link float" activity_id="{{$activity->id}}""><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    @endforeach
                    <div class="pagination_container apply_bootstrap">
                        {{$activities->links()}} 
                    </div>
                    @else
                    <div>
                        Er zijn geen komende activiteiten. <a class="link" href="{{url('add_activity')}}">Maak er nu één aan</a>.
                    </div>
                    @endif
                </div>

                <h3>Voorbije activiteiten</h3>
                <div class="list">
                    <div class="activity header clearfix">
                        <div class="is_visible float"><i class="fa fa-eye" aria-hidden="true"></i></div>
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="participants float">Inschrijvingen</div>
                        <div class="edit float"><i class="fa fa-pencil" aria-hidden="true"></i></div>
                        <div class="delete float"><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    @foreach($past_activities as $activity)
                    <div class="activity clearfix">
                        <div class="is_visible float">
                            <input type="checkbox" id="visible{{$activity->id}}" name="visible{{$activity->id}}"  @if($activity->is_visible) {{'checked'}}@endif hidden>
                            <label for="visible{{$activity->id}}" activity_id="{{$activity->id}}"><i class="fa fa-eye" aria-hidden="true"></i></label>
                        </div>
                        <div class="date float">{{date('d/m', strtotime($activity->start))}}<span class="year">{{date('/Y', strtotime($activity->start))}}</span></div>
                        <div class="title float"><a href="{{url('activity_details/' . $activity->id)}}">{{$activity->title}}</div>
                        <div class="participants float"><a href="{{url('activity_participants/' . $activity->id)}}">{{count($activity->participants)}}/{{$activity->max_participants}}</a></div>
                        <div class="edit float"><a href="{{url('edit_activity/' . $activity->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                        <div class="delete link float" activity_id="{{$activity->id}}""><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    @endforeach

                    <div class="pagination_container apply_bootstrap">
                        {{$past_activities->links()}} 
                    </div>
                </div>
            </div>
        </div>

        <div id="delete_activity_modal" class="lightbox_modal light">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    Zeker dat je de activiteit "<strong class="activity_name">"activiteit x"</strong>" wil verwijderen?
                </div>
                <div class="modal_footer">
                    <form method="post" action="{{url('delete_activity')}}">
                        {{ csrf_field() }}
                        <input type="number" name="activity_id" value="0" hidden="">
                        <input type="submit" name="submit" value="Ja, nu verwijderen">
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('custom_js')
<script src="{{ asset('js/activities/activities_extra.js') }}"></script>

@endsection
