import React from "react";
import { connect } from 'react-redux';
import UsersBlockList from './UsersBlock_List';
import ToolbarBlock from './UsersBlock_Toolbar';
import LoadingPanel from '../LoadingPanel';

import { fetchChats, fetchUnread, fetchOnline } from '../../actions/chat-actions';

/** Блок списка пользователей */

@connect((store) => {
    return {
        userNameIsFirst: store.configState.config.userNameIsFirst,
        unreadInterval: store.configState.config.unreadInterval,
        timeoutCheckOnline: store.configState.config.timeoutCheckOnline,
        isFetchChats: store.chatState.isFetchChats,
        isChatRooms: store.configState.config.isChatRooms
    }
})
export default class UsersBlock extends React.Component {

    componentDidMount() {
        const { dispatch, userNameIsFirst, unreadInterval, timeoutCheckOnline } = this.props;
        
        dispatch(fetchChats(userNameIsFirst));
        this.timerUnread = setInterval(() => dispatch(fetchUnread(userNameIsFirst)), unreadInterval); // Таймер обновления непрочитанных сообщений
        this.timerOnline = setInterval(() => dispatch(fetchOnline()), timeoutCheckOnline); // Таймер обновления онлайна
    }

    componentWillUnmount() {
        clearInterval(this.timerUnread);
        clearInterval(this.timerOnline);
    }

    render() {
        const { isFetchChats, isChatRooms } = this.props;

        return (
            <div className="userListBlock assol-grey-panel" style={{paddingBottom: isChatRooms ? "70px" : "15px"}}>
                <ToolbarBlock />
                {(() => isFetchChats ? <LoadingPanel /> : <UsersBlockList /> )()}
            </div>
        );
    }

}