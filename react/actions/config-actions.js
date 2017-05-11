import axios from "../client"
import * as types from './action-types';

export function fetchConfig() {
    return function (dispatch) {
        dispatch({type: types.FETCH_CONFIG_START});

        axios.get('config')
            .then((response) => {
                dispatch({
                    type: types.FETCH_CONFIG_SUCCESS,
                    config: response.data.config,
                    employees: response.data.employees
                })
            })
            .catch((error) => {
                dispatch({type: types.FETCH_CONFIG_FAILED, payload: error})
            })
    };
}