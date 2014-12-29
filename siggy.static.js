/*
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */
 
var blackHoleEffects = {
	1: [
		['Missile Velocity', '+15%'],
		['Missile Explosion Velocity', '+30%'],
		['Ship Velocity', '+30%'],
		['Stasis Webifier Strength', '-15%'],
		['Inertia', '+15%'],
		['Lock range', '+30%']
	],
	2: [
		['Missile Velocity', '+22%'],
		['Missile Explosion Velocity', '+44%'],
		['Ship Velocity', '+44%'],
		['Stasis Webifier Strength', '-22%'],
		['Inertia', '+22%'],
		['Lock range', '+44%']
	],
	3: [
		['Missile Velocity', '+29%'],
		['Missile Explosion Velocity', '+58%'],
		['Ship Velocity', '+58%'],
		['Stasis Webifier Strength', '-29%'],
		['Inertia', '+29%'],
		['Lock range', '+58%']
	],
	4: [
		['Missile Velocity', '+36%'],
		['Missile Explosion Velocity', '+72%'],
		['Ship Velocity', '+72%'],
		['Stasis Webifier Strength', '-36%'],
		['Inertia', '+36%'],
		['Lock range', '+72%']
	], 
	5: [
		['Missile Velocity', '+43%'],
		['Missile Explosion Velocity', '+86%'],
		['Ship Velocity', '+86%'],
		['Stasis Webifier Strength', '-43%'],
		['Inertia', '+43%'],
		['Lock range', '+86%']
	],
	6: [
		['Missile Velocity', '+50%'],
		['Missile Explosion Velocity', '+100%'],
		['Ship Velocity', '+100%'],
		['Stasis Webifier Strength', '-50%'],
		['Inertia', '+50%'],
		['Lock range', '+100%']
	]
};

var wolfRayetEffects = {
	1: [
		['Armor HP', '+30%'],
		['Shield Resists', '-15%'],
		['Small Weapon Damage', '+60%'],
		['Signature Size', '-15%']
	],
	2: [
		['Armor HP', '+44%'],
		['Shield Resists', '-22%'],
		['Small Weapon Damage', '+88%'],
		['Signature Size', '-22%']
	],
	3: [
		['Armor HP', '+58%'],
		['Shield Resists', '-29%'],
		['Small Weapon Damage', '+116%'],
		['Signature Size', '-29%']
	],
	4: [
		['Armor HP', '+72%'],
		['Shield Resists', '-36%'],
		['Small Weapon Damage', '+144%'],
		['Signature Size', '-36%']
	], 
	5: [
		['Armor HP', '+86%'],
		['Shield Resists', '-43%'],
		['Small Weapon Damage', '+172%'],
		['Signature Size', '-43%']
	],
	6: [
		['Armor HP', '+100%'],
		['Shield Resists', '-50%'],
		['Small Weapon Damage', '+200%'],
		['Signature Size', '-50%']
	]
};

var redGiantEffects = {
	1: [
		['Heat Damage', '+15%'],
		['Overheat Bonus', '+30%'],
		['Smart Bomb Range', '+30%'],
		['Smart Bomb Damage', '+30%'],
		['Bomb Damage', '+30%']
	],
	2: [
		['Heat Damage', '+22%'],
		['Overheat Bonus', '+44%'],
		['Smart Bomb Range', '+44%'],
		['Smart Bomb Damage', '+44%'],
		['Bomb Damage', '+44%']
	],
	3: [
		['Heat Damage', '+29%'],
		['Overheat Bonus', '+58%'],
		['Smart Bomb Range', '+58%'],
		['Smart Bomb Damage', '+58%'],
		['Bomb Damage', '+58%']
	],
	4: [
		['Heat Damage', '+36%'],
		['Overload Bonus', '+72%'],
		['Smart Bomb Range', '+72%'],
		['Smart Bomb Damage', '+72%'],
		['Bomb Damage', '+72%']
	], 
	5: [
		['Heat Damage', '+43%'],
		['Overheat Bonus', '+86%'],
		['Smart Bomb Range', '+86%'],
		['Smart Bomb Damage', '+86%'],
		['Bomb Damage', '+86%']
	],
	6: [
		['Heat Damage', '+50%'],
		['Overheat Bonus', '+100%'],
		['Smart Bomb Range', '+100%'],
		['Smart Bomb Damage', '+100%'],
		['Bomb Damage', '+100%']
	]
};

