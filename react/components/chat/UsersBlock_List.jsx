// Core
import * as React from 'react'
import { connect } from 'react-redux'

// Actions
import { changeRecipient, spyModeOff } from '../../actions/chat-actions'
import { fetchMessages, changeMessageText } from '../../actions/message-actions'

// Components
import ChatItem from './UsersBlock_ChatItem'
import UserItem from './UsersBlock_UserItem'

/** Список пользователей  */
@connect((store) => {
    return {
        userNameIsFirst: store.configState.config.userNameIsFirst,
        messageLimit: store.configState.config.messageLimit,
        recipient: store.chatState.recipient,
        chats: store.chatState.chats,
        online: store.chatState.online,
        unread: store.chatState.unread,
        filterText: store.chatState.filterText,
        spyMode: store.chatState.spyMode
    }
})
export default class UsersBlockList extends React.Component {

    /**
     * Поиск непрочитыннх сообщений для указанного пользователя
     *
     * @param id ид пользователя
     * @param unread список непрочитанных сообщений
     * @param isChat поиск сообщений с флагом isChat
     * @returns {*}
     */
    static findUnreadItem(id, unread, isChat) {
        for(var i=0; i < unread.length; i++) {
            var item = unread[i];
            if ((parseInt(item.sender) == id)   // Проверяем что совпадает ID отправителя
                && (Boolean(parseInt(item.isChat)) == isChat)) { // Проверяем что совпадает тип (чат или пользователь)
                return item;
            }
        }

        return false;
    }

    onChangeRecipient = (recipient) => {
        const { dispatch, messageLimit, spyMode } = this.props;

        // Смена текущего чата
        dispatch(changeRecipient(recipient));
        // Очистка текста сообщения
        dispatch(changeMessageText(''));

        // Загрузка списка сообщений для выбранного чата
        dispatch(fetchMessages(parseInt(recipient.id), parseInt(recipient.isChat), messageLimit));

        // Если включен режим "Шпион", то сбрасываем его
        if (spyMode) dispatch(spyModeOff());
    };

    render() {
        /** Строка поиска */
        var filterText = this.props.filterText.toLowerCase();
        /** Текущий выбранный чат */
        var selectRecipient = this.props.recipient;
        /** Список ID пользователей онлайн */
        var online = this.props.online;
        /** Список непрочитанных сообщений */
        var unread = this.props.unread;
        /** Настройки чата */
        var userNameIsFirst = this.props.userNameIsFirst;

        /** Функция проверяет запрет отображения чата на основе строки поиска */
        function isFilter(item) {
            // строка в которой искать
            var name = Boolean(parseInt(item.isChat))
                ? item.name // если это чат
                : userNameIsFirst // если это пользователь
                    ? (item.FName + ' ' + item.SName)
                    : (item.SName + ' ' + item.FName);

            return name.toLowerCase().indexOf(filterText) === -1;
        }

        function isOnline(id) {
            return $.inArray(id, online) > -1;
        }

        function isCurrent(recipient) {
            return (parseInt(selectRecipient.isChat) == parseInt(recipient.isChat))
                && (parseInt(selectRecipient.id) == parseInt(recipient.id));
        }

        var chatNodes = [];

        this.props.chats.forEach(function (chat) {
            if (isFilter(chat)) return;

            chatNodes.push(
                Boolean(parseInt(chat.isChat))
                    ? <ChatItem
                        key={'chat_' + chat.id}
                        recipient={chat}
                        unread={UsersBlockList.findUnreadItem(chat.id, unread, true)}
                        isCurrent={isCurrent(chat)}
                        onChangeRecipient={this.onChangeRecipient} />
                    : <UserItem
                        key={'user_' + chat.id}
                        recipient={chat}
                        online={isOnline(chat.id)}
                        unread={UsersBlockList.findUnreadItem(chat.id, unread, false)}
                        isCurrent={isCurrent(chat)}
                        onChangeRecipient={this.onChangeRecipient} />
            );
        }.bind(this));

        return (
            <div className="userListWrap">
                <ul className="userList">
                    {chatNodes}
                </ul>
            </div>
        );
    }
    
}