<?php
    if (IS_LOVE_STORY) {
        $isAdmin = $role['isDirector'];
    } else {
        $isAdmin = $role['isDirector'] || $role['isSecretary'];
    }
?>

<script>
    var UserRoles = {};
    <?php foreach($user_role as $item): ?>
    UserRoles["<?= $item['ID'] ?>"] = "<?= $item['ReferenceValue'] ?>";
    <?php endforeach ?>
</script>

<div class="panel assol-grey-panel">
    <table class="clients-view-table">
        <tr>
            <td>
                <?php if ($role['isDirector']): ?>
                    <div class="form-group">
                        <label for="EmployeesStatus">Статус</label>
                        <div class="btn-group assol-select-dropdown" id="EmployeesStatus">
                            <div class="label-placement-wrap">
                                <button class="btn" data-label-placement=""><span class="data-label">Активные</span></button>
                            </div>
                            <button data-toggle="dropdown" class="btn dropdown-toggle">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <input type="radio" id="EmployeesStatus_0" name="Status" value="0">
                                    <label for="EmployeesStatus_0">Все</label>
                                </li>
                                <li>
                                    <input type="radio" id="EmployeesStatus_1" name="Status" value="1" checked>
                                    <label for="EmployeesStatus_1">Активные</label>
                                </li>
                                <li>
                                    <input type="radio" id="EmployeesStatus_2" name="Status" value="2">
                                    <label for="EmployeesStatus_2">Заблокированные</label>
                                </li>
                                <li>
                                    <input type="radio" id="EmployeesStatus_3" name="Status" value="3">
                                    <label for="EmployeesStatus_3">Удаленные</label>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>
            </td>
            <td>
                <div class="form-group">
                    <label for="">ФИ</label>
                    <div class=""><input type="text" class="assol-input-style filter-input" id="FilterFIO"></div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <div class="form-group">
                        <label for="UserRole">Должность</label>
                        <div class="btn-group assol-select-dropdown" id="UserRole">
                            <div class="label-placement-wrap">
                                <button class="btn" data-label-placement>Все</button>
                            </div>
                            <button data-toggle="dropdown" class="btn dropdown-toggle">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <input type="radio" id="UserRole_0" name="UserRole" value="0">
                                    <label for="UserRole_0">Все</label>
                                </li>
                                <?php foreach($user_role as $item): ?>
                                    <li>
                                        <input type="radio" id="UserRole_<?=$item['ID']?>" name="UserRole" value="<?=$item['ID']?>">
                                        <label for="UserRole_<?=$item['ID']?>"><?=$item['ReferenceValue']?></label>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <label for="">Сайт</label>
                    <div class="btn-group assol-select-dropdown" id="FilterSite">
                        <div class="label-placement-wrap">
                            <button class="btn" data-label-placement=""><span class="data-label">Все</span></button>
                        </div>
                        <button data-toggle="dropdown" class="btn dropdown-toggle">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach($sites as $item): ?>
                                <li>
                                    <input type="radio" id="Site_<?=$item['ID']?>" name="Site" value="<?=$item['ID']?>">
                                    <label for="Site_<?=$item['ID']?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <?php if ($role['isDirector']): ?>
                        <a href="<?=current_url_build('add')?>" data-toggle="modal" data-target="#remoteDialog"
                           class="" role="button" title="Добавить сотрудника">
                           <button class="btn assol-btn add">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            Сотрудника
                           </button>
                        </a>
                    <?php endif ?>
                </div>
            </td>
        </tr>
    </table>
</div>

<?

    $isShowEmail = !IS_LOVE_STORY; // только для assol
    $isShowStatus = !IS_LOVE_STORY; // только для assol
    $isShowBlocked = !IS_LOVE_STORY && $role['isDirector']; // только для директора assol
    $isShowNote = !IS_LOVE_STORY; // только для assol

    $isShowDOB = IS_LOVE_STORY; // только LoveStory
    $isShowSchedule = IS_LOVE_STORY; // только LoveStory

?>

<script>
    function printDateOnline(date) {
        date += '';
        var m = moment(date);
        return m.isValid() ? m.calendar() : 'нет данных';
    }
</script>

