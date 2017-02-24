
	<script id="template-structure-table-row" type="text/x-handlebars-template">?
		<tr id='structure-{{ id }}' data-id='{{ id }}'>
			<td class='text-center'>
				{{ corporation_name }}
			</td>
			<td>
				{{structureTypeName type_id}}
			</td>
			<td class='text-center'>
				{{ created_at }}
			</td>
			<td class='text-center'>
				{{ notes }}
			</td>
			<td class='text-center'>
				<button data-id='{{ id }}' class='button-structure-edit btn btn-xs btn-primary'>Edit</button>
				<button data-id='{{ id }}' class='button-structure-remove btn btn-xs btn-danger'>Remove</button>
			</td>
		</tr>
	</script>
