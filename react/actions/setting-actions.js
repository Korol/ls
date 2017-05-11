import axios from "../client"
import qs from "qs";
import * as types from './action-types'

/** Сохранить E-Mail для отчетов */
export function saveReportEmail(email) {
    return function (dispatch) {
        axios.post("/setting/save", qs.stringify({ReportEmail: email}))
            .then((response) => {
                dispatch({type: types.SETTING_SAVE_REPORT_EMAIL, email: email});
            })
    }
}

/** E-Mail для отчетов */
export function changeReportEmail(email) {
    return {
        type: types.SETTING_CHANGE_REPORT_EMAIL,
        email: email
    }
}

/**
 * Загрузка списка вопросов для пользовательской анкеты
 */
export function fetchQuestionTemplate() {
    return function (dispatch) {
        dispatch({type: types.FETCH_SETTING_CUSTOMER_PROFILE_START});

        axios.get("/question/template")
            .then((response) => {
                dispatch({
                    type: types.FETCH_SETTING_CUSTOMER_PROFILE_SUCCESS,
                    questions: response.data.questions
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_SETTING_CUSTOMER_PROFILE_FAILED, payload: error})
            })
    };
}

/**
 * Добавление нового вопроса
 *
 * @param {string} question - текст вопроса
 */
export function addQuestion(question) {
    return function (dispatch) {
        dispatch({type: types.APPEND_SETTING_CUSTOMER_PROFILE_START});

        axios.post("/question/template/add", qs.stringify({question: question}))
            .then((response) => {
                dispatch({type: types.APPEND_SETTING_CUSTOMER_PROFILE_SUCCESS, id: response.data.id, question: question})
            })
            .catch((error) => {
                dispatch({type: types.APPEND_SETTING_CUSTOMER_PROFILE_FAILED, payload: error})
            })
    }
}

/**
 * Сохранение вопроса
 *
 * @param {number}  id          - ID вопроса
 * @param {string}  question    - текст вопроса
 */
export function editQuestion(id, question) {
    return function (dispatch) {
        dispatch({type: types.EDIT_SETTING_CUSTOMER_PROFILE_START});

        axios.post("/question/template/edit", qs.stringify({id: id, question: question}))
            .then((response) => {
                dispatch({type: types.EDIT_SETTING_CUSTOMER_PROFILE_SUCCESS, id: id, question: question})
            })
            .catch((error) => {
                dispatch({type: types.EDIT_SETTING_CUSTOMER_PROFILE_FAILED, payload: error})
            })
    }
}

/**
 * Удаление вопроса
 *
 * @param {number} id - ID вопроса
 */
export function removeQuestion(id) {
    return function (dispatch) {
        dispatch({type: types.REMOVE_SETTING_CUSTOMER_PROFILE_START});

        axios.post("/question/template/remove", qs.stringify({id: id}))
            .then((response) => {
                dispatch({type: types.REMOVE_SETTING_CUSTOMER_PROFILE_SUCCESS, id: id})
            })
            .catch((error) => {
                dispatch({type: types.REMOVE_SETTING_CUSTOMER_PROFILE_FAILED, payload: error})
            })
    }
}