@extends('layouts.app')
@section('title', 'Leden')
@section('custom_css')
<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
@endsection
@section('content')
<div class="members_overview" ng-controller="RolesController">
    <div class="block">
        <div class="heading">
            Ledenlijst
        </div>
        <div class="content">

       		@if (session('success_msg'))
                <div class="success_msg">
                    {{ session('success_msg') }}
                </div>
            @endif
            @if (session('error_msg'))
                <div class="error_msg">
                    {{ session('error_msg') }}
                </div>
            @endif
            @if (session('warning_users'))
                <div class="warning_msg">
                    <strong>Opmerking:</strong> <br>
                    Bij onderstaande leden ontbrak de geboortedatum of was het geslacht ongeldig (enkel M/V zijn toegelaten):
                    <ul>
                    	@foreach(session('warning_users') as $user)
                    	<li>{{$user}}</li>
                    	@endforeach
                    </ul>
                </div>
            @endif

        	<p class="descriptive_info">Hieronder vind je een overzicht van alle Sportiva leden.  Wanneer je op hun naam klikt kan je extra contact informatie bekijken.</p>

        	<div class="search">
        		<form id="search_members" method="get" action="{{url('members_overview')}}">
        			<input type="text" name="searching" value="true" hidden="">
	        		<div class="quick">
	        			<div class="clearfix">
	        				<input class="float" type="text" name="name" placeholder="Wie wil je zoeken?">
	        				<span class="searchbutton float"><i class="fa fa-search" aria-hidden="true"></i></span>
	        			</div>
	        		</div>
	        		<div class="open_advanced link">Uitgebreid zoeken</div>

	        		<div class="advanced clearfix">
	        			<div class="ranking float small_no_float">
	        				<h3>Klassement</h3>
	        				<div class="apply_bootstrap">
	        					<label for="from_ranking">Van:</label>
		        				<select class="selectpicker" data-size="10" id="from_ranking" name="from_ranking">
		        					<option value="from">Van</option>
		        					@foreach($rankings as $ranking)
			        				<option value="{{$ranking}}">{{$ranking}}</option>
			        				@endforeach
		        				</select>
	        				
	        				
		        				<label for="to_ranking">tot:</label>
		        				<select class="selectpicker" data-size="10" id="to_ranking" name="to_ranking">
		        					<option value="to">Tot</option>
		        					@foreach($rankings as $ranking)
			        				<option value="{{$ranking}}">{{$ranking}}</option>
			        				@endforeach
		        				</select>
		        			</div>
	        			</div>
	        			<div class="birth_year float small_no_float">
	        				<h3>Geboortejaar</h3>
	        				<div class="apply_bootstrap">
	        					<label for="from_birth_year">Van:</label>
		        				<select class="selectpicker" data-size="10" id="from_birth_year" name="from_birth_year">
		        					<option value="from">Van</option>
		        					@for($i = (date('Y')-80); $i < date('Y'); $i++)
			        				<option value="{{$i}}"
			        				{{--
			        				@if(isset($input))
				        				@if($input['from_birth_year'] == $i)
				        				selected=""
				        				@endif
			        				@endif
			        				--}}
			        				>{{$i}}</option>
			        				@endfor
		        				</select>
		        				<label for="to_birth_year">tot:</label>
		        				<select class="selectpicker" data-size="10" id="to_birth_year" name="to_birth_year">
		        					<option value="to">Tot</option>
		        					@for($i = (date('Y')-80); $i < date('Y'); $i++)
			        				<option value="{{$i}}"
			        				{{--
			        				@if(isset($input))
				        				@if($input['to_birth_year'] == $i)
				        				selected=""
				        				@endif
			        				@endif
			        				--}}
			        				>{{$i}}</option>
			        				@endfor
		        				</select>
	        				</div>
	        				
	        			</div>

	        			<input type="submit" value="Zoek">
	        		</div>
        		</form>
        	</div>

        	<div class="links clearfix">
        		<div class="download link float">
	        		<a href="{{url('download_members_as_excel')}}">
		        		<i class="fa fa-file-excel-o" aria-hidden="true"></i>
		        		Ledenlijst downloaden
		        	</a>
	        	</div>
	        	@if(Auth::user()->isAdmin())
	        	<div class="import_members link float">
	        		<i class="fa fa-upload" aria-hidden="true"></i> Ledenlijst importeren
	        	</div>
	        	@endif
        	</div>
        	

        	<div class="list">
        		@if(isset($input['name']))
        		<h4>
        			Resultaten voor "<strong>{{$input['name']}}</strong>"
        			@if($input['from_ranking'] != 'from' || $input['to_ranking'] != 'to')
	        		( Klassement: 
		        		@if($input['from_ranking'] != 'from')
		        		{{$input['from_ranking']}}
		        		@else
		        		NG
		        		@endif
		        		 - 
		        		@if($input['to_ranking'] != 'to')
		        		{{$input['to_ranking']}}
		        		@else
		        		A Internationaal
		        		@endif
		        	)
	        		@endif
	        		&nbsp;
	        		@if($input['from_birth_year'] != 'from' || $input['to_birth_year'] != 'to')
	        		( Geboortejaar: 
		        		@if($input['from_birth_year'] != 'from')
		        		{{$input['from_birth_year']}}
		        		@else
		        		{{(date('Y')-80)}}
		        		@endif
		        		 - 
		        		@if($input['to_birth_year'] != 'to')
		        		{{$input['to_birth_year']}}
		        		@else
		        		{{date('Y')}}
		        		@endif
		        	)
		        	@endif
        		</h4>
        		@endif
        		<div class="member_block">
        			<div class="row header clearfix">
        				<div class="vtv_nr float">VTV-nr</div>
        				<div class="name float">Naam</div>
        				<div class="date float">Geboortedatum</div>
        				<div class="singles float">Enkel</div>
        				<div class="doubles float">Dubbel</div>
        			</div>
        		</div>
        		@if($members->isEmpty())
	        	<div class="no_results">
	        		<h2>Geen resultaten ...</h2>
	        		Er werden geen leden gevonden 
	        		@if($input['name'])
	        		voor "<strong>{{$input['name']}}</strong>"
	        		@endif
	        		@if($input['from_ranking'] != 'from' || $input['to_ranking'] != 'to')
	        		met ranking tussen
		        		@if($input['from_ranking'] != 'from')
		        		{{$input['from_ranking']}}
		        		@else
		        		NG
		        		@endif
		        		en
		        		@if($input['to_ranking'] != 'to')
		        		{{$input['to_ranking']}}
		        		@else
		        		A Internationaal
		        		@endif

		        		@if($input['from_birth_year'] != 'from' || $input['to_birth_year'] != 'to')
		        		en
		        		@endif
	        		@endif
	        		@if($input['from_birth_year'] != 'from' || $input['to_birth_year'] != 'to')
	        		met geboortejaar tussen
		        		@if($input['from_birth_year'] != 'from')
		        		{{$input['from_birth_year']}}
		        		@else
		        		{{(date('Y')-80)}}
		        		@endif
		        		en
		        		@if($input['to_birth_year'] != 'to')
		        		{{$input['to_birth_year']}}
		        		@else
		        		{{date('Y')}}
		        		@endif
		        	@endif
		        	<div><a class="link" href="{{url('members_overview')}}">Toon volledige lijst</a></div>
	        	</div>
	        	@else
        		@foreach($members as $member)
        		<div class="member_block">
        			<div class="row clearfix">
        				<div class="vtv_nr float link">{{$member->vtv_nr}}</div>
        				<div class="name float link">{{$member->last_name}} {{$member->first_name}}</div>
        				<div class="date float">
        					@if(strtotime($member->birth_date))
        					{{date('d/m/Y', strtotime($member->birth_date))}}
        					@endif
        				</div>
        				<div class="singles float">{{$member->ranking_singles}}</div>
        				<div class="doubles float">{{$member->ranking_doubles}}</div>
        			</div>
        			<div class="details clearfix">
        				<div class="profile_pic float">
	        				<img src="{{url('images/profile_pictures/' . $member->image)}}" alt="{{$member->first_name}} {{$member->last_name}}">
	        			</div>
	        			<div class="contact_info float clearfix">
		        			<div class="rs clearfix smartphone">
		        				<span class="float">S</span>
		        				<span class="float">{{$member->ranking_singles}}</span>
		        			</div>
		        			<div class="rd clearfix smartphone">
		        				<span class="float">D</span>
		        				<span class="float">{{$member->ranking_doubles}}</span>
		        			</div>
		        			<div class="birth_date smartphone">
		        				<i class="fa fa-birthday-cake" aria-hidden="true"></i>
		        				<span>{{date('d/m/Y', strtotime($member->birth_date))}}</span>
		        			</div>
		        			<div class="gsm float small_no_float">
		        				<i class="fa fa-mobile" aria-hidden="true"></i>
		        				<span>{{substr($member->gsm, 0, 4) . ' ' . chunk_split(substr($member->gsm, 4), 2, ' ')}}</span>
		        			</div>
		        			<div class="tel float small_no_float">
		        				<i class="fa fa-phone" aria-hidden="true"></i>
		        				<span>{{substr($member->tel, 0, 3) . ' ' . chunk_split(substr($member->tel, 3), 2, ' ')}}</span>
		        			</div>
		        			<div class="email float small_no_float">
		        				<i class="fa fa-envelope-o" aria-hidden="true"></i>
		        				<span><a href="mailto:{{$member->email}}">{{$member->email}}</a></span>
		        			</div>
		        		</div>
	        			
	        			<div class="clearfix">
	        				@if(Auth::user()->isAdmin() || Auth::user()->isYouthChairman() || Auth::user()->isHeadtrainer())
		        			<div class="roles link float" ng-click="open_roles_modal($event, {{$member->id}}, '{{$member->first_name}} {{$member->last_name}}')">
		        				Rollen beheren
		        			</div>
		        			@endif
		        			@if(Auth::user()->isAdmin() && $member->tmp_password)
		        			<div class="temp_pwd float">
		        				(Voorlopig wachtwoord: {{$member->tmp_password}})
		        			</div>
		        			@endif
	        			</div>
	        			
        			</div>
        		</div>
        		@endforeach
        		@endif
        	</div>
        	<div class="pagination_container apply_bootstrap">
        		{{ $members->links() }}
        	</div>
        	

        </div>
    </div>


    <div id="import_members_modal" class="lightbox_modal light">
        <div class="modal">
            <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="modal_body">

	            <div class="descriptive_info">
	            	<p>Hieronder kan je de ledenlijst importeren.</p>
	            	<p>Gebruikers waarvan er geen VTV-nr beschikbaar is krijgen een willekeurig nummer toegewezen om te kunnen inloggen op de applicatie.</p>
	            	<p>Het importeren kan even duren.</p>
	            	<p>(<a class="link" href="{{url('download_members_example')}}">Voorbeeld Excel downloaden</a>)</p>
	            </div>
            	@if(session('error_messages'))
            	<div class="error_msg">
            		<p>Niet alle leden werden geïmporteerd om de volgende redenen:</p>
            		<ul>
            			@foreach(session('error_messages') as $error)
            			<li>{{$error}}</li>
            			@endforeach
            		</ul>
            		<p>Volgende leden leverden problemen op:</p>
            		<ul>
            			@foreach(session('problem_users') as $user)
            			<li>{{$user}}</li>
            			@endforeach
            		</ul>
            		
            	</div>
            	@endif
                
            </div>
            <div class="modal_footer">
                <form method="post" action="{{url('import_members')}}" enctype="multipart/form-data">
	                {{ csrf_field() }}
	                <input id="import_members" type="file" name="members_excel" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" hidden="">
	                <label for="import_members"><i class="fa fa-upload" aria-hidden="true"></i>
	        		Selecteer een Excel (.xlsx) om te importeren</label>
	                <input type="submit" name="submit" value="Leden importeren">
	            </form>
            </div>
        </div>
    </div>

    <div id="roles_modal" class="lightbox_modal light">
        <div class="modal">
            <div class="modal_header"><i class="fa fa-times" aria-hidden="true"></i></div>
            <div class="modal_body">
	            <div>
	            	Rollen toewijzen aan <strong>@{{member_name}}</strong><br>
	            </div>
	            <div class="descriptive_info">
	            	Rollen worden rechtstreeks opgeslagen bij het aan- of afvinken.
	            </div>
	            <div class="roles">
	            	<div class="role" ng-repeat="role in allowed_update_roles">
	            		<input id="@{{role.id}}" type="checkbox" name="role[]" value="@{{role.id}}" hidden ng-checked='check_if_role_is_assigned(role.id)'>
                        <label for="@{{role.id}}" ng-click="update_user_role($event, role.id)">
                            <span class="checkbox"><i class="fa fa-check"></i></span>
                            <span class="name">@{{role.name}}</span>
                        </label>
	            	</div>
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
	var errors = false;

	errors = <?php if(session('error_messages')) {echo("true");} else {echo("false"); } ?>;

</script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="{{ asset('js/members/members_overview.js') }}"></script>
@endsection