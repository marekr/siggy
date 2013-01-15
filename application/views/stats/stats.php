<h4>siggy stats</h4>
<div class="btn-toolbar">
  <div class="btn-group">
    <a class="btn<?php echo ( $statsMode == 'weekly' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo date("Y");?>/week/<?php echo date("W");?>">Weekly</a>
    <a class="btn<?php echo ( $statsMode == 'monthly' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo date("Y");?>/month/<?php echo date("n");?>">Monthly</a>
    <a class="btn<?php echo ( $statsMode == 'yearly' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo date("Y");?>">Yearly</a>
  </div>
</div>
<div class="well" style="text-align:center;font-size:16px;">
	<?php if( $statsMode == 'yearly' ): ?>
		<a style="float:left;" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo $yearlyPrevYear;?>">
			&lt;&lt; Year <?php echo $yearlyPrevYear;?>
		</a>
		<a  style="float:right;" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo $yearlyNextYear;?>">
			 Year <?php echo $yearlyNextYear;?> &gt;&gt;
		</a>
		<strong>Year <?php echo $year; ?></strong>
	<?php elseif( $statsMode == 'weekly' ): ?>
		<a style="float:left;" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo $weeklyPrevYear;?>/week/<?php echo $weeklyPrevWeek;?>">
			&lt;&lt; Week <?php echo $weeklyPrevWeek;?>, <?php echo $weeklyPrevYear;?>
		</a>
		<a  style="float:right;" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo $weeklyNextYear;?>/week/<?php echo $weeklyNextWeek;?>">
			 Week <?php echo $weeklyNextWeek;?>, <?php echo $weeklyNextYear;?> &gt;&gt;
		</a>
		<strong>Week #<?php echo $week; ?>, <?php echo $year; ?></strong>
	<?php else: ?>
		<a style="float:left;" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo $monthlyPrevYear;?>/month/<?php echo $monthlyPrevMonth;?>">
			&lt;&lt; <?php echo $monthlyPrevMonthName;?>, <?php echo $monthlyPrevYear;?>
		</a>
		<a  style="float:right;" href="<?php echo URL::base(TRUE, TRUE);?>stats/year/<?php echo $monthlyNextYear;?>/month/<?php echo $monthlyNextMonth;?>">
			 <?php echo $monthlyNextMonthName;?>, <?php echo $monthlyNextYear;?> &gt;&gt;
		</a>
		<strong><?php echo $monthName; ?>, <?php echo $year; ?></strong>
	<?php endif; ?>
</div>

<div class="row">
	<?php echo $addsHTML; ?>
	<?php echo $editsHTML; ?>
	<?php echo $whsHTML; ?>
</div>