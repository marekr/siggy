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
}