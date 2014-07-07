<div class="panel panel-default">
	<div class="panel-heading"><?php echo $title; ?></div>
	<table cellspacing='1' class='table table-striped'>
	<?php 
	$pos = 1;
	if( count($data) > 0 ): 
	foreach( $data as $person ): 
	?>
		<tr>
			<td width='10%' class='center'><?php echo $pos; ?>.</td>
			<td class='center' width='50%'>
				<b><a href='javascript:CCPEVE.showInfo(1377, <?php echo $person['charID']; ?>)'><?php echo $person['charName']; ?></a></b><br />
				<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
			</td>
			<td class='center' width='40%' style='padding:10px;'>
				<?php echo $person['value']; ?>
				
				<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:#FF9800;width:<?php echo round( ($person['value']/$max)*100 ); ?>%;height:10px;'></div></div>
			</td>
		</tr>
	<?php 
	$pos++;
	endforeach;
	else: ?>
		<tr>
			<td class='center' colspan='3'>
				No records found.
			</td>
		</tr>
	<?php endif;?>
	</table>
</div>