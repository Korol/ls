import * as types from '../actions/action-types';

/**
 * @property {Array} employees - список активных пользователей согласно правам доступа 
 * 
 * @type {{config: boolean, setting: {email: string, isFetchQuestions: boolean, questions: Array}, employees: Array, customers: Array}}
 */
const initialState = {
    config: false,
    setting: {
        email: '',
        isFetchQuestions: false,
        questions: []
    },
    employees: []
};

export default function(state = initialState, action) {
    switch (action.type) {
        case types.FETCH_CONFIG_SUCCESS:
            return {...state, config: action.config, employees: sortAndNormalizationEmployees(action.employees, action.config.userNameIsFirst)};
        case types.SETTING_CHANGE_REPORT_EMAIL:
            return {...state, setting: {...state.setting, email: action.email}};
        case types.SETTING_SAVE_REPORT_EMAIL:
            return {...state, config: {...state.config, email: action.email}};
        case types.FETCH_SETTING_CUSTOMER_PROFILE_START:
            return {...state, setting: {...state.setting, isFetchQuestions: true}};
        case types.FETCH_SETTING_CUSTOMER_PROFILE_SUCCESS:
            return {...state, setting: {...state.setting, isFetchQuestions: false, questions: action.questions}};
        case types.FETCH_SETTING_CUSTOMER_PROFILE_FAILED:
            return {...state, setting: {...state.setting, isFetchQuestions: false}};
        case types.APPEND_SETTING_CUSTOMER_PROFILE_SUCCESS:
            var questions = state.setting.questions.concat({id: action.id, question: action.question});
            return {...state, setting: {...state.setting, questions: questions}};
        case types.EDIT_SETTING_CUSTOMER_PROFILE_SUCCESS:
            var questionsUpdate = [...state.setting.questions];

            questionsUpdate.forEach(function (item) {
                if (item.id == action.id) {
                    item.question = action.question;
                }
            });

            return {...state, setting: {...state.setting, questions: questionsUpdate}};
        case types.REMOVE_SETTING_CUSTOMER_PROFILE_SUCCESS:
            var questionsRemove = [...state.setting.questions];

            questionsRemove.forEach(function (item, index) {
                if (item.id == action.id) {
                    questionsRemove.splice(index, 1);
                }
            });

            return {...state, setting: {...state.setting, questions: questionsRemove}};
        case types.FETCH_EMPLOYEES_SUCCESS:
            return {...state, employees: sortAndNormalizationEmployees(action.employees, state.config.userNameIsFirst)};
        default:
            return state;
    }
};

/** Сортировка списка пользователей + нормализация имени для общего чата */
function sortAndNormalizationEmployees(employees, userNameIsFirst) {
    // Внедрение нормализованного имени
    employees.forEach(function (employee) {
        employee.name = userNameIsFirst
            ? employee.FName + ' ' + employee.SName
            : employee.SName + ' ' + employee.FName;
    });

    return employees.sort(function (a, b) {
        var aName = userNameIsFirst
            ? a.FName + ' ' + a.SName // Сортировка по имени
            : a.SName + ' ' + a.FName; // Сортировка по фамилии

        var bName = userNameIsFirst
            ? b.FName + ' ' + b.SName // Сортировка по имени
            : b.SName + ' ' + b.FName; // Сортировка по фамилии

        return aName.localeCompare(bName);
    });
}