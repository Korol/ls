<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Модель для работы с сообщениями
 */
class Message_model extends MY_Model {
    
    /*     
        Типы чатов:
            1) Общий
            2) Пользовательский
            4) Личный    
     */

    private $table_chat =
        "CREATE TABLE IF NOT EXISTS `assol_chat` (
            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `type` int(11) NOT NULL COMMENT 'Тип чата: 0 - общий, 1 - пользовательский, 2 - личный',
            `access` int(11) NOT NULL COMMENT 'Тип доступа (только для общих чатов): 0 - видят сообщения всех пользователей, 1 - только разрешенных пользователей',
            `name` varchar(128) NULL COMMENT 'Название - только для чатов типа 0 и 1',
            `avatar` int(11) NULL COMMENT 'Уникальный номер изображения на аватар (только для чатов типа 0 и 1)',
            `dateCreate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания',
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Чаты';";

    private $table_chat_base =
        "INSERT INTO `assol_chat` (`id`, `type`, `access`, `name`, `dateCreate`) VALUES (1, 0, 1, 'Общий чат', NOW());";

    private $table_chat_user =
        "CREATE TABLE IF NOT EXISTS `assol_chat_user` (
          `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
          `idChat` int(11) NOT NULL COMMENT 'Уникальный номер чата',
          `idEmployee` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
          `idReadMessage` INT(11) NOT NULL DEFAULT 0 COMMENT 'Уникальный номер последнего прочитанного сообщения',
          `idChatMessage` INT(11) NOT NULL DEFAULT 0 COMMENT 'Уникальный номер последнего сообщения (избыточные данные для ускорения выборок)',
          `countUnread` INT(11) NOT NULL DEFAULT 0 COMMENT 'Количество непрочитанных сообщений (избыточные данные для ускорения выборок)',
          `dateCreate` timestamp NULL DEFAULT NULL COMMENT 'Дата создания (добавление в чат)',
          `dateRead` timestamp NULL DEFAULT NULL COMMENT 'Время прочтения последнего сообщения',
          `dateRemove` timestamp NULL DEFAULT NULL COMMENT 'Дата удаления (удаление из чата)',
          `dateUpdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
          ON UPDATE CURRENT_TIMESTAMP COMMENT 'Дата и время последнего редактирования (используется как время прочтения последнего сообщения)',
          PRIMARY KEY (`id`),
          FOREIGN KEY (`idChat`) REFERENCES `assol_chat` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Участники чатов';";

    private $table_chat_message =
        "CREATE TABLE IF NOT EXISTS `assol_chat_message` (
          `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
          `idChat` int(11) NOT NULL COMMENT 'Уникальный номер чата',
          `idEmployee` INT(11) NOT NULL COMMENT 'Уникальный номер сотрудника',
          `message` TEXT NOT NULL COMMENT 'Текст сообщения',
          `dateCreate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Дата создания сообщения',
          PRIMARY KEY (`id`),
          FOREIGN KEY (`idChat`) REFERENCES `assol_chat` (`id`) ON UPDATE NO ACTION ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сообщения чатов';";

    private $table_message =
        "CREATE TABLE IF NOT EXISTS `assol_message` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `sender` INT(11) NOT NULL COMMENT 'ID сотрудника - Отправитель',
            `recipient` INT(11) NOT NULL COMMENT 'ID сотрудника - Получатель',
            `message` TEXT NOT NULL COMMENT 'Текст сообщения',
            `ts` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Временная метка',
            `tsRead` TIMESTAMP COMMENT 'Время прочтения соообщения',
            `read` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Флаг прочтения сообщения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Сообщения';";

    private $table_message_image =
        "CREATE TABLE IF NOT EXISTS `assol_message_image` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи',
            `message` INT(11) NOT NULL COMMENT 'ID сообщения',
            `image` INT(11) NOT NULL COMMENT 'ID изображения',
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Фотки из сообщений';";

    /**
     * Инициализация таблицы
     */
    public function initDataBase() {
        $this->db()->query($this->table_message);
        $this->db()->query($this->table_message_image);
        $this->db()->query($this->table_chat);
        $this->db()->query($this->table_chat_base);
        $this->db()->query($this->table_chat_user);
        $this->db()->query($this->table_chat_message);
    }

    public function dropTables() {
        $this->load->dbforge();

        $this->dbforge->drop_table(self::TABLE_MESSAGE_IMAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_MESSAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CHAT_MESSAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CHAT_USER_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CHAT_NAME, TRUE);
    }

    /**
     * Добавление сообщения
     *
     * @param int       $sender     ID сотрудника - Отправитель
     * @param int       $recipient  ID сотрудника - Получатель
     * @param string    $message    Текст сообщения
     *
     * @return int ID нового сообщения
     */
    public function messageInsert($sender, $recipient, $message) {
        $data = array(
            'sender' => $sender,
            'recipient' => $recipient,
            'message' => $message
        );
        $this->db()->insert(self::TABLE_MESSAGE_NAME, $data);

        return $this->db()->insert_id();
    }

    /**
     * Сохранение ID картинки в сообщение для возможности удаления вместе с сообщением
     *
     * @param int $idMessage    ID сообщения
     * @param int $idImage      ID картинки
     *
     * @return int ID нового сообщения
     */
    public function messageImageInsert($idMessage, $idImage) {
        $data = array(
            'message' => $idMessage,
            'image' => $idImage
        );
        $this->db()->insert(self::TABLE_MESSAGE_IMAGE_NAME, $data);

        return $this->db()->insert_id();
    }

    public function messageGetList($sender, $recipient, $limit, $EmployeeID, $EmployeeRole) {
        $this->db()
            ->select('msg.*')
            ->from(self::TABLE_MESSAGE_NAME . ' AS msg')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = msg.sender AND e.IsDeleted = 0', 'inner');

        // Если выбран не общий чат, то включаем условие по отправителю
        if ($recipient > 0) {
            $this->db()->where_in('msg.sender', [$sender, $recipient]);
            $this->db()->where_in('msg.recipient', [$sender, $recipient]);
        } else {
            $this->db()->where('msg.recipient', 0);

            // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
            if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
                $this->db()
                    ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                    ->group_start()
                        ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                        ->or_where('e.ID', $EmployeeID)
                        ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                    ->group_end();
            }
        }

        $records = $this->db()
            ->group_by('msg.id')
            ->order_by('msg.id', 'DESC')
            ->limit($limit)
            ->get()->result_array();

        function cmp($a, $b) {
            if ($a['id'] == $b['id']) {
                return 0;
            }
            return ($a['id'] < $b['id']) ? -1 : 1;
        }

        usort($records, 'cmp');

        return $records;
    }

    public function messageHistoryGetList($sender, $recipient, $min, $limit, $EmployeeID, $EmployeeRole) {
        $this->db()
            ->select('msg.*')
            ->from(self::TABLE_MESSAGE_NAME . ' AS msg')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = msg.sender AND e.IsDeleted = 0', 'inner');

        // Если выбран не общий чат, то включаем условие по отправителю
        if ($recipient > 0) {
            $this->db()->where_in('msg.sender', [$sender, $recipient]);
            $this->db()->where_in('msg.recipient', [$sender, $recipient]);
        } else {
            $this->db()->where('msg.recipient', 0);

            // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
            if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
                $this->db()
                    ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                    ->group_start()
                        ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                        ->or_where('e.ID', $EmployeeID)
                        ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                    ->group_end();
            }
        }

        return $this->db()
            ->where('msg.id <', $min)
            ->group_by('msg.id')
            ->order_by('msg.id', 'DESC')
            ->limit($limit)
            ->get()->result_array();
    }

    public function messageNowGetList($sender, $recipient, $lastMessageID, $EmployeeID, $EmployeeRole) {
        $this->db()
            ->select('msg.*')
            ->from(self::TABLE_MESSAGE_NAME . ' AS msg')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = msg.sender AND e.IsDeleted = 0', 'inner');

        if ($recipient > 0) {
            $this->db()
                ->where('msg.sender', $recipient)
                ->where('msg.recipient', $sender);
        } else {
            $this->db()
                ->where('msg.sender !=', $sender)
                ->where('msg.recipient', 0);

            // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
            if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
                $this->db()
                    ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$EmployeeID AND rgt.TargetEmployeeID=e.ID", 'left')
                    ->group_start()
                        ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                        ->or_where('e.ID', $EmployeeID)
                        ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                    ->group_end();
            }
        }

        return $this->db()
            ->where('msg.id >', $lastMessageID)
            ->group_by('msg.id')
            ->get()->result_array();
    }

    public function messageUnread($recipient) {
        return $this->db()
            ->where('recipient', $recipient)
            ->where('read', 0)
            ->count_all_results(self::TABLE_MESSAGE_NAME);
    }

    public function messageUnreadForEmployees($recipient) {
        return $this->db()
            ->select("`sender`, COUNT(*) as 'count', max(id) as 'id', 0 as 'isChat'")
            ->where('recipient', $recipient)
            ->where('read', 0)
            ->group_by('sender')
            ->get(self::TABLE_MESSAGE_NAME)->result_array();
    }

    public function messageRead($employee, $recipient) {
        $this->db()
            ->set('tsRead', 'NOW()', FALSE)
            ->where('recipient', $employee)
            ->where('sender', $recipient)
            ->where('read !=', 1)
            ->update(self::TABLE_MESSAGE_NAME, ['read' => 1]);
    }

    public function messageOrder($idEmployee) {
        $records = $this->db()
            ->from(self::TABLE_MESSAGE_NAME)
            ->select('max(id) as id, sender, recipient, ts')
            ->group_start()
                ->where('sender', $idEmployee)
                ->where('recipient !=', 0)
            ->group_end()
            ->or_group_start()
                ->or_where('recipient', $idEmployee)
            ->group_end()
            ->group_by('sender, recipient')
            ->order_by('id', 'ASC')
            ->get()->result_array();

        $data = [];

        foreach ($records as $record) {
            if ($record['sender'] != $idEmployee)
                $data[$record['sender']] = $record;
            if ($record['recipient'] != $idEmployee)
                $data[$record['recipient']] = $record;
        }

        // Функция сравнения
        function cmp($a, $b) {
            $d1 = new DateTime($a['ts']);
            $d2 = new DateTime($b['ts']);

            if ($d1 == $d2) {
                return 0;
            }
            return ($d1 < $d2) ? -1 : 1;
        }

        uasort($data, 'cmp');

        return $data;
    }

    public function messageTimeRead($employee, $recipient) {
        return $this->db()
            ->select('id, tsRead')
            ->where('recipient', $recipient)
            ->where('sender', $employee)
            ->where('read', 1)
            ->limit(1)
            ->order_by('id', 'DESC')
            ->get(self::TABLE_MESSAGE_NAME)->row_array();
    }

    /**
     * Получить список чатов
     *
     * @param $userID int ID текущего пользователя
     * @param $employeeRole int роль текущего пользователя
     * @return array
     */
    public function chatGetList($userID, $employeeRole) {
        
        // 1. Получить список общих чатов и разговоров (тип 0 и 1)
        $chats = $this->db()
            ->select("c.id, c.type, c.access, c.name, 1 AS 'isChat'")
            ->select("CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cu', 'c.id = cu.idChat', 'left')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'img.ID = c.avatar', 'left')
            ->where('c.type', 0) // если это общий чат
            ->or_group_start() // или это разговор и пользователь состоит в нем + нет метки удаления
                ->where('c.type', 1) 
                ->where('cu.idEmployee', $userID)    
                ->where('cu.dateRemove', null)    
            ->group_end()
            ->group_by('c.id')
            ->get()->result_array();

        // 2. Получить список пользовательских чатов (тип 2)
        $userChats = $this->db()
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            // ID пользователя
            ->select("cu.idEmployee, c.type")
            // Максимальный ID сообщения в чате
            ->select("IF(curCu.idChatMessage > cu.idChatMessage, curCu.idChatMessage, cu.idChatMessage) as MaxMessageID")
            ->join(self::TABLE_CHAT_USER_NAME . ' AS curCu', 'curCu.idChat = c.id AND curCu.idEmployee = ' . $userID, 'inner')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cu', 'cu.idChat = c.id AND cu.idEmployee != ' . $userID, 'inner')
            ->where('c.type', 2)
            ->get()->result_array();

        // 3. Получить список доступных пользователей
        $this->db()
            ->select("e.ID AS 'id', e.SName, e.FName, e.MName, 0 as 'isChat', 2 as 'type'")
            ->select("CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left')
            ->where('e.ID !=', $userID)
            ->where('e.IsDeleted', 0);

        // TODO: Возможно для нового чата надо пересмотреть. Вынести необходимость фильтрации списка в конфиг?
        // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
        if (in_array($employeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
            $this->db()
                ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$userID AND rgt.TargetEmployeeID=e.ID", 'left')
                ->group_start()
                ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                ->group_end();
        }

        $users = $this->db()
            ->group_by('e.ID')
            ->get()->result_array();

        // 4. Внедрение максимального ID сообщения в список пользователей
        foreach ($users as $key => $user) {
            foreach ($userChats as $info) {
                if ($user['id'] == $info['idEmployee']) {
                    $users[$key]['MaxMessageID'] = $info['MaxMessageID'];
                    break;
                }
            }
        }

        return array_merge($chats, $users);
    }

    public function chatMessageGetList($isChat, $recipient, $limit, $currentUserID, $EmployeeRole, $offset = FALSE) {
        $this->db()
            ->select('msg.*')
            ->select('e.FName, e.SName')
            ->select("cus.idReadMessage")
            ->select("IF(((msg.idEmployee != $currentUserID) AND (msg.id > cus.idReadMessage)), 1, 0) AS 'isNew'")
            ->select("IF(msg.idEmployee = $currentUserID, 1, 0) AS 'isCur'")
            ->select("CONCAT(img.ID, '.', img.ext) AS 'FileName'")
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cus', 'cus.idChat = c.id AND cus.idEmployee=' . $currentUserID, 'inner')
            ->join(self::TABLE_CHAT_MESSAGE_NAME . ' AS msg', 'msg.idChat = c.id', 'inner')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = msg.idEmployee', 'left')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left');

        if ($isChat) {
            // Поиск чата по ID
            $this->db()->where('c.id', $recipient);

            // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
            if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
                $this->db()
                    ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$currentUserID AND rgt.TargetEmployeeID=e.ID", 'left')
                    ->group_start()
                        ->where('c.access', 0)
                        ->or_group_start()
                            ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                            ->or_where('e.ID', $currentUserID)
                            ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                        ->group_end()
                    ->group_end();
            }

        } else {
            // Поиск чата двух пользователей
            $this->db()
                ->join(self::TABLE_CHAT_USER_NAME . ' AS cur', 'cur.idChat = c.id AND cur.idEmployee=' . $recipient, 'inner')
                ->where('c.type', 2);
        }

        if ($offset) {
            $this->db()->where('msg.id < ',  $offset);
        }

        $records = $this->db()
            ->limit($limit)
            ->order_by('msg.id', 'DESC')
            ->get()->result_array();

        return $records;
    }

    /**
     * Получение сообщения по ID
     *
     * @param $id int ID сообщения
     */
    public function chatMessageGet($id) {
        return $this->db()
            ->select('msg.*, 1 as isCur')
            ->select('e.FName, e.SName')
            ->where('msg.id', $id)
            ->select("CONCAT(img.ID, '.', img.ext) as 'FileName'")
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = msg.idEmployee')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left')
            ->get(self::TABLE_CHAT_MESSAGE_NAME . ' AS msg')->row_array();
    }

    /**
     * Количество непрочитанных сообщений
     *
     * @param $currentUserID int ID текущего пользователя
     * @return array
     */
    public function chatMessageUnread($currentUserID) {
        // 1. Количество непрочитанных сообщений в общих и групповых чатах
        $countBaseChats = $this->db()
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->select("c.id as 'sender'")                     // ID чата
            ->select("cu.idReadMessage as 'idReadMessage'")  // ID последнего прочитанного сообщения
            ->select("cu.idChatMessage as 'maxMessageId'")   // ID максимального сообщения от других пользователей в чате
            ->select("cu.countUnread as 'count'")            // Количество непрочитанных сообщений
            ->select("1 as 'isChat'")                        // Указываем что это чат
            // Подключение информации о текущем пользователе чата
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cu',
                'cu.idChat = c.id AND cu.countUnread > 0 AND cu.idEmployee = ' . $currentUserID, 'inner')
            ->where('c.type !=', 2) // Только общие и групповые разговоры (c.type = 0 или 1)
            ->get()->result_array();

        // 3. Количество непрочитанных сообщений в личных чатах
        $countUserChats = $this->db()
            ->from(self::TABLE_CHAT_NAME . ' AS c')

            ->select("cu.idEmployee as 'sender'")               // ID отправителя
            ->select("cuCur.idReadMessage as 'idReadMessage'")  // ID последнего прочитанного сообщения
            ->select("cuCur.idChatMessage as 'maxMessageId'")   // ID максимального сообщения от других пользователей в чате
            ->select("cuCur.countUnread as 'count'")            // Количество непрочитанных сообщений
            ->select("0 as 'isChat'")                           // Указываем что это не чат
            // Подключение информации о текущем пользователе чата
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cuCur', 'cuCur.idChat = c.id AND cuCur.countUnread > 0 AND cuCur.idEmployee = ' . $currentUserID, 'inner')
            // Подключение информации об отправителе
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cu', 'cu.idChat = c.id AND cu.idEmployee <> ' . $currentUserID, 'inner')

            ->where('c.type', 2) // Только частные разговоры (c.type = 2)
            ->get()->result_array();

        return array_merge($countBaseChats, $countUserChats);
    }

    /**
     * Общеее количество непрочитанных сообщений
     *
     * @param $currentUserID int ID текущего пользователя
     * @return array
     */
    public function chatMessageUnreadCount($currentUserID) {
        $count = 0;
        $records = $this->chatMessageUnread($currentUserID);
        foreach($records as $record) {
            $count += (int) $record['count'];
        }

        return $count;
    }

    /**
     * Добавление сообщения
     *
     * @param bool      $isChat     тип получателя - чат или пользователь
     * @param int       $sender     ID текущего сотрудника
     * @param int       $recipient  ID получателя
     * @param string    $message    Текст сообщения
     *
     * @return int ID нового сообщения
     */
    public function chatMessageSend($isChat, $sender, $recipient, $message) {
        // Получение чата для отправки
        $chat = $this->chatGet($isChat, $sender, $recipient);

        $data = array(
            'idChat' => $chat['id'],
            'idEmployee' => $sender,
            'message' => $message
        );

        $this->db()->insert(self::TABLE_CHAT_MESSAGE_NAME, $data);

        // Получение ID нового сообщения
        $idMessage = $this->db()->insert_id();

        // Обновление избыточных данных для участников чата
        $this->chatUpdateUnreadInfo($chat['id'], $sender, $idMessage);

        return $this->chatMessageGet($idMessage);
    }

    /**
     * Получение чата. Если чат не существует, то он создается
     *
     * @param bool  $isChat     тип получателя - чат или пользователь
     * @param int   $sender     ID текущего сотрудника
     * @param int   $recipient  ID получателя
     */
    private function chatGet($isChat, $sender, $recipient) {
        if ($isChat) {
            $chat = $this->chatFindById($recipient);

            if (empty($chat))
                throw new RuntimeException('Не найден указанный чат');

            return $chat;
        } else {
            // Поиск чата двух пользователей
            $chat = $this->db()
                ->select('c.*')
                ->from(self::TABLE_CHAT_NAME . ' AS c')
                ->join(self::TABLE_CHAT_USER_NAME . ' AS cus', 'cus.idChat = c.id AND cus.idEmployee=' . $sender, 'inner')
                ->join(self::TABLE_CHAT_USER_NAME . ' AS cur', 'cur.idChat = c.id AND cur.idEmployee=' . $recipient, 'inner')
                ->where('c.type', 2)
                ->get()->row_array();

            // Если пользователский чат не найден, то создаем его
            if (empty($chat)) {
                // Добавление нового чата
                $this->db()->insert(self::TABLE_CHAT_NAME, ['type' => 2]);
                $idChat = $this->db()->insert_id();
                // Добавление пользователей к чату
                $this->chatAddUser($idChat, $sender);
                $this->chatAddUser($idChat, $recipient);

                return $this->chatFindById($idChat);
            }

            return $chat;
        }
    }

    /**
     * Создать новый чат
     *
     * @param int       $currentUserId  ID текущего пользователя
     * @param int       $idChat         ID чата
     * @param int       $type           тип чата
     * @param int       $access         права доступа чата
     * @param string    $name           название чата
     * @param array     $employees      список пользователей
     * @param int       $avatar         ID изображения для аватара
     *
     * @return mixed новый чат
     */
    public function chatSave($currentUserId, $idChat, $type, $access, $name, $employees, $avatar = null) {
        $chat = $this->chatFindById($idChat);

        // Создаем новый чат, если он не найден
        if (empty($chat)) {
            $this->db()->insert(self::TABLE_CHAT_NAME, ['name' => $name, 'avatar' => $avatar, 'type' => $type, 'access' => $access]);
            $chat = $this->chatFindById($this->db()->insert_id());
        } else {
            $update = ['name' => $name, 'access' => $access];

            if (!empty($avatar)) {
                $update['avatar'] = $avatar;
            }

            $this->db()->update(self::TABLE_CHAT_NAME, $update, ["id" => $idChat]);
            // Возвращаем обновленный чат
            $chat = $this->chatFindById($idChat);
        }

        // Если это общий чат, то добавляем в чат всех пользователей
        if ($type == 0) {
            $users = $this->db()->get(self::TABLE_EMPLOYEE_NAME)->result_array();

            foreach ($users as $user)
                $this->chatAddUser($chat['id'], $user['ID']);
        } else {
            // Добавление в чат создателя
            $employees[] = $currentUserId;

            // Ставим метку удаления
            $this->db()
                ->where('idChat', $chat['id'])
                ->where_not_in('idEmployee', $employees)
                ->delete(self::TABLE_CHAT_USER_NAME);

            // Добавление указанных пользователей в чат
            foreach ($employees as $employee)
                $this->chatAddUser($chat['id'], $employee);
        }

        // Возвращаем новый чат
        $chat['isChat'] = 1;

        // Подключаем аватар
        $image = $this->db()->get_where(self::TABLE_IMAGE_NAME, ['ID' => $chat['avatar']])->row_array();

        if ($image) {
            $chat['FileName'] = $image['ID'] . '.' . $image['ext'];
        }

        return $chat;
    }

    public function chatFindById($id) {
        return $this->db()
            ->get_where(self::TABLE_CHAT_NAME, ['id' => $id])
            ->row_array();
    }

    private function chatAddUser($idChat, $idEmployee) {
        // Поиск пользователя в указанном чате
        $user = $this->db()->get_where(self::TABLE_CHAT_USER_NAME, ['idChat' => $idChat, 'idEmployee' => $idEmployee])->row_array();
        // если пользователь не найден, то добавляем его
        if (empty($user)) {
            $this->db()
                ->set('DateCreate', 'NOW()', FALSE)
                ->insert(self::TABLE_CHAT_USER_NAME, ['idChat' => $idChat, 'idEmployee' => $idEmployee]);
        }
    }

    /**
     * Обновление информации о непрочитанных сообщениях для участников чата
     *
     * @param $idChat int идентификатор чата
     * @param $sender int отправитель сообщения
     * @param $idMessage int идентификатор сообщения
     */
    private function chatUpdateUnreadInfo($idChat, $sender, $idMessage) {
        // 1. Запрос списка пользователей, которым будет доступно новое сообщение
        $users = $this->db()
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->select('cu.*')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cu', "cu.idChat = c.id AND cu.idEmployee != $sender", 'inner')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS es', "es.ID = $sender", 'left')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = cu.idEmployee', 'left')
            ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$sender AND rgt.TargetEmployeeID=e.ID", 'left')
            ->group_start()
                ->where('c.type', 2)        // Если это личный чат
                ->or_where('c.access', 0)   // Или если доступ разрешен всем
                // TODO: Для групповых чатов пересмотреть - добавить фильтрацию только по участникам разговора
                ->or_where_in('es.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Если сообщение отправил директор.
                ->or_group_start()          // Или есть связка между пользователями
                    ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                    ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                ->group_end()
            ->group_end()
            ->where('c.id', $idChat)
            ->group_by('cu.id')
            ->get()->result_array();

        foreach ($users as $user) {
            $this->db()->query(
                "UPDATE `assol_chat_user` cu SET cu.idChatMessage = $idMessage, cu.countUnread = cu.countUnread + 1 WHERE cu.id = " . $user['id']);
        }
    }

    /**
     * Обновление максимального ID прочитанного сообщения
     *
     * @param $currentUserId  int   идентификатор текущего пользователя
     * @param $isChat         bool  тип получателя - чат или пользователь
     * @param $recipient      int   ID получателя
     * @param $idMaxMessage   int   ID максимального сообщения в чате
     */
    public function chatMessageRead($currentUserId, $isChat, $recipient, $idMaxMessage) {
        $chat = $this->chatGet($isChat, $currentUserId, $recipient);
        $this->db()
            ->set('dateRead', 'NOW()', FALSE)
            ->update(self::TABLE_CHAT_USER_NAME,
            ['idReadMessage' => $idMaxMessage, 'countUnread' => 0], ['idEmployee' => $currentUserId, 'idChat' => $chat['id']]);
    }

    /**
     * Получение списка новых сообщений
     *
     * @param $currentUserID  int   идентификатор текущего пользователя
     * @param $isChat         bool  тип получателя - чат или пользователь
     * @param $recipient      int   ID получателя
     * @param $idMaxMessage   int   ID максимального сообщения в чате
     * @param $EmployeeRole   int   роль текущего пользователя
     * @return
     */
    public function chatNewMessageGetList($currentUserID, $isChat, $recipient, $idMaxMessage, $EmployeeRole) {
        $this->db()
            ->select('msg.*')
            ->select('e.FName, e.SName')
            ->select("cus.idReadMessage")
            ->select("IF(((msg.idEmployee != $currentUserID) AND (msg.id > cus.idReadMessage)), 1, 0) AS 'isNew'")
            ->select("IF(msg.idEmployee = $currentUserID, 1, 0) AS 'isCur'")
            ->select("CONCAT(img.ID, '.', img.ext) AS 'FileName'")
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cus', 'cus.idChat = c.id AND cus.idEmployee=' . $currentUserID, 'inner')
            ->join(self::TABLE_CHAT_MESSAGE_NAME . ' AS msg', 'msg.idChat = c.id AND msg.idEmployee != ' . $currentUserID, 'left')
            ->join(self::TABLE_EMPLOYEE_NAME . ' AS e', 'e.ID = msg.idEmployee', 'left')
            ->join(self::TABLE_IMAGE_NAME . ' AS img', 'e.Avatar = img.ID', 'left');

        if ($isChat) {
            // Поиск чата по ID
            $this->db()->where('c.id', $recipient);

            // Фильтруем список для ролей "Сотрудник" и "Переводчик" на основе прав доступа
            if (in_array($EmployeeRole, [USER_ROLE_TRANSLATE, USER_ROLE_EMPLOYEE])) {
                $this->db()
                    ->join(self::TABLE_EMPLOYEE_RIGHTS_NAME . ' AS rgt', "rgt.EmployeeID=$currentUserID AND rgt.TargetEmployeeID=e.ID", 'left')
                    ->group_start()
                        ->where('c.access', 0)
                        ->or_group_start()
                            ->where_in('e.UserRole', [USER_ROLE_DIRECTOR, USER_ROLE_SECRETARY]) // Подключаем роли "Директор" и "Секретарь"
                            ->or_where('e.ID', $currentUserID)
                            ->or_where('rgt.ID !=', null) // Остальных подключаем согласно правам доступа
                        ->group_end()
                    ->group_end();
            }
        } else {
            // Поиск чата двух пользователей
            $this->db()
                ->join(self::TABLE_CHAT_USER_NAME . ' AS cur', 'cur.idChat = c.id AND cur.idEmployee=' . $recipient, 'inner')
                ->where('c.type', 2);
        }

        $records = $this->db()
            ->where('msg.id > ',  $idMaxMessage)
            ->get()->result_array();

        return $records;
    }

    /**
     * Получение информации о прочтение сообщения
     *
     * @param $currentUserID  int   идентификатор текущего пользователя
     * @param $isChat         bool  тип получателя - чат или пользователь
     * @param $recipient      int   ID получателя
     * @return null
     */
    public function chatTimeRead($isChat, $currentUserID, $recipient) {
        if ($isChat) return null;

        // Поиск чата получателя
        return $this->db()
            ->select('cur.*')
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cus', 'cus.idChat = c.id AND cus.idEmployee=' . $currentUserID, 'inner')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cur', 'cur.idChat = c.id AND cur.idEmployee=' . $recipient, 'inner')
            ->where('c.type', 2)
            ->get()->row_array();
    }

    /**
     * Подключение общих чатов для указанного пользователя
     *
     * @param $idEmployee
     */
    public function connectCommonChats($idEmployee) {
        // 1. Получить список общих чатов (тип 0)
        $chats = $this->db()
            ->get_where(self::TABLE_CHAT_NAME, ['type' => 0])->result_array();

        foreach ($chats as $chat) {
            $this->chatAddUser($chat['id'], $idEmployee);
        }
    }

    /**
     * Перенос сообщений и чатов в новую систему
     */
    public function importChatLastSystem() {
        // Очистка и инициализация базы
        $this->load->dbforge();
        $this->dbforge->drop_table(self::TABLE_CHAT_MESSAGE_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CHAT_USER_NAME, TRUE);
        $this->dbforge->drop_table(self::TABLE_CHAT_NAME, TRUE);
        $this->db()->query($this->table_chat);
        $this->db()->query($this->table_chat_base);
        $this->db()->query($this->table_chat_user);
        $this->db()->query($this->table_chat_message);

        // 1. Подключение всех пользователей системы к главному чату
        $users = $this->db()
            ->from(self::TABLE_EMPLOYEE_NAME . ' AS e')
            ->get()->result_array();

        foreach ($users as $user) {
            $this->chatAddUser(1, $user['ID']);
        }

        // 2. Перенос сообщений в главный чат
        $messages = $this->db()
            ->from(self::TABLE_MESSAGE_NAME . ' AS msg')
            ->where('msg.recipient', 0)
            ->get()->result_array();

        foreach ($messages as $message) {
            $this->db()->insert(self::TABLE_CHAT_MESSAGE_NAME, [
                'idChat' => 1,
                'idEmployee' => $message['sender'],
                'message' => $message['message'],
                'dateCreate' => $message['ts']
            ]);
        }

        // 3. Перенос личных сообщений
        $messages = $this->db()
            ->from(self::TABLE_MESSAGE_NAME . ' AS msg')
            ->where('msg.recipient !=', 0)
            ->get()->result_array();

        foreach ($messages as $message) {
            // Получение чата
            $chatUser = $this->chatGet(FALSE, $message['sender'], $message['recipient']);

            $this->db()->insert(self::TABLE_CHAT_MESSAGE_NAME, [
                'idChat' => $chatUser['id'],
                'idEmployee' => $message['sender'],
                'message' => $message['message'],
                'dateCreate' => $message['ts']
            ]);
        }

        // 4. Устанавливаем для всех чатов максимальное значение для текущего и прочитанного сообщения
        $chatUsers = $this->db()
            ->from(self::TABLE_CHAT_NAME . ' AS c')
            ->select('cu.id, MAX(msg.id) AS maxMessageId')
            ->join(self::TABLE_CHAT_USER_NAME . ' AS cu', 'cu.idChat = c.id', 'left')
            ->join(self::TABLE_CHAT_MESSAGE_NAME . ' AS msg', 'msg.idChat = c.id', 'left')
            ->group_by('cu.id')
            ->get()->result_array();

        foreach ($chatUsers as $chatUser) {
            $this->db()
                ->set('dateRead', 'NOW()', FALSE)
                ->update(self::TABLE_CHAT_USER_NAME, [
                    'idReadMessage' => $chatUser['maxMessageId'], 'idChatMessage' => $chatUser['maxMessageId']], ['id' => $chatUser['id']]);
        }

        exit('Импорт успешно завершен!');
    }

    /**
     * Получить список пользователей привязанных к чату
     *
     * @param int $id id чата
     * @return mixed
     */
    public function chatEmployees($id) {
        $records = $this->db()
            ->select("idEmployee")
            ->from(self::TABLE_CHAT_USER_NAME)
            ->where("idChat", $id)
            ->get()->result_array();

        function map($record) {
            return $record['idEmployee'];
        }

        return array_map('map', $records);
    }

    /**
     * Удалить чат
     *
     * @param int $id ID чата
     */
    public function chatRemove($id) {
        $this->db()->delete(self::TABLE_CHAT_NAME, ['id' => $id]);
    }

}