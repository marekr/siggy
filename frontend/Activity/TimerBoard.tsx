/**
 * @license Proprietary
 * @copyright Copyright (c) 2014 borkedLabs - All Rights Reserved
 */

import $ from 'jquery';
import * as Handlebars from '../vendor/handlebars';
import Activity from './Activity';
import * as React from "react";
import * as ReactDOM from "react-dom";
import { Siggy as SiggyCore } from '../Siggy';
import { TimerBoard as TimerBoardComponent } from "../components/TimerBoard";
import { Timer } from '../Models';

export class TimerBoard extends Activity {

	public key:string = 'timer-board';
	public title:string = 'Timer Board';
	
	private _updateTimeout = null;
	private updateRate = 60000;

	private templateRow = null;
	private table = null;

	constructor(core: SiggyCore)
	{
		super(core);
		var $this = this;
	}

	public start(args): void
	{
		$('#activity-' + this.key).show();
		this.update();
	}

	public load(args): void
	{
	}
	
	public stop(): void
	{
		ReactDOM.unmountComponentAtNode(document.getElementById('activity-timer-board'));

		clearTimeout(this._updateTimeout);
		$('#activity-' + this.key).hide();
	}

	public update()
	{
		var $this = this;
		

		let timers: Array<Timer> = [
			{
				id: 1,
				system_id: 30000001,
				expires_at: 'test',
				type:'citadel'
			}
		];
		ReactDOM.render(
			<TimerBoardComponent timers={timers} />,
			document.getElementById("activity-timer-board")
		);
	}

	public updateTable( systems )
	{
	}
}