import axios from "../client"
import qs from "qs";
import * as types from './action-types';

/**
 * Загрузка списка чатов и пользователей с сервера
 * 
 * @param {String} userNameIsFirst - порядок отображение ФИ пользователей
 */
export function fetchChats(userNameIsFirst) {
    return function (dispatch) {
        dispatch({type: types.FETCH_CHATS_START});

        axios.get("/chat/chats")
            .then((response) => {
                dispatch({
                    type: types.FETCH_CHATS_SUCCESS, 
                    chats: response.data.chats,
                    online: response.data.online,
                    unread: response.data.unread,
                    userNameIsFirst: userNameIsFirst
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CHATS_FAILED, payload: error})
            })
    };
}

/**
 * Загрузка количества непрочитанных сообщений
 * 
 * @param {String} userNameIsFirst - порядок отображение ФИ пользователей
 */
export function fetchUnread(userNameIsFirst) {
    return function (dispatch) {
        axios.get("/chat/unread")
            .then((response) => {
                dispatch({
                    type: types.FETCH_UNREAD_SUCCESS,
                    unread: response.data,
                    userNameIsFirst: userNameIsFirst
                })
            })
    };
}

/**
 * Загрузка онлайн пользователей
 */
export function fetchOnline() {
    return function (dispatch) {
        axios.get("/employee/online")
            .then((response) => {
                dispatch({
                    type: types.FETCH_ONLINE_SUCCESS,
                    online: response.data
                })
            })
    };
}

export function changeRecipient(recipient) {
    return {
        type: types.CHANGE_RECIPIENT,
        recipient: recipient
    }
}

export function changeFilterText(filterText) {
    return {
        type: types.CHANGE_CHAT_FILTER,
        filterText: filterText
    }
}

/** Включение режима "Шпион" */
export function spyModeOn() {
    return {
        type: types.SPY_MODE_ON
    }
}

/** Выключение режима "Шпион" */
export function spyModeOff() {
    return {
        type: types.SPY_MODE_OFF
    }
}

export function spyChangeSender(sender) {
    return {
        type: types.SPY_CHANGE_SENDER,
        sender: sender
    }
}

export function spyChangeRecipient(recipient) {
    return {
        type: types.SPY_CHANGE_RECIPIENT,
        recipient: recipient
    }
}

/** Флаг отображения чата */
export function showChat() {
    return {
        type: types.CHAT_SHOW
    }
}

/** Флаг скрытия чата */
export function hideChat() {
    return {
        type: types.CHAT_HIDE
    }
}