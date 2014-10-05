
//first key is wh class, second is just unique for mag in the class
var anomsLookup = {
	1: {
		0: "",
		1: "Perimeter Ambush Point",
		2: "Perimeter Camp",
		3: "Phase Catalyst Node",
		4: "The Line"
	},
	2: {
		0: "",
		1: "Perimeter Checkpoint",
		2: "Perimeter Hangar",
		3: "The Ruins of Enclave Cohort 27",
		4: "Sleeper Data Sanctuary"
	},
	3: {
		0: "",
		1: "Fortification Frontier Stronghold",
		2: "Outpost Frontier Stronghold",
		3: "Solar Cell",
		4: "The Oruze Construct"
	},
	4: {
		0: "",
		1: "Frontier Barracks",
		2: "Frontier Command Post",
		3: "Integrated Terminus",
		4: "Sleeper Information Sanctum"
	},
	5: {
		0: "",
		1: "Quarantine Area",
		2: "Core Garrison",
		3: "Core Stronghold",
		4: "Oruze Osobnyk"
	},
	6: {
		0: "",
		1: "Core Citadel",
		2: "Core Bastion",
		3: "Strange Energy Readings",
		4: "The Mirror"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};


var magsLookup = {
	1: {
		0: "",
		1: "Forgotten Perimeter Coronation Platform",
		2: "Forgotten Perimeter Power Array"
	},
	2: {
		0: "",
		1: "Forgotten Perimeter Gateway",
		2: "Forgotten Perimeter Habitation Coils"
	},
	3: {
		0: "",
		1: "Forgotten Frontier Quarantine Outpost",
		2: "Forgotten Frontier Recursive Depot"
	},
	4: {
		0: "",
		1: "Forgotten Frontier Conversion Module",
		2: "Forgotten Frontier Evacuation Center"
	},
	5: {
		0: "",
		1: "Forgotten Core Data Field",
		2: "Forgotten Core Information Pen"
	},
	6: {
		0: "",
		1: "Forgotten Core Assembly Hall",
		2: "Forgotten Circuitry Disassembler"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};

var radarsLookup = {
	1: {
		0: "",
		1: "Unsecured Perimeter Amplifier",
		2: "Unsecured Perimeter Information Center"
	},
	2: {
		0: "",
		1: "Unsecured Perimeter Comms Relay",
		2: "Unsecured Perimeter Transponder Farm"
	},
	3: {
		0: "",
		1: "Unsecured Frontier Database",
		2: "Unsecured Frontier Receiver"
	},
	4: {
		0: "",
		1: "Unsecured Frontier Digital Nexus",
		2: "Unsecured Frontier Trinary Hub"
	},
	5: {
		0: "",
		1: "Unsecured Frontier Enclave Relay",
		2: "Unsecured Frontier Server Bank"
	},
	6: {
		0: "",
		1: "Unsecured Core Backup Array",
		2: "Unsecured Core Emergence"
	},
	7: {
		0: ""
	},
	8: {
		0: ""
	},
	9: {
		0: ""
	}
};

var gravsLookup = {
	0: "",
	1: "Average Frontier Deposit",
	2: "Unexceptional Frontier Deposit",
	3: "Common Perimeter Deposit",
	4: "Exceptional Core Deposit",
	5: "Infrequent Core Deposit",
	6: "Unusual Core Deposit",
	7: "Rarified Core Deposit",
	8: "Ordinary Perimeter Deposit",
	9: "Uncommon Core Deposit",
	10: "Isolated Core Deposit"
};

var ladarsLookup = {
	0: "",
	1: "Barren Perimeter Reservoir",
	2: "Minor Perimeter Reservoir",
	3: "Ordinary Perimeter Reservoir",
	4: "Sizeable Perimeter Reservoir",
	5: "Token Perimeter Reservoir",
	6: "Bountiful Frontier Reservoir",
	7: "Vast Frontier Reservoir",
	8: "Instrumental Core Reservoir",
	9: "Vital Core Reservoir"
};

var whLookup = {
	1: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "H121 (to C1)",
		8: "C125  (to C2)",
		9: "O883 (to C3)",
		10: "M609 (to C4)",
		11: "L614 (to C5)",
		12: "S804 (to C6)",
		13: "Z060 (to Nullsec)",
		14: "J244 (to Lowsec)",
		15: "N110 (to Highsec)",
		/* frigate hole range 30 to 39*/
		31: "E004 (to C1, Frigate)",
		32: "L005 (to C2, Frigate)",
		33: "Z006 (to C3, Frigate)",
		34: "M001 (to C4, Frigate)",
		35: "C008 (to C5, Frigate)",
		36: "G008 (to C6, Frigate)",
		37: "Q003 (to Nullsec, Frigate)"
	},
	2: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z647 (to C1)",
		8: "D382 (to C2)",
		9: "O477 (to C3)",
		10: "Y683 (to C4)",
		11: "N062 (to C5)",
		12: "R474 (to C6)",
		13: "E545 (to Nullsec)",
		14: "A239 (to Lowsec)",
		15: "B274 (to Highsec)",
		/* frigate hole range 30 to 39*/
		31: "E004 (to C1, Frigate)",
		32: "L005 (to C2, Frigate)",
		33: "Z006 (to C3, Frigate)",
		34: "M001 (to C4, Frigate)",
		35: "C008 (to C5, Frigate)",
		36: "G008 (to C6, Frigate)",
		37: "Q003 (to Nullsec, Frigate)"
	},
	3: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "V301 (to C1)",
		8: "I182 (to C2)",
		9: "N968 (to C3)",
		10: "T405 (to C4)",
		11: "N770 (to C5)",
		12: "A982 (to C6)",
		13: "K346 (to Nullsec)",
		14: "U210 (to Lowsec)",
		15: "D845 (to Highsec)",
		/* frigate hole range 30 to 39*/
		31: "E004 (to C1, Frigate)",
		32: "L005 (to C2, Frigate)",
		33: "Z006 (to C3, Frigate)",
		34: "M001 (to C4, Frigate)",
		35: "C008 (to C5, Frigate)",
		36: "G008 (to C6, Frigate)",
		37: "Q003 (to Nullsec, Frigate)"
	},
	4: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "P060 (to C1)",
		8: "N766 (to C2)",
		9: "C247 (to C3)",
		10: "X877 (to C4)",
		11: "H900 (to C5)",
		12: "U574 (to C6)",
		13: "K329 (to Nullsec)",
		14: "N290 (to Lowsec)",
		15: "S047 (to Highsec)",
		/* frigate hole range 30 to 39*/
		31: "E004 (to C1, Frigate)",
		32: "L005 (to C2, Frigate)",
		33: "Z006 (to C3, Frigate)",
		34: "M001 (to C4, Frigate)",
		35: "C008 (to C5, Frigate)",
		36: "G008 (to C6, Frigate)",
		37: "Q003 (to Nullsec, Frigate)"
	},
	5: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Y790 (to C1)",
		8: "D364 (to C2)",
		9: "M267 (to C3)",
		10: "E175 (to C4)",
		11: "H296 (to C5)",
		12: "V753 (to C6)",
		13: "Z142 (to Nullsec)",
		14: "C140 (to Lowsec)",
		15: "D792 (to Highsec)",
		/* frigate hole range 30 to 39*/
		31: "E004 (to C1, Frigate)",
		32: "L005 (to C2, Frigate)",
		33: "Z006 (to C3, Frigate)",
		34: "M001 (to C4, Frigate)",
		35: "C008 (to C5, Frigate)",
		36: "G008 (to C6, Frigate)",
		37: "Q003 (to Nullsec, Frigate)"
	},
	6: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Q317  (to C1)",
		8: "G024 (to C2)",
		9: "L477 (to C3)",
		10: "Z457 (to C4)",
		11: "V911 (to C5)",
		12: "W237 (to C6)",
		13: "Z142 (to Nullsec)",
		47: "C248 (to Nullsec, 48hr)",
		14: "C140 (to Lowsec)",
		48: "C391 (to Lowsec, 48hr)",
		15: "D792  (to Highsec)",
		/* frigate hole range 31 to 39*/
		31: "E004 (to C1, Frigate)",
		32: "L005 (to C2, Frigate)",
		33: "Z006 (to C3, Frigate)",
		34: "M001 (to C4, Frigate)",
		35: "C008 (to C5, Frigate)",
		36: "G008 (to C6, Frigate)",
		37: "Q003 (to Nullsec, Frigate)"
	},
	7: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z971 (to C1)",
		8: "R943 (to C2)",
		9: "X702 (to C3)",
		10: "O128 (to C4)",
		11: "M555 (to C5)",
		12: "B041 (to C6)",
		13: "V283 (to Nullsec)",
		14: "R051 (to Lowsec)",
		15: "A641 (to Highsec)"
	},
	8: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z971 (to C1)",
		8: "R943  (to C2)",
		9: "X702 (to C3)",
		10: "O128 (to C4)",
		11: "N432 (to C5)",
		12: "B041 (to C6)",
		46: "U319 (to C6)",	/* mass regen type */
		13: "S199  (to Nullsec)",
		14: "N944  (to Lowsec)",
		15: "B449  (to Highsec)"
	},
	9: {
		0: "Unstable Wormhole",
		1: "K162 (from Unknown)",
		2: "K162 (from Dangerous unknown)",
		3: "K162 (from Deadly unknown)",
		4: "K162 (from Nullsec)",
		5: "K162 (from Lowsec)",
		6: "K162 (from Highsec)",
		7: "Z971 (to C1)",
		8: "R943 (to C2)",
		9: "X702 (to C3)",
		10: "O128  (to C4)",
		11: "N432 (to C5)",
		12: "B041 (to C6)",
		13: "S199 (to Nullsec)",
		14: "N944 (to Lowsec)",
		15: "B449 (to Highsec)"
	}
};

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