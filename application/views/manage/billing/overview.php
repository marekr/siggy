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
  <dd><?php echo number_format($group->iskBalance); ?> isk</dd>
  <dt>Member Count</dt>
  <dd><?php echo $numUsers; ?></dd>
  <dt>Daily cost*</dt>
  <dd><?php echo number_format(miscUtils::computeCostPerDays($numUsers,1)); ?> isk</dd>
  <dt>Monthly cost*</dt>
  <dd><?php echo number_format(miscUtils::computeCostPerDays($numUsers,30)); ?> isk</dd>
</dl>
<span class="help-inline">* Projected costs on current member count</span>


<h4>Make a payment</h4>
<p>All isk should be sent to the corp <strong>borkedLabs</strong>.<br />
You <strong>must</strong> enter the following text as the reason: <strong>siggy-<?php echo $group->paymentCode; ?></strong><br />
* Any payments without the proper text as the reason cannot be proceeded automatically and may count as donations.<br />
</p>
<br />

<h4>Recent Charges</h4>
<table class="table table-striped" width="100%">
	<tr>
		<th width="25%">Date Charged</th>
		<th width="25%">Amount (isk)</th>
		<th width="25%">Message</th>
	</tr>
<?php foreach( $charges as $c ): ?>
	<tr>
		<td><?php echo date("d/m/y @ h:m:s",$c['date']); ?></td>
		<td>-<?php echo number_format($c['amount']); ?></td>
		<td><?php echo $c['message']; ?></td>
	</tr>
<?php endforeach ?>
</table>

<h4>Recent Payments</h4>
<table class="table table-striped" width="100%">
	<tr>
		<th width="25%">Date Processed</th>
		<th width="25%">Date Submitted</th>
		<th width="25%">Amount (isk)</th>
		<th width="25%">Payee</th>
	</tr>
<?php foreach( $payments as $p ): ?>
	<tr>
		<td><?php echo date("d/m/y @ h:m:s",$p['paymentProcessedTime']); ?></td>
		<td><?php echo date("d/m/y @ h:m:s",$p['paymentTime']); ?></td>
		<td><?php echo number_format($p['paymentAmount']); ?></td>
		<td><?php echo $p['payeeName']; ?></td>
		
	</tr>
<?php endforeach ?>
</table>
