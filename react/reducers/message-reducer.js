import * as types from '../actions/action-types';

/**
 * @property {boolean}          isFetchMessages             - флаг загрузки сообщений
 * @property {boolean}          isScrollToBottom            - флаг необходимости сдвига скроллинга сообщений вниз
 * @property {boolean|string}   fetchMessagesError          - описание ошибки загрузки сообщений
 * @property {boolean}          isFetchHistoryMessages      - флаг загрузки истории сообщений
 * @property {boolean|string}   fetchHistoryMessagesError   - описание ошибки загрузки истории сообщений
 * @property {string}           messageText                 - текст сообщения для отправки
 * @property {string}           prefixMessageText           - префикс перед сообщением
 * @property {Array}            messages                    - список сообщений
 * @property {number}           messagesState.minId         - минимальное ID загруженного сообщения
 * @property {number}           messagesState.maxId         - максимальный ID загруженного сообщения
 * @property {boolean}          messagesState.existUnread   - флаг наличия непрочитанных сообщений
 */
const initialState = {
    isFetchMessages: false,
    isScrollToBottom: false,
    fetchMessagesError: false,
    isFetchHistoryMessages: false,
    fetchHistoryMessagesError: false,
    messageText: '',
    prefixMessageText: '',
    messages: [],
    chatState: {
        minId: Number.MAX_SAFE_INTEGER,
        maxId: Number.MIN_SAFE_INTEGER,
        existUnread: false
    }
};

/**
 * Получить состояние списка сообщений
 *
 * @param {Array}   messages    список сообщений
 * @param {{minId: number, maxId: number, existUnread: boolean}} state
 * @returns {{minId: number, maxId: number, existUnread: boolean}}
 */
function getChatState(messages, state = {minId: Number.MAX_SAFE_INTEGER, maxId: Number.MIN_SAFE_INTEGER, existUnread: false}) {
    messages.forEach(function (message) {
        var idMessage = parseInt(message.id);

        state.minId = Math.min(state.minId, idMessage);
        state.maxId = Math.max(state.maxId, idMessage);
        state.existUnread |= (parseInt(message.isNew) > 0);
    });

    return state;
}

/**
 * Обновление метки прочтения
 *
 * @param {Array}   messages        список сообщений
 * @param {number}  idMaxMessage    ID последнего прочитанного сообщения
 */
function updateReadFlag(messages, idMaxMessage) {
    messages.forEach(function (message) {
        message.isNew = (parseInt(message.id) > idMaxMessage) && message.isNew;
    });

    return messages;
}

export default function(state = initialState, action) {
    switch (action.type) {
        case types.FETCH_MESSAGES_START:
            return {...state, isFetchMessages: true, fetchMessagesError: false, messages: []};
        case types.FETCH_MESSAGES_SUCCESS:
            return {...state, isFetchMessages: false, isScrollToBottom: true, messages: action.messages,
                chatState: getChatState(action.messages)};
        case types.FETCH_MESSAGES_FAILED:
            return {...state, isFetchMessages: false, fetchMessagesError: action.error};
        case types.FETCH_HISTORY_MESSAGES_START:
            return {...state, isFetchHistoryMessages: true, fetchHistoryMessagesError: false};
        case types.FETCH_HISTORY_MESSAGES_SUCCESS:
            return {...state, isFetchHistoryMessages: false, messages: action.messages.concat(state.messages),
                chatState: getChatState(action.messages, state.chatState)};
        case types.FETCH_HISTORY_MESSAGES_FAILED:
            return {...state, isFetchHistoryMessages: false, fetchHistoryMessagesError: action.error};
        case types.SEND_MESSAGE_START:
            return {...state, messageText: '', prefixMessageText: ''};
        case types.SEND_MESSAGE_SUCCESS:
            return {...state, messages: [...state.messages, action.message], isScrollToBottom: true};
        case types.SEND_MESSAGE_FAILED:
            return {...state}; // TODO: Отработать
        case types.CHANGE_MESSAGE_TEXT:
            return {...state, messageText: action.text};
        case types.PREFIX_MESSAGE_TEXT:
            // Новый префикс
            let prefix = action.prefix.trim() + ', ';
            // Если прошлый префикс равен тексту сообщения или сообщение пустое, то присваиваем новый префикс
            let text = (state.prefixMessageText == state.messageText || !state.messageText.trim())
                ? prefix
                : state.messageText;
            return {...state, prefixMessageText: prefix, messageText: text};
        case types.SCROLL_TO_BOTTOM_DISABLE:
            return {...state, isScrollToBottom: false};
        case types.CLEAR_MESSAGES:
            return {...state, messages: []};
        case types.READ_MESSAGE_SUCCESS:
            return {...state, messages: updateReadFlag(state.messages, action.idMaxMessage), chatState: {...state.chatState, existUnread: false}};
        case types.FETCH_NEW_MESSAGES_SUCCESS:
            return {...state, messages: state.messages.concat(action.messages), isScrollToBottom: true,
                chatState: getChatState(action.messages, state.chatState)};
        default:
            return state;
    }
};