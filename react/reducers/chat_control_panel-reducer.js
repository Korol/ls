import * as types from '../actions/action-types'

const initialState = {
    id: 0,
    avatar: 0,
    fileName: null,
    selected: [],
    showModal: false,
    error: false
};

export default function(state = initialState, action) {
    switch (action.type) {
        case types.PANEL_CHAT_OPEN:
            return {...state, showModal: true, id: action.id};
        case types.SAVE_CHAT_START:
            return {...state, error: false};
        case types.PANEL_CHAT_CLOSE:
            return initialState;
        case types.SAVE_CHAT_SUCCESS:
            return initialState;
        case types.SAVE_CHAT_FAILED:
            return {...state, error: action.error};
        case types.FETCH_CHAT_USERS_SUCCESS:
            return {...state, selected: action.selected};
        case types.PANEL_CHAT_SELECTED:
            return {...state, selected: action.records};
        case types.SAVE_AVATAR_CHAT_SUCCESS:
            return {...state, avatar: action.avatar, fileName: action.fileName};
        default:
            return state;
    }
}