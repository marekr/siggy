<!DOCTYPE html> 
<html>
  <head>
    <title>siggy</title>
       <style type='text/css'></style>
     <link type="text/css" href="<?php echo URL::base(TRUE, TRUE);?>public/css/siggy.css?8" rel="stylesheet" media="screen" /> 
    <?php if( Kohana::$environment == Kohana::DEVELOPMENT ): ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery-1.6.1.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.tablesorter.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.ezpz_tooltip.min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.blockUI.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.autocomplete.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/jquery.color.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/raphael-min.js'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.js?2'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggycalc.js?2'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggymap.js?2'></script>
    <?php else: ?>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/thirdparty.compiled.js?9'></script>
    <script type='text/javascript' src='<?php echo URL::base(TRUE, TRUE);?>public/js/siggy.compiled.js?14'></script>
    <?php endif; ?>
  </head>
  <body>
    <div id="wrapper">
      <?php if( $trusted==true || Kohana::$environment == Kohana::DEVELOPMENT): ?>
      <div id="topBar">
        <div>
          <img src="http://image.eveonline.com/Corporation/<?php echo $_SERVER['HTTP_EVE_CORPID']; ?>_64.png" height="32px" />
          <img src="http://image.eveonline.com/Character/<?php echo $_SERVER['HTTP_EVE_CHARID']; ?>_64.jpg" height="32px"/>
          <p class="name"><?php echo $_SERVER['HTTP_EVE_CHARNAME']; ?> <?php if(isset($group['groupTicker']) ) { print('<br /><span style="font-size:0.8em;font-style:italic;font-weight: normal;">Accessing as '.$group['accessName'].' as part of the '.$group['groupTicker'].' group </span>'); } ?></p>
        </div>
      </div> 
      <br />
      <div style='width:520px;margin: 0 auto;'>
				<h1 style='font-size:1.5em;font-weight:bold;'>Stats for all of <?php echo $group['groupTicker']; ?></h1>
				<div style='border:1px solid #000;width:250px;float:left;'>
					<table cellspacing='1' class='stats'>
						<tr>
							<th colspan='3'>Sigs recorded this week</th>
						</tr>
					<?php 
					$pos = 1;
					if( count($groupTop10) > 0 ): 
					foreach( $groupTop10 as $person ): 
					?>
						<tr>
							<td width='10%' class='center'><?php echo $pos; ?>.</td>
							<td class='center' width='50%'>
								<b><a href='javascript:CCPEVE.showInfo(1377, <?php echo $person['charID']; ?>)'><?php echo $person['charName']; ?></a></b><br />
								<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
							</td>
							<td class='center' width='40%' style='padding:10px;'>
								<?php echo $person['adds']; ?>
								
								<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:#FF9800;width:<?php echo round( ($person['adds']/$groupTop10Max)*100 ); ?>%;height:10px;'></div></div>
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
				<div style='border:1px solid #000;width:250px;float:left;'>
					<table cellspacing='1' class='stats'>
						<tr>
							<th colspan='3'>Sigs recorded last week</th>
						</tr>
					<?php 
					$pos = 1;
					if( count($groupTop10LastWeek) > 0 ): 
					foreach( $groupTop10LastWeek as $person ): 
					?>
						<tr>
							<td width='10%' class='center'><?php echo $pos; ?>.</td>
							<td class='center' width='50%'>
								<b><a><?php echo $person['charName']; ?></a></b><br />
								<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
							</td>
							<td class='center' width='40%' style='padding:10px;'>
								<?php echo $person['adds']; ?>
								
								<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:blue;width:<?php echo round( ($person['adds']/$groupTop10LastWeekMax)*100 ); ?>%;height:10px;'></div></div>
							</td>
						</tr>
					<?php 
					$pos++;
					endforeach; 
					else:
					?>
						<tr>
							<td class='center' colspan='3'>
								No records found.
							</td>
						</tr>
					<?php endif;?>
					</table>
				</div>
				<div class='clear'></div>
      </div>
      <!-- next -->

      <br />
      <div style='width:520px;margin: 0 auto;'>
				<div style='border:1px solid #000;width:250px;float:left;'>
					<table cellspacing='1' class='stats'>
						<tr>
							<th colspan='3'>Sigs edited this week</th>
						</tr>
					<?php 
					$pos = 1;
					if( count($groupTop10Edits) > 0 ): 
					foreach( $groupTop10Edits as $person ): 
					?>
						<tr>
							<td width='10%' class='center'><?php echo $pos; ?>.</td>
							<td class='center' width='50%'>
								<b><a href='javascript:CCPEVE.showInfo(1377, <?php echo $person['charID']; ?>)'><?php echo $person['charName']; ?></a></b><br />
								<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
							</td>
							<td class='center' width='40%' style='padding:10px;'>
								<?php echo $person['edits']; ?>
								
								<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:#FF9800;width:<?php echo round( ($person['edits']/$groupTop10EditsTotal)*100 ); ?>%;height:10px;'></div></div>
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
				<div style='border:1px solid #000;width:250px;float:left;'>
					<table cellspacing='1' class='stats'>
						<tr>
							<th colspan='3'>Sigs edited last week</th>
						</tr>
					<?php 
					$pos = 1;
					if( count($groupTop10EditsLastWeek) > 0 ): 
					foreach( $groupTop10EditsLastWeek as $person ): 
					?>
						<tr>
							<td width='10%' class='center'><?php echo $pos; ?>.</td>
							<td class='center' width='50%'>
								<b><a><?php echo $person['charName']; ?></a></b><br />
								<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
							</td>
							<td class='center' width='40%' style='padding:10px;'>
								<?php echo $person['edits']; ?>
								
								<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:blue;width:<?php echo round( ($person['edits']/$groupTop10EditsLastWeekTotal)*100 ); ?>%;height:10px;'></div></div>
							</td>
						</tr>
					<?php 
					$pos++;
					endforeach; 
					else:
					?>
						<tr>
							<td class='center' colspan='3'>
								No records found.
							</td>
						</tr>
					<?php endif;?>
					</table>
				</div>
				<div class='clear'></div>
      </div>      
      

      <!-- next -->

      <br />
      <div style='width:520px;margin: 0 auto;'>
				<div style='border:1px solid #000;width:250px;float:left;'>
					<table cellspacing='1' class='stats'>
						<tr>
							<th colspan='3'>Wormholes mapped this week</th>
						</tr>
					<?php 
					$pos = 1;
					if( count($groupTop10WHs) > 0 ): 
					foreach( $groupTop10WHs as $person ): 
					?>
						<tr>
							<td width='10%' class='center'><?php echo $pos; ?>.</td>
							<td class='center' width='50%'>
								<b><a href='javascript:CCPEVE.showInfo(1377, <?php echo $person['charID']; ?>)'><?php echo $person['charName']; ?></a></b><br />
								<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
							</td>
							<td class='center' width='40%' style='padding:10px;'>
								<?php echo $person['wormholes']; ?>
								
								<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:#FF9800;width:<?php echo round( ($person['wormholes']/$groupTop10WHsTotal)*100 ); ?>%;height:10px;'></div></div>
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
				<div style='border:1px solid #000;width:250px;float:left;'>
					<table cellspacing='1' class='stats'>
						<tr>
							<th colspan='3'>Wormholes mapped last week</th>
						</tr>
					<?php 
					$pos = 1;
					if( count($groupTop10WHsLastWeek) > 0 ): 
					foreach( $groupTop10WHsLastWeek as $person ): 
					?>
						<tr>
							<td width='10%' class='center'><?php echo $pos; ?>.</td>
							<td class='center' width='50%'>
								<b><a><?php echo $person['charName']; ?></a></b><br />
								<img src='http://image.eveonline.com/Character/<?php echo $person['charID'];?>_32.jpg' />
							</td>
							<td class='center' width='40%' style='padding:10px;'>
								<?php echo $person['wormholes']; ?>
								
								<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'><div style='background-color:blue;width:<?php echo round( ($person['wormholes']/$groupTop10WHsLastWeekTotal)*100 ); ?>%;height:10px;'></div></div>
							</td>
						</tr>
					<?php 
					$pos++;
					endforeach; 
					else:
					?>
						<tr>
							<td class='center' colspan='3'>
								No records found.
							</td>
						</tr>
					<?php endif;?>
					</table>
				</div>
				<div class='clear'></div>
      </div>            
			<?php endif; ?>
   </div>
  </body>
</html>