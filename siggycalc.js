

/**
* @constructor
*/
function siggyCalc()
{
	this.currentProfile = {
		covertOps: 0,
		rangeFinding: 0,
		rigs: 0,
		prospector: 0,
		sistersLauncher: 0,
		sistersProbes: 0
	};
	this.currentProfileID = 0;
	this.baseSigSizes = [1.25, 1.67, 2.2, 2.5, 4, 5, 6.67, 10, 20];
	this.baseUrl = '';
	this.mode = '';
	this.profiles = {};
	this.loaded = false;
	//setCookie('pizza','good', 20);
	//alert(document.cookie);
}
siggyCalc.prototype.loadData = function ()
{
	var select = $("#scanProfiles");
	var that = this;
	$.ajax(
	{
		url: this.baseUrl + 'doLoadScanProfiles',
		async: false,
		dataType: 'json',
		success: function (data)
		{

			var num = 0;
			select.empty();

			that.profiles = data;

			for (var i in data)
			{
				num++;
				select.append($("<option>").attr('value', data[i].profileID).text(data[i].profileName));
				data[i].profileID = parseInt(data[i].profileID);
				data[i].covertOps = parseInt(data[i].covertOps);
				data[i].rangeFinding = parseInt(data[i].rangeFinding);
				data[i].rigs = parseInt(data[i].rigs);
				data[i].prospector = parseInt(data[i].prospector);
				data[i].sistersLauncher = parseInt(data[i].sistersLauncher);
				data[i].sistersProbes = parseInt(data[i].sistersProbes);
				data[i].preferred = parseInt(data[i].preferred);
				if (data[i].preferred == 1)
				{
					that.currentProfile = data[i];
					that.currentProfileID = i;
					select.val(i);
				}
			}

			$('#createProfile').attr('disabled', false);
			if (num > 0)
			{
				$('#editProfile').attr('disabled', false);
				$('#deleteProfile').attr('disabled', false);

				if (that.currentProfileID == 0)
				{
					var first = 0;
					for (first in data) break;
					that.currentProfile = data[first];
					that.currentProfileID = first;
					select.val(i);
				}
				that.fillProfileInfo(that.currentProfile);
				that.calcAndPopulate();

				$('#strengthTable').show();
				$('#profileInfo').show();
			}
			else
			{
				$('#editProfile').attr('disabled', true);
				$('#deleteProfile').attr('disabled', true);
			}
			that.loaded = true;
		}
	});
}

