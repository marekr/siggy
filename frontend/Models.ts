export interface Sig {
	id: number,
	sig: string,
	type: string,
	description: string,
	siteID: number,
	systemID: number,
	chainmap_wormhole: number,
	sigSize: number,
	created_at: string,
	
	editing: boolean,
	exists: boolean,
	showSigSizeCol: boolean,
	sysClass: number,
	showWormhole: boolean,
	chainmap_wormholes: any
};

export interface SigArray {
	[key: string]: Sig
}

export interface System {
	id: number;
	name: string;
	region_name: string;
	class: number;
	effect_id: number;
}

export interface DScanRecord {
	id: number;
	type_id: number;
	record_name: string;
	item_distance: string;
	type_name: string;
}

export interface DScanRecordGroup {
	id: number;
	name: string;
	is_structure: boolean;
	is_ship: boolean;
	records: Array<DScanRecord>
}

type DScanRecordGroupsType = {
	[key:number]: DScanRecordGroup;
	
}

export interface DScan {
	title: string;
	groups: DScanRecordGroupsType;
}

export interface SystemEffect {
	id: number;
	effectTitle: string;
}

export interface SystemEffectArray {
	[index: number]: SystemEffect;
}

export interface Site {
	id: number;
	name: string;
	description: string;
	type: string;
}

export interface Ship {
	id: number;
	name: string;
	class: string;
	mass: number;
}

export interface ShipArray {
	[index: number]: Ship;
}