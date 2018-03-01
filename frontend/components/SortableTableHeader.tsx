
import * as React from "react";
import * as FA from 'react-fontawesome';

enum SortDirection {
    Descending = "desc",
    Ascending = "asc",
    Both = "both"
}

export interface SortableTableHeaderItemProps {
	index: number,
    headerProps: {},
    sortable: boolean,
    sorting: SortDirection,
    iconStyle: {}
    iconDesc: JSX.Element,
    iconAsc: JSX.Element,
	iconBoth: JSX.Element,
	onClick: Function,
	style?: React.CSSProperties,
	header?: any
}


class SortableTableHeaderItem extends React.Component<SortableTableHeaderItemProps, any> {

  static defaultProps = {
    headerProps: {},
    sortable: true
  }

  onClick(e) {
    if (this.props.sortable)
      this.props.onClick(this.props.index);
  }

  render() {
    let sortIcon;
    if (this.props.sortable) {
      if (this.props.iconBoth) {
        sortIcon = this.props.iconBoth;
      } else {
        sortIcon = <FA name="sort" style={this.props.iconStyle} />;
      }
      if (this.props.sorting == "desc") {
        if (this.props.iconDesc) {
          sortIcon = this.props.iconDesc;
        } else {
          sortIcon = <FA name="sort-desc" style={this.props.iconStyle} />;
        }
      } else if (this.props.sorting == "asc") {
        if (this.props.iconAsc) {
          sortIcon = this.props.iconAsc;
        } else {
          sortIcon = <FA  name="sort-asc" style={this.props.iconStyle} />;
        }
      }
    }

    return (
      <th style={this.props.style} onClick={this.onClick.bind(this)} {...this.props.headerProps} >
        {this.props.header}
        {sortIcon}
      </th>
    );
  }
}

export interface SortableTableHeaderProps {    
	columns: Array<any>,
    sortings:Array<any>,
    onStateChange: Function
    iconStyle: object,
    iconDesc: JSX.Element,
    iconAsc: JSX.Element,
    iconBoth: JSX.Element
}

export default class SortableTableHeader extends React.Component<SortableTableHeaderProps, any> {
  static propTypes = {

  }

  onClick(index) {
    this.props.onStateChange.bind(this)(index);
  }

  render() {
    const headers = this.props.columns.map(((column, index) => {
      const sorting = this.props.sortings[index];
      return (
        <SortableTableHeaderItem
          sortable={column.sortable}
          key={index}
          index={index}
          header={column.header}
          sorting={sorting}
          onClick={this.onClick.bind(this)}
          style={column.headerStyle}
          headerProps={column.headerProps}
          iconStyle={this.props.iconStyle}
          iconDesc={this.props.iconDesc}
          iconAsc={this.props.iconAsc}
          iconBoth={this.props.iconBoth} />
      );
    }).bind(this));

    return (
      <thead>
        <tr>
          {headers}
        </tr>
      </thead>
    );
  }
}