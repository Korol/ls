<?
    $isDirector = $role['isDirector'];
    $isSecretary = $role['isSecretary'];
    $isTranslate = $role['isTranslate'];
    $isEmployee = $role['isEmployee'];

    if (IS_LOVE_STORY) {
        $isAddWestern = $isDirector || $isSecretary || $isTranslate;
        $isAddMeeting = $isDirector || $isSecretary || $isTranslate;
        $isAddDelivery = $isDirector || $isSecretary || $isTranslate;

        $isHideWesternAndMeeting = false;

        $isEditWestern = $isDirector || $isSecretary || $isTranslate;
        $isEditMeeting = $isDirector || $isSecretary || $isTranslate;
        $isEditDelivery = $isDirector || $isSecretary || $isTranslate;

        $isAdmin = $isDirector || $isSecretary;
    } else {
        $isAddWestern = $isDirector || $isTranslate;
        $isAddMeeting = $isDirector || $isTranslate;
        $isAddDelivery = $isDirector || $isSecretary;

        $isHideWesternAndMeeting = !IS_LOVE_STORY && $isSecretary; // Флаг скрытия блоков "Вестерны" и "Встречи" для секретаря

        $isEditWestern = $isDirector || $isTranslate;
        $isEditMeeting = $isDirector || $isTranslate;
        $isEditDelivery = $isDirector || $isSecretary;

        $isAdmin = $isDirector || $isSecretary;
    }
?>

<script>
    var Sites = {};
    <?php foreach($sites as $site): ?>
    Sites["<?= $site['ID'] ?>"] = "<?= empty($site['Name']) ? $site['Domen'] : $site['Name'] ?>";
    <?php endforeach ?>


    var Employees = {};
    <?php foreach($employees as $employee): ?>
    Employees["<?= $employee['ID'] ?>"] = "<?= $employee['SName'].' '.mb_substr($employee['FName'],0,1).'.'.mb_substr($employee['MName'],0,1) ?>";
    <?php endforeach ?>
</script>

