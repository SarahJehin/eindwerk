@extends('layouts.app')
@section('title', 'Welkom')
@section('custom_css')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.4.0/croppie.css">
@endsection
@section('content')
<div class="home profile">
    <div class="block">
        <div class="heading">
            Welkom {{Auth::user()->first_name}}
        </div>
        <div class="content">

            @if (session('success_msg'))
                <div class="success_msg">
                    {{ session('success_msg') }}
                </div>
            @endif
            @if (session('error_msg'))
                <div class="error_msg">
                    @foreach(session('error_msg') as $error)
                    <div>{{$error}}</div>
                    @endforeach
                </div>
            @endif

            <div class="personal_info clearfix">
                <div class="edit_button">
                    <a href="{{url('edit_profile')}}"><i class="fa fa-pencil" aria-hidden="true"></i> Bewerken</a>
                </div>
                <div class="profile_pic float">
                    <img src="{{url('images/profile_pictures/' . Auth::user()->image)}}" alt="{{Auth::user()->first_name}} {{Auth::user()->last_name}}">
                    <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                </div>
                <div class="data float">
                    <h2>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h2>
                    <div class="clearfix">
                        <div class="contact_info float">
                            <div class="vtv_nr clearfix">
                                <div class="title float">VTV</div>
                                <div class="colon float">:</div>
                                <div class="value float">{{Auth::user()->vtv_nr}}</div>
                            </div>
                            <div class="singles clearfix">
                                <div class="title float">Enkel</div>
                                <div class="colon float">:</div>
                                <div class="value float">{{Auth::user()->ranking_singles}}</div>
                            </div>
                            <div class="doubles clearfix">
                                <div class="title float">Dubbel</div>
                                <div class="colon float">:</div>
                                <div class="value float">{{Auth::user()->ranking_doubles}}</div>
                            </div>
                            <div class="gsm clearfix">
                                <div class="title float">GSM</div>
                                <div class="colon float">:</div>
                                <div class="value float"><input type="text" name="new_gsm" value="{{substr(Auth::user()->gsm, 0, 4) . ' ' . chunk_split(substr(Auth::user()->gsm, 4), 2, ' ')}}"></div>
                                <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                                <div class="save_button"><i class="fa fa-floppy-o" aria-hidden="true"></i></div>
                            </div>
                            <div class="tel clearfix">
                                <div class="title float">Tel.</div>
                                <div class="colon float">:</div>
                                <div class="value float"><input type="text" name="new_tel" value="{{substr(Auth::user()->tel, 0, 3) . ' ' . chunk_split(substr(Auth::user()->tel, 3), 2, ' ')}}"></div>
                                <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                                <div class="save_button"><i class="fa fa-floppy-o" aria-hidden="true"></i></div>
                            </div>
                            <div class="email clearfix">
                                <div class="title float">E-mail</div>
                                <div class="colon float">:</div>
                                <div class="value float"><input type="text" name="new_tel" value="{{Auth::user()->email}}"></div>
                                <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                                <div class="save_button"><i class="fa fa-floppy-o" aria-hidden="true"></i></div>
                            </div>
                            <div class="update_pwd link">
                                Wachtwoord updaten
                            </div>
                        </div>
                        <div class="badges float">
                            <h3>Badges</h3>
                            @if(count($badges) > 0)
                            <div class="clearfix">
                                @foreach($badges as $badge)
                                <div class="badge float" data-toggle="tooltip" data-placement="bottom" title="{{$badge['title']}}">
                                    <span>{{$badge['amount_activities']}}</span>
                                    <i style="color: {{$badge['bg_color']}};" class="fa fa-certificate" aria-hidden="true"></i>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="descriptive_info">Je hebt nog geen badges verdiend.</p>
                            @endif
                        </div>
                    </div>
                    
                </div>
            </div>

            <div class="upcoming_activities">
                <h3>Mijn activiteiten</h3>
                @if(!$user->activities_as_participant_coming->isEmpty())
                <div class="list">
                    <div class="row header clearfix">
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($user->activities_as_participant_coming as $activity)
                    <div class="row activity clearfix">
                        <div class="date float">{{date('d/m', strtotime($activity->start))}}<span class="year">{{date('/Y', strtotime($activity->start))}}</span></div>
                        <div class="title float"><a class="link" href="{{url('activity_details/' . $activity->id)}}">{{$activity->title}}</a></div>
                        <div class="paid float">
                            @if($activity->price > 0)
                                @if($activity->pivot->status == 2)
                                <i class="fa fa-check" aria-hidden="true"></i>
                                @endif
                            @else
                            n.v.t.
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="descriptive_info">Je hebt geen komende activiteiten.  Kijk op de <a class="link" href="{{url('activities_overview')}}">kalender</a> welke activiteiten je allemaal kan meedoen.</p>
                @endif
            </div>

            <div class="past_activities">
                <h3>Activiteiten waaraan ik heb deelgenomen</h3>
                @if(!$user->activities_as_participant_past->isEmpty())
                <div class="list">
                    <div class="row header clearfix">
                        <div class="date float">Datum</div>
                        <div class="title float">Titel</div>
                        <div class="paid float">Betaald</div>
                    </div>
                    @foreach($user->activities_as_participant_past as $activity)
                    <div class="row activity clearfix">
                        <div class="date float">{{date('d/m', strtotime($activity->start))}}<span class="year">{{date('/Y', strtotime($activity->start))}}</span></div>
                        <div class="title float"><a class="link" href="{{url('activity_details/' . $activity->id)}}">{{$activity->title}}</a></div>
                        <div class="paid float">
                            @if($activity->price > 0)
                                @if($activity->pivot->status == 2)
                                <i class="fa fa-check" aria-hidden="true"></i>
                                @endif
                            @else
                            n.v.t.
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="descriptive_info">Je hebt dit seizoen nog niet aan activiteiten deelgenomen.</p>
                @endif
            </div>

            <div class="winterhours_block">
                <h3>Mijn winteruren</h3>
                @if(count($winterhours) > 0)
                <div class="descriptive_info">
                    Hieronder vind je een overzicht van de eerst drievolgende speeldata per winteruurgroep.
                </div>
                <div class="winterhours">
                    @foreach($winterhours as $winterhour)
                    <div class="winterhour">
                        <h4>{{$winterhour['title']}}</h4>
                        <div class="dates clearfix">
                            @foreach($winterhour['dates'] as $date)
                            <div class="date float">
                                <h5>{{date('d/m/Y', strtotime($date->date))}}</h5>
                                @foreach($date->assigned_participants as $participant)
                                <div class="participant {{($participant->id == Auth::user()->id ? 'active' : '')}}">
                                    {{$participant->first_name}} {{substr($participant->last_name, 0, 1)}}.
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="descriptive_info">
                    Je heb nog geen winteruurgroepen. <a class="link" href="{{url('add_winterhour')}}">Maak er nu één aan</a>.
                </div>
                @endif
            </div>
        </div>
    </div>


    <div class="edit_profile_pic_modal">
        <div class="content">
            <div class="header"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="body">
                <p>Upload hieronder een nieuwe profielfoto van minstens 400x400:</p>
                <form id="upload_profile_pic_form" method="post" action="{{url('update_profile_pic')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <label for="upload"><i class="fa fa-upload" aria-hidden="true"></i> Upload nieuwe profielfoto:</label>
                    <input id="upload" value="Choose a file" accept="image/*" type="file" hidden="">

                    <div class="upload-wrap">
                        <div id="upload-container" class="croppie-container"></div>
                    </div>
                    <button type="button" class="upload-result submit">Profielfoto bewaren</button>
                    <input type="hidden" id="imagebase64" name="imagebase64">
                </form>
            </div>
        </div>
    </div>


    <div id="update_pwd_modal" class="lightbox_modal light">
        <div class="modal">
            <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="modal_body">
                <form id="update_pwd_form" method="post" action="{{url('update_pwd')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="clearfix">
                        <label class="float" for="old_pwd">Oude paswoord:</label>
                        <input class="float" id="old_pwd" name="old_pwd">
                    </div>
                    
                    <div class="clearfix">
                        <label class="float" for="new_pwd">Nieuwe paswoord:</label>
                        <input class="float" id="new_pwd" name="new_pwd">
                    </div>
                    
                    <div class="clearfix">
                        <label class="float" for="new_pwd_check">Bevestiging nieuwe paswoord:</label>
                        <input class="float" id="new_pwd_check" name="new_pwd_check">
                    </div>
                    
                    <input type="submit" value="Paswoord updaten">
                </form>
                
            </div>
        </div>
    </div>
    
</div>
@endsection
@section('custom_js')
<script type="text/javascript">
    var user_id = {{Auth::user()->id}};
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.4.0/croppie.js"></script>
<script type="text/javascript" src="{{asset('js/members/edit_profile.js')}}"></script>

@endsection