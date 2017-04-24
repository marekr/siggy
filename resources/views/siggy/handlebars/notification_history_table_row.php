<script id="template-notification-history-table-row" type="text/x-handlebars-template">?
	<tr id='notification-history-{{ id }}' data-id='{{ id }}' class='exit-row'>
		<td class='text-center'>
			{{ displayTimestamp created_at }}
		</td>
		<td class='text-center'>
			{{{ notificationToString type data }}}
		</td>
	</tr>
</script>
