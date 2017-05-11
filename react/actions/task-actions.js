import axios from "../client"
import qs from "qs";
import * as types from './action-types';

export function changeMode(mode) {
    return {
        type: types.CHANGE_TASK_MODE,
        mode
    }
}

export function changeFilterWhomTask(value) {
    return {
        type: types.CHANGE_TASK_FILTER_WHOM_TASK,
        value
    }
}

export function changeFilterByWhomTask(value) {
    return {
        type: types.CHANGE_TASK_FILTER_BY_WHOM_TASK,
        value
    }
}

export function changeTaskCardMode(mode) {
    return {
        type: types.CHANGE_TASK_CARD_MODE,
        mode
    }
}

export function changeTaskName(value) {
    return {
        type: types.CHANGE_TASK_CARD_NAME,
        value
    }
}

export function changeEditText(value) {
    return {
        type: types.CHANGE_TASK_CARD_EDIT_TEXT,
        value
    }
}

export function changeConfirmation(value) {
    return {
        type: types.CHANGE_TASK_CARD_CONFIRMATION,
        value
    }
}

export function createTask() {
    return function (dispatch) {
        dispatch({type: types.TASK_CARD_CREATE});
        dispatch({type: types.FETCH_TASK_SITES_START});

        axios.post("/tasks/sites", qs.stringify({}))
            .then((response) => {
                dispatch({
                    type: types.FETCH_TASK_SITES_SUCCESS,
                    sites: response.data.records
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_TASK_SITES_FAILED, payload: error})
            })
    };
}

/**
 * Отображение задачи с загрузкой списка комментариев
 */
export function showTask(task) {
    return function (dispatch) {
        dispatch({type: types.TASK_CARD_SHOW, task});
        dispatch({type: types.FETCH_TASK_COMMENTS_START});

        axios.post("/tasks/comments", qs.stringify({id: task.ID}))
            .then((response) => {
                dispatch({
                    type: types.FETCH_TASK_COMMENTS_SUCCESS,
                    comments: response.data.records
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_TASK_COMMENTS_FAILED, payload: error})
            })
    };
}

/**
 * Отображение задачи
 */
export function hideTask() {
    return {
        type: types.TASK_CARD_HIDE
    }
}

/**
 * Загрузка списка задач
 */
export function fetchTasks(mode, filterWhomTask, filterByWhomTask) {
    return function (dispatch) {
        dispatch({type: types.FETCH_TASKS_START});

        let data = {TypeTask: mode, WhomTask: filterWhomTask, ByWhomTask: filterByWhomTask};

        axios.post("/tasks/data", qs.stringify({data: data}))
            .then((response) => {
                dispatch({
                    type: types.FETCH_TASKS_SUCCESS,
                    tasks: response.data.records,
                    expired: response.data.expired
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_TASKS_FAILED, payload: error})
            })
    };
}

export function createNewTask(title, deadline, description, employees, sites, confirmation, callback) {
    return function (dispatch) {
        dispatch({type: types.CREATE_TASK_START});

        let data = {
            title: title,
            deadline: toServerDate(deadline),
            description: description,
            employees: employees,
            sites: sites,
            confirmation: confirmation ? 1 : 0
        };

        axios.post(`/tasks/add`, qs.stringify(data))
            .then((response) => {
                if (response.data.status) {
                    dispatch({type: types.CREATE_TASK_SUCCESS, task: response.data.task});
                    callback();
                } else {
                    dispatch({type: types.CREATE_TASK_FAILED, error: response.data.message})
                }
            })
            .catch((error) => {
                dispatch({type: types.CREATE_TASK_FAILED, payload: error})
            })
    };
}

export function doneTask(id, onSuccess, onFailure) {
    return function (dispatch) {
        axios.post(`/tasks/${id}/done`, false)
            .then((response) => {
                if (response.data.status) {
                    onSuccess();
                } else {
                    onFailure(response.data.message);
                }
            })
            .catch((error) => {
                onFailure("Ошибка обновления задачи");
            })
    }
}

export function updateTask(id, description, confirmation) {
    return function (dispatch) {
        dispatch({type: types.UPDATE_TASK_START});

         let data = {
            Description: description,
            confirmation: confirmation ? 1 : 0,
            IsRead: 0
         };

        axios.post(`/tasks/${id}/update`, qs.stringify({data: data}))
            .then((response) => {
                if (response.data.status) {
                    dispatch({type: types.UPDATE_TASK_SUCCESS, task: response.data.task})
                } else {
                    dispatch({type: types.UPDATE_TASK_FAILED, error: response.data.message})
                }
            })
            .catch((error) => {
                dispatch({type: types.UPDATE_TASK_FAILED, payload: error})
            })
    };
}

export function readTask(id) {
    return function (dispatch) {
        axios.post(`/tasks/${id}/update`, qs.stringify({data: {IsRead: 1}}))
            .then((response) => {
                if (response.data.status) {
                    dispatch({type: types.UPDATE_TASK_SUCCESS, task: response.data.task})
                } else {
                    dispatch({type: types.UPDATE_TASK_FAILED, error: response.data.message})
                }
            })
            .catch((error) => {
                dispatch({type: types.UPDATE_TASK_FAILED, payload: error})
            })
    };
}

export function readComments(id) {
    return function (dispatch) {
        axios.post(`/task/${id}/comment/read`, qs.stringify({}))
            .then((response) => {
                if (!response.data.status) {
                    dispatch({type: types.UPDATE_TASK_FAILED, error: response.data.message})
                }
            })
            .catch((error) => {
                dispatch({type: types.UPDATE_TASK_FAILED, payload: error})
            })
    };
}

export function removeTask(id, callback) {
    return function (dispatch) {
        dispatch({type: types.REMOVE_TASK_START});

        axios.post(`/tasks/${id}/remove`, qs.stringify({}))
            .then((response) => {
                if (response.data.status) {
                    callback();
                    dispatch({type: types.REMOVE_TASK_SUCCESS});
                } else {
                    dispatch({type: types.REMOVE_TASK_FAILED, error: response.data.message})
                }
            })
            .catch((error) => {
                dispatch({type: types.REMOVE_TASK_FAILED, payload: error})
            })
    };
}

export function appendComment(id, comment) {
    return function (dispatch) {
        dispatch({type: types.APPEND_TASK_COMMENTS_START});

        axios.post(`/task/${id}/comment/add`, qs.stringify({comment: comment}))
            .then((response) => {
                if (response.data.status) {
                    dispatch({type: types.APPEND_TASK_COMMENTS_SUCCESS, comment: response.data.record})
                } else {
                    dispatch({type: types.APPEND_TASK_COMMENTS_FAILED, error: response.data.message})
                }
            })
            .catch((error) => {
                dispatch({type: types.APPEND_TASK_COMMENTS_FAILED, payload: error})
            })
    };
}