var catacylsmicEffects = {
	1: [
		['Armor Repair', '-15%'],
		['Shield Boost', '-15%'],
		['Shield Transfer', '+30%'],
		['Remote Repair', '+30%'],
		['Capacitor capacity', '+30%'],
		['Capacitor recharge time', '+30%'],
		['Capacitor transfer amount', '-15%']
	],
	2: [
		['Armor Repair', '-22%'],
		['Shield Boost', '-22%'],
		['Shield Transfer', '+44%'],
		['Remote Repair', '+44%'],
		['Capacitor capacity', '+44%'],
		['Capacitor recharge time', '+44%'],
		['Capacitor transfer amount', '-22%']
	],
	3: [
		['Armor Repair', '-29%'],
		['Shield Boost', '-29%'],
		['Shield Transfer', '+55%'],
		['Remote Repair', '+55%'],
		['Capacitor capacity', '+58%'],
		['Capacitor recharge time', '+55%'],
		['Capacitor transfer amount', '-29%']
	],
	4: [
		['Armor Repair', '-36%'],
		['Shield Boost', '-36%'],
		['Shield Transfer', '+72%'],
		['Remote Repair', '+72%'],
		['Capacitor capacity', '+72%'],
		['Capacitor recharge time', '+72%'],
		['Capacitor transfer amount', '-36%']
	], 
	5: [
		['Armor Repair', '-43%'],
		['Shield Boost', '-43%'],
		['Shield Transfer', '+86%'],
		['Remote Repair', '+86%'],
		['Capacitor capacity', '+86%'],
		['Capacitor recharge time', '+86%'],
		['Capacitor transfer amount', '-43%']
	],
	6: [
		['Armor Repair', '-50%'],
		['Shield Boost', '-50%'],
		['Shield Transfer', '+100%'],
		['Remote Repair', '+100%'],
		['Capacitor capacity', '+100%'],
		['Capacitor recharge time', '+100%'],
		['Capacitor transfer amount', '-50%']
	]
};

var magnetarEffects = {
	1: [
		['Damage (weapons and drones)', '+30%'],
		['Drone Tracking', '-15%'],
		['Missile Explosion Radius', '+15%'],
		['Drone Velocity', '-15%'],
		['Targeting Range', '-15%'],
		['Tracking Speed (guns and drones)', '-15%']
	], 
	2: [
		['Damage (weapons and drones)', '+44%'],
		['Drone Tracking', '-22%'],
		['Missile Explosion Radius', '+22%'],
		['Drone Velocity', '-22%'],
		['Targeting Range', '-22%'],
		['Tracking Speed (guns and drones)', '-22%']
	], 
	3: [
		['Damage (weapons and drones)', '+55%'],
		['Drone Tracking', '-29%'],
		['Missile Explosion Radius', '+29%'],
		['Drone Velocity', '-29%'],
		['Targeting Range', '-29%'],
		['Tracking Speed (guns and drones)', '-29%']
	],
	4: [
		['Damage (weapons and drones)', '+72%'],
		['Drone Tracking', '-36%'],
		['Missile Explosion Radius', '+36%'],
		['Drone Velocity', '-36%'],
		['Targeting Range', '-36%'],
		['Tracking Speed (guns and drones)', '-36%']
	], 
	5: [
		['Damage (weapons and drones)', '+86%'],
		['Drone Tracking', '-43%'],
		['Missile Explosion Radius', '+43%'],
		['Drone Velocity', '-43%'],
		['Targeting Range', '-43%'],
		['Tracking Speed (guns and drones)', '-43%']
	], 
	6 :[
		['Damage (weapons and drones)', '+100%'],
		['Drone Tracking', '-50%'],
		['Missile Explosion Radius', '+50%'],
		['Drone Velocity', '-50%'],
		['Targeting Range', '-50%'],
		['Tracking Speed (guns and drones)', '-50%']
	]
};

