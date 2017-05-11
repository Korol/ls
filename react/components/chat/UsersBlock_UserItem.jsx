import * as React from "react";
import { connect } from 'react-redux';
import UserImg from './UsersBlock_UserImg';

/** Пользователь */ 
@connect((store) => {
    return {
        userNameIsFirst: store.configState.config.userNameIsFirst
    }
})
export default class UserItem extends React.Component {

    constructor(props) {
        super(props);

        this.onClick = this.onClick.bind(this);
    }

    onClick() {
        this.props.onChangeRecipient(this.props.recipient)
    }
    
    render() {
        const {recipient, unread, isCurrent, userNameIsFirst} = this.props;

        return (
            <li className="userItem">
                <a href="#" className="chat-user-in clear" onClick={this.onClick}>
                    <div>
                        <UserImg
                            isChat={Boolean(parseInt(recipient.isChat))}
                            online={this.props.online}
                            fileName={recipient.FileName}
                        />

                        <div className={'chat-user-name ' + (isCurrent ? 'current' : '')}>
                            {(() => {
                                return userNameIsFirst
                                    ? (<span>{recipient.FName}<br/>{recipient.SName}</span>)
                                    : (<span>{recipient.SName}<br/>{recipient.FName}</span>);
                            })()}
                        </div>
                        {(() => {
                            if (unread) {
                                return <div className='chat-nums'>{unread.count}</div>
                            }
                        })()}
                    </div>
                </a>
            </li>
        );
    }
    
}