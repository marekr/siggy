<div id="routePlanner" class="box" style="width:400px;display:none;">
	<h3>Route Planner</h3>
	<div style="padding:10px;">
		<ul class="errors">
		</ul>
		
		<div style="float:left;text-align:right;">
			From<input type='text' name='fromSys' /><br />
			<label style="display:block;margin-top:3px;">
				<input type='checkbox' name='fromCurrent' value='1' />  Current Location
			</label>
		</div>
		<div style="float:right;text-align:right;">
			To <input type='text' name='toSys' /><br />
			<label style="display:block;margin-top:3px;">
				<input type='checkbox' name='toCurrent' value='1' />  Current Location
			</label>
		</div>
		<br clear="all" />
		<br />
		*All routes are currently "shortest"
		<br />
		<br />
		<div class="center">
			<button id="computeRoute">Compute Route</button>
			<button id='routeCancel'>Cancel</button><br />
		</div>
	</div>
	
	<h3>Computed Route</h3>
	<div style="padding:10px;">
		<p>
			Number of jumps: <span>421</span>
		</p>
		<div style="height:210px;overflow: auto;">
			<ul class="itemList">
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
				<li>Adawd</li>
				<li>Asda</li>
			</ul>
		</div>
	</div>
</div>