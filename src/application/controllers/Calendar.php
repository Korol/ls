<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * TODO:
 * "выполненно" - событие в календаре становится серым
 * (как и те, дата выполнения которых прошла).
 * Если событие стоит на 17.10 и пользователь нажал выполнить 15.10 то меняется дата на 15.10 и добавляется в отчет дня
 */
class Calendar extends MY_Controller {

    public function index() {
        $data = array(
            'birthdays' => array(
                'employee' => $this->getEmployeeModel()->getBirthdays($this->getUserID(), $this->getUserRole()),
                'customer' => $this->getCustomerModel()->getBirthdays($this->getUserID())
            ),
            'isCalendar' => true,
            'tasks' => $this->getTaskModel()->taskInGetList($this->getUserID())
        );
        $this->viewHeader($data);
        $this->view('form/calendar/index');
        $this->viewFooter([
            'isWysiwyg' => true,
            'js_array' => [
                'public/js/assol.calendar.js'
            ]
        ]);
    }

    public function data() {
        try {
            $start = $this->input->get('start');
            $end = $this->input->get('end');

            $data = [];

            $records = $this->getCalendarModel()->calendarGet($this->getUserID(), $start, $end);
            foreach ($records as $record) {
                $row = [
                    'id' => $record['id'],
                    'title' => $record['title'],
                    'description' => $record['description'],
                    'completed' => $record['completed'],
                    'className' => $record['completed'] > 0 ? 'calendar-completed' : '',
                    'remind' => $record['remind']
                ];

                // Выстовляем флаг полного дня если start и end со временем '00:00:00' и разница между ними в один день
                if ($this->isFullDay($record['start'], $record['end'])) {
                    $row['start'] = date_create($record['start'])->format('Y-m-d');
                } else {
                    $row['start'] = $record['start'];
                    $row['end'] = $record['end'];
                }

                $data[] = $row;
            }

            $birthdays = $this->getEmployeeModel()->getBirthdays($this->getUserID(), $this->getUserRole(), $start, $end);
            foreach($birthdays as $birthday) {
                $data[] = array(
                    'className' => 'action-birthday',
                    'title' => 'День рождения сотрудника - ' . $birthday['SName'] . ' ' . $birthday['FName'],
                    'start' => $this->normalizeDOB($start, $end, $birthday['DOB'])
                );
            }

            $birthdays = $this->getCustomerModel()->getBirthdays($this->getUserID(), $start, $end);
            foreach($birthdays as $birthday) {
                $data[] = array(
                    'className' => 'action-birthday',
                    'title' => 'День рождения клиентки - ' . $birthday['SName'] . ' ' . $birthday['FName'],
                    'start' => $this->normalizeDOB($start, $end, $birthday['DOB'])
                );
            }

            $this->json_response($data);
        } catch (Exception $e) {
            $this->json_response(array('error' => 1, 'message' => $e->getMessage()));
        }
    }

