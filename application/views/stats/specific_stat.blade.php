@extends('stats.wrapper')

@section('stats_content')
<div class="container">
	<div class="row">
		{!! $pagination !!}
		<table class="table table-striped">
			<tr>
				<th width="5%">Rank</th>
				<th width="80%">Name</th>
				<th width="15%">Total</th>
			</tr>
			@foreach($results as $result)
			<tr>
				<td>{{++$rank_offset}}</td>
				<td class="center">
					<b><a href='javascript:siggy2.Eve.EveWho("{{urlencode($result->charName)}}")'>{{$result->charName}}</a></b><br />
					<img src='https://image.eveonline.com/Character/{{$result->charID}}_32.jpg' />
				</td>
				<td>{{$result->value}}</td>
			</tr>
			@endforeach
		</table>
		{!! $pagination !!}
	</div>
</div>
@endsection