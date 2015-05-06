	<script id="template-notification-notifier-table-row" type="text/x-handlebars-template">?
		<tr id='notification-notifier-{{ id }}' data-id='{{ id }}' class='exit-row'>
			<td>
				{{{ capitalize scope }}}
			</td>
			<td class='text-center'>
				{{ displayTimestamp created_at }}
			</td>
			<td class='text-center'>
				{{{ notifierToString type data }}}
			</td>
			<td>
				<button class='btn btn-default btn-xs btn-danger' class='notifier-delete' data-id='{{ id }}'>Delete</button>
			</td>
		</tr>
	</script>
