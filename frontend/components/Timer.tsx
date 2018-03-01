import * as React from "react";
import { StaticData } from '../StaticData';
import * as moment from 'moment';

export interface TimerProps { 
	endDate?: string,
	beginDate?: string
}

interface TimerState {
	beginDate: moment.Moment,
	endDate: moment.Moment,
	remaining: string
}

export class Timer extends React.Component<TimerProps, TimerState> {
	private interval: any;

	constructor(props: any) {
		let beginDate = moment.utc(props.beginDate);
		let endDate = null;
		
		if( props.endDate != null )
		{
			endDate = moment.utc(props.endDate);
		}

		super(props);
        this.state = {
			endDate: endDate,
			beginDate: beginDate,
			remaining: '--'
        };
    }

	componentDidMount() {
		this.tick()
		this.interval = setInterval(this.tick.bind(this), 1000)
	}
	
	componentWillUnmount() {
		clearInterval(this.interval)
	}

	tick() {
		let remaining = '';
		var currDate;
		var prevDate;
		if( this.state.endDate != null )
		{
			currDate = this.state.endDate;
			prevDate = moment.utc();
		}
		else
		{
			currDate = moment.utc();
			prevDate = this.state.beginDate;
		}

		var t = currDate.diff(prevDate);
		
		var days = moment.duration(t).days();
		var hours = moment.duration(t).hours().lpadZero(2);
		
		if( days == 0 )
		{
			var minutes = moment.duration(t).minutes().lpadZero(2);
			var seconds = moment.duration(t).seconds().lpadZero(2);
			remaining = hours + ":" + minutes + ":" + seconds;
		}
		else
		{
			remaining = days + "d " + hours + "h";
			window.clearInterval(this.interval)
		}
	
		this.setState({
			remaining: remaining
		})
	  }

    render() {
        return (
			<span className="timer">{this.state.remaining}</span>
		);
    }
}