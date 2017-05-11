import axios from "../client"
import qs from "qs";
import * as types from './action-types';

/**
 * Загрузка списка 
 */
export function fetchSchedules() {
    return function (dispatch) {
        dispatch({type: types.FETCH_SCHEDULES_START});

        axios.get("/schedule/data")
            .then((response) => {
                dispatch({
                    type: types.FETCH_SCHEDULES_SUCCESS,
                    schedules: response.data.schedules,
                    schedule: response.data.schedule
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_SCHEDULES_FAILED, payload: error})
            })
    };
}

export function saveSchedule(data, onSuccess) {
    return function (dispatch) {
        dispatch({type: types.UPDATE_SCHEDULE_START});

        axios.post("/schedule/save", qs.stringify({data}))
            .then((response) => {
                dispatch({
                    type: types.UPDATE_SCHEDULE_SUCCESS
                });
                onSuccess();
            })
            .catch((error) => {
                dispatch({type: types.UPDATE_SCHEDULE_FAILED, payload: error})
            })
    };
}