siggyCalc.prototype.initialize = function ()
{
	var select = $("#scanProfiles");
	var that = this;

	$('#editProfile').click(function ()
	{
		$('#createProfile').attr('disabled', true);
		$('#editProfile').attr('disabled', true);
		$('#deleteProfile').attr('disabled', true);
		$('#scanProfiles').attr('disabled', true);

		$('#inputProfileName').val(that.currentProfile.profileName);
		$('#selectCovertOps').val(that.currentProfile.covertOps);
		$('#selectRigs').val(that.currentProfile.rigs);
		$('#selectRangeFinding').val(that.currentProfile.rangeFinding);
		$('#selectProspector').val(that.currentProfile.prospector);

		$('input[name=sistersLauncher]').filter('[value=' + that.currentProfile.sistersLauncher + ']').attr('checked', true);
		$('input[name=sistersProbes]').filter('[value=' + that.currentProfile.sistersProbes + ']').attr('checked', true);
		$('input[name=preferred]').filter('[value=' + that.currentProfile.preferred + ']').attr('checked', true);

		that.mode = 'edit';

		$('#strengthTable').hide();
		$('#profileInfo').hide();
		$('#scanProfileOptions').show();
	});

	$('#createProfile').click(function ()
	{
		$('#createProfile').attr('disabled', true);
		$('#editProfile').attr('disabled', true);
		$('#deleteProfile').attr('disabled', true);
		$('#scanProfiles').attr('disabled', true);

		$('#inputProfileName').val('');
		$('#selectCovertOps').val(0);
		$('#selectRigs').val(0);
		$('#selectRangeFinding').val(0);
		$('#selectProspector').val(0);

		$('input[name=sistersLauncher]').filter('[value="0"]').attr('checked', true);
		$('input[name=sistersProbes]').filter('[value="0"]').attr('checked', true);
		$('input[name=preferred]').filter('[value="1"]').attr('checked', true);

		that.mode = 'create';

		$('#strengthTable').hide();
		$('#profileInfo').hide();
		$('#scanProfileOptions').show();


	});

	$('#deleteProfile').click(function ()
	{
		$.post(that.baseUrl + 'dodeleteScanProfile', {
			profileID: that.currentProfileID
		});

		$('#scanProfiles option[value=' + that.currentProfileID + ']').remove();
		delete that.profiles[that.currentProfileID];
		delete that.currentProfile;

		var first = 0;
		for (first in that.profiles) {};
		if (first != 0)
		{
			that.currentProfileID = first;
			that.currentProfile = that.profiles[that.currentProfileID];
			select.val(that.currentProfileID);
			that.fillProfileInfo(that.currentProfile);
			that.calcAndPopulate();
		}
		else
		{
			//no more profiles :'(
			$('#editProfile').attr('disabled', true);
			$('#deleteProfile').attr('disabled', true);
			$('#profileInfo').hide();
			$('#strengthTable').hide();

		}

	});

	$('#saveScanProfile').click(function ()
	{
		data = {};
		data.profileName = $('#inputProfileName').val();
		if (data.profileName == '')
		{
			return false;
		}
		data.covertOps = $('#selectCovertOps').val();
		data.rigs = $('#selectRigs').val();
		data.rangeFinding = $('#selectRangeFinding').val();
		data.prospector = $('#selectProspector').val();
		data.sistersLauncher = $('input[name=sistersLauncher]:checked').val();
		data.sistersProbes = $('input[name=sistersProbes]:checked').val();
		data.preferred = $('input[name=preferred]:checked').val();
		data.mode = that.mode;

		if (that.mode == 'edit')
		{
			data.profileID = that.currentProfileID;
		}

		$.post(that.baseUrl + 'doTweakScanProfile', data, function (profile)
		{

			profile.profileID = parseInt(profile.profileID);
			profile.covertOps = parseInt(profile.covertOps);
			profile.rangeFinding = parseInt(profile.rangeFinding);
			profile.rigs = parseInt(profile.rigs);
			profile.prospector = parseInt(profile.prospector);
			profile.sistersLauncher = parseInt(profile.sistersLauncher);
			profile.sistersProbes = parseInt(profile.sistersProbes);
			profile.preferred = parseInt(profile.preferred);

			that.profiles[profile.profileID] = profile;
			that.currentProfile = that.profiles[profile.profileID];
			that.currentProfileID = that.profiles[profile.profileID];

			if (that.mode == 'create')
			{
				select.append($("<option>").attr('value', profile.profileID).text(profile.profileName));
				select.val(profile.profileID);
			}

			if (profile.preferred)
			{
				for (var i in that.profiles)
				{
					if (i != profile.profileID)
					{
						that.profiles[i].preferred = 0;
					}
				}
			}

			that.fillProfileInfo(that.currentProfile);
			$('#profileInfo').show();
			$('#strengthTable').show();

			$('#scanProfileOptions').hide();

			that.calcAndPopulate();
			that.mode = '';

			$('#createProfile').attr('disabled', false);
			$('#editProfile').attr('disabled', false);
			$('#deleteProfile').attr('disabled', false);
			$('#scanProfiles').attr('disabled', false);
		}, 'json');

	});

	$('#cancelProfile').click(function ()
	{
		$('#createProfile').attr('disabled', false);
		if (Object.size(that.profiles) > 0)
		{
			$('#editProfile').attr('disabled', false);
			$('#deleteProfile').attr('disabled', false);
		}
		$('#scanProfiles').attr('disabled', false);
		that.mode = '';
		$('#scanProfileOptions').hide();

		if (that.currentProfileID != 0)
		{
			$('#profileInfo').show();
			$('#strengthTable').show();
			that.calcAndPopulate();
		}
	});

	select.change(function ()
	{
		var newID = $(this).val();
		if (newID != that.currentProfileID && newID != 0)
		{
			that.currentProfile = that.profiles[newID];
			that.currentProfileID = newID;
			that.fillProfileInfo(that.currentProfile);
			that.calcAndPopulate();
		}
	});

	$('#strengthCalcButton').click(function ()
	{
		if ($('#sigCalculator').is(":visible"))
		{
			$('#sigCalculator').hide();
			$(this).html('Strength Calc. &#x25BC;');
		}
		else
		{
			if (!that.loaded)
			{
				that.loadData();
			}
			$('#sigCalculator').show();
			$(this).html('Strength Calc. &#x25B2;');
		}
	}); /* make dialog draggable, assign handle to title */
}

siggyCalc.prototype.fillProfileInfo = function (profileData)
{
	$('#infoCovertOps').text(this.fancyLevelString(profileData.covertOps));
	$('#infoRangeFinding').text(this.fancyLevelString(profileData.rangeFinding));
	$('#infoRigs').text(this.fancyRigs(profileData.rigs));
	$('#infoSistersLauncher').text(this.intToYesNo(profileData.sistersLauncher));
	$('#infoSistersProbes').text(this.intToYesNo(profileData.sistersProbes));
	$('#infoProspector').text(this.fancyProspector(profileData.prospector));
}

siggyCalc.prototype.fancyLevelString = function (level)
{
	switch (level)
	{
	case 1:
		return 'Level I';
		break;
	case 2:
		return 'Level II';
		break;
	case 3:
		return 'Level III';
		break;
	case 4:
		return 'Level IV';
		break;
	case 5:
		return 'Level V';
	default:
		return '';
		break;
	}
}

