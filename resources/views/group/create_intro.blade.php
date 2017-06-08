@extends('layouts.public',[
							'title' => 'siggy: create group',
							'selectedTab' => 'createGroup',
							'layoutMode' => 'blank'
						])

@section('content')
<div class="container">
	<div class="well">
		<h2>siggy Group Creation</h2>
		<p><strong>IF YOUR CORP OR ALLIANCE HAS ACCESS ALREADY, THIS IS NOT HOW YOU GAIN ACCESS</strong></p>
		<p>This page will begin the process of creating a new siggy group.</p>
		<p>The following are general conditions you must accept before continuing:</p>

		<ol>
			<li>Billing occurs on 30 day cycles. At the start of a billing period you are expected to pay for the bill generated based on your current group member count. Any changes afterwards during the 30 days are ignored.</li>
			<li>Groups that are overdue in payment by 10 days will be disabled until paid</li>
			<li>Groups that are disabled for more than 6 months will be permenatly deleted, this means all associated data will be lost, you can however create a new group if desired afterwards</li>
			<li>Security of your group is dependent on you. We are not responsible for any issues caused by metagaming.</li>
			<li>You will not attempt to intefere with the usage of other siggy users and groups</li>
		</ol>
		<div class="text-centered">
			<a href="{{url('group/create/form')}}" class="btn btn-large btn-primary text-centered">Continue to group creation</a>
		</div>
	</div>
</div>
@endsection
