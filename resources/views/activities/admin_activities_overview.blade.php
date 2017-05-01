@extends('layouts.app')


@section('content')


    <div class="activities_list">

        <div class="block">
            <div class="heading">
                Activiteitenoverzicht
            </div>
            <div class="content">

                <div class="searchbox">
                    Searchbox for filtering the activities
                </div>

                <div class="list">
                    <div class="activity header clearfix">
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="participants float">Inschrijvingen</div>
                        <div class="edit float"><i class="fa fa-pencil" aria-hidden="true"></i></div>
                        <div class="delete float"><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    @foreach($activities as $activity)
                    <div class="activity clearfix">
                        <div class="date float">{{date('d/m/Y', strtotime($activity->start))}}</div>
                        <div class="title float">{{$activity->title}}</div>
                        <div class="participants float"><a href="{{url('activity_participants/' . $activity->id)}}">{{count($activity->participants)}}/{{$activity->max_participants}}</a></div>
                        <div class="edit float"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                        <div class="delete float"><i class="fa fa-times" aria-hidden="true"></i></div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div id="delete_activity_modal" class="lightbox_modal light">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    Zeker dat de activiteit <span class="activity_name">"activiteit x"</span> wil verwijderen?
                </div>
                <div class="modal_footer">
                    <form method="post" action="#">
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
<script type="text/javascript">
    var lightbox;
</script>
<script src="{{ asset('js/activities/activities_extra.js') }}"></script>

@endsection
