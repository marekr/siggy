@extends('stats.wrapper')

@section('stats_content')
<div class="container">
	<div class="row">
        <div class="col-sm-4">
			@include('stats.top10',['list' => $top10Adds])
		</div>
        <div class="col-sm-4">
			@include('stats.top10',['list' => $top10Edits])
		</div>
        <div class="col-sm-4">
			@include('stats.top10',['list' => $top10WHs])
		</div>
	</div>
</div>
@endsection