<div class="services-page">
    <div class="panel assol-grey-panel">
        <div class="services-view-setting">
            <div>
                <div>
                    Показать за:
                </div>
                <div>
                    <div class="date-field">
                        <input type="text" class="assol-input-style" id="date-start">
                    </div>
                </div>
                <div>
                    по:
                </div>
                <div>
                    <div class="date-field">
                        <input type="text" class="assol-input-style" id="date-end">
                    </div>
                </div>
                <?php if ($isAdmin): ?>
                    <div>
                        Сотрудник:
                    </div>
                    <div>
                        <div class="btn-group assol-select-dropdown" id="EmployeeFilter">
                            <div class="label-placement-wrap">
                                <button class="btn" data-label-placement=""><span class="data-label">Выбрать</span></button>
                            </div>
                            <button data-toggle="dropdown" class="btn dropdown-toggle">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <input type="radio" id="EmployeeFilter_0" name="EmployeeFilter" value="0" checked>
                                    <label for="EmployeeFilter_0">Все</label>
                                </li>
                                <?php foreach($employees as $employee): ?>
                                    <li>
                                        <input type="radio" id="EmployeeFilter_<?= $employee['ID'] ?>" name="EmployeeFilter" value="<?= $employee['ID'] ?>">
                                        <label for="EmployeeFilter_<?= $employee['ID'] ?>"><?= $employee['SName'].' '.mb_substr($employee['FName'],0,1).'.'.mb_substr($employee['MName'],0,1) ?></label>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div></div>
                    <div></div>
                <?php endif ?>
                <div>
                    <button id="btnShow" class="btn assol-btn add form-control">ПРОСМОТРЕТЬ</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .site-name {
            color: #2067b0;
            font-weight: 700;
        }

        tr th:last-child {
            width: 20px;
        }

        tr td:last-child {
            padding: 0;
        }

        tr td:last-child a {
            padding: 0;
        }

        .done {
            width: 44px;
        }
    </style>

    <script id="westernTemplate" type="text/x-jquery-tmpl">
        <tr>
            <?php if ($isAdmin): ?>
            <td><span class="nobr">${Employees[EmployeeID]}</span></td>
            <?php endif ?>
            <td>${toClientDate(Date)}</td>
            <td>{{html Girl.trim().replace(" ","<br>")}}</td>
            <td>{{html Men.trim().replace(" ","<br>")}}</td>
            <td><span class="site-name">${Sites[SiteID]}</span></td>
            <td>${Sum}</td>
            <td>${Code}</td>
            <td id-western="${ID}">
                <div class="checkbox-line">
                    <label>
                        <input type="checkbox" <?= $isAdmin ? "class=\"action-western-send\"":"disabled=\"disabled\"" ?> {{if IsSend > 0}}checked{{/if}}>
                        <mark></mark>
                    </label>
                </div>
            </td>
            <? if (IS_LOVE_STORY): ?>
                <td id-western="${ID}">
                <div class="checkbox-line">
                    <label>
                        <input type="checkbox" <?= $isAdmin ? "class=\"action-western-per\"":"disabled=\"disabled\"" ?> {{if IsPer > 0}}checked{{/if}}>
                        <mark></mark>
                    </label>
                </div>
            </td>
            <? endif ?>
            <td class="centertext done" id-western="${ID}">
                <div class="round-check {{if IsDone > 0}}on{{else}}off <?= $isAdmin ? "action-western-done":"" ?>{{/if}}"></div>
            </td>
            <? if ($isEditWestern): ?>
                <td style="padding: 10px">
                    <a href="<?= base_url('services/western') ?>/${ID}/edit" data-toggle="modal" data-target="#remoteDialog" class="btn" role="button" title="Редактировать">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                    </a>
                </td>
            <? endif ?>
        </tr>
    </script>

    <? if ($isHideWesternAndMeeting): ?>
        <style>
            .western-block, .meeting-block {
                display: none;
            }
        </style>
    <? endif ?>

    <div class="service-block western-block">
        <div class="service-block-title">ВЕСТЕРНЫ</div>
        <div class="service-block-info-table">
            <table class="">
                <thead>
                    <tr>
                        <?php if ($isAdmin): ?>
                        <th>Сотрудник</th>
                        <?php endif ?>
                        <th>Дата</th>
                        <th>Девушка</th>
                        <th>Мужчина</th>
                        <th>Сайт</th>
                        <th>Сумма</th>
                        <th>Код</th>

                        <? if (IS_LOVE_STORY): ?>
                            <th>% Кли-ки</th>
                            <th>% Пер-ка</th>
                        <? else: ?>
                            <th>Выслали</th>
                        <? endif ?>

                        <th class="centertext">Вып.</th>
                        <? if ($isEditWestern): ?>
                            <th></th>
                        <? endif ?>
                    </tr>
                </thead>
                <tbody id="western-list"></tbody>
            </table>
        </div>
    </div>
    <div class="service-block-settings-btns western-block">
        <? if ($isAddWestern): ?>
        <a href="<?=base_url('services/western/add')?>" data-toggle="modal" data-target="#remoteDialog"
           class="" role="button" title="Добавить поле">
            <button class="btn assol-btn add right">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                ДОБАВИТЬ ПОЛЕ
            </button>
        </a>
        <? endif ?>
    </div>

    <script id="meetingTemplate" type="text/x-jquery-tmpl">
        <tr>
            <?php if ($isAdmin): ?>
            <td><span class="nobr">${Employees[EmployeeID]}</span></td>
            <?php endif ?>
            <td>${toClientDate(DateIn)}</td>
            <td>${toClientDate(DateOut)}</td>
            <td>{{html Girl.trim().replace(" ","<br>")}}</td>
            <td>{{html Men.trim().replace(" ","<br>")}}</td>
            <td><span class="site-name">${Sites[SiteID]}</span></td>
            <? if (IS_LOVE_STORY): ?>
                <td>
                    {{if UserTranslateDuring}}
                        {{html UserTranslateDuring.trim().replace(" ","<br>")}}
                    {{/if}}
                </td>
            <? else: ?>
                <td>{{html UserTranslate.trim().replace(" ","<br>")}}</td>
            <? endif ?>
            <td>${City}</td>
            <td>${Transfer}</td>
            <td>${Housing}</td>
            <td>${Translate}</td>
            <td class="centertext done" id-meeting="${ID}">
                <div class="round-check {{if IsDone > 0}}on{{else}}off <?= $isAdmin ? "action-meeting-done":"" ?>{{/if}}"></div>
            </td>
            <? if ($isEditMeeting): ?>
                <td style="padding: 10px">
                    <a href="<?= base_url('services/meeting') ?>/${ID}/edit" data-toggle="modal" data-target="#remoteDialog" class="btn" role="button" title="Редактировать">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                    </a>
                </td>
            <? endif ?>
        </tr>
    </script>
    <div class="service-block meeting-block">
        <div class="service-block-title">ВСТРЕЧИ</div>
        <div class="service-block-info-table">
            <table>
                <thead>
                    <tr>
                        <?php if ($isAdmin): ?>
                            <th>Сотрудник</th>
                        <?php endif ?>
                        <th>Приезда</th>
                        <th>Отъезда</th>
                        <th>Девушка</th>
                        <th>Мужчина</th>
                        <th>Сайт</th>
                        <th>Переводчик</th>
                        <th>Город</th>
                        <th>Трансф.</th>
                        <th>Жилье</th>
                        <th>Перевод</th>
                        <th class="centertext">Вып.</th>
                        <? if ($isEditMeeting): ?>
                            <th></th>
                        <? endif ?>
                    </tr>
                </thead>
                <tbody id="meeting-list"></tbody>
            </table>
        </div>
    </div>
    <div class="service-block-settings-btns meeting-block">
        <? if ($isAddMeeting): ?>
        <a href="<?=base_url('services/meeting/add')?>" data-toggle="modal" data-target="#remoteDialog"
           class="" role="button" title="Добавить поле">
            <button class="btn assol-btn add right">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                ДОБАВИТЬ ПОЛЕ
            </button>
        </a>
        <? endif ?>
    </div>

    <script id="deliveryTemplate" type="text/x-jquery-tmpl">
        <tr>
            <?php if ($isAdmin): ?>
            <td><span class="nobr">${Employees[EmployeeID]}</span></td>
            <?php endif ?>
            <td>${toClientDate(Date)}</td>
            <td><span class="site-name">${Sites[SiteID]}</span></td>
            <td>${Employees[UserTranslateID]}</td>
            <td>{{html Men.trim().replace(" ","<br>")}}</td>
            <td>{{html Girl.trim().replace(" ","<br>")}}</td>
            <td>${Delivery}</td>
            <td>${Gratitude}</td>
            <td class="centertext done" id-delivery="${ID}">
                <div class="round-check {{if IsDone > 0}}on{{else}}off <?= $isAdmin ? "action-delivery-done":"" ?>{{/if}}"></div>
            </td>
            <? if ($isEditDelivery): ?>
                <td style="padding: 10px">
                    <a href="<?= base_url('services/delivery') ?>/${ID}/edit" data-toggle="modal" data-target="#remoteDialog" class="btn" role="button" title="Редактировать">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                    </a>
                </td>
            <? endif ?>
        </tr>
    </script>
    <div class="service-block">
        <div class="service-block-title">ДОСТАВКА</div>
        <div class="service-block-info-table">
            <table>
                <thead>
                    <tr>
                        <?php if ($isAdmin): ?>
                            <th>Сотрудник</th>
                        <?php endif ?>
                        <th>Дата</th>
                        <th>Сайт</th>
                        <th>Переводчик</th>
                        <th>Мужчина</th>
                        <th>Девушка</th>
                        <th>Доставка</th>
                        <th>Благодарность</th>
                        <th class="centertext">Вып.</th>
                        <? if ($isEditDelivery): ?>
                            <th></th>
                        <? endif ?>
                    </tr>
                </thead>
                <tbody id="delivery-list"></tbody>
            </table>
        </div>
    </div>
    <div class="service-block-settings-btns">
        <? if ($isAddDelivery): ?>
        <a href="<?=base_url('services/delivery/add')?>" data-toggle="modal" data-target="#remoteDialog"
           class="" role="button" title="Добавить поле">
            <button class="btn assol-btn add right">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                ДОБАВИТЬ ПОЛЕ
            </button>
        </a>
        <? endif ?>
    </div>

</div>

<script>
    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
    });
</script>