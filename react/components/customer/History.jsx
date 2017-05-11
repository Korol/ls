import React from "react"
import { connect } from 'react-redux';
import { BootstrapTable, TableHeaderColumn } from 'react-bootstrap-table'

import { fetchHistory } from '../../actions/customer-actions'

@connect((store) => {
    return {
        history: store.customersState.history,
        userNameIsFirst: store.configState.config.userNameIsFirst
    }
})
export default class History extends React.Component {

    // TODO: Сделать постраничную навигацию сервером
    // https://github.com/AllenFang/react-bootstrap-table/blob/master/examples/js/pagination/pagination-hook-table.js
    // https://github.com/AllenFang/react-bootstrap-table/blob/master/examples/js/pagination/custom-pagination-table.js

    componentDidMount() {
        const {dispatch} = this.props;

        dispatch(fetchHistory(CustomerID)); // TODO: CustomerID берется из profile.php - переделать!!!

        $('a[href="#AdditionallyPane"]').on('shown.bs.tab', function () {
            dispatch(fetchHistory(CustomerID)); // TODO: CustomerID берется из profile.php - переделать!!!
        });
    }

    render() {
        return (
            <BootstrapTable
                tableBodyClass="reactTableBody"
                data={this.props.history}
                pagination={true}
                striped={true}
                hover={true}
                options={{noDataText: "Нет данных для отображения"}}
            >
                <TableHeaderColumn dataField='id' isKey={true} className="colBtn" columnClassName="colBtn" hidden={true} >#</TableHeaderColumn>
                <TableHeaderColumn dataField='date'>Время изменения</TableHeaderColumn>
                <TableHeaderColumn dataField='author' dataFormat={this.authorFormatter} >Автор изменения</TableHeaderColumn>
                <TableHeaderColumn dataField='description'>Измененное значение</TableHeaderColumn>
            </BootstrapTable>
        );
    }

    authorFormatter = (cell, row) => {
        return this.props.userNameIsFirst
            ? (row.FName + ' ' + row.SName)
            : (row.SName + ' ' + row.FName);
    };

}