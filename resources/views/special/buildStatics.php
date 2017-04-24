Static Data Generation!


<form action='buildStatics?key=PIZZAMOFO' method='POST'>
Static:
<select name="static">
<?php echo $statics; ?>
</select><br />

<h4>Apply to:</h4>

<label> Region: 
<select name="region">
<?php echo $regions; ?>
</select> 
</label><br />
				
<label> Constellation:
<select name="constellation">
<option value='0'>--</option>
<?php echo $constellations; ?>
</select>
</label>
<br />
								
<input type='submit' value='Apply' />
</form>