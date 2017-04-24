<script id="template-search-result-pos" type="text/x-handlebars-template">
	<div class="search-result">
		<h3>POS</h3>
		<table class="siggy-table">
			<tr>
				<td class="title">System</td>
				<td class="content">{{ data.system.name }}</td>
				<td class="title">Owner</td>
				<td class="content">{{ data.owner }}</td>
			</tr>
			<tr>
				<td class="title">POS Planet</td>
				<td class="content">{{ data.location_planet }}</td>
				<td class="title">POS Moon</td>
				<td class="content">{{ data.location_moon }}</td>
			</tr>
			<tr>
				<td class="title">Date Added</td>
				<td class="content">{{ displayTimestamp data.created }}</td>
				<td class="title"></td>
				<td class="content"></td>
			</tr>
		</table>
	</div>
</script>