    private function isFullDay($start, $end) {
        if ((strpos($start, '00:00:00') !== FALSE) && (
                (strpos($end, '00:00:00') !== FALSE) // deprecated
                || (strpos($end, '23:59:59') !== FALSE)
            )) {

            $diff = date_create($start)->diff(date_create($end));

            if ($diff->days == 0) {
                if (($diff->h == 23) && ($diff->i == 59) && ($diff->s == 59)) {
                    return true;
                }
            } elseif ($diff->days == 1) {
                if (!($diff->h || $diff->i || $diff->s)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function done() {
        try {
            $id = $this->input->post('id');

            if (empty($id))
                throw new RuntimeException('Не указан ID события');

            $this->getCalendarModel()->eventDone($id);

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('error' => 1, 'message' => $e->getMessage()));
        }
    }

    public function save() {
        try {
            $id = $this->input->post('id');
            $title = $this->input->post('title');
            $description = $this->input->post('description');
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $remind = $this->input->post('remind');

            if (empty($title))
                throw new RuntimeException('Не указан заголовок');

            if (empty($start) || empty($end))
                throw new RuntimeException('Не указан временной интервал');

            if (empty($id)) {
                $this->getCalendarModel()->eventInsert($this->getUserID(), $title, $description, $start, $end, $remind);
            } else {
                $this->getCalendarModel()->eventUpdate($id, $title, $description, $start, $end, $remind);
            }

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('error' => 1, 'message' => $e->getMessage()));
        }
    }

    // assol-reports@mail.ru
    public function report() {
        try {
            $data = $this->input->post('data');

            $events = $this->getCalendarModel()->calendarGet($this->getUserID());

            $message  = '<table border="1" cellpadding="10">';
            $message .= '<tr style="background: grey; color: white;">';
            $message .= '<td>Время</td>';
            if (IS_LOVE_STORY) {
                $message .= '<td>Событие</td>';
            } else {
                $message .= '<td>План</td>';
            }
            $message .= '<td>Комментарий</td>';
            $message .= '</tr>';

            $format = '<tr><td>%s</td><td>%s</td><td>%s</td></tr>';

            foreach($data as $id => $comment) {
                $key = array_search($id, array_column($events, 'id'));

                // Выстовляем флаг полного дня если start и end со временем '00:00:00' и разница между ними в один день
                $isFullDay = (strpos($events[$key]['start'], '00:00:00') !== FALSE)
                    && (strpos($events[$key]['end'], '00:00:00') !== FALSE)
                    && date_create($events[$key]['start'])->diff(date_create($events[$key]['end']))->days == 1;

                if ($isFullDay) {
                    $dt = date_create($events[$key]['start'])->format('Y-m-d');
                } else {
                    $dt = $events[$key]['start'] . ' - ' . $events[$key]['end'];
                }

                $message .= sprintf($format, $dt, $events[$key]['title'], $comment);
            }

            $message .= '</table>';

            $url = parse_url(base_url());

            $this->email()
                ->from($this->getUserID().'@'.$url['host'], $this->user['SName'] . ' ' . $this->user['FName'])
                ->to($this->getSettingModel()->get('ReportEmail'))
                ->subject(sprintf('Отчет дня за %s %s %s', date('d.m.Y'), $this->user['SName'], $this->user['FName']))
                ->message($message)
                ->send();

            $this->json_response(array('status' => 1));
        } catch (Exception $e) {
            $this->json_response(array('error' => 1, 'message' => $e->getMessage()));
        }
    }

    /**
     * @return CI_Email
     */
    private function email() {
        if (!isset($this->email))
            $this->load->library('email');
        return $this->email;
    }

    public function show($idTask) {
        // 1. Обработка данных формы
        if (!empty($_POST)) {
            try {
                $description = $this->input->post('description');

                if (empty($description))
                    throw new Exception('Не указано описание задачи');

                $this->json_response(array('status' => 1));
            } catch (Exception $e) {
                $this->json_response(array('status' => 0, 'message' => $e->getMessage()));
            }
        }

        // 2. Загрузка шаблона
        $data = array(
            'task' => $this->getTaskModel()->taskGet($idTask),
            'CurrentUser' => $this->getUserID()
        );

        $this->load->view('form/tasks/show', $data);
    }

    /**
     * Нормализация даты рождения под календарь
     *
     * @param string $start дата начало выборки
     * @param string $end дата окончания выборки
     * @param string $dob дата дня рождения
     *
     * @return string запрашиваемый год календаря + месяц и число дня рождения
     */
    private function normalizeDOB($start, $end, $dob) {
        $start = new DateTime($start);
        $end = new DateTime($end);
        $dob = new DateTime($dob);

        $startYear = (int) $start->format('Y');
        $endYear = (int) $end->format('Y');

        if ($startYear != $endYear) {
            $dobMount = (int) $dob->format('m');
            $startMount = (int) $start->format('m');

            if ($dobMount > 0 && $dobMount < $startMount)
                return sprintf('%d-%s', $endYear, $dob->format('m-d'));
        }

        return sprintf('%d-%s', $startYear, $dob->format('m-d'));
    }
}
