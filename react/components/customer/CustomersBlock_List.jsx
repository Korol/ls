import React from 'react'
import { connect } from 'react-redux'

import CustomerItem from './CustomersBlock_CustomerItem'
import LoadingPanel from '../LoadingPanel'

@connect((store) => {
    return {
        isFetch: store.customersState.customers.isFetch,
        data: store.customersState.customers.data
    }
})
export default class CustomersBlock_List extends React.Component {

    componentDidMount() {
        this.props.refresh();
    }

    render() {
        const { isFetch, data } = this.props;

        return isFetch ? <LoadingPanel /> : (
            <div className="clients-wrap">
                {data.map((item) => (<CustomerItem key={item.ID} customer={item} refresh={this.props.refresh} />))}
            </div>
        );
    }
    
}