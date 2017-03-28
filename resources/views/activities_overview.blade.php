@extends('layouts.app')

@section('custom_css')
<link href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" type="text/css" rel="stylesheet">
@endsection

@section('content')
    <div class="">

        <div class="block">
            <div class="heading">
                Activiteiten overzicht
            </div>
            <div class="content">
                Hierin komt een overzichtje met activiteiten

                @if (session('message'))
                    <div>
                        {{ session('message') }}
                    </div>
                @endif

                {{--
                <ul>
                @foreach($activities as $activity)
                    <li>{{$activity->title}}
                    <ul>
                        <li>{{$activity->startdate}}</li>
                        <li>{{$activity->min_participants}}</li>
                    </ul></li>
                @endforeach
                </ul>
                --}}
                
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
