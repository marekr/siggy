@extends('layouts.public')

@section('content')
<div class="container">
	<h2>Announcements</h2>
	{!! $pagination !!}

	@foreach($announcements as $announce)
		<div class="panel panel-default panel-announce">
			<div class="panel-heading">
				<h3>{{$announce->title}}</h3>
				<h5><span>Posted</span> - <span>{{miscUtils::getDateTimeString($announce->datePublished)}}</span> </h5>
			</div>
			<div class="panel-body">
				{{$announce->content}}
			</div>
		</div>
	@endforeach
</div>
@endsection