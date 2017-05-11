import React from 'react'
import { connect } from 'react-redux'

import { Glyphicon } from 'react-bootstrap'

import { changeCurrentPage } from './../../actions/customer-actions'

@connect((store) => {
    return {
        pageRecordLimit: store.configState.config.customers.pageRecordLimit,
        count: store.customersState.customers.count,
        currentPage: store.customersState.customers.currentPage
    }
})
export default class CustomersBlock_Pagination extends React.Component {

    countPage = () => Math.ceil(this.props.count / this.props.pageRecordLimit) || 1;
    
    render() {
        return (
            <div className="assol-pagination assol-grey-panel">
                <div className="assol-pagination-in clear">

                    <div className="assol-pagination-left">
                        <input
                            type="number"
                            className="assol-input-style now-page-input"
                            value={this.props.currentPage}
                            onChange={this.onChange}
                        />
                        <span className="assol-pagination-all">из {this.countPage()}</span>
                    </div>
                    <div className="assol-pagination-right">
                        <div className="assol-pagination-arrs">
                            <button className="prev" onClick={this.onPrev}>
                                <Glyphicon glyph="chevron-left" />
                            </button>
                            <button className="next" onClick={this.onNext}>
                                <Glyphicon glyph="chevron-right" />
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        );
    }

    componentWillUpdate(nextProps) {
        // Обновление списка клиентов, если была изменена страница
        if (this.props.currentPage != nextProps.currentPage) {
            this.props.refresh();
        }
    }

    onNext = () => {
        const {dispatch, currentPage} = this.props;

        if (currentPage < this.countPage()) {
            dispatch(changeCurrentPage(currentPage + 1));
        }
    };

    onPrev = () => {
        const {dispatch, currentPage} = this.props;

        if (currentPage > 1) {
            dispatch(changeCurrentPage(currentPage - 1));
        }
    };

    onChange = (e) => {
        let value = parseInt(e.target.value) || 0;

        if ((value > 0) && (value <= this.countPage())) {
            this.props.dispatch(changeCurrentPage(value));
        }
    }
    
}