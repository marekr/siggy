@extends('layouts.public',[
							'title' => 'costs',
							'selectedTab' => 'costs',
							'layoutMode' => 'blank'
						])

@section('content')
<div class="container">
	<div class="well">
		<h2>Costs</h2>

		<p>
			siggy's costs are computed on a daily basis counting on total number of characters that accessed your group from the web interface.
			Currently alt characters that may be recorded by siggy on the map but not "used" as don't cost extra.
		</p>

		<table class="table table-striped">
		<tr>
			<th>Total characters used</th>
			<th>Daily cost (isk)</th>
			<th>Daily cost for a month (isk)</th>
		</tr>
		@for($i = 0; $i <= 200; $i += 25)
		<tr>
			<td>{{$i}}</td>
			<td>{{ number_format(miscUtils::computeCostPerDays($i,1)) }}</td>
			<td>{{ number_format(miscUtils::computeCostPerDays($i,30)) }}</td>
		</tr>
		@endfor
		</table>
	</div>
</div>
@endsection