import React from "react"
import { connect } from 'react-redux'

import SearchBlock from './UsersBlock_Search'
import ControlPanel from './UsersBlock_ControlPanel'

@connect((store) => {
    return {
        isChatRooms: store.configState.config.isChatRooms
    }
})
export default class ToolbarBlock extends React.Component {

    render() {
        return (
            <div>
                {this.renderControlPanel()}
                <SearchBlock />
            </div>
        );
    }

    renderControlPanel = () => {
        return this.props.isChatRooms ? <ControlPanel /> : null
    }

}