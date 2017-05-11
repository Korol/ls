import React from "react";
import { connect } from 'react-redux';

import { changeFilterText } from '../../actions/chat-actions';

/** Форма поиска пользователей */
@connect((store) => {
    return {
        filterText: store.chatState.filterText
    }
})
export default class SearchBlock extends React.Component {

    handleChange() {
        this.props.dispatch(changeFilterText(this.refs.filterTextInput.value));
    }

    render() {
        return (
            <div className="sidebar-search">
                <div className="search-block">
                    <input
                        id="search-field"
                        className="search-field"
                        type="search"
                        placeholder="поиск"
                        value={this.props.filterText}
                        ref="filterTextInput"
                        onChange={this.handleChange.bind(this)}
                    />
                    <button type="button" className="search-btn">
                        <span className="glyphicon glyphicon-search"></span>
                    </button>
                </div>
            </div>
        );
    }

}