@extends('layouts.manage',[
							'title' => 'siggy.manage: billing overview'
						])

@section('content')

<?php if( !$group->billable ): ?>
<br />
<div class="alert alert-warning">
Your group is currently set in a legacy billing mode. By submitting payment with a valid payment reason, you will automatically be enrolled in the new mode.
Please be warned, as this new method of billing phases in, you will be automatically switched over.
</div>
<?php endif; ?>

<h4>Overview</h4>
<dl class="dl-horizontal">
	<dt>Account Balance</dt>
	<dd>{{ number_format($group->isk_balance) }} isk</dd>
	<dt>Member Count</dt>
	<dd>{{ ($numUsers > 0) ? $numUsers : 'Pending' }}</dd>
	<dt>Daily cost*</dt>
	<dd>{{ number_format(miscUtils::computeCostPerDays($numUsers,1)) }} isk</dd>
	<dt>Monthly cost*</dt>
	<dd>{{ number_format(miscUtils::computeCostPerDays($numUsers,30)) }} isk</dd>
</dl>
<span class="help-inline">* Projected costs on current member count. Ensure you have enough isk to cover daily costs at the minimum. You do not have to pay ahead any predefined amount of days.</span>


<h4>Make a payment</h4>
<p>All isk should be sent to the corp <strong><a onclick="javascript:CCPEVE.showInfo(2,98046548);" style="cursor:pointer">borkedLabs</a></strong>.<br />
You <strong>must</strong> enter the following text as the reason:
<input class="input-large" id="disabledInput" type="text" readonly="readonly" value="siggy-<?php echo $group->payment_code; ?>" style="cursor:text; "/>
</strong><br />
* Any payments without the proper text as the reason cannot be proceeded automatically and may count as donations.<br />
</p>
<br />

<h4>Recent Charges</h4>
<table class="table table-striped" width="100%">
	<thead>
		<tr>
			<th width="25%">Date Charged</th>
			<th width="25%">Amount (isk)</th>
			<th width="25%">Message</th>
		</tr>
	</thead>
	<tbody>
		@foreach( $charges as $c )
		<tr>
			<td>{{ $c->charged_at }}</td>
			<td>-{{ number_format($c->amount,2 ) }}</td>
			<td>{{ $c->message }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<h4>Recent Payments</h4>
<table class="table table-striped" width="100%">
	<thead>
		<tr>
			<th width="25%">Date Processed</th>
			<th width="25%">Date Submitted</th>
			<th width="25%">Amount (isk)</th>
			<th width="25%">Payer</th>
		</tr>
	</thead>
	<tbody>
		@foreach( $payments as $p )
		<tr>
			<td>{{$p->processed_at}}</td>
			<td>{{$p->paid_at}}</td>
			<td>{{number_format($p->amount,2)}}</td>
			<td>{{$p->payer_name}}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endsection