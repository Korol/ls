import * as types from '../actions/action-types'
import { TASK_MODE_INBOX } from "../constants/task";

/**
 * @property {number} mode - режим списка (0 - входящие, 1 - исходящие, 2 - просроченные, 3 - архив)
 * @property {number} filterWhomTask - фильтр "Кому была поставлена"
 * @property {number} filterByWhomTask - фильтр "Кем была поставлена"
 * @property {number} expired - количество просроченных задач
 * @property {bool} isFetch - флаг загрузки списка задач
 * @property {array} tasks - список задач
 */
const initialState = {
    mode: TASK_MODE_INBOX,
    filterWhomTask: 0,
    filterByWhomTask: 0,
    isFetch: false,
    expired: 0,
    tasks: []
};

export default function(state = initialState, action) {
    switch (action.type) {
        case types.CHANGE_TASK_MODE:
            return {...state, mode: action.mode};
        case types.CHANGE_TASK_FILTER_WHOM_TASK:
            return {...state, filterWhomTask: action.value};
        case types.CHANGE_TASK_FILTER_BY_WHOM_TASK:
            return {...state, filterByWhomTask: action.value};

        case types.FETCH_TASKS_SUCCESS:
            return {...state, tasks: action.tasks, expired: action.expired, isFetch: false};
        case types.FETCH_TASKS_START:
            return {...state, isFetch: true};
        case types.FETCH_TASKS_FAILED:
            return {...state, isFetch: false};

        default:
            return state;
    }
};