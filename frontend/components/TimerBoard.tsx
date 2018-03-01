import * as React from "react";
import { StaticData } from '../StaticData';
import { Timer as TimerModel } from '../Models';
import { Timer } from "../components/Timer";
import SortableTable from "../components/SortableTable";


export interface TimerBoardProps { 
	timers: Array<TimerModel>
}

interface TimerBoardState {
	timers: Array<TimerModel>,
	data: any
}

class TimerBoardTableRow extends React.Component<{ timer: TimerModel, onDelete: Function }, any> {
	constructor(props: any) {
		super(props);
	}

	public handleRemove() {
		this.props.onDelete(this.props.timer.id);
		return false;
	}

	public render() {
		let timer = this.props.timer;
		let system = StaticData.getSystemByID(timer.system_id);
		return (
			<tr>
				<td>Citadel</td>
				<td className='text-center'>
					<div className="dropdown">
						<button className="btn btn-default btn-hidden dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
							{ system.name }
						</button>
						<ul className="dropdown-menu" role="menu">
							<li>
								<a className='eve-set-destination' data-system-id='{{ id }}'>Set Destination</a>
							</li>
							<li><a target="_blank" href={'http://evemaps.dotlan.net/system/' + system.name}>DOTLAN</a></li>
						</ul>
					</div>
				</td>
				<td>
					<div className="dropdown">
						<button className="btn btn-default btn-hidden dropdown-toggle" type="button" id="region-drop" data-toggle="dropdown" aria-expanded="true">
							{ system.region_name }
						</button>
						<ul className="dropdown-menu" role="menu">
							<li><a target="_blank" href={'http://evemaps.dotlan.net/map/'+system.region_name}>DOTLAN</a></li>
						</ul>
					</div>
				</td>
				<td>02/17/2018 10:55 AM</td>
				<td><Timer endDate={'02/17/2018 10:55 AM'} /></td>
				<td className='text-center'>
					<button className='btn btn-danger' onClick={this.handleRemove.bind(this)}>Delete</button>
				</td>
			</tr>
		);
	}
}

export class TimerBoard extends React.Component<TimerBoardProps, TimerBoardState> {
	constructor(props: any) {
		super(props);
		this.state = {
			timers: props.timers,
			data: props.timers
		};
	}

	public removeFromTimers(id: number): void {
		var timers = [...this.state.timers];

		for (var i = 0; i < timers.length; i++) {
			if (timers[i].id == id) {
				timers.splice(i, 1);
				this.setState({ timers });
				break;
			}
		}
	}
	
	public getTimerById(id: number): TimerModel {
		for (var i = 0; i < this.state.timers.length; i++) {
			if (this.state.timers[i].id == id) {
				return this.state.timers[i];
			}
		}

		return null;
	}

	public handleRemove(id: number): boolean {
		var timer = this.getTimerById(id);
		this.removeFromTimers(id);
		return false;
	}

	render() {
		
		const columns = [
			{
				header: 'Type',
				key: 'type',
			},
			{
				header: 'System',
				key: 'name',
				headerStyle: { 
					fontSize: '15px' 
				},
				headerProps: { 
					className: 'align-left' 
				}
			}
		];


		const iconStyle = {
			color: '#aaa',
			paddingLeft: '5px',
			paddingRight: '5px'
		};

		return (
			<div>
				<SortableTable data={this.state.data} columns={columns} iconStyle={iconStyle} />
				<table className='siggy-table siggy-table-striped table-with-dropdowns'>
					<thead>
						<tr>
							<th>
								Type
							</th>
							<th>
								System
							</th>
							<th>
								Region
							</th>
							<th>
								Time
							</th>
							<th>
								Remaining
							</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						{this.state.timers.map((timer,i) =>
								<TimerBoardTableRow timer={timer} key={timer.id} onDelete={this.handleRemove.bind(this)} />
							)}
					</tbody>
				</table>
			</div>
		);
    }
}