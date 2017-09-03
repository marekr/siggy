/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

export default abstract class Activity {

	public abstract key: string;
	protected core;

	constructor(core) {
		this.core = core;
	}

	
	public abstract start(args): void;
	public abstract stop(args): void;
}