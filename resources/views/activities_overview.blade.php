@extends('layouts.app')
@section('title', 'Activiteiten')
@section('custom_css')
<link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" type="text/css" rel="stylesheet">
@endsection

@section('content')
    <div class="activities_overview">

        <div class="block">
            <div class="heading">
                Activiteiten overzicht
            </div>
            <div class="content">

                @if (session('message'))
                    <div>
                        {{ session('message') }}
                    </div>
                @endif

                @if($is_admin)
                <div class="edit_button">
                    <a href="{{url('activities_list')}}"><i class="fa fa-pencil" aria-hidden="true"></i> Bewerken</a>
                </div>
                @endif
                
                <div id="calendar">
                    
                </div>

            </div>
        </div>
    </div>
@endsection

@section('custom_js')
<script src="{{ asset('js/moment.js') }}"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/locale/nl-be.js"></script>
<script type="text/javascript">
    var base_url = '{{ url('/') }}';
</script>
<script src="{{ asset('js/activities_overview.js') }}"></script>

</script>
@endsection
