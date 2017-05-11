import * as React from "react"
import { connect } from 'react-redux'

import { spyModeOff, spyChangeSender, spyChangeRecipient } from '../../actions/chat-actions'
import { fetchSpyMessages, clearMessages } from '../../actions/message-actions'

/** Форма отправки сообщений */

@connect((store) => {
    return {
        userNameIsFirst: store.configState.config.userNameIsFirst,
        messageLimit: store.configState.config.messageLimit,
        spySender: store.chatState.spySender,
        spyRecipient: store.chatState.spyRecipient,
        chats: store.chatState.chats
    }
})
export default class SpyFormMessages extends React.Component {

    /** Поиск чата по ID */
    findChatById = (id) =>
        this.props.chats.find((element) => parseInt(element.id) == parseInt(id));

    onChangeSender = (event) => {
        const { dispatch, spyRecipient, messageLimit } = this.props;

        // Поиск выбранного отправителя
        let spySender = this.findChatById(event.target.value);

        // Оповещение о смене отправителя
        dispatch(spyChangeSender(spySender));

        // Если указан получатель и отправитель
        if (spySender && spyRecipient) {
            // Загрузка списка сообщений
            dispatch(fetchSpyMessages(parseInt(spySender.id), parseInt(spyRecipient.id), messageLimit));
        } else {
            // Очистка списка сообщений
            dispatch(clearMessages());
        }
    };

    onChangeRecipient = (event) => {
        const { dispatch, spySender, messageLimit } = this.props;

        // Поиск выбранного получателя
        let spyRecipient = this.findChatById(event.target.value);

        // Оповещение о смене получателя
        dispatch(spyChangeRecipient(spyRecipient));

        // Если указан получатель и отправитель
        if (spySender && spyRecipient) {
            // Загрузка списка сообщений
            dispatch(fetchSpyMessages(parseInt(spySender.id), parseInt(spyRecipient.id), messageLimit));
        } else {
            // Очистка списка сообщений
            dispatch(clearMessages());
        }
    };

    handleSpyMode = () => {
        this.props.dispatch(spyModeOff());
    };

    sortChats = (a, b) => {
        // 1. Сортируем по типу чата
        if (Boolean(parseInt(a.isChat)) || Boolean(parseInt(b.isChat))) return -1;

        // 2. Сортировка по ФИ
        var aName = this.props.userNameIsFirst
            ? a.FName + ' ' + a.SName // Сортировка по имени
            : a.SName + ' ' + a.FName; // Сортировка по фамилии

        var bName = this.props.userNameIsFirst
            ? b.FName + ' ' + b.SName // Сортировка по имени
            : b.SName + ' ' + b.FName; // Сортировка по фамилии

        return aName.localeCompare(bName);
    };

    render() {
        var chatNodes = [];
        var userNameIsFirst = this.props.userNameIsFirst;

        this.props.chats.sort(this.sortChats).forEach(function (chat) {
            // Пропускаем общие чаты
            if (Boolean(parseInt(chat.isChat))) return;

            if (userNameIsFirst) {
                chatNodes.push(<option key={chat.id} value={chat.id}>{chat.FName} {chat.SName}</option>);
            } else {
                chatNodes.push(<option key={chat.id} value={chat.id}>{chat.SName} {chat.FName}</option>);
            }
        });
        
        return (
            <div className="main-chat-settings-wrap">
                <div className="main-chat-settings assol-grey-panel">
                    <div className="main-chat-settings-in">
                        <div className="director-show-block">
                            <div className="form-group">
                                <select
                                    className="assol-btn-style chat-select"
                                    onChange={this.onChangeSender}
                                >
                                    <option value="0">Выбрать</option>
                                    {chatNodes}
                                </select>
                            </div>

                            <div className="sort">
                                <span className="glyphicon glyphicon-sort" aria-hidden="true"></span>
                            </div>

                            <div className="form-group">
                                <select
                                    className="assol-btn-style chat-select"
                                    onChange={this.onChangeRecipient}
                                >
                                    <option value="0">Выбрать</option>
                                    {chatNodes}
                                </select>
                            </div>
                            <div>
                                <button
                                    className="btn assol-btn remove"
                                    title="Очистить выбор"
                                    onClick={this.handleSpyMode} >

                                    <span className="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

}