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
            <div class="personal_info clearfix">
                <div class="edit_button">
                    <a href="{{url('edit_profile')}}"><i class="fa fa-pencil" aria-hidden="true"></i> Bewerken</a>
                </div>
                <div class="profile_pic float">
                    <img src="{{url('images/profile_pictures/' . Auth::user()->image)}}" alt="{{Auth::user()->first_name}} {{Auth::user()->last_name}}">
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
                            <div class="gsm clearfix">
                                <div class="title float">GSM</div>
                                <div class="colon float">:</div>
                                <div class="value float"><input type="text" name="new_gsm" value="{{substr(Auth::user()->gsm, 0, 4) . ' ' . chunk_split(substr(Auth::user()->gsm, 4), 2, ' ')}}" readonly="" disabled=""></div>
                                <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                                <div class="save_button"><i class="fa fa-floppy-o" aria-hidden="true"></i></div>
                            </div>
                            <div class="tel clearfix">
                                <div class="title float">Tel.</div>
                                <div class="colon float">:</div>
                                <div class="value float"><input type="text" name="new_tel" value="{{substr(Auth::user()->gsm, 0, 4) . ' ' . chunk_split(substr(Auth::user()->gsm, 4), 2, ' ')}}" readonly="" disabled=""></div>
                                <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                                <div class="save_button"><i class="fa fa-floppy-o" aria-hidden="true"></i></div>
                            </div>
                            <div class="email clearfix">
                                <div class="title float">E-mail</div>
                                <div class="colon float">:</div>
                                <div class="value float"><input type="text" name="new_tel" value="{{Auth::user()->email}}" readonly="" disabled=""></div>
                                <div class="edit_button"><a href="#"><i class="fa fa-pencil" aria-hidden="true"></i></a></div>
                                <div class="save_button"><i class="fa fa-floppy-o" aria-hidden="true"></i></div>
                            </div>
                        </div>
                        <div class="tennis_info float">
                            <div class="singles">
                                <span>Enkel:</span>
                                <span>{{Auth::user()->ranking_singles}}</span>
                            </div>
                            <div class="doubles">
                                <span>Dubbel:</span>
                                <span>{{Auth::user()->ranking_doubles}}</span>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="badges">
                <h3>Badges</h3>
                @if(count($badges) > 0)
                    @foreach($badges as $badge)
                    <div class="badge" title="{{$badge['title']}}">
                        <span>{{$badge['amount_activities']}}</span>
                        <i style="color: {{$badge['bg_color']}};" class="fa fa-certificate" aria-hidden="true"></i>
                    </div>
                    @endforeach
                @else
                <p class="descriptive_info">Je hebt nog geen badges verdiend.</p>
                @endif
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
                        <div class="date float">{{date('d-m-Y', strtotime($activity->start))}}</div>
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
                        <div class="date float">{{date('d-m-Y', strtotime($activity->start))}}</div>
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

            <div class="winter_hours">
                <h3>Mijn winteruren</h3>
            </div>

            <div class="croppie-test">
                <!--<img src="{{url('images/profile_pictures/' . Auth::user()->image)}}">-->
                <img src="{{url('images/activity_images/gps_messages_idea.jpg')}}">
                <form id="testform01" method="post" action="{{url('update_profile_pic')}}">
                    {{ csrf_field() }}
                    <input id="testbase64" type="text" name="testbase64">
                </form>
            </div>
            <img id="profile_image" src="http://www.assuropoil.fr/wp-content/uploads/chat-heureux-en-appartement-savoir.jpg"/>

            <div class="croppietest">
                <div class="demo-wrap upload-demo">
                    <div class="container">
                    <div class="grid">
                        <div class="col-1-2">
                            <strong>Upload Example (with exif orientation compatability)</strong>
                            <div class="actions">
                                <a class="btn file-btn">
                                    <span>Upload</span>
                                    <input id="upload" value="Choose a file" accept="image/*" type="file">
                                </a>
                                <button class="upload-result">Result</button>
                            </div>
                        </div>
                        <div class="col-1-2">
                            <div class="upload-msg">
                                Upload a file to start cropping
                            </div>
                            <div class="upload-demo-wrap">
                                <div id="upload-demo" class="croppie-container"><div class="cr-boundary" style=""><canvas class="cr-image"></canvas><div class="cr-viewport cr-vp-circle" style="width: 100px; height: 100px;" tabindex="0"></div><div class="cr-overlay"></div></div><div class="cr-slider-wrap"><input class="cr-slider" step="0.0001" type="range"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="testtest">
                    <div class="resultaat_test">resultaat</div>
                    <img src="">
                </div>

                <div class="last_test">
                    <form id="last_test" method="post" action="{{url('update_profile_pic')}}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input id="last_test_input" type="file" name="last_test_upload">
                        <img class="img_tag_to_render" src="">
                        <input id="last_test_base64" type="text" name="last_test_base64">
                        <div class="last_test_result">
                            Resultaat
                        </div>
                    </form>
                </div>
            </div>




            <p>Welkom op het dashboard van TC Sportiva!</p>
            <p>Op deze persoonlijke pagina vind je een overzicht van activiteiten waarvoor je bent ingeschreven, je winteruurschema('s), ...</p>

            <div class="current_score">
                Je huidige score is {{$total_adult_score}}
            </div>

            <div class="test">
                <form method="post" action="{{url('import_members')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="file" name="members_excel">
                    <input type="submit" name="submit" value="Leden importeren">
                </form>
            </div>
        </div>
    </div>

    
    <div id="update_profile_pic_modal" class="lightbox_modal light">
        <div class="modal">
            <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="modal_body">
                <p>Upload hieronder een nieuwe profielfoto van minstens 400x400:</p>
                <div>
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
            <div class="modal_footer">

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
<!--<script type="text/javascript">
(function ( window, document, $, undefined ) {
    var $uploadCrop;

        function readFile(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function (e) {
                    $('.upload-demo').addClass('ready');
                    $uploadCrop.croppie('bind', {
                        url: e.target.result
                    }).then(function(){
                        console.log('jQuery bind complete');
                    });
                    
                }
                
                reader.readAsDataURL(input.files[0]);
            }
            else {
                console.log("Sorry - you're browser doesn't support the FileReader API");
            }
        }

        $uploadCrop = $('#upload-demo').croppie({
            viewport: {
                width: 400,
                height: 400,
                type: 'circle'
            },
            enableExif: true
        });

        $('#upload').on('change', function () { readFile(this); });
        $('.upload-result').on('click', function (ev) {
            $uploadCrop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function (resp) {
        $('#imagebase64').val(resp);
        console.log($('#imagebase64').val());
                popupResult({
                    src: resp
                });
            });
        });
        })(window, window.document, window.jQuery);
</script>-->
@endsection