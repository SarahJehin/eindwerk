@extends('layouts.app')
@section('title', 'Leden')
@section('custom_css')
<link href="{{url('css/bootstrap.css')}}" type="text/css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
@endsection
@section('content')
<div class="members_overview">

    <div class="block">
        <div class="heading">
            Ledenlijst
        </div>
        <div class="content">
        	<p>Hieronder vind je een overzicht van alle Sportiva leden</p>

        	<div class="search">
        		<form id="search_members" method="post" action="{{url('members_overview')}}">
        			{{ csrf_field() }}
	        		<div class="quick">
	        			<div class="clearfix">
	        				<input class="float" type="text" name="name" placeholder="Wie wil je zoeken?"
	        				@if(isset($input['name']))
	        				value="{{$input['name']}}"
	        				@endif>
	        				<span class="searchbutton float"><i class="fa fa-search" aria-hidden="true"></i></span>
	        			</div>
	        		</div>
	        		<div class="open_advanced link">Uitgebreid zoeken</div>

	        		<div class="advanced clearfix">
	        			<div class="ranking float">
	        				<h3>Klassement</h3>
	        				<div class="apply_bootstrap">
	        					<label for="from_ranking">Van:</label>
		        				<select class="selectpicker" data-size="10" id="from_ranking" name="from_ranking">
		        					<option value="from">Van</option>
		        					@foreach($rankings as $ranking)
			        				<option value="{{$ranking}}"
			        				@if(isset($input))
				        				@if($input['from_ranking'] == $ranking)
				        				selected=""
				        				@endif
			        				@endif
			        				>{{$ranking}}</option>
			        				@endforeach
		        				</select>
	        				
	        				
		        				<label for="to_ranking">tot:</label>
		        				<select class="selectpicker" data-size="10" id="to_ranking" name="to_ranking">
		        					<option value="to">Tot</option>
		        					@foreach($rankings as $ranking)
			        				<option value="{{$ranking}}"
			        				@if(isset($input))
				        				@if($input['to_ranking'] == $ranking)
				        				selected=""
				        				@endif
			        				@endif
			        				>{{$ranking}}</option>
			        				@endforeach
		        				</select>
		        			</div>
	        			</div>
	        			<div class="birth_year float">
	        				<h3>Geboortejaar</h3>
	        				<div class="apply_bootstrap">
	        					<label for="from_birth_year">Van:</label>
		        				<select class="selectpicker" data-size="10" id="from_birth_year" name="from_birth_year">
		        					<option value="from">Van</option>
		        					@for($i = (date('Y')-80); $i < date('Y'); $i++)
			        				<option value="{{$i}}"
			        				@if(isset($input))
				        				@if($input['from_birth_year'] == $i)
				        				selected=""
				        				@endif
			        				@endif
			        				>{{$i}}</option>
			        				@endfor
		        				</select>
		        				<label for="to_birth_year">tot:</label>
		        				<select class="selectpicker" data-size="10" id="to_birth_year" name="to_birth_year">
		        					<option value="to">Tot</option>
		        					@for($i = (date('Y')-80); $i < date('Y'); $i++)
			        				<option value="{{$i}}"
			        				@if(isset($input))
				        				@if($input['to_birth_year'] == $i)
				        				selected=""
				        				@endif
			        				@endif
			        				>{{$i}}</option>
			        				@endfor
		        				</select>
	        				</div>
	        				
	        			</div>

	        			<input type="submit" value="Zoek">
	        		</div>
        		</form>
        	</div>

        	<div class="download link">
        		<a href="{{url('download_members_as_excel')}}">
	        		<i class="fa fa-file-excel-o" aria-hidden="true"></i>
	        		Ledenlijst downloaden
	        	</a>
        	</div>
        	<div class="list">
        		<div class="member_block">
        			<div class="row header clearfix">
        				<div class="name float">Naam</div>
        				<div class="date float">Geboortedatum</div>
        				<div class="singles float">Enkel</div>
        				<div class="doubles float">Dubbel</div>
        			</div>
        		</div>
        		@foreach($members as $member)
        		<div class="member_block">
        			<div class="row clearfix">
        				<div class="name float link">{{$member->last_name}} {{$member->first_name}}</div>
        				<div class="date float">{{date('d-m-Y', strtotime($member->birth_date))}}</div>
        				<div class="singles float">{{$member->ranking_singles}}</div>
        				<div class="doubles float">{{$member->ranking_doubles}}</div>
        			</div>
        			<div class="details clearfix">
        				<div class="profile_pic float">
	        				<img src="{{url('images/profile_pictures/' . $member->image)}}" alt="{{$member->first_name}} {{$member->last_name}}">
	        			</div>
	        			<div class="contact_info float clearfix">
	        				<div class="gsm float">
	        					<i class="fa fa-mobile" aria-hidden="true"></i>
	        					<span>{{$member->gsm}}</span>
	        				</div>
	        				<div class="tel float">
	        					<i class="fa fa-phone" aria-hidden="true"></i>
	        					<span>{{$member->gsm}}</span>
	        				</div>
	        				<div class="email float">
	        					<i class="fa fa-envelope-o" aria-hidden="true"></i>
	        					<span>{{$member->email}}</span>
	        				</div>
	        			</div>
        			</div>
        		</div>
        		@endforeach
        	</div>
        </div>
    </div>
</div>

@endsection
@section('custom_js')
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="{{ asset('js/members/members_overview.js') }}"></script>
@endsection