<script id="employeeTemplate" type="text/x-jquery-tmpl">
    <div class="">
        <div class="employee-wrap" id-employee="${ID}">
            <div class="employee">
                <div class="employee-on-off {{if IsOnline > 0}}on{{else}}off{{/if}}">
                    {{if IsOnline > 0}}online{{else}}offline (${printDateOnline(DateOnline)}){{/if}}
                </div>
                <div class="employee-img">
                    <? if ($isAdmin): ?>
                    <a href="<?=base_url('employee')?>/${ID}/profile" class="employee-img-wrap">
                    <? else: ?>
                    <div class="employee-img-wrap">
                    <? endif ?>
                        <div class="employee-img-in">
                        {{if Avatar > 0}}
                            <img src="<?= base_url("thumb") ?>/?src=/files/images/${FileName}&w=160" alt="avatar">
                        {{else}}
                            <img src="<?= base_url("public/img/avatar.jpeg") ?>" alt="avatar">
                        {{/if}}
                        </div>
                    <? if ($isAdmin): ?>
                    </a>
                    <? else: ?>
                    </div>
                    <? endif ?>
                </div>
                <div class="employee-info">
                    <div class="employee-id">
                        <strong>ID:</strong>
                        <? if ($isAdmin): ?>
                        <a href="<?=base_url('employee')?>/${ID}/profile">
                        <? endif ?>
                            ${ID}
                        <? if ($isAdmin): ?>
                        </a>
                        <? endif ?>
                        
                    </div>
                    <div><strong>Фамилия:</strong> ${SName}</div>
                    <div><strong>Имя:</strong> ${FName}</div>
                    <div><strong>Отчество:</strong> ${MName}</div>
                    <div><strong>Телефон:</strong> ${Phone}</div>
                    <? if ($isShowEmail): ?>
                    <div>
                        <strong>E-mail:</strong>
                        {{if Email}}
                            <a href="mailto:${Email}">${Email}</a>
                        {{else}}
                            не указан
                        {{/if}}
                    </div>
                    <? endif ?>
                    <div><strong>Должность:</strong> ${UserRoles[UserRole]}</div>

                    <? if ($isShowDOB): ?>
                        <div><strong>Дата рождения:</strong> ${toClientDate(DOB)}</div>
                    <? endif ?>

                    <? if ($isShowStatus): ?>
                        <div>
                            <strong>Статус:</strong>
                            {{if IsDeleted > 0}}
                                Удаленный
                            {{else IsBlocked > 0}}
                                Заблокированный
                            {{else}}
                                Активный
                            {{/if}}
                        </div>
                    <? endif ?>

                    <? if ($isShowBlocked): ?>
                        <div class="checkbox-line">
                            <label>
                                <span><strong>Заблокировать:</strong></span>
                                <input class="action-blocked-employee" type="checkbox" {{if IsBlocked > 0}} checked {{/if}}>
                                <mark></mark>
                            </label>
                        </div>
                    <? endif ?>

                    <? if ($isShowNote): ?>
                        <div class="employee-footnote">
                            <strong>Последние изменения:</strong><br>
                            <div id="" class="assol-input-style employee-footnote-block-wrap">
                                <div class="employee-footnote-block">
                                    <div class="assol-input-style employee-footnote-block-in" id="Note_${ID}">
                                        последние изменения
                                        ${toClientDateTime(DateUpdate)}
                                        ${Note}
                                    </div>
                                </div>
                            </div>
                        </div>
                    <? endif ?>

                    <? if ($isShowSchedule): ?>
                        <br>
                        <div>
                            <strong>График работы:</strong><br>
                            Пн: ${clearSchedule(schedule, 'Monday')}<br>
                            Вт: ${clearSchedule(schedule, 'Tuesday')}<br>
                            Ср: ${clearSchedule(schedule, 'Wednesday')}<br>
                            Чт: ${clearSchedule(schedule, 'Thursday')}<br>
                            Пт: ${clearSchedule(schedule, 'Friday')}<br>
                            Сб: ${clearSchedule(schedule, 'Saturday')}<br>
                            Вс: ${clearSchedule(schedule, 'Sunday')}<br>
                        </div>
                    <? endif ?>
                </div>
            </div>
        </div>
    </div>
</script>

<script>
    function clearSchedule(schedule, day) {
        return schedule ? schedule[day].replace('<br>', ' ') : '';
    }
</script>

<? if (IS_LOVE_STORY): ?>
    <style>
        .employee {
            height: 600px;
        }
    </style>
<? endif ?>

<div class="employees-wrap">
    <div id="employees" class="clear employees"></div>
</div>

<div class="assol-pagination assol-grey-panel">
    <div class="assol-pagination-in clear">

        <div class="assol-pagination-left">
            <input type="number" class="assol-input-style now-page-input" id="CurrentPage" value="1">
            <span class="assol-pagination-all">из <span id="CountPage">1<span></span>
        </div>
        <div class="assol-pagination-right">
            <div class="assol-pagination-arrs">
                <button class="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
    });
</script>