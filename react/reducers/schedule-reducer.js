import * as types from '../actions/action-types'

const initialState = {
    isFetch: false,
    error: false,
    schedule: {},
    schedules: []
};

export default function(state = initialState, action) {
    switch (action.type) {

        case types.FETCH_SCHEDULES_START:
            return {...state, isFetch: true};
        case types.FETCH_SCHEDULES_SUCCESS:
            return {...state, schedule: action.schedule, schedules: action.schedules, isFetch: false};
        case types.FETCH_SCHEDULES_FAILED:
            return {...state, isFetch: false};

        default:
            return state;
    }
};