siggyCalc.prototype.intToYesNo = function (val)
{
	return (val == 1 ? 'Yes' : 'No');
}

siggyCalc.prototype.fancyProspector = function (val)
{
	switch (val)
	{
	case 1:
		return 'PPH-0';
		break;
	case 2:
		return 'PPH-1';
		break;
	case 3:
		return 'PPH-2';
		break;
	default:
		return 'None';
		break;
	}
}

siggyCalc.prototype.fancyRigs = function (val)
{
	switch (val)
	{
	case 1:
		return 'T1 - 1 rig';
		break;
	case 2:
		return 'T1 - 2 rigs';
		break;
	case 3:
		return 'T2 - 1 rig';
		break;
	case 4:
		return 'T2 - 2 rig';
		break;
	case 5:
		return 'T1 & T2 - mixed';
		break;
	default:
		return 'None';
		break;
	}
}

siggyCalc.prototype.calcAndPopulate = function ()
{
	var covertOpsBonus = this.currentProfile.covertOps * 0.1;
	var rangeFindingBonus = this.currentProfile.rangeFinding * 0.1;

	var rigBonus = 0;
	switch (this.currentProfile.rigs)
	{
		//t1- 1 rig
	case 1:
		rigBonus = 0.1;
		break;
		//t1 - 2 rigs
	case 2:
		rigBonus = 0.2;
		break;
		//t2 - 1 rig
	case 3:
		rigBonus = 0.15;
		break;
		//t2 - 2 rigs
	case 4:
		rigBonus = 0.3;
		break;
	case 5:
		rigBonus = 0.25;
		break;
	default:
		rigBonus = 0;
		break;
	}

	var prospectorBonus = 0;
	switch (this.currentProfile.prospector)
	{
		//pph-0
	case 1:
		prospectorBonus = 0.02;
		break;
		//pph-1
	case 2:
		prospectorBonus = 0.06;
		break;
		//pph-2
	case 3:
		prospectorBonus = 0.1;
		break;
	default:
		prospectorBonus = 0;
		break;
	}
	
	/*

	var virtueBonus = ( this.currentProfile.virtueAlpha == 1 ? (this.currentProfile.virtueOmega == 1 ? 0.01*1.25 : 0.01) : 0 ) + ( this.currentProfile.virtueBeta == 1 ? (this.currentProfile.virtueOmega == 1 ? 0.02*1.25 : 0.01) : 0 ) + ( this.currentProfile.virtueDelta == 1 ? (this.currentProfile.virtueOmega == 1 ? 0.04*1.25 : 0.01) : 0 );
	virtueBonus += ( this.currentProfile.virtueEpsilon == 1 ? (this.currentProfile.virtueOmega == 1 ? 0.05*1.25 : 0.01) : 0 ) + ( this.currentProfile.virtueGamma == 1 ? (this.currentProfile.virtueOmega == 1 ? 0.03*1.25 : 0.01) : 0 )
	
	if( this.currentProfile.virtueAlpha && this.currentProfile.virtueBeta && this.currentProfile.virtueEpsilon && this.currentProfile.virtueGamma && this.currentProfile.virtueDelta && this.currentProfile.virtueOmega )
	{
			virtueBonus *= 1.10;
	}
	*/

	var sistersLauncherBonus = (this.currentProfile.sistersLauncher == 1 ? 0.05 : 0);


	var sistersProbesBonus = (this.currentProfile.sistersProbes == 1 ? 0.1 : 0);
	//var virtueBonus = 0;
	var probeMultiplier = (1 + covertOpsBonus) * (1 + rangeFindingBonus) * (1 + rigBonus) * (1 + prospectorBonus) * (1 + sistersLauncherBonus) * (1 + sistersProbesBonus);
	var deepProbeMultiplier = (1 + covertOpsBonus) * (1 + rangeFindingBonus) * (1 + rigBonus) * (1 + prospectorBonus) * (1 + sistersLauncherBonus);

	for (var i in this.baseSigSizes)
	{
		//changed 5 to 2.56 in base strength
		var baseStrength = this.baseSigSizes[i] * 2.57 / 12800;
		var idFriendly = String(this.baseSigSizes[i]).replace('.', '');
		//considering ceiling instead of the rounds.
		var coreStrength = roundNumber(baseStrength * probeMultiplier * 4 * 100, 2);
		$('#core-' + idFriendly).text(coreStrength + '%');
		var combatStrength = roundNumber(baseStrength * probeMultiplier * 2 * 100, 2);
		$('#combat-' + idFriendly).text(combatStrength + '%');
		var deepStrength = roundNumber(baseStrength * deepProbeMultiplier * 1 * 100, 2);
		$('#deep-' + idFriendly).text(deepStrength + '%');
	}

}