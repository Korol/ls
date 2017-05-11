import axios from "../client"
import qs from "qs";
import * as types from './action-types'

/**
 * Загрузка списка вопросов для пользовательской анкеты
 */
export function fetchQuestions(customerID) {
    return function (dispatch) {
        dispatch({type: types.FETCH_CUSTOMER_QUESTION_START});

        axios.get("/customer/"+customerID+"/question/data")
            .then((response) => {
                dispatch({
                    type: types.FETCH_CUSTOMER_QUESTION_SUCCESS,
                    questions: response.data.questions
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CUSTOMER_QUESTION_FAILED, payload: error})
            })
    };
}

/**
 * Сохранение ответа на вопрос
 *
 * @param {number}  id          - ID вопроса
 * @param {number}  customer    - ID клиента
 * @param {string}  answer      - текст ответа на вопрос
 */
export function saveQuestion(id, customer, answer) {
    return function (dispatch) {
        dispatch({type: types.SAVE_CUSTOMER_QUESTION_START});

        axios.post("/customer/"+customer+"/question/save", qs.stringify({id: id, customer: customer, answer: answer}))
            .then((response) => {
                dispatch({type: types.SAVE_CUSTOMER_QUESTION_SUCCESS, id: id, customer: customer, answer: answer})
            })
            .catch((error) => {
                dispatch({type: types.SAVE_CUSTOMER_QUESTION_FAILED, payload: error})
            })
    }
}

/**
 * Загрузка истории изменений карточки клиента
 */
export function fetchHistory(customerID) {
    return function (dispatch) {
        dispatch({type: types.FETCH_CUSTOMER_HISTORY_START});

        axios.get("/customer/"+customerID+"/history/data")
            .then((response) => {
                dispatch({
                    type: types.FETCH_CUSTOMER_HISTORY_SUCCESS,
                    history: response.data.history
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CUSTOMER_HISTORY_FAILED, payload: error})
            })
    };
}

/**
 * Загрузка списка клиентов
 */
export function fetchCustomers(offset, limit, filter) {
    return function (dispatch) {
        dispatch({type: types.FETCH_CUSTOMERS_START});

        let data = {
            Offset: offset,
            Limit: limit,
            Status: filter.status,
            ID: filter.id,
            FIO: filter.fi,
            City: filter.city
        };

        if (filter.ageMin) data['MinAge'] = filter.ageMin;
        if (filter.ageMax) data['MaxAge'] = filter.ageMax;

        axios.post("/customer/data", qs.stringify({data: data}))
            .then((response) => {
                dispatch({
                    type: types.FETCH_CUSTOMERS_SUCCESS,
                    data: response.data.data
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CUSTOMERS_FAILED, payload: error})
            })
    };
}

/**
 * Добавление пользователя
 */
export function appendCustomer(fName, sName, mName, onSuccess, onFailure) {
    axios.post("/customer/add", qs.stringify({sName: sName, fName: fName, mName: mName}))
        .then((response) => {
            if (response.data.status) {
                onSuccess(response.data.id);
            } else {
                onFailure(response.data.message);
            }
        })
        .catch((error) => {
            onFailure(error.statusText);
        })
}

/**
 * Добавление пользователя
 */
export function restoreCustomer(id, onSuccess, onFailure) {
    axios.post("/customer/restore", qs.stringify({id: id}))
        .then((response) => {
            if (response.data.status) {
                onSuccess();
            } else {
                onFailure(response.data.message);
            }
        })
        .catch((error) => {
            onFailure(error.statusText);
        })
}

/**
 * Обновление пользователя
 */
export function updateCustomer(CustomerID, data, onSuccess, onFailure) {
    axios.post('/customer/'+CustomerID+'/update', qs.stringify({data: data}))
        .then((response) => {
            if (response.data.status) {
                onSuccess();
            } else {
                onFailure(response.data.message);
            }
        })
        .catch((error) => {
            onFailure(error.statusText);
        })
}

/**
 * Загрузка дополнительных данных для списка клиентов
 */
export function fetchMetaCustomers() {
    return function (dispatch) {
        dispatch({type: types.FETCH_CUSTOMERS_META_START});

        axios.get("/customer/meta")
            .then((response) => {
                dispatch({
                    type: types.FETCH_CUSTOMERS_META_SUCCESS,
                    data: response.data.data
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CUSTOMERS_META_FAILED, payload: error})
            })
    };
}

/** Изменить текущую страницу для списка клиентов */
export function changeCurrentPage(page) {
    return {
        type: types.CHANGE_CUSTOMER_PAGE,
        page: page
    }
}

/** Изменить фильтр "ID" для списка клиентов */
export function changeFilterId(value) {
    return {
        type: types.CHANGE_CUSTOMER_FILTER_ID,
        value: value
    }
}

/** Изменить фильтр "ФИ" для списка клиентов */
export function changeFilterFI(value) {
    return {
        type: types.CHANGE_CUSTOMER_FILTER_FI,
        value: value
    }
}

/** Изменить фильтр "Город" для списка клиентов */
export function changeFilterCity(value) {
    return {
        type: types.CHANGE_CUSTOMER_FILTER_CITY,
        value: value
    }
}

/** Изменить фильтр "Статус" для списка клиентов */
export function changeFilterStatus(value) {
    return {
        type: types.CHANGE_CUSTOMER_FILTER_STATUS,
        value: value
    }
}

/** Изменить фильтр "Возраст - От" для списка клиентов */
export function changeFilterAgeMin(value) {
    return {
        type: types.CHANGE_CUSTOMER_FILTER_AGE_MIN,
        value: value
    }
}

/** Изменить фильтр "Возраст - До" для списка клиентов */
export function changeFilterAgeMax(value) {
    return {
        type: types.CHANGE_CUSTOMER_FILTER_AGE_MAX,
        value: value
    }
}