<script id="template-dialog-mass-add-content" type="text/x-handlebars-template">
	<div>
		<p>
			This is for copy and pasting signature results from your scanner window.<br />
			Simply select a signature, hit Ctrl+A, then Ctrl+C, then paste into the box below.<br />
			This tool can add and update signatures.
		</p>
		<form action='post'>
			<textarea name="blob" rows="12" style="width:100%;font-size:11px;" class="dialog-focus-me"></textarea>
			<div class="form-group">
				<label for="delete_nonexistent_sigs">Delete nonexistent sigs</label>
				<input name='delete_nonexistent_sigs' id='delete_nonexistent_sigs' type='checkbox' value='1' />
			</div>
		</form>
	</div>
</script>
