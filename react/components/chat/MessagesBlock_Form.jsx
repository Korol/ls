import * as React from "react"
import { connect } from 'react-redux'

import { sendMessage, changeMessageText, clearMessages, setMessagePrefix } from '../../actions/message-actions'
import { spyModeOn } from '../../actions/chat-actions'

import ImageForm from './MessagesBlock_ImageForm'

/** Форма отправки сообщений */

@connect((store) => {
    return {
        isSpyForm: store.configState.config.isSpyForm,
        recipient: store.chatState.recipient,
        messageText: store.messageState.messageText,
        isSendImage: store.configState.config.isSendImage,
        isAutoPrefixMessage: store.configState.config.isAutoPrefixMessage
    }
})
export default class FormMessages extends React.Component {
    
    // TODO: 1) Отработать подключение / отключение при создание / удаление пользователей
    // TODO: 2) Отработать права доступа в общем чате - например проверить количество непрочитанных сообщений
    // TODO: 3) Отработать временную метку прочтения

    constructor(props) {
        super(props);
    }

    onClick = () => {
        const { isAutoPrefixMessage, dispatch, recipient } = this.props;

        // Добавление префикса если взведен флаг автодобавления и это не общий чат
        if (isAutoPrefixMessage && parseInt(recipient.isChat) == 0)
            dispatch(setMessagePrefix(recipient.FName));
    };

    handleSpyMode = () => {
        this.props.dispatch(spyModeOn());
        this.props.dispatch(clearMessages());
    };

    handleMessageChange(e) {
        this.props.dispatch(
            changeMessageText(e.target.value));
    }

    handleKeyPress(e) {
        if (e.key == 'Enter') {
            this.clickSendMessage();
        }
    }

    /** Обработка кнопки отправить */
    clickSendMessage() {
        const {recipient, messageText} = this.props;

        // Если сообщение не пустое, то отправляем
        if (messageText) {
            this.props.dispatch(
                sendMessage(parseInt(recipient.id), parseInt(recipient.isChat), messageText));
        }
    }

    render() {
        const {recipient, messageText, isSpyForm, isSendImage} = this.props;

        return (
            <div className="main-chat-settings-wrap">
                <div className="main-chat-settings assol-grey-panel">
                    <div className="main-chat-settings-in">
                        <div className="main-chat-user-settings">
                            {(() => {
                                if (recipient) return (
                                    <div>
                                        <input
                                            type="text"
                                            className="assol-input-style"
                                            value={messageText}
                                            onClick={this.onClick}
                                            onChange={this.handleMessageChange.bind(this)}
                                            onKeyPress={this.handleKeyPress.bind(this)} />

                                        {(() => {
                                            if (isSendImage) return <ImageForm />
                                        })()}
                                    </div>
                                )
                            })()}
                            {(() => {
                                if (recipient) return (
                                    <div>
                                        <button
                                            className="btn assol-btn add"
                                            title="Отправить сообщение"
                                            onClick={this.clickSendMessage.bind(this)} >

                                            <span className="glyphicon glyphicon-log-in" aria-hidden="true" />
                                        </button>
                                    </div>
                                )
                            })()}
                            {(() => {
                                if (isSpyForm) return (
                                    <div>
                                        <button
                                            className="btn assol-btn eye"
                                            onClick={this.handleSpyMode} >

                                            <span className="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                )
                            })()}
                        </div>
                    </div>
                </div>
            </div>
        );
    }

}