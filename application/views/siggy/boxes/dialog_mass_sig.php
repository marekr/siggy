<div id="mass-add-sig-box" class="box">
	<div class='box-header'>Mass Sig Reader</div>
	<div class='box-content'>
		<p>This is for copy pasted signatures from your scanner window.
			Simply select a signature, hit CTRL+A, then CTRL-C, then paste into the box below.
			This tool can add AND update signatures.
		</p>
		<form action='post'>
			<textarea name="blob" rows="12" style="width:100%;font-size:11px;">
			</textarea>


			<div class="form-group">
				<label for="delete_nonexistent_sigs">Delete nonexistent sigs</label>
				<input name='delete_nonexistent_sigs' type='checkbox' value='1' />
			</div>

			<div class="text-center form-actions">
				<button name='add' class="btn btn-primary" type="submit">Submit</button>
				<button name='cancel' type="button" class="btn btn-default">Cancel</button>
			</div>
		</form>
	</div>
</div>
