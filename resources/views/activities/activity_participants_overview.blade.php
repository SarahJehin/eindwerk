@extends('layouts.app')
@section('title')
Deelnemers {{$activity->title}}
@endsection
@section('content')
    <div class="activity_participants">

        <div class="block">
            <div class="heading" activity_id="{{$activity->id}}">
                {{$activity->title}}
            </div>
            <div class="content">
                <div>
                    <a class="link" href="{{url('activities_list')}}">Terug naar overzicht</a>
                </div>
                @if (session('success_msg'))
                    <div class="success_msg">
                        {{ session('success_msg') }}
                    </div>
                @endif
                <p class="center">Hieronder vind je een overzicht van iedereen die zich ingeschreven heeft voor deze activiteit, alsook door wie ze ingeschreven zijn, en of ze betaald hebben.</p>
                
                @if($activity->participants->isEmpty())
                <div class="center">Er zijn nog geen inschrijvingen voor deze activiteit...</div>
                @else
                <div class="clearfix">
                    <div class="float link download"><a href="{{url('download_participants_as_excel/' . $activity->id)}}"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Downloaden</a></div>
                    <div class="float print"><i class="fa fa-print" aria-hidden="true"></i> Afdrukken</div>
                    <div class="amount_participants">({{count($activity->participants)}}/{{$activity->max_participants}})</div>
                </div>
                
                <div class="list">
                    <div class="participant_block header clearfix">
                        <div class="participant float">Deelnemer</div>
                        <div class="sign_out float">Uitschrijven</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($activity->participants as $participant)
                    <div class="participant_block clearfix" user_id="{{$participant->id}}">
                        <div class="row clearfix">
                            <div class="participant float clearfix">
                                <div class="name float link">{{$participant->last_name}} {{$participant->first_name}}</div>
                                <div class="age float">({{(date('Y')-date('Y', strtotime($participant->birth_date)))}}j.)</div>
                            </div>
                            <div class="sign_out float link">Uitschrijven</div>
                            <div class="paid float">
                                @if($participant->pivot->status == 1)
                                <input type="checkbox" name="paid{{$participant->id}}" is_checked='false'>
                                @elseif($participant->pivot->status == 2)
                                <input type="checkbox" name="paid{{$participant->id}}" checked="" is_checked='true'>
                                @endif
                            </div>
                        </div>
                        <div class="details">
                            <div class="clearfix">
                                <div class="float gsm small_no_float"><i class="fa fa-mobile" aria-hidden="true"></i> {{substr($participant->gsm, 0, 4) . ' ' . chunk_split(substr($participant->gsm, 4), 2, ' ')}}</div>
                                <div class="float phone small_no_float"><i class="fa fa-phone" aria-hidden="true"></i> {{substr($participant->tel, 0, 3) . ' ' . chunk_split(substr($participant->tel, 3), 2, ' ')}}</div>
                                <div class="float email small_no_float"><i class="fa fa-at" aria-hidden="true"></i> {{$participant->email}}</div>
                            </div>
                            <div class="signed_up_by">
                                Ingeschreven door: {{$participant->pivot->signed_up_by_user->first_name}} {{$participant->pivot->signed_up_by_user->last_name}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div id="sign_out_modal" class="lightbox_modal light">
            <div class="modal">
                <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
                <div class="modal_body">
                    Zeker dat je <span class="participant"></span> wil uitschrijven?
                </div>
                <div class="modal_footer">
                    <form method="post" action="{{url('sign_out_for_activity')}}">
                        {{ csrf_field() }}
                        <input type="number" name="user_id" value="" hidden="">
                        <input type="number" name="activity_id" value="{{$activity->id}}" hidden="">
                        <input type="submit" name="submit" value="Ja, nu uitschrijven">
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('custom_js')
<script src="{{ asset('js/activities/activities_extra.js') }}"></script>
@endsection
