<div class="calendar-day-events">
	<table>
		<tr>
			<td>
				<h4>События дня</h4>
				<div class="day-events">
					<div class="cde-block-in">
						<ul>
							<?php foreach($birthdays['employee'] as $birthday): ?>
                                <li>День рождения сотрудника - <?= $birthday['SName'] ?> <?= $birthday['FName'] ?></li>
                            <?php endforeach ?>
                            <?php foreach($birthdays['customer'] as $birthday): ?>
                                <li>День рождения клиентки - <?= $birthday['SName'] ?> <?= $birthday['FName'] ?></li>
                            <?php endforeach ?>
						</ul>
					</div>
				</div>
			</td>
			<td>
				<h4>Задачи</h4>
				<div class="day-tasks">
					<div class="cde-block-in">
						<ul>
							<?php foreach($tasks as $task): ?>
                                <li>
                                    <a href="<?= base_url('tasks/show/'.$task['ID']) ?>" data-toggle="modal" data-target="#remoteDialog">
                                        <?= $task['Title'] ?>
                                    </a>
                                </li>
							<?php endforeach ?>
						</ul>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>

<div id="alertError" class="alert alert-danger" role="alert" style="display: none">
    <h4>Ошибка!</h4>
    <p id="alertErrorMessage"></p>
</div>

<style>
    .calendar-completed .fc-content, .calendar-completed .fc-content .fc-time, .calendar-completed .fc-content .fc-title {
        font-weight: bold;
        text-decoration: line-through;
    }

</style>

<div class="calendar-wrap">
	<!-- http://fullcalendar.io/   использовался этот плагин для календаря -->
	<div id="calendar"></div>
</div>

<button id="btnShowDayReport" class="btn assol-btn add right" role="button" style="margin-top: 20px">
    ОТЧЕТ ДНЯ
</button>

<script id="reportTemplate" type="text/x-jquery-tmpl">
    <tr>
        <td>${moment(start).format('HH:mm')}</td>
        <td>${title}</td>
        <td><input type="text" id-event="${id}" class="assol-input-style"></td>
    </tr>
</script>

<script>

    $(function () {
        $('#formDayReport').find('h3').html('ОТЧЕТ ЗА ' + moment().format('DD MMMM').toUpperCase());
    });

</script>

<div id="formDayReport" class="day-report" style="display: none">
	<h3 class="light-title">ОТЧЕТ ЗА</h3>
	<div class="day-report-in">
		<table id="reportItems"></table>
		<button id="btnSaveDayReport" class="btn assol-btn add right" role="button">
			ОТПРАВИТЬ ОТЧЕТ
		</button>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="AddCalendarEvent" tabindex="-1" role="dialog" aria-labelledby="AddCalendarEventLabel">
	<div class="modal-dialog" role="document" style="width: 977px">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="AddCalendarEventLabel">НОВОЕ СОБЫТИЕ</h4>
			</div>
			<div class="modal-body">
                <div class="calendar-event-page">
                    <div class="new-event-settings">
                        <input type="hidden" id="AddCalendarEventID">
                        <table>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <label for="Remind">Напомнить за</label>
                                        <div class="btn-group assol-select-dropdown" id="Remind">
                                            <div class="label-placement-wrap">
                                                <button class="btn" data-label-placement=""><span class="data-label">Выбрать</span></button>
                                            </div>
                                            <button data-toggle="dropdown" class="btn dropdown-toggle">
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <input type="radio" id="Remind_0" name="Remind" value="0" checked>
                                                    <label for="Remind_0">Выбрать</label>
                                                </li>
                                                <?
                                                $remind = [
                                                    ['minutes' => 15,    'name' => '15 мин' ],
                                                    ['minutes' => 30,    'name' => '30 мин' ],
                                                    ['minutes' => 60,    'name' => '1 час'  ],
                                                    ['minutes' => 120,   'name' => '2 часа' ],
                                                    ['minutes' => 180,   'name' => '3 часа' ],
                                                    ['minutes' => 360,   'name' => '6 часов'],
                                                    ['minutes' => 1440,  'name' => '1 день' ],
                                                    ['minutes' => 10080, 'name' => '7 дней' ]
                                                ];
                                                ?>
                                                <? foreach ($remind as $item): ?>
                                                    <li>
                                                        <input type="radio" id="Remind_<?= $item['minutes'] ?>" name="Remind" value="<?= $item['minutes'] ?>">
                                                        <label for="Remind_<?= $item['minutes'] ?>"><?= $item['name'] ?></label>
                                                    </li>
                                                <? endforeach ?>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group calendar-block">
                                        <label for="">Время начала</label>
                                        <div class='input-group date' id='event-start'>
                                            <input type='text' class="assol-btn-style" />
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar">
                                                    <img src="<?=base_url()?>/public/img/calendar-icon.png" alt="">
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group calendar-block">
                                        <label for="">Время завершения</label>
                                        <div class='input-group date' id='event-end'>
                                            <input type='text' class="assol-btn-style" />
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar">
                                                    <img src="<?=base_url()?>/public/img/calendar-icon.png" alt="">
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="checkbox-line">
                                            <label>
                                                <input type="checkbox" id="AllDayCheckbox">
                                                <mark></mark>
                                                <span>
                                                    целый день
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="form-group">
                        <label for="">Название события:</label>
                        <input type="text" class="assol-input-style" id="AddCalendarEventTitle" placeholder="" value="">
                    </div>

                    <div class="new-event-description">
                        <div class="form-group">
                            <label for="AddCalendarEventDescription">Описание</label>
                            <textarea id="AddCalendarEventDescription" name="" class="assol-input-style" placeholder="Текст сообщения" rows="10"></textarea>
                        </div>
                    </div>

                    <div id="alertError" class="alert alert-danger" role="alert" style="display: none">
                        <h4>Ошибка!</h4>
                        <p id="alertErrorMessage"></p>
                    </div>

                    <div class="clear save-edit-wrap">
                        <button id="btnAddCalendarEvent" class="btn assol-btn add action-restore-customer right" role="button">
                            СОХРАНИТЬ
                        </button>
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="ShowCalendarEvent" tabindex="-1" role="dialog" aria-labelledby="ShowCalendarEventLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ShowCalendarEventLabel"></h4>
            </div>
            <div class="modal-body">
                <div class="calendar-event-page">
                    <div class="change-task-info-table">
                        <table>
                            <tr>
                                <th>Время начала:</th>
                                <td>
                                    <div id="showEventStart" class="task-date"></div>
                                </td>
                                <th>Время завершения:</th>
                                <td>
                                    <div id="showEventEnd" class="task-date"></div>
                                </td>
                            </tr>
                            <tr style="display: none">
                                <th>Напомнить за:</th>
                                <td colspan="3">
                                    <div id="showRemind"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="change-task-description-wrap">
                        <label for="showTaskDescription"><strong>Описание:</strong></label><br><br>
                        <div id="showTaskDescription" class="change-task-description"></div>
                    </div>

                    <div class="form-group clear save-edit-wrap">
                        <button id="btnEditEvent" class="btn assol-btn add right download active">РЕДАКТИРОВАТЬ</button>
                        <button id="btnDoneEvent" class="btn assol-btn add right" role="button">ВЫПОЛНЕНО</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
	$(function() {
		$('#event-start').datetimepicker({
			locale: 'ru'
		});
		$('#event-end').datetimepicker({
			locale: 'ru'
		});
	});

    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        // Очистка редактора для корректного открытия
        tinymce.EditorManager.editors = [];

        $(this).removeData('bs.modal');
    });
</script>