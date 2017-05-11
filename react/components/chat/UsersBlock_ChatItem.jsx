import * as React from "react";
import UserImg from './UsersBlock_UserImg';

/** Чат */
export default class ChatItem extends React.Component {

    constructor(props) {
        super(props);

        this.onClick = this.onClick.bind(this);
    }

    onClick() {
        this.props.onChangeRecipient(this.props.recipient)
    }

    render() {
        const {recipient, isCurrent, unread} = this.props;

        return (
            <li className="userItem">
                <a href="#" className="chat-user-in clear" onClick={this.onClick}>
                    <div>
                        <UserImg
                            isChat={true}
                            online={false}
                            fileName={recipient.FileName} />

                        <div className={'chat-user-name ' + (isCurrent ? 'current' : '')}>
                            {recipient.name}
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