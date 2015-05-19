<!-- mass add box start -->
<div id="pos-form" class="box" style="display:none;">
	<div class='box-header'>Add POS</div>
	<div class='box-content'>
		<form>
			<div>
				Location(Planet - Moon)<br />
				<input type="text" class="siggy-input" value="" name="pos_location_planet" size="2" maxlength="2" style="width:auto"/> -
				<input type="text" class="siggy-input" value="" name="pos_location_moon" size="3" maxlength="4" style="width:auto"/>
			</div>
			<label>
				Owner
				<input class="siggy-input" type="text" value="" name="pos_owner" />
			</label>
			<label>
				Type
				<select class="siggy-input" name="pos_type">
					<option value="1">Amarr</option>
					<option value="2">Caldari</option>
					<option value="3">Gallente</option>
					<option value="4">Minmatar</option>
					<option value="5">Dread Guristas</option>
					<option value="6">Shadow Serpentis</option>
					<option value="7">Guristas</option>
					<option value="8">Serpentis</option>
					<option value="9">Angel</option>
					<option value="10">Blood</option>
					<option value="11">Dark Blood</option>
					<option value="12">Domination</option>
					<option value="13">Sansha</option>
					<option value="14">True Sansha</option>
				</select>
			</label>
			<label>
				Size
				<select class="siggy-input" name="pos_size">
					<option value="small">Small</option>
					<option value="medium">Medium</option>
					<option value="large">Large</option>
				</select>
			</label>
			<label>
				Status
				<select class="siggy-input" name="pos_status">
					<option value="1">Online</option>
					<option value="0">Offline</option>
				</select>
			</label>
			<label>
				Notes
				<textarea name="pos_notes" rows="6" style="width:100%;font-size:11px;"></textarea>
			</label>
			<div class="text-center form-actions">
				<button name='submit' class="btn btn-primary btn-xs" type="submit">Submit</button>
				<button name='cancel' type="button" class="btn btn-default btn-xs dialog-cancel">Cancel</button>
			</div>
		</form>
	</div>
</div>
