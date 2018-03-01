import * as React from "react";

class SortableTableRow extends React.Component<any, any> {
	render() {
		var tds = this.props.columns.map(function (item, index) {
			var value = this.props.data[item.key];
			if ( item.render ) {
				value = item.render(value)
			}
			return (
				<td key={index} style={item.dataStyle} {...(item.dataProps || {})} >
					{value}
				</td>
			);
		}.bind(this));

		return (
		<tr>
			{tds}
		</tr>
		);
	}
}

export interface SortableTableBodyProps {
	data: Array<any>,
	columns: Array<any>,
	sortings: Array<any>,
	rowRender?: JSX.Element
}


function RemoveComponent(props) {
	if (props.removeComponent) {
	  const Component = props.removeComponent;
	  return <Component {...props} />;
	}
  
	return (
	  <a onClick={props.onClick} className={props.className}>
		{String.fromCharCode(215)}
	  </a>
	);
  }

export default class SortableTableBody extends React.Component<SortableTableBodyProps, any> {
	render() {
		let row: any;

		if(this.props.rowRender != null)
		{
			const Component = this.props.rowRender;
			row =  <RemoveComponent  />;
		}
		else
		{
			row = this.props.data.map(((item, index) => {
				return (
					<SortableTableRow
					key={index}
					data={item}
					columns={this.props.columns} />
				);
			}).bind(this));
		}


		return (
			<tbody>
				{row}
			</tbody>
		);
	}
}