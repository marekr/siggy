export default abstract class Activity {

	public abstract key: string;
	protected core;

	constructor(core) {
		this.core = core;
	}

	
	public abstract start(args): void;
	public abstract stop(args): void;
}