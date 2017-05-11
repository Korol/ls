import * as types from '../actions/action-types';

const initialState = {
    questions: [],
    history: [],
    customers: {
        isFetch: false,
        isLoveStoryCustomerCard: false,
        isAssolCustomerCard: false,
        currentPage: 1,
        data: [
        ],
        count: 0,
        ageMin: 0,
        ageMax: 0,
        filter: {
            status: 0,
            id: '',
            fi: '',
            city: '',
            ageMin: 0,
            ageMax: 0
        }
    }
};

export default function(state = initialState, action) {
    switch (action.type) {
        case types.FETCH_CUSTOMER_QUESTION_SUCCESS:
            return {...state, questions: action.questions};
        case types.FETCH_CUSTOMER_HISTORY_SUCCESS:
            return {...state, history: action.history};
        case types.SAVE_CUSTOMER_QUESTION_SUCCESS:
            var questionsUpdate = [...state.questions];

            questionsUpdate.forEach(function (item) {
                if (item.id == action.id) {
                    item.answer = action.answer;
                }
            });

            return {...state, questions: questionsUpdate};
        case types.FETCH_CUSTOMERS_START:
            return {...state, customers: {...state.customers, isFetch: true}};
        case types.FETCH_CUSTOMERS_SUCCESS:
            return {...state, customers: {...state.customers, isFetch: false, data: action.data.records, count: action.data.count}};
        case types.FETCH_CUSTOMERS_META_SUCCESS:
            return {...state, customers: {
                    ...state.customers, 
                    ageMin: parseInt(action.data.minMaxAge.min) || 0, 
                    ageMax: parseInt(action.data.minMaxAge.max) || 0,
                    isLoveStoryCustomerCard: action.data.isLoveStoryCustomerCard,
                    isAssolCustomerCard: action.data.isAssolCustomerCard
                }
            };
        case types.CHANGE_CUSTOMER_PAGE:
            return {...state, customers: {...state.customers, currentPage: action.page, isFetch: true}};
        case types.CHANGE_CUSTOMER_FILTER_ID:
            return {...state, customers: {...state.customers, currentPage: 1, filter: {...state.customers.filter, id: action.value}}};
        case types.CHANGE_CUSTOMER_FILTER_FI:
            return {...state, customers: {...state.customers, currentPage: 1, filter: {...state.customers.filter, fi: action.value}}};
        case types.CHANGE_CUSTOMER_FILTER_CITY:
            return {...state, customers: {...state.customers, currentPage: 1, filter: {...state.customers.filter, city: action.value}}};
        case types.CHANGE_CUSTOMER_FILTER_STATUS:
            return {...state, customers: {...state.customers, currentPage: 1, filter: {...state.customers.filter, status: action.value}}};
        case types.CHANGE_CUSTOMER_FILTER_AGE_MIN:
            return {...state, customers: {...state.customers, currentPage: 1, filter: {...state.customers.filter, ageMin: action.value}}};
        case types.CHANGE_CUSTOMER_FILTER_AGE_MAX:
            return {...state, customers: {...state.customers, currentPage: 1, filter: {...state.customers.filter, ageMax: action.value}}};
        default:
            return state;
    }
};