@extends('layouts.manage',[
							'title' => 'siggy.manage: add member'
						])

@section('content')
<?php
$type = array('corp' => 'Corp', 'char' => 'Character');
?>


	<?php if(count($results) > 0 ): ?>
	<h3>Add New Member Search Results</h3>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th width="80%">Name</th>
				<th width="10%">Ingame ID</th>
				<th width="5%">Options</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $results as $result ): ?>
			<tr>
				<?php if( $memberType == 'corp' ): ?>
				<td><img src="https://image.eveonline.com/Corporation/<?php echo $result->id; ?>_64.png" width="64" height="64" /></td>
				<td><?php echo $result->name; ?></td>
				<td><?php echo $result->id; ?></td>
				<td>
					<form class="form-inline" action="<?php echo URL::base(TRUE,TRUE).'manage/group/addMember/2'; ?>" method="post">
						<input type="hidden" name="act" value="doForm" />
						<input type="hidden" name="eveID" value="<?php echo $result->id; ?>" />
						<input type="hidden" name="accessName" value="<?php echo $result->name; ?>" />
						<input type="hidden" name="memberType" value="<?php echo $memberType; ?>" />
						<button type="submit" class="btn btn-primary">Select</button>
					</form>
				</td>
				<?php else: ?>
				<td><img src="https://image.eveonline.com/Character/<?php echo $result->id; ?>_64.jpg" width="64" height="64" /></td>
				<td><?php echo $result->name; ?></td>
				<td><?php echo $result->id; ?></td>
				<td>
					<form class="form-inline" action="<?php echo URL::base(TRUE,TRUE).'manage/group/addMember/2'; ?>" method="post">
						<input type="hidden" name="act" value="doForm" />
						<input type="hidden" name="eveID" value="<?php echo $result->id; ?>" />
						<input type="hidden" name="accessName" value="<?php echo $result->name; ?>" />
						<input type="hidden" name="memberType" value="<?php echo $memberType; ?>" />
						<button type="submit" class="btn btn-primary">Select</button>
					</form>
				</td>
				<?php endif; ?>
			</tr>
			<?php endforeach; ?>
		</tbody>
   </table>
   <?php endif; ?>

   <form class="form-horizontal" action="<?php echo URL::base(TRUE,TRUE).'manage/group/addMember/1'; ?>" method="post">
		<?php if(count($results) > 0 ): ?>
		<h3>Or search again</h3>
		<?php else: ?>
		<h3>Add New Member By Search</h3>
		<?php endif; ?>
		<?php echo formRenderer::select('Member Type', 'memberType', $type, 'corp', '', $errors); ?>
		<?php echo formRenderer::input('Name', 'searchName', '', 'Exact name from EVE ingame for the character or corp', $errors); ?>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Search</button>
			
			<button type="button" class="btn" onclick="history.go(-1);return false;">Cancel</button>
		</div>
	</form>
@endsection
