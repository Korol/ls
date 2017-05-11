import React from 'react'
import { connect } from 'react-redux'

import Header from './CustomersBlock_Header'
import List from './CustomersBlock_List'
import Pagination from './CustomersBlock_Pagination'
import LoadingPanel from '../LoadingPanel'

import { fetchCustomers } from './../../actions/customer-actions'

@connect((store) => {
    return {
        config: store.configState.config,
        currentPage: store.customersState.customers.currentPage,
        filter: store.customersState.customers.filter
    }
})
export default class CustomersBlock extends React.Component {

    render() {
        return this.props.config ? (
            <div>
                <Header refresh={this.onRefresh} />
                <List refresh={this.onRefresh} />
                <Pagination refresh={this.onRefresh} />
            </div>
        ) : ( <LoadingPanel /> );
    }
    
    onRefresh = () => {
        const { dispatch, filter, currentPage, config } = this.props;
        const { pageRecordLimit } = config.customers;
        
        let offset = (currentPage - 1) * pageRecordLimit;

        dispatch(fetchCustomers(offset, pageRecordLimit, filter));
    } 

}