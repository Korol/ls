import * as React from "react"
import { connect } from "react-redux"

import { scrollToBottomDisable, readMessage, fetchNewMessages, setMessagePrefix } from '../../actions/message-actions'

import UserImg from './UsersBlock_UserImg'

/** Список сообщений */
@connect((store) => {
    return {
        isFetchMessages: store.messageState.isFetchMessages,
        isScrollToBottom: store.messageState.isScrollToBottom,
        spyMode: store.chatState.spyMode,
        chatState: store.messageState.chatState,
        online: store.chatState.online,
        recipient: store.chatState.recipient,
        recipientChatInfo: store.chatState.recipientChatInfo,
        messages: store.messageState.messages,
        timeoutCheckMessages: store.configState.config.timeoutCheckMessages
    };
})
export default class ListMessages extends React.Component {

    /** Сортировка списка сообщений по ID */
    static messageCompare(a, b) {
        var aId = parseInt(a.id);
        var bId = parseInt(b.id);

        return (aId > bId) ? 1 : ((aId < bId) ? -1 : 0);
    }

    fetchNewMessages = () => {
        // Пропускаем подгрузку списка новых сообщений для режима "Шпион"
        if (this.props.spyMode) return;

        const {dispatch, recipient, chatState } = this.props;
        dispatch(fetchNewMessages(parseInt(recipient.id), parseInt(recipient.isChat), chatState.maxId));
    };

    scrollToBottom() {
        var scroll = function () {
            var block = document.getElementById("messageList");
                block.scrollTop = block.scrollHeight;
        };

        /*
            Костыль!!! Запускаем дополнительный скроллинг три раза через 100мс для корректного скролинга при подгрузке
            картинок в чате. Сохраняем таймеры для корректной отмены в componentWillUnmount предворительно очистив список
        */
        this.clearScrollTimeout();
        for (var i = 0; i <= 3; i++) {
            this.scrollTimeout.push(setTimeout(scroll, i * 100));
        }
    }

    clearScrollTimeout() {
        if (this.scrollTimeout)
            this.scrollTimeout.forEach(clearTimeout);
        this.scrollTimeout = [];
    }

    componentDidMount() {
        const {timeoutCheckMessages } = this.props;

        // Запуск таймера получения новых сообщений
        this.timer = setInterval(this.fetchNewMessages, timeoutCheckMessages);

        // Запускаем скроллинг списка
        this.scrollToBottom();
    }

    componentWillUnmount() {
        // Удаление таймера получений новых сообщений
        clearInterval(this.timer);
        // Удаление неотработанных таймеров
        this.clearScrollTimeout();
    }

    componentDidUpdate() {
        const { dispatch, recipient, chatState, isScrollToBottom } = this.props;

        // Если взведен флаг необходимости скроллинга списка сообщений
        if (isScrollToBottom) {
            // Запускаем скроллинг списка
            this.scrollToBottom();

            // Сбрасываем флаг необходимости скроллинга
            dispatch(scrollToBottomDisable());
        } else if (chatState.existUnread) {
            // Делаем через else для последовательной обработки dispatch, иначе будет двойной вызов метода readMessage
            // Отправляем метку прочтения на сервер, в случае если есть непрочитанные сообщения
            setTimeout(() => dispatch(readMessage(parseInt(recipient.id), parseInt(recipient.isChat), chatState.maxId)), 3000);
        }
    }

    render() {
        const { dispatch, messages, online, recipientChatInfo } = this.props;

        function isOnline(id) {
            return $.inArray(id, online) > -1;
        }

        // ID последнего сообщения прочитанное получателем
        let recipientIdReadMessage = recipientChatInfo ? parseInt(recipientChatInfo.idReadMessage) : null;

        var blockDate; // Дата текущего блока
        var messageItems = [];

        messages.sort(ListMessages.messageCompare).forEach(function (item) {
            // Получение даты текущего сообщения
            var date = moment(item.dateCreate).format('YYYY-MM-DD');

            // Выводим дату если она изменилась
            if (blockDate != date) {
                blockDate = date;
                messageItems.push(<div key={blockDate} className="chat-date">{blockDate}</div>);
            }

            // Флаг нового сообщения
            var isNew = parseInt(item.isNew) > 0;
            // Флаг сообщения текущего пользователя 
            var isCur = parseInt(item.isCur) > 0;

            messageItems.push(
                <div key={item.id} className={"chat-date-message-line" + (isNew ? " new" : "") + (isCur ? " my-message" : "")}>
                    <div className="chat-date-message-user">
                        <a onClick={() => { if (!isCur) dispatch(setMessagePrefix(item.FName))} }>
                            <UserImg
                                isChat={false}
                                online={isOnline(item.idEmployee)}
                                fileName={item.FileName}
                            />
                        </a>
                    </div>
                    <div className="chat-date-message-text-wrap">
                        <div
                            className="chat-date-message-text"
                            dangerouslySetInnerHTML={{__html: item.message}} ></div>
                    </div>
                </div>
            );

            // Если указан ID последнего просмотренного сообщения и оно совпадает с текущем
            if (recipientIdReadMessage && recipientIdReadMessage == item.id)
                messageItems.push(<div className="read-info" key="ReadInfo">Просмотрено, {recipientChatInfo.dateRead}</div>);
        });

        return (
            <div className="main-chat-block-wrap">
                <div className="main-chat-block" id="messageList">
                    {messageItems}
                </div>
            </div>
        );
    }

}