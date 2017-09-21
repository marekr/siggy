
	<script id="template-dscan-table-row" type="text/x-handlebars-template">?
		<tr id='dscan-{{ id }}' data-id='{{ id }}'>
			<td>
				{{ title }}
			</td>
			<td>
				{{ created_at }}
			</td>
			<td>
				{{ added_by }}
			</td>
			<td class='text-center'>
				<a data-navigo href='/dscan/{{ id }}' class='btn btn-xs btn-primary'>View</a>
				<button data-id='{{ id }}' class='button-dscan-remove btn btn-xs btn-danger'>Remove</button>
			</td>
		</tr>
	</script>
