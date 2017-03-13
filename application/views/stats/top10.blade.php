<div class="panel panel-default">
	<div class="panel-heading"><?php echo $title; ?></div>
	<table cellspacing='1' class='table table-striped'>
	<?php $pos = 1; ?>
	@if( count($list['top10']) > 0 )
	@foreach( $list['top10'] as $person )
		<tr>
			<td width='10%' class='center'><?php echo $pos; ?>.</td>
			<td class='center' width='50%'>
				<b><a href='javascript:siggy2.Eve.EveWho("{{urlencode($person->charName)}}")'>{{$person->charName}}</a></b><br />
				<img src='https://image.eveonline.com/Character/{{$person->charID}}_32.jpg' />
			</td>
			<td class='center' width='40%' style='padding:10px;'>
				{{$person->value}}
				
				<div style='margin-top:5px;background-color:#5F5F5F;width:100%; height:10px;'>
					<div style='background-color:#FF9800;width:<?php echo round( ($person->value/$list['max'])*100 ); ?>%;height:10px;'>
					</div>
				</div>
			</td>
		</tr>
	<?php $pos++;?>
	@endforeach
	@else
		<tr>
			<td class='center' colspan='3'>
				No records found.
			</td>
		</tr>
	@endif
	</table>
</div>