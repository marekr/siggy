
	<script id="template-pos-table-row" type="text/x-handlebars-template">?
		<tr id='pos-{{ id }}' data-id='{{ id }}'>
			<td class='{{status_class}}'>
				{{ status }}
			</td>
			<td class='text-center'>
				{{ location_planet }} - {{ location_moon }}
			</td>
			<td>
				{{ owner }}
			</td>
			<td class='text-center'>
				{{ posTypeName type_id }}
			</td>
			<td>
				{{ capitalize size }}
			</td>
			<td>
				{{ displayTimestamp added_date }}
			</td>
			<td class='text-center'>
				{{ notes }}
			</td>
			<td class='text-center'>
				<button data-id='{{ id }}' class='button-pos-edit btn btn-xs btn-primary'>Edit</button>
				<button data-id='{{ id }}' class='button-pos-remove btn btn-xs btn-danger'>Remove</button>
			</td>
		</tr>
	</script>
