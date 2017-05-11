import 'es6-shim'
import * as React from 'react';
import * as ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import store from './store';

// import UI
import ApplicationPanel from './components/chat/ApplicationPanel';
import SettingPage from './components/settings/SettingPage';
import Questionnaire from './components/customer/Questionnaire';
import CustomersBlock from './components/customer/CustomersBlock';
import ScheduleBlock from './components/schedule/ScheduleBlock';
import History from './components/customer/History';
import TasksBlock from './components/tasks/TasksBlock';

ReactDOM.render(
    <Provider store={store}>
        <ApplicationPanel />
    </Provider>,
    document.getElementById('react-chat')
);

$(function () {
    /**
     * Внедрение модулей React в текущее приложение. TODO: перевести на react-route
     *
     * @param selector ID элемента для внедрения
     * @param component компонент для внедрения
     */
    function inject(selector, component) {
        if (document.getElementById(selector)) {
            ReactDOM.render(
                <Provider store={store}>
                    {component}
                </Provider>,
                document.getElementById(selector)
            );
        }
    }

    // Внедрение настроек на React
    inject('react-setting-page', <SettingPage />);

    // Внедрение списка клиенток на React
    inject('react-customers-page', <CustomersBlock />);

    // Внедрение графика работ на React
    inject('react-schedule-page', <ScheduleBlock />);

    // Внедрение списка задач на React
    inject('react-tasks-page', <TasksBlock />);

    // Внедрение анкеты для клиентов при открытие вкладки "Анкета"
    $('a[href="#Questions"]').on('shown.bs.tab', function () {
        inject('react-customer-profile-page', <Questionnaire />);
    });

    // Внедрение истории изменений карточки клиентки при открытие вкладки "Изменения"
    $('a[href="#AdditionallyPane"]').on('shown.bs.tab', function () {
        inject('react-customer-history-page', <History />);
    });
});