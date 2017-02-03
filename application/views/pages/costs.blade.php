@extends('layouts.public',[
							'title' => 'siggy: costs',
							'selectedTab' => 'costs',
							'layoutMode' => 'blank'
						])

@section('content')
<div class="container">
	<div class="well">
		<h2>Costs</h2>

		<p>siggy's costs are computed on a daily basis depending on the number of characters total in your group. The costs are currently computed using a linear model where the cost per member decreases with the total number of members. The table below shows values calculated live by the current payment model. Since costs will be constantly changing with your member count totals, these are just approximate numbers.</p>

		<table class="table table-striped">
		<tr>
			<th>Num. Members</th>
			<th>Daily Cost (isk)</th>
			<th>Approx. 30 Days (isk)</th>
		</tr>
		@for($i =1; $i <= 310; $i += 25)
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