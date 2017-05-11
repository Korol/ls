import axios from "../client"
import qs from "qs";
import * as types from './action-types'

/**
 * Загрузка списка сообщений с сервера
 *
 * @param {number}  recipient   - ID чата или пользователя
 * @param {number}  isChat      - тип получателя (чат или пользователь)
 * @param {number}  limit       - количество сообщений для загрузки
 */
export function fetchMessages(recipient, isChat, limit) {
    return function (dispatch) {
        dispatch({type: types.FETCH_MESSAGES_START});
        
        axios.post("/chat/messages", qs.stringify({recipient: recipient, isChat: isChat, limit: limit}))
            .then((response) => {
                dispatch({type: types.CHANGE_RECIPIENT_CHAT_INFO, recipientChatInfo: response.data.recipient_chat_info});
                dispatch({type: types.FETCH_MESSAGES_SUCCESS, messages: response.data.records});
            })
            .catch((error) => {
                dispatch({type: types.FETCH_MESSAGES_FAILED, payload: error})
            })
    };
}

/**
 * Загрузка истории сообщений
 *
 * @param {number}  recipient   - ID чата или пользователя
 * @param {number}  isChat      - тип получателя (чат или пользователь)
 * @param {number}  min         - минимальное загруженное сообщение на текущей момент
 * @param {number}  limit       - количество сообщений для загрузки
 */
export function fetchMessagesHistory(recipient, isChat, min, limit) {
    return function (dispatch) {
        dispatch({type: types.FETCH_HISTORY_MESSAGES_START});

        axios.post("/chat/history", qs.stringify({recipient: recipient, isChat: isChat, min: min, limit: limit}))
            .then((response) => {
                dispatch({type: types.FETCH_HISTORY_MESSAGES_SUCCESS, messages: response.data.records})
            })
            .catch((error) => {
                dispatch({type: types.FETCH_HISTORY_MESSAGES_FAILED, payload: error})
            })
    };
}

/**
 * Загрузка списка сообщений с сервера в режиме "Шпион"
 *
 * @param {number}  sender      - ID отправителя
 * @param {number}  recipient   - ID получателя
 * @param {number}  limit       - количество сообщений для загрузки
 */
export function fetchSpyMessages(sender, recipient, limit) {
    return function (dispatch) {
        dispatch({type: types.FETCH_MESSAGES_START});

        axios.post("/chat/messages", qs.stringify({sender: sender, recipient: recipient, limit: limit, isChat: 0}))
            .then((response) => {
                dispatch({type: types.FETCH_MESSAGES_SUCCESS, messages: response.data.records})
            })
            .catch((error) => {
                dispatch({type: types.FETCH_MESSAGES_FAILED, payload: error})
            })
    };
}

/**
 * Загрузка истории сообщений в режиме "Шпион"
 *
 * @param {number}  sender      - ID отправителя
 * @param {number}  recipient   - ID получателя
 * @param {number}  min         - минимальное загруженное сообщение на текущей момент
 * @param {number}  limit       - количество сообщений для загрузки
 */
export function fetchSpyMessagesHistory(sender, recipient, min, limit) {
    return function (dispatch) {
        dispatch({type: types.FETCH_HISTORY_MESSAGES_START});

        axios.post("/chat/history", qs.stringify({sender: sender, recipient: recipient, min: min, limit: limit, isChat: 0}))
            .then((response) => {
                dispatch({type: types.FETCH_HISTORY_MESSAGES_SUCCESS, messages: response.data.records})
            })
            .catch((error) => {
                dispatch({type: types.FETCH_HISTORY_MESSAGES_FAILED, payload: error})
            })
    };
}

/** 
 * Отправка сообщения на сервер 
 *
 * @param {number}  recipient   - ID чата или пользователя
 * @param {number}  isChat      - тип получателя (чат или пользователь)
 * @param {string}  message     - текст сообщения 
 */
export function sendMessage(recipient, isChat, message) {
    return function (dispatch) {
        dispatch({type: types.SEND_MESSAGE_START});

        axios.post("/chat/send", qs.stringify({recipient: recipient, isChat: isChat, message: message}))
            .then((response) => {
                dispatch({type: types.SEND_MESSAGE_SUCCESS, message: response.data.message})
            })
            .catch((error) => {
                dispatch({type: types.SEND_MESSAGE_FAILED, payload: error})
            })
    }
}

/**
 * Отправка метки прочтения сообщений
 *
 * @param {number}  recipient       - ID чата или пользователя
 * @param {number}  isChat          - тип получателя (чат или пользователь)
 * @param {number}  idMaxMessage    - максимальный ID сообщения
 */
export function readMessage(recipient, isChat, idMaxMessage) {
    return function (dispatch) {
        axios.post("/chat/read", qs.stringify({recipient: recipient, isChat: isChat, idMaxMessage: idMaxMessage}))
            .then((response) => {
                dispatch({type: types.READ_MESSAGE_SUCCESS, idMaxMessage: idMaxMessage})
            })
    }
}

/**
 * Запрос списка новых сообщений для указанного чата
 *
 * @param {number}  recipient       - ID чата или пользователя
 * @param {number}  isChat          - тип получателя (чат или пользователь)
 * @param {number}  idMaxMessage    - максимальный ID сообщения
 */
export function fetchNewMessages(recipient, isChat, idMaxMessage) {
    return function (dispatch) {
        axios.post("/chat/check", qs.stringify({recipient: recipient, isChat: isChat, idMaxMessage: idMaxMessage}))
            .then((response) => {
                dispatch({type: types.CHANGE_RECIPIENT_CHAT_INFO, recipientChatInfo: response.data.recipient_chat_info});
                if (response.data.records.length) {
                    dispatch({type: types.FETCH_NEW_MESSAGES_SUCCESS, messages: response.data.records});
                }
            })
    }
}

/** Текст сообщения */
export function changeMessageText(text) {
    return {
        type: types.CHANGE_MESSAGE_TEXT, 
        text: text
    }
}

/** Сбрасываем флаг необходимости скролла сообщений */
export function scrollToBottomDisable() {
    return {
        type: types.SCROLL_TO_BOTTOM_DISABLE
    }
}

/** Очистить список сообщений */
export function clearMessages() {
    return {
        type: types.CLEAR_MESSAGES
    }
}

/** Установить префикс сообщения */
export function setMessagePrefix(prefix) {
    return {
        type: types.PREFIX_MESSAGE_TEXT,
        prefix: prefix
    }
}