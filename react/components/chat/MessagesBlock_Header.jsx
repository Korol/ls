import * as React from 'react'
import { connect } from 'react-redux'

import { fetchMessagesHistory, fetchSpyMessagesHistory } from '../../actions/message-actions'

import UserImg from './UsersBlock_UserImg'

/** Заголовок панели сообщений */

@connect((store) => {
    return {
        userNameIsFirst: store.configState.config.userNameIsFirst,
        messageLimit: store.configState.config.messageLimit,
        recipient: store.chatState.recipient,
        chatState: store.messageState.chatState,
        online: store.chatState.online,
        spyMode: store.chatState.spyMode,
        spySender: store.chatState.spySender,
        spyRecipient: store.chatState.spyRecipient
    }
})
export default class HeaderMessages extends React.Component {

    constructor(props) {
        super(props);
        
        this.onClick = this.onClick.bind(this);
    }

    onClick() {
        const { dispatch, recipient, messageLimit, chatState, spyMode, spySender, spyRecipient } = this.props;
        
        if (spyMode) {
            dispatch(
                fetchSpyMessagesHistory(parseInt(spySender.id), parseInt(spyRecipient.id), chatState.minId, messageLimit));
        } else {
            dispatch(
                fetchMessagesHistory(parseInt(recipient.id), parseInt(recipient.isChat), chatState.minId, messageLimit));
        }
    }

    render() {
        const { recipient, userNameIsFirst, spyMode, spySender, spyRecipient, isChatRooms } = this.props;

        function isOnline(id, online) {
            return $.inArray(id, online) > -1;
        }

        function getName(recipient) {
            return parseInt(recipient.isChat)
                ? recipient.name
                : userNameIsFirst
                    ? recipient.FName + ' ' + recipient.SName
                    : recipient.SName + ' ' + recipient.FName;
        }

        return (
            <div className="chat-select-user">
                <table>
                    <tbody>
                        {(() => spyMode ? (
                            <tr>
                                {(() => spySender ? (
                                    <td style={{whiteSpace: 'nowrap'}}>
                                        <table>
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <UserImg
                                                        isChat={false}
                                                        online={isOnline(spySender.id, this.props.online)}
                                                        fileName={spySender.FileName} />
                                                </td>
                                                <td style={{textDecoration: "none"}}>
                                                    <div className="chat-user-name">{getName(spySender)}</div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                ) : <td><span>Выбрать</span></td>)()}

                                <td style={{textAlign: 'center'}}>
                                    <span className="glyphicon glyphicon-sort" aria-hidden="true"></span>
                                </td>

                                {(() => spyRecipient ? (
                                    <td style={{whiteSpace: 'nowrap', float: 'left'}}>
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <UserImg
                                                            isChat={false}
                                                            online={isOnline(spyRecipient.id, this.props.online)}
                                                            fileName={spyRecipient.FileName} />
                                                    </td>
                                                    <td style={{textDecoration: "none"}}>
                                                        <div className="chat-user-name">{getName(spyRecipient)}</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                ) : <td><span>Выбрать</span></td>)()}

                                <td>
                                    <a href="#" onClick={this.onClick}>ранее</a>
                                </td>
                            </tr>
                        ) : (
                            <tr>
                                <td>
                                    <UserImg
                                        isChat={Boolean(parseInt(recipient.isChat))}
                                        online={isOnline(recipient.id, this.props.online)}
                                        fileName={recipient.FileName} />
                                </td>
                                <td>
                                    <div className="chat-user-name">{getName(recipient)}</div>
                                </td>
                                <td>
                                    <a href="#" onClick={this.onClick}>ранее</a>
                                </td>
                            </tr>
                        ))()}
                    </tbody>
                </table>
            </div>
        );
    }
}