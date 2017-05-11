import * as types from '../actions/action-types'
import { TASK_CARD_MODE_VIEW } from "../constants/task";

/**
 * @property {number} mode - режим отображения формы
 * @property {bool} isFetch - флаг загрузки списка комментарий
 * @property {bool} showTaskForm - флаг отображения формы с описанием задачи
 * @property {bool} createTaskForm - флаг отображения формы создания новой задачи
 * @property {string} name - название задачи
 * @property {string} editText - описание задачи
 * @property {string | bool} error - описание ошибки
 * @property {bool} confirmation - задача требует подтверждения
 * @property {Object} task - текущая задача
 * @property {array} sites - список сайтов. Используется для создание задачи в разрезе сайтов
 * @property {array} comments - список комментарий к задаче
 */
const initialState = {
    mode: TASK_CARD_MODE_VIEW,
    isFetch: false,
    showTaskForm: false,
    createTaskForm: false,
    name: '',
    editText: '',
    confirmation: false,
    error: false,
    task: {},
    sites: [],
    comments: []
};

export default function(state = initialState, action) {
    switch (action.type) {
        case types.CHANGE_TASK_CARD_MODE:
            return {...state, mode: action.mode, error: false};
        case types.CHANGE_TASK_CARD_NAME:
            return {...state, name: action.value};
        case types.CHANGE_TASK_CARD_EDIT_TEXT:
            return {...state, editText: action.value};
        case types.CHANGE_TASK_CARD_CONFIRMATION:
            return {...state, confirmation: action.value};

        case types.FETCH_TASK_COMMENTS_START:
            return {...state, isFetch: true};
        case types.FETCH_TASK_COMMENTS_SUCCESS:
            return {...state, comments: action.comments, isFetch: false};
        case types.FETCH_TASK_COMMENTS_FAILED:
            return {...state, isFetch: false};

        case types.FETCH_TASK_SITES_START:
            return {...state, isFetch: true};
        case types.FETCH_TASK_SITES_SUCCESS:
            return {...state, sites: action.sites, isFetch: false};
        case types.FETCH_TASK_SITES_FAILED:
            return {...state, isFetch: false};

        case types.APPEND_TASK_COMMENTS_START:
            return {...state, error: false};
        case types.APPEND_TASK_COMMENTS_SUCCESS:
            return {...state, mode: TASK_CARD_MODE_VIEW, comments: [...state.comments, action.comment]};
        case types.APPEND_TASK_COMMENTS_FAILED:
            return {...state, error: action.error};

        case types.CREATE_TASK_START:
            return {...state, error: false};
        case types.CREATE_TASK_SUCCESS:
            return initialState;
        case types.CREATE_TASK_FAILED:
            return {...state, error: action.error};

        case types.UPDATE_TASK_START:
            return {...state, error: false};
        case types.UPDATE_TASK_SUCCESS:
            return {...state, mode: TASK_CARD_MODE_VIEW, task: action.task};
        case types.UPDATE_TASK_FAILED:
            return {...state, error: action.error};

        case types.REMOVE_TASK_START:
            return {...state, error: false};
        case types.REMOVE_TASK_SUCCESS:
            return initialState;
        case types.REMOVE_TASK_FAILED:
            return {...state, error: action.error};

        case types.TASK_CARD_CREATE:
            return {...state, createTaskForm: true, confirmation: true};
        case types.TASK_CARD_SHOW:
            return {...state, task: action.task, showTaskForm: true};
        case types.TASK_CARD_HIDE:
            return initialState;

        default:
            return state;
    }
};