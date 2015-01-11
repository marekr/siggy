<script id="template-search-result-legacy-pos" type="text/x-handlebars-template">
	<div class="search-result">
		<h3>Legacy POS (Added by signature)</h3>
		<table class="siggy-table">
			<tr>
				<td class="title">System</td>
				<td class="content">{{ data.system_name }}</td>
				<td class="title">Description</td>
				<td class="content"><span class="highlight">{{ data.description }}</span></td>
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
