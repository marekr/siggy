@extends('stats.wrapper')

@section('stats_content')
<div class="container">
	<div class="row">
        <div class="col-sm-4">
			@include('stats.top10',['list' => $top10Adds, 'title' => 'Signatures added'])
		</div>
        <div class="col-sm-4">
			@include('stats.top10',['list' => $top10Edits, 'title' => 'Signatures updated'])
		</div>
        <div class="col-sm-4">
			@include('stats.top10',['list' => $top10WHs, 'title' => 'Wormholes mapped'])
		</div>
	</div>
</div>
@endsection