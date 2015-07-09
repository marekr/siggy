<div class="container">
	<div class="row">
		<h2>siggy stats</h2>
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group">
				<a class="btn btn-default<?php echo ( $stats_mode == DatePager::MODEWEEKLY ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/<?php echo $sub_page; ?>/year/<?php echo date("Y");?>/week/<?php echo date("W");?>">Weekly</a>
				<a class="btn btn-default<?php echo ( $stats_mode == DatePager::MODEMONTHLY  ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/<?php echo $sub_page; ?>/year/<?php echo date("Y");?>/month/<?php echo date("n");?>">Monthly</a>
				<a class="btn btn-default<?php echo ( $stats_mode == DatePager::MODEYEARLY  ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/<?php echo $sub_page; ?>/year/<?php echo date("Y");?>">Yearly</a>
			</div>
		</div>
	</div>
	<br />
	<div class="row">
		<div class="btn-toolbar" role="toolbar">
			<div class="btn-group">
				<a class="btn btn-default<?php echo ( $sub_page == 'overview' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/<?php echo $current_date['urlbit']; ?>">Overview</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'leaderboard' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/leaderboard/<?php echo $current_date['urlbit']; ?>">Leaderboard</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'pos_adds' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/pos_adds/<?php echo $current_date['urlbit']; ?>">POS Additions</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'pos_updates' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/pos_updates/<?php echo $current_date['urlbit']; ?>">POS Updates</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'sig_adds' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/sig_adds/<?php echo $current_date['urlbit']; ?>">Sig Additions</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'sig_updates' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/sig_updates/<?php echo $current_date['urlbit']; ?>">Sig Updates</a>
				<a class="btn btn-default<?php echo ( $sub_page == 'wormholes' ? ' active' : '') ?>" href="<?php echo URL::base(TRUE, TRUE);?>stats/wormholes/<?php echo $current_date['urlbit']; ?>">Wormholes Mapped</a>
			</div>
		</div>
	</div>
</div>
<br />
<div class="container">
	<div class="row">
		<div class="well" style="text-align:center;font-size:16px;">
			<a style="float:left;" href="<?php echo URL::base(TRUE, TRUE);?>stats/<?php echo $sub_page; ?>/<?php echo $previous_date['urlbit'];?>">
				&lt;&lt; <?php echo $previous_date['text'];?>
			</a>
			<a  style="float:right;" href="<?php echo URL::base(TRUE, TRUE);?>stats/<?php echo $sub_page; ?>/<?php echo $next_date['urlbit'];?>">
				<?php echo $next_date['text'];?> &gt; &gt;
			</a>
			<strong><?php echo $current_date['text']; ?></strong>
		</div>
	</div>
</div>

<?php echo $content; ?>