var pulsarEffects = {
	1: [
		['Shield HP', '+30%'],
		['Armor Resists', '-15%'],
		['Capacitor recharge time', '-15%'],
		['Signature Size', '+30%'],
		['Energy neut/nos bonus', '+30%']
	], 
	2: [
		['Shield HP', '+44%'],
		['Armor Resists', '-22%'],
		['Capacitor recharge time', '-22%'],
		['Signature Size', '+44%'],
		['Energy neut/nos bonus', '+44%']
	], 
	3: [
		['Shield HP', '+58%'],
		['Armor Resists', '-29%'],
		['Capacitor recharge time', '-29%'],
		['Signature Size', '+58%'],
		['Energy neut/nos bonus', '+58%']
	], 
	4: [
		['Shield HP', '+72%'],
		['Armor Resists', '-36%'],
		['Capacitor recharge time', '-36%'],
		['Signature Size', '+72%'],
		['Energy neut/nos bonus', '+72%']
	], 
	5: [
		['Shield HP', '+86%'],
		['Armor Resists', '-43%'],
		['Capacitor recharge time', '-43%'],
		['Signature Size', '+86%'],
		['Energy neut/nos bonus', '+86%']
	], 
	6: [
		['Shield HP', '+100%'],
		['Armor Resists', '-50%'],
		['Capacitor recharge time', '-50%'],
		['Signature Size', '+100%'],
		['Energy neut/nos bonus', '-100%']
	]
}

siggy2.StaticData = {
	wormholeClassMap: [],
	wormholeTypes: [],
	sites: [],
	maps: {},
	baseListWormholes: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)"
	},
	templateWormholeInfoTooltip: function(dummy)
	{
	},
	templateSiteTooltip: function(dummy)
	{
	}

};

siggy2.StaticData.load = function(baseURL)
{
	var $this = this;
	
	$this.templateWormholeInfoTooltip = Handlebars.compile( $("#template-statics-tooltip").html() );
	$this.templateSiteTooltip = Handlebars.compile( $("#template-site-tooltip").html() );
	
	jQuery.ajax({
		 url: baseURL + 'data/sig_types',
		 success: function(result) {
					$this.wormholeClassMap = result.wormholes;
					$this.wormholeTypes = result.wormhole_types;
					$this.sites = result.sites;
					$this.maps = result.maps;
				  },
		 async: false,
		 dataType: 'json'
	});
}

siggy2.StaticData.getSiteNameByID = function( id )
{
	var site = this.getSiteByID(id);
	if(  site != null )
		return site.name;
	else
		return "";
}

siggy2.StaticData.getSiteByID = function( id )
{
	if( typeof( this.sites[ id ] ) != 'undefined' )
		return this.sites[ id ];
	else
		return null;
}

siggy2.StaticData.getSiteList = function( type, sysClass )
{	
	var result  = {0: ''};
	
	var map = this.maps[type];
	if( typeof(map) == 'undefined' )
	{
		return result;
	}
	
	map = map[sysClass];
	
	if( typeof(map) != 'undefined' )
	{
		for( var i in map )
		{
			var info = this.getSiteByID(map[i]);
			result[info.id] = info.name;
		}
	}
	
	return result;
}


siggy2.StaticData.getWormholeByID = function( id )
{
	if( typeof( this.wormholeTypes[ id ] ) != 'undefined' )
		return this.wormholeTypes[ id ];
	else
		return null;
}

siggy2.StaticData.getWormholesForClass = function( sysClass )
{
	return this.wormholeClassMap[ sysClass ];
}

siggy2.StaticData.systemClassToString = function ( sysClass )
{
	if( sysClass == 7 )
		return "Highsec";
	else if ( sysClass == 8 )
		return "Lowsec"
	else if( sysClass == 9 )
		return "Nullsec";
	else if( sysClass == 12 )
		return "Thera";
	else
		return sysClass;
}

siggy2.StaticData.getWormholeFancyName = function( whInfo )
{
	return whInfo.name + " (to " + this.systemClassToString(whInfo.dest_class) + ")";
}

siggy2.StaticData.getWormholeFancyNameByID = function( id )
{
	if( id <= 6 && id >= 0 )
	{
		return this.baseListWormholes[ id ];
	}
	else
	{
		var wh = this.getWormholeByID(id);
		
		if( wh != null )
		{
			return this.getWormholeFancyName( wh );
		}
		else
		{
			return "";
		}
	}
}


siggy2.StaticData.getWormholesForList = function( sysClass )
{
	var result  = jQuery.extend({}, this.baseListWormholes);
	
	var map = this.wormholeClassMap[ sysClass ];
	
	if( typeof(map) != 'undefined' )
	{
		for( var i in map )
		{
			var whInfo = this.getWormholeByID(map[i].static_id);
			result[map[i].static_id] = this.getWormholeFancyName(whInfo);
		}
	}
	
	return result;
}