<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Config extends MY_Controller {

    public function index() {
        try {
            // TODO: Перенести настройки чата в таблицу assol_settings
            // TODO: Здесь будут настройки всего приложения. Продумать разбиение на секции?
            $config = [
                'employee' => ['id' => $this->getUserID()],
                /*
                    Порядок отображения имени и фамилии. true - первое имя, false - первая фамилия
                        Assol: Имя - Фамилия (true)
                        LoveStory: Фамилия - Имя (false)
                 */
                'userNameIsFirst' => !IS_LOVE_STORY,
                /* Возможность создавать общие чаты */
                'isChatRooms' => $this->isDirector(),
                /* Возможность просмотра чужих чатов. Доступно только для директора Assol*/
                'isViewUserChat' => ($this->isDirector() && !IS_LOVE_STORY),
                /* Лимит сообщений за раз при загрузке в чат */
                'messageLimit' => 20,
                /* Интервал проверки новых сообщений для открытого чата */
                'timeoutCheckMessages' => 2500,
                /* Интервал обновления online для списка сотрудников */
                'timeoutCheckOnline' => 5000,
                /* Флаг отображения кнопки отправки изображений в чат */
                'isSendImage' => true,
//                'isSendImage' => IS_LOVE_STORY,
                /* Флаг отображения кнопки шпионской формы */
                'isSpyForm' => (!IS_LOVE_STORY && $this->isDirector()),
                /* Таймер обновления непрочитанных сообщений */
                'unreadInterval' => 2500,
                /* Флаг автодобавления префикса к сообщению */
                'isAutoPrefixMessage' => IS_LOVE_STORY,
                /* E-Mail для отправки отчетов TODO: Вынести в контроллер Setting? */
                'email' => $this->getSettingModel()->get('ReportEmail'),
                /* Настройки отображения клиентов */
                'customers' => $this->customersConfig(),
                /* Расширенный режим отображения списка задач */
                'taskViewExtended' => IS_LOVE_STORY
            ];

            $employees = $this->getEmployeeModel()->employeeGetActiveList($this->getUserID(), $this->getUserRole());

            $this->json_response(["status" => 1, 'config' => $config, 'employees' => $employees]);
        } catch (Exception $e) {
            $this->json_response(['status' => 0, 'message' => $e->getMessage()]);
        }
    }

    /** Получить настройки отображения клиентов */
    private function customersConfig() {
        return [
            /* Отображение анкеты клиенток */
            'isCustomerProfile' => true,
            /* Разрешение редактирования ответов в анкете клиенток */
            'isEditQuestionAnswer' => ($this->isDirector() || $this->isSecretary()),
            /* Разрешение редактирования даты фотосессии на карточки клиентки */
            'isEditPhotoSessionDate' => ($this->isDirector() || $this->isSecretary()),
            /* Разрешение редактирования поля встречи на карточки клиентки */
            'isEditMeetings' => ($this->isDirector() || $this->isSecretary()),
            /* Разрешение редактирования поля доставки на карточки клиентки */
            'isEditDelivery' => ($this->isDirector() || $this->isSecretary()),
            /* Количество клиентов на страницу */
            'pageRecordLimit' => 20,
            /* Отображение фильтра "Статус" для списка клиентов */
            'showStateFilter' => $this->isDirector(),
            /* Отображение кнопки добавления нового клиента */
            'showAppendButton' => ($this->isDirector() || $this->isSecretary())
        ];
    }

}