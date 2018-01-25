/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import { Siggy as SiggyCore } from '../Siggy';

export default abstract class Activity {

	public abstract key: string;
	public abstract title: string;
	protected core: SiggyCore;

	constructor(core: SiggyCore) {
		this.core = core;
	}

	
	public abstract start(args): void;
	public abstract stop(args): void;
	
	public abstract load(args): void;
}