<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Events extends MY_Controller {

    public function index() {
        // Обновление статуса Онлайн для текущего пользователя
        $this->getEmployeeModel()->onlineUpdate($this->getUserID());

        $isAdmin = IS_LOVE_STORY
            ? ($this->isDirector() || $this->isSecretary())
            : $this->isDirector();

        // Запрос списка оповещений
        $this->json_response(array(
            'news' => [
                'replay' => $this->getMax($this->getNewsModel()->getCountUnreadNews($this->getUserID(), ($this->isTranslate() || $this->isEmployee()))),
                'once' => 0
            ],
            'calendar' => [
                'replay' => 0,
                'once' => $this->getMax(
                    $this->getCalendarModel()->calendarCount($this->getUserID(), date('Y-m-d H:i:s'), date('Y-m-d H:i:s')) +
                    $this->getCalendarModel()->calendarRemind($this->getUserID()) +
                    $this->getEmployeeModel()->getBirthdaysCount($this->getUserID(), $this->getUserRole()) +
                    $this->getCustomerModel()->getBirthdaysCount($this->getUserID())
                )
            ],
            'customer'  => ['replay' => 0, 'once' => 0],
            'employee'  => ['replay' => 0, 'once' => 0],
            'tasks'     => [
                'replay' => $this->getMax($this->getTaskModel()->getCountConfirmationTask($this->getUserID())),
                'tasks' => $this->getMax($this->getTaskModel()->getCountUnreadTask($this->getUserID())),
                'undone' => IS_LOVE_STORY ? $this->getMax($this->getTaskModel()->getCountUndoneTask($this->getUserID())) : 0,
                'comment' => $this->getMax($this->getTaskModel()->getCountUnreadComment($this->getUserID())),
                'once' => 0
            ],
            'messages'  => [
//                'replay' => $this->getMax($this->getMessageModel()->messageUnread($this->getUserID())),
                'replay' => $this->getMax($this->getMessageModel()->chatMessageUnreadCount($this->getUserID())),
                'once' => 0
            ],
            'services'  => [
                'replay' => 0,
                'once' => $this->getMax($this->getServiceModel()->getCountUnreadService($isAdmin, $this->getUserID()))
            ],
            'reports'   => ['replay' => 0, 'once' => 0],
            'documents' => ['replay' => 0, 'once' => 0],
            'training'  => ['replay' => 0, 'once' => 0],
            'sites'     => ['replay' => 0, 'once' => 0],
            'schedule'  => ['replay' => 0, 'once' => 0]
        ));
    }

    private function getMax($value) {
        return $value < 100 ? $value : 99;
    }

}
