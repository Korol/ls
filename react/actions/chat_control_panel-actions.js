import axios from "../client"
import qs from "qs";
import * as types from './action-types';

/**
 * Создать новый чат
 */
export function saveChat(id, type, access, avatar, name, employees) {
    return function (dispatch) {
        dispatch({type: types.SAVE_CHAT_START});

        axios.post("/chat/save", qs.stringify({id: id, type: type, access: access, avatar: avatar, name: name, employees: employees}))
            .then((response) => {
                if (response.data.status) {
                    dispatch({type: types.SAVE_CHAT_SUCCESS, chat: response.data.chat});
                } else {
                    dispatch({type: types.SAVE_CHAT_FAILED, error: response.data.message});
                }
            })
            .catch((error) => {
                dispatch({type: types.SAVE_CHAT_FAILED, payload: error})
            })
    };
}

/**
 * Удалить чат
 */
export function removeChat(id) {
    return function (dispatch) {
        dispatch({type: types.REMOVE_CHAT_START});

        axios.post("/chat/remove", qs.stringify({id: id}))
            .then((response) => {
                if (response.data.status) {
                    dispatch({type: types.REMOVE_CHAT_SUCCESS, id: id});
                } else {
                    dispatch({type: types.REMOVE_CHAT_FAILED, error: response.data.message});
                }
            })
            .catch((error) => {
                dispatch({type: types.REMOVE_CHAT_FAILED, payload: error})
            })
    };
}

/**
 * Получить список пользователей чата
 */
export function fetchChatUsers(id) {
    return function (dispatch) {
        dispatch({type: types.FETCH_CHAT_USERS_START});

        axios.post("/chat/chat_users", qs.stringify({id: id}))
            .then((response) => {
                dispatch({
                    type: types.FETCH_CHAT_USERS_SUCCESS, 
                    selected: response.data.selected
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CHAT_USERS_FAILED, payload: error})
            })
    };
}

export function chatPanelOpen(id) {
    return {
        type: types.PANEL_CHAT_OPEN,
        id: id
    }
}

export function chatPanelSelectHandler(records) {
    return {
        type: types.PANEL_CHAT_SELECTED,
        records: records
    }
}

export function chatPanelClose() {
    return {
        type: types.PANEL_CHAT_CLOSE
    }
}