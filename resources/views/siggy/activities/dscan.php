<div id="activity-dscan" class="wrapper" style="display:none">
<p>
	
</p>

<script id="template-dscan-everything-row" type="text/x-handlebars-template">
	<a href="#" class="list-group-item" data-toggle="collapse" data-target="#dscan-{{table_type}}-{{ group.id }}" data-parent="#menu">
		<span class="label label-info">{{ group.records.length }}</span> {{ group.name }}
		
		<i class="fa fa-chevron-down pull-right" aria-hidden="true"></i>
	</a>
	<div id="dscan-{{table_type}}-{{group.id}}" class="sublinks collapse">
		{{#each group.records}}
		<a class="list-group-item small">
			<img src='https://image.eveonline.com/Type/{{ type_id }}_32.png' /> {{ type_name }} - {{ record_name }}
		</a>
		{{/each}}
	</div>
</script>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Everything</div>
				<div class='panel-body list-group' id='activity-dscan-everything-list'>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading">Ships</div>
				<div class='panel-body list-group' id='activity-dscan-ships-list'>
				</div>
			</div>
			
			<div class="panel panel-default">
				<div class="panel-heading">Structures</div>
				<div class='panel-body list-group' id='activity-dscan-structures-list'>
				</div>
			</div>
		</div>
		<div class="col-md-4">
		
			<div class="panel panel-default">
				<div class="panel-heading">Filters</div>
				<div class='panel-body'>
					<div>
						<h5>Totals:</h5>
						<ul>
							<li>Everything: <span id='dscan-total-everything'></span> </li>
							<li>Ships: <span id='dscan-total-ships'></span></li>
							<li>Structures: <span id='dscan-total-structures'></span></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</div>
