<?php
    $isUserProfile = $employee['ID'] == $user['ID'];
    $roleUserProfile = $employee['UserRole'];

    $isDirectorProfile = ($employee['UserRole'] == USER_ROLE_DIRECTOR);
    $isSecretaryProfile = ($employee['UserRole'] == USER_ROLE_SECRETARY);
    $isTranslateProfile = ($employee['UserRole'] == USER_ROLE_TRANSLATE);
    $isEmployeeProfile = ($employee['UserRole'] == USER_ROLE_EMPLOYEE);

    $isDirector = $role['isDirector'];
    $isSecretary = $role['isSecretary'];
    $isTranslate = $role['isTranslate'];
    $isEmployee = $role['isEmployee'];

    /* Вкладку "Удаление" может видеть только директор */
    $isShowPageRemove = $isDirector;

    // Настройка возможности редактирование вкладок
    if (IS_LOVE_STORY) { /* На сайте Love story редактировать карточку может только директор */
        $isEditPersonalData     = ($isDirector);
        $isEditContacts         = ($isDirector);
        $isEditAccess           = ($isDirector);
        $isEditRole             = ($isDirector);
        $isEditWork             = ($isDirector);
        $isEditPhotoAndVideo    = ($isDirector);

        $isShowPagePersonalData = ($isDirector);
        $isShowPageContacts = ($isDirector);
        $isShowPageWork = true;
        $isShowPageWork_RoleAndSite = ($isDirector);
        $isShowPagePhotoAndVideo = ($isDirector);
    } else { /* На сайте Assol секретарь может редактировать вкладки "контакты" и “фото/видео” */
        $isEditPersonalData     = ($isDirector);
        $isEditContacts         = ($isDirector || $isSecretary);
        $isEditAccess           = ($isDirector);
        $isEditRole             = ($isDirector);
        $isEditWork             = ($isDirector || $isSecretary);
        $isEditPhotoAndVideo    = ($isDirector || $isSecretary);

        $isShowPagePersonalData = ($isDirector);
        $isShowPageContacts = ($isDirector || $isSecretary);
        $isShowPageWork = ($isDirector || $isSecretary);
        $isShowPageWork_RoleAndSite = true;
        $isShowPagePhotoAndVideo = ($isDirector || $isSecretary);
    }

?>


<div class="employee-profile-page">

<ol class="breadcrumb assol-grey-panel">
  <li><a href="<?= base_url('employee') ?>">Сотрудники</a></li>
  <li class="active"><?=$employee['SName']?> <?=$employee['FName']?></li>
</ol>



<script>
    var EmployeeID = <?=$employee['ID']?>;
    <? if ($isUserProfile): ?>
    var IsUserProfile = true;
    <? else: ?>
    var IsUserProfile = false;
    <? endif ?>

    var EmployeeRecord = <?= json_encode($employee) ?>;
    var EmployeeRights = <?= json_encode($rights) ?>;

    var Sites = {};
    <?php foreach($sites as $site): ?>
    Sites["<?= $site['ID'] ?>"] = "<?= empty($site['Name']) ? $site['Domen'] : $site['Name'] ?>";
    <?php endforeach ?>

    $(function () {
        <? if (!$isEditPersonalData): ?>
        $('#PersonalData').find('input, select, textarea').attr('disabled', 'disabled');

        if (IsUserProfile) {
            $('#SName, #FName, #MName, #DOB').removeAttr('disabled');
        }

        <? endif ?>
        <? if (!$isEditContacts): ?>
        $('#Contacts').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>

        <? if (!$isEditRole): ?>
        $('#UserRole').find('input').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditAccess): ?>
        $('#employeeAccess').find('input').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditWork): ?>
        $('#WorkSite').find('input').attr('disabled', 'disabled');
        <? endif ?>

        <? if (!$isEditPhotoAndVideo): ?>
        $('#PhotoAndVideo').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
    });
</script>


<div class="employee-card clear">

    <div class="employee-on-off <?= ($employee['IsOnline'] > 0) ? 'on' : 'off' ?>">
        <?= ($employee['IsOnline'] > 0) ? 'online' : 'offline' ?>
    </div>

    <div class="employee-img">
        <div class="employee-img-wrap">
            <div class="employee-img-in">
                <?php
                $isAvatar = !empty($employee['Avatar']);
                $avatar = $isAvatar
                    ? base_url("thumb/?src=/files/images/".$employee['FileName']."&w=152")
                    : base_url("public/img/avatar.jpeg")
                ?>
                <? if ($isAvatar): ?>
                <a href="<?= base_url("thumb/?src=/files/images/".$employee['FileName']) ?>" data-lightbox="AvatarCard">
                    <? endif; ?>
                    <img id="AvatarCard" src="<?= $avatar ?>" alt="avatar">
                    <? if ($isAvatar): ?>
                </a>
            <? endif; ?>
            </div>
        </div>
    </div>
    <div class="employee-info">
        <table>
            <tr>
                <td>
                    <div class="blue-style"><strong>ID:</strong> <span><?=sprintf("%'.07d", $employee['ID'])?></span></div>
                    <div><strong>Фамилия:</strong> <span> <?=$employee['SName']?></span></div>
                    <div><strong>Имя:</strong> <span> <?=$employee['FName']?></span></div>
                    <div><strong>Возраст:</strong> <span id="employeeAge"> не указан</span></div>

                    <?php if($employee['DOB']): ?>
                        <script>
                            $(document).ready(function(){
                                var age = -(moment('<?=$employee['DOB']?>').diff(moment(), 'years'));
                                $('#employeeAge').html(age + ' лет');
                            });
                        </script>
                    <?php endif ?>
                </td>
                <td>
                    <div><strong>Статус:</strong> <span class="blue-style"><?= ($employee['IsDeleted'] > 0 ? "удален" : ($employee['IsBlocked'] > 0 ? "заблокированный" : "активный")) ?></span></div>
                    <div><strong>Создана:</strong> <span><?= toClientDateTime($employee['DateCreate']) ?></span></div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="tabs-info-fields">
    <div class="assol-tabs">
        <div class="assol-tabs-line">
            <div>
                <ul class="assol-tabs-btns nav nav-tabs clear" role="tablist">
                    <? if($isShowPagePersonalData): ?>
                    <li role="presentation" class="active">
                        <a href="#PersonalData" aria-controls="PersonalData" role="tab" data-toggle="tab">
                            Личные данные
                        </a>
                    </li>
                    <? endif ?>

                    <? if($isShowPageContacts): ?>
                    <li role="presentation">
                        <a href="#Contacts" aria-controls="Contacts" role="tab" data-toggle="tab">
                            Контакты
                        </a>
                    </li>
                    <? endif ?>

                    <? if($isShowPageWork): ?>
                    <li role="presentation">
                        <a href="#Work" aria-controls="Work" role="tab" data-toggle="tab">
                            Работа
                        </a>
                    </li>
                    <? endif ?>

                    <? if($isShowPageRemove): ?>
                    <li role="presentation">
                        <a href="#Remove" aria-controls="Remove" role="tab" data-toggle="tab">
                            <? if (IS_LOVE_STORY): ?>
                                Редактирование
                            <? else: ?>
                                Удаление
                            <? endif ?>
                        </a>
                    </li>
                    <? endif ?>

                    <? if($isShowPagePhotoAndVideo): ?>
                    <li role="presentation">
                        <a href="#PhotoAndVideo" aria-controls="PhotoAndVideo" role="tab" data-toggle="tab">
                            <? if (IS_LOVE_STORY): ?>
                            Документация
                            <? else: ?>
                            Фото / Видео
                            <? endif ?>
                        </a>
                    </li>
                    <? endif ?>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <?php if($isShowPagePersonalData): ?>
            <div role="tabpanel" class="tab-pane active" id="PersonalData">
                <? if(IS_LOVE_STORY && $isDirector): ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="CardNumber">Номер карточки</label>
                                <input type="text" class="assol-input-style" id="CardNumber" placeholder="Номер карточки" value="<?=$employee['CardNumber']?>">
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                <? endif; ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="SName">Фамилия</label>
                            <input type="text" class="assol-input-style" id="SName" placeholder="Фамилия" value="<?=$employee['SName']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="FName">Имя</label>
                            <input type="text" class="assol-input-style" id="FName" placeholder="Имя" value="<?=$employee['FName']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MName">Отчество</label>
                            <input type="text" class="assol-input-style" id="MName" placeholder="Отчество" value="<?=$employee['MName']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="DOB">Дата рождения</label>
                            <div class="date-field">
                                <input type="text" class="assol-input-style" id="DOB" placeholder="Дата рождения" value="<?= toClientDate($employee['DOB']) ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MaritalStatus">Семейное положение</label>
                            <div class="btn-group assol-select-dropdown" id="MaritalStatus">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="MaritalStatus_0" name="MaritalStatus" value="0">
                                        <label for="MaritalStatus_0">
                                            <span class="data-label">Выбрать</span>
                                        </label>
                                    </li>
                                    <?php foreach($marital as $item): ?>
                                        <?php $isSelected = $item['ID']== $employee['MaritalStatus']; ?>
                                        <li>
                                            <input type="radio" id="MaritalStatus_<?=$item['ID']?>" name="MaritalStatus" <?= $isSelected ? 'checked="checked"':'' ?> value="<?=$item['ID']?>">
                                            <label for="MaritalStatus_<?=$item['ID']?>">
                                                <span class="data-label"><?=$item['ReferenceValue']?></span>
                                            </label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="NameSatellite">ФИ спутника</label>
                            <input type="text" class="assol-input-style" id="NameSatellite" placeholder="ФИ спутника" value="<?=$employee['NameSatellite']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="OccupationSatellite">Род деятельности</label>
                            <input type="text" class="assol-input-style" id="OccupationSatellite" placeholder="Род деятельности" value="<?=$employee['OccupationSatellite']?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="NameFather">ФИО отца</label>
                            <input type="text" class="assol-input-style" id="NameFather" placeholder="ФИО отца" value="<?=$employee['NameFather']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="OccupationFather">Род деятельности</label>
                            <input type="text" class="assol-input-style" id="OccupationFather" placeholder="Род деятельности" value="<?=$employee['OccupationFather']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="NameMother">ФИ матери</label>
                            <input type="text" class="assol-input-style" id="NameMother" placeholder="ФИ матери" value="<?=$employee['NameMother']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="OccupationMother">Род деятельности</label>
                            <input type="text" class="assol-input-style" id="OccupationMother" placeholder="Род деятельности" value="<?=$employee['OccupationMother']?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <script id="relativeTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="RelativeFIO_${ID}" placeholder="Брат / Сестра ФИ" value="${FIO}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <input type="text" class="assol-input-style" id="RelativeOccupation_${ID}" placeholder="Род деятельности" value="${Occupation}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                        <? if ($isEditPersonalData): ?>
                                            {{if ID > 0}}
                                            <button record="${ID}" class="btn assol-btn save action-save-relative" title="Сохранить запись">
                                                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                            </button>
                                            <button record="${ID}" class="btn assol-btn remove action-remove-relative" title="Удалить запись">
                                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                            </button>
                                            {{else}}
                                            <button record="${ID}" class="btn assol-btn add action-save-relative" title="Добавить запись">
                                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                            </button>
                                            {{/if}}
                                        <? endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </script>
                        <div class="form-group">
                            <div class="row customer-children">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Брат / Сестра ФИ</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Род деятельности</label>
                                    </div>
                                </div>
                            </div>

                            <div id="relativeList"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-9">
                        <script id="childrenTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="ChildrenFIO_${ID}" placeholder="Дети ФИ" value="${FIO}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="date-field">
                                        <input type="text" class="assol-input-style" id="ChildrenDOB_${ID}" placeholder="Дата рождения" value="${toClientDate(DOB)}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="btn-group assol-select-dropdown" id="ChildrenSex_${ID}">
                                                    <div class="label-placement-wrap">
                                                        <button class="btn" data-label-placement>Выбрать</button>
                                                    </div>
                                                    <button data-toggle="dropdown" class="btn dropdown-toggle">
                                                        <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <input type="radio" id="ChildrenSex_${ID}_0" name="ChildrenSex_${ID}" value="0" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                                            <label for="ChildrenSex_${ID}_0">
                                                                <span class="data-label">Выбрать</span>
                                                            </label>
                                                        </li>
                                                        <?php foreach($child_sex as $item): ?>
                                                            <li>
                                                                <input type="radio" id="ChildrenSex_${ID}_<?=$item['ID']?>" name="ChildrenSex_${ID}" {{if (<?=$item['ID']?> == SexID)}} checked="checked" {{/if}} value="<?=$item['ID']?>" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                                                <label for="ChildrenSex_${ID}_<?=$item['ID']?>">
                                                                    <span class="data-label"><?=$item['ReferenceValue']?></span>
                                                                </label>
                                                            </li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                        <? if ($isEditPersonalData): ?>
                                            {{if ID > 0}}
                                            <button record="${ID}" class="btn assol-btn save action-save-children" title="Сохранить запись">
                                                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                            </button>
                                            <button record="${ID}" class="btn assol-btn remove action-remove-children" title="Удалить запись">
                                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                            </button>
                                            {{else}}
                                            <button record="${ID}" class="btn assol-btn add action-save-children" title="Добавить запись">
                                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                            </button>
                                            {{/if}}
                                        <? endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </script>
                        <div class="form-group">
                            <div class="row customer-children">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="ChildrenFI">Дети ФИ</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="ChildrenDOB">Дата рождения</label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="ChildrenSex">Пол</label>
                                    </div>
                                </div>
                            </div>

                            <div id="childrenList"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Forming">Образование</label>
                            <div class="btn-group assol-select-dropdown" id="Forming">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="Forming_0" name="Forming" value="0">
                                        <label for="Forming_0">
                                            <span class="data-label">Выбрать</span>
                                        </label>
                                    </li>
                                    <?php foreach($forming as $item): ?>
                                        <?php $isSelected = $item['ID']== $employee['Forming']; ?>
                                        <li>
                                            <input type="radio" id="Forming_<?=$item['ID']?>" name="Forming" <?= $isSelected ? 'checked="checked"':'' ?> value="<?=$item['ID']?>">
                                            <label for="Forming_<?=$item['ID']?>">
                                                <span class="data-label"><?=$item['ReferenceValue']?></span>
                                            </label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="FormingNameInstitution">Название заведения</label>
                            <input type="text" class="assol-input-style" id="FormingNameInstitution" placeholder="Название заведения" value="<?=$employee['FormingNameInstitution']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="FormingFormStudy">Форма обучения</label>
                            <div class="btn-group assol-select-dropdown" id="FormingFormStudy">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="FormingFormStudy_0" name="FormingFormStudy" value="0">
                                        <label for="FormingFormStudy_0">
                                            <span class="data-label">Выбрать</span>
                                        </label>
                                    </li>
                                    <?php foreach($forming_form as $item): ?>
                                        <?php $isSelected = $item['ID']== $employee['FormingFormStudy']; ?>
                                        <li>
                                            <input type="radio" id="FormingFormStudy_<?=$item['ID']?>" name="FormingFormStudy" <?= $isSelected ? 'checked="checked"':'' ?> value="<?=$item['ID']?>">
                                            <label for="FormingFormStudy_<?=$item['ID']?>">
                                                <span class="data-label"><?=$item['ReferenceValue']?></span>
                                            </label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="FormingFaculty">Факультет</label>
                            <input type="text" class="assol-input-style" id="FormingFaculty" placeholder="Факультет" value="<?=$employee['FormingFaculty']?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Smoking">Курение</label>
                            <div id="Smoking">
                                
                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Smoking" id="Smoking_1" value="1" <?=($employee['Smoking']==1)?'checked':''?>>
                                        <span>
                                            <strong>Да:</strong>
                                        </span>
                                        <mark></mark>
                                    </label>
                                </div>

                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Smoking" id="Smoking_2" value="2" <?=($employee['Smoking']==2)?'checked':''?>>
                                        <span>
                                            <strong> Нет:</strong>
                                        </span>
                                        <mark></mark>
                                    </label>
                                </div>

                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Smoking" id="Smoking_3" value="3" <?=($employee['Smoking']==3)?'checked':''?>>
                                        <span>
                                            <strong> Иногда:</strong>
                                        </span>
                                        <mark></mark>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="WorkOccupation">Последнее место работы</label>
                            <input type="text" class="assol-input-style" id="WorkOccupation" placeholder="Последнее место работы" value="<?=$employee['WorkOccupation']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="WorkReasonLeaving">Причина увольнения</label>
                            <input type="text" class="assol-input-style" id="WorkReasonLeaving" placeholder="Причина увольнения" value="<?=$employee['WorkReasonLeaving']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="WorkLatestDirector">Последний директор</label>
                            <input type="text" class="assol-input-style" id="WorkLatestDirector" placeholder="Директор последней работы" value="<?=$employee['WorkLatestDirector']?>">
                        </div>
                    </div>
                </div>

                <? if ($isEditWork || (IS_LOVE_STORY && $isUserProfile)): ?>
                <button id="SavePersonalData" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
                <? endif ?>
            </div>
            <? endif ?>

            <?php if($isShowPageContacts): ?>
            <div role="tabpanel" class="tab-pane" id="Contacts">
                <div class="row">
                    <? if (IS_LOVE_STORY): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="Country">Страна</label>
                                <input type="text" class="assol-input-style" id="Country" placeholder="Страна" value="<?=$employee['Country']?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="City">Город</label>
                                <input type="text" class="assol-input-style" id="City" placeholder="Город" value="<?=$employee['City']?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="HomeAddress">Домашний адресс</label>
                                <input type="text" class="assol-input-style" id="HomeAddress" placeholder="Домашний адресс" value="<?=$employee['HomeAddress']?>">
                            </div>
                        </div>
                    <? else: ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="City">Город</label>
                                <input type="text" class="assol-input-style" id="City" placeholder="Город" value="<?=$employee['City']?>">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="HomeAddress">Домашний адресс</label>
                                <input type="text" class="assol-input-style" id="HomeAddress" placeholder="Домашний адресс" value="<?=$employee['HomeAddress']?>">
                            </div>
                        </div>
                    <? endif ?>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <script id="phoneTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="Phone_${ID}" placeholder="Телефон" value="${Phone}">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <? if ($isEditContacts): ?>
                                    {{if ID > 0}}
                                    <button record="${ID}" class="btn assol-btn save action-save-phone" title="Сохранить запись">
                                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                    </button>
                                    <button record="${ID}" class="btn assol-btn remove action-remove-phone" title="Удалить запись">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                    {{else}}
                                    <button record="${ID}" class="btn assol-btn add action-save-phone" title="Добавить запись">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                    {{/if}}
                                    <? endif ?>
                                </div>
                            </div>
                        </script>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Телефон</label>
                                </div>
                            </div>
                            <div id="phoneList"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <script id="emailTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="Email_${ID}" placeholder="E-Mail" value="${Email}">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <? if ($isEditContacts): ?>
                                    {{if ID > 0}}
                                    <button record="${ID}" class="btn assol-btn save action-save-email" title="Сохранить запись">
                                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                    </button>
                                    <button record="${ID}" class="btn assol-btn remove action-remove-email" title="Удалить запись">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                    {{else}}
                                    <button record="${ID}" class="btn assol-btn add action-save-email" title="Добавить запись">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                    {{/if}}
                                    <? endif ?>
                                </div>
                            </div>
                        </script>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>E-Mail</label>
                                </div>
                            </div>
                            <div id="emailList"></div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <script id="skypeTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="Skype_${ID}" placeholder="Skype" value="${Skype}">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <? if ($isEditContacts): ?>
                                    {{if ID > 0}}
                                    <button record="${ID}" class="btn assol-btn save action-save-skype" title="Сохранить запись">
                                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                    </button>
                                    <button record="${ID}" class="btn assol-btn remove action-remove-skype" title="Удалить запись">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                    {{else}}
                                    <button record="${ID}" class="btn assol-btn add action-save-skype" title="Добавить запись">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                    {{/if}}
                                    <? endif ?>
                                </div>
                            </div>
                        </script>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Skype</label>
                                </div>
                            </div>
                            <div id="skypeList"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <script id="socnetTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="Socnet_${ID}" placeholder="Соцсети" value="${Profile}">
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <? if ($isEditContacts): ?>
                                    {{if ID > 0}}
                                    <button record="${ID}" class="btn assol-btn save action-save-socnet" title="Сохранить запись">
                                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                    </button>
                                    <button record="${ID}" class="btn assol-btn remove action-remove-socnet" title="Удалить запись">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                    </button>
                                    {{else}}
                                    <button record="${ID}" class="btn assol-btn add action-save-socnet" title="Добавить запись">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                    {{/if}}
                                    <? endif ?>
                                </div>
                            </div>
                        </script>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Соцсети</label>
                                </div>
                            </div>
                            <div id="socnetList"></div>
                        </div>
                    </div>
                </div>

                <button id="SaveContacts" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
            </div>
            <? endif ?>

            <?php if($isShowPageWork): ?>
            <div role="tabpanel" class="tab-pane" id="Work">
                <?php if($isShowPageWork_RoleAndSite): ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="UserRole">Должность</label>
                            <div class="btn-group assol-select-dropdown" id="UserRole">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <input type="radio" id="UserRole_0" name="UserRole" value="0">
                                        <label for="UserRole_0">Выбрать</label>
                                    </li>
                                    <?php foreach($user_role as $item): ?>
                                        <?php $isSelected = $item['ID']== $employee['UserRole']; ?>
                                        <li>
                                            <input type="radio" id="UserRole_<?=$item['ID']?>" name="UserRole" <?= $isSelected ? 'checked="checked"':'' ?> value="<?=$item['ID']?>">
                                            <label for="UserRole_<?=$item['ID']?>"><?=$item['ReferenceValue']?></label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="WorkSite">Сайт</label>
                            <div class="btn-group assol-select-dropdown" id="WorkSite">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <?php foreach($sites as $item): ?>
                                        <li>
                                            <input type="checkbox" id="WorkSite_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                                            <label for="WorkSite_<?= $item['ID'] ?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                                <script>
                                    $.each(<?= json_encode($work_sites) ?>, function(key, site) {
                                        $('#WorkSite_'+site.SiteID).click();
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>

                <? if ($isDirector && ($isEmployeeProfile || $isTranslateProfile)): ?>
                    <div class="form-group">
                        <label for="Name">Доступ к сотрудникам</label>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="btn-group assol-select-dropdown" id="employeeAccess">
                                    <div class="label-placement-wrap">
                                        <button class="btn" data-label-placement>Только директор и секретарь</button>
                                    </div>
                                    <button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <? foreach($employees as $item): ?>
                                            <li>
                                                <input type="checkbox" id="employeeAccess_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                                                <label for="employeeAccess_<?= $item['ID'] ?>"><?= $item['SName'] ?> <?= $item['FName'] ?></label>
                                            </li>
                                        <? endforeach ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button id="SaveEmployeeAccess" class="btn assol-btn save" title="Сохранить изменения">
                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                <? endif ?>

                <script id="siteTemplate" type="text/x-jquery-tmpl">
                    <div class="work-sites-block" id-work-site="${ID}" id-site="${SiteID}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="site-item">
                                        <span class="glyphicon glyphicon-remove-circle <? if ($isEditWork): ?> action-remove-site <? endif ?>" aria-hidden="true" title="Удалить сайт"></span>
                                        <span>${Sites[SiteID]}</span>
                                        <div class="arrow">
                                            <div class="arrow-in"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <div class="user-id-field-wrap">
                                                    ID
                                                    <div class="user-id-field">
                                                        <input type="text" class="assol-input-style user-id-input" id="" placeholder="" value="" <? if (!$isEditWork): ?> disabled="disabled" <? endif ?>>
                                                        <div class="user-id-tooltip"> <!-- Появляется на фокус поля, но можно єто и убрать.... -->
                                                            <div id="UserIdField_${SiteID}" class="tooltip-content">
                                                                <a href="javascript: void(0);" class="action-append-customer" id-customer="0">Выбрать всех</a>
                                                            </div>
                                                            <div class="arrow"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <div class="assol-btn-style clients-list">
                                                    <ul id="ClientsList_${ID}"></ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </script>

                <script id="clientsTemplate" type="text/x-jquery-tmpl">
                    <li class="clients-item">
                        <span class="glyphicon glyphicon-remove-circle <? if ($isEditWork): ?> action-remove-customer <? endif ?>" record="${ID}" aria-hidden="true" title="Удалить клиента из списка"></span>
                        <a href="javascript: void(0);">${SName} ${FName}</a>
                    </li>
                </script>

                <script id="userIdFieldTemplate" type="text/x-jquery-tmpl">
                    {{if SiteExists}}
                        <a href="javascript: void(0);" class="action-append-customer" id-customer="${ID}">${SName} ${FName}</a>
                    {{else}}
                        ${SName} ${FName}
                        <p style="color: red">не закреплена</p>
                    {{/if}}
                </script>

                <div id="siteList" class="work-sites-block-wrap"></div>
                <? endif ?>

                <? if (IS_LOVE_STORY): ?>
                    <div class="schedule-table-wrap">
                        <p class="my-schedule">График работы</p>

                        
                        <div class="schedule-table">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Дни работы</th>
                                    <th>Часы работы</th>
                                    <th>Примечание</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Понедельник</td>
                                    <td class="schedule-data" id="Monday"><?= $schedule['Monday'] ?></td>
                                    <td class="schedule-data" id="MondayNote"><?= $schedule['MondayNote'] ?></td>
                                </tr>
                                <tr>
                                    <td>Вторник</td>
                                    <td class="schedule-data" id="Tuesday"><?= $schedule['Tuesday'] ?></td>
                                    <td class="schedule-data" id="TuesdayNote"><?= $schedule['TuesdayNote'] ?></td>
                                </tr>
                                <tr>
                                    <td>Среда</td>
                                    <td class="schedule-data" id="Wednesday"><?= $schedule['Wednesday'] ?></td>
                                    <td class="schedule-data" id="WednesdayNote"><?= $schedule['WednesdayNote'] ?></td>
                                </tr>
                                <tr>
                                    <td>Четверг</td>
                                    <td class="schedule-data" id="Thursday"><?= $schedule['Thursday'] ?></td>
                                    <td class="schedule-data" id="ThursdayNote"><?= $schedule['ThursdayNote'] ?></td>
                                </tr>
                                <tr>
                                    <td>Пятница</td>
                                    <td class="schedule-data" id="Friday"><?= $schedule['Friday'] ?></td>
                                    <td class="schedule-data" id="FridayNote"><?= $schedule['FridayNote'] ?></td>
                                </tr>
                                <tr>
                                    <td>Суббота</td>
                                    <td class="schedule-data" id="Saturday"><?= $schedule['Saturday'] ?></td>
                                    <td class="schedule-data" id="SaturdayNote"><?= $schedule['SaturdayNote'] ?></td>
                                </tr>
                                <tr>
                                    <td>Воскресенье</td>
                                    <td class="schedule-data" id="Sunday"><?= $schedule['Sunday'] ?></td>
                                    <td class="schedule-data" id="SundayNote"><?= $schedule['SundayNote'] ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <script>
                        var isEdit = true;

                        updateTableMode();

                        function updateTableMode() {
                            $('.schedule-data').each(function(index, cell) {
                                if (cell.contentEditable != null) {
                                    $(cell).attr("contentEditable", isEdit);
                                } else {
                                    if (isEdit) {
                                        $(cell).html("<input type='text' style='width: 100%' value='"+$(cell).html()+"'>");
                                    } else {
                                        $(cell).html($(cell).find('input').val());
                                    }
                                }
                            });
                        }
                    </script>
                <? endif ?>

                <? if ($isEditWork || (IS_LOVE_STORY && $isUserProfile)): ?>
                <button id="SaveWork" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
                <? endif ?>

            </div>
            <? endif ?>

            <?php if($isShowPageRemove): ?>
            <div role="tabpanel" class="tab-pane" id="Remove">

                <div class="remove-navs-wrap">
                    <div class="pseudo-table-wrap">
                        <div class="pseudo-table">
                            <div class="pseudo-td content-width">
                                <button id="EmployeeMarkRemove" class="btn assol-btn remove form-control">Удалить</button>
                            </div>
                            <div class="pseudo-td content-width">
                                <button id="EmployeeRemove" class="btn assol-btn remove form-control">УДАЛИТЬ БЕЗВОЗВРАТНО</button>
                            </div>
                            <div class="pseudo-td">
                                <? if($employee['IsDeleted']): ?>
                                    Удалено: <?= date_format(date_create($employee['DateDeleted']), 'Y-m-d') ?>
                                <? endif ?>
                            </div>
                            <div class="pseudo-td content-width">
                                <button id="EmployeeRestore" class="btn assol-btn add form-control">ВОССТАНОВИТЬ</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="ReasonForDeleted">Причина удаления</label>
                        <textarea class="assol-input-style vertical-center" id="ReasonForDeleted" rows="3" placeholder="Причина удаления"><?=$employee['ReasonForDeleted']?></textarea>
                    </div>
                </div>

                <hr>

                <? if (IS_LOVE_STORY): ?>
                    <div class="remove-navs-wrap">
                        <div class="pseudo-table-wrap">
                            <div class="pseudo-table">
<!--                                <div class="pseudo-td content-width">-->
<!--                                    <button id="EmployeeMarkRemove" class="btn assol-btn remove form-control">Заблокировать</button>-->
<!--                                </div>-->
                                <div class="checkbox-line" style="margin-left: 6px;">
                                    <label>
                                        <input class="action-blocked-employee" type="checkbox" <? if ($employee['IsBlocked'] > 0): ?> checked <? endif ?>>
                                        <mark></mark>
                                        <span><strong>Заблокировать</strong></span>
                                    </label>
                                </div>

                                <div class="pseudo-td">
                                    <div class="remove-txt nobr">
                                        Заблокировано: <a href="javascript: void(0);">Administrator</a>
                                        <span class="date">24.08.2015</span>
                                    </div>
                                </div>
<!--                                <div class="pseudo-td content-width">-->
<!--                                    <button id="EmployeeRestore" class="btn assol-btn add form-control">ВОССТАНОВИТЬ</button>-->
<!--                                </div>-->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ReasonForBlocked">Причина блокировки</label>
                            <textarea class="assol-input-style vertical-center" id="ReasonForBlocked" rows="3" placeholder="Причина блокировки"><?=$employee['ReasonForBlocked']?></textarea>
                        </div>
                    </div>

                    <br>
                    <br>

                <? else: ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="Login">Логин</label>
                                <input type="text" class="assol-input-style" id="Login" placeholder="Логин" value="<?=$employee['ID']?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Password">Пароль</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="assol-input-style" id="Password" placeholder="Пароль" value="<?=$employee['Password']?>">
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn assol-btn" id="EmployeeSavePassword">изменить пароль</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <? endif ?>

                <button id="SaveRemove" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
            </div>
            <? endif ?>

            <?php if($isShowPagePhotoAndVideo): ?>
            <div role="tabpanel" class="tab-pane" id="PhotoAndVideo">
                <div class="row">
                    <div class="col-md-4 photo-block">
                        <style>
                            .avatar-file .input-group {
                                display: block;
                            }

                            .employee-avatar-in img {
                                width: 100%;
                            }
                        </style>

                        <div class="employee-avatar-wrap">
                            <div class="employee-avatar-in">
                                <?php
                                $isAvatar = !empty($employee['Avatar']);
                                $avatar = $isAvatar
                                    ? base_url("thumb/?src=/files/images/".$employee['FileName']."&w=221")
                                    : base_url('public/img/avatar-example.png')
                                ?>
                                <? if ($isAvatar): ?>
                                    <a href="<?= base_url("thumb/?src=/files/images/".$employee['FileName']) ?>" data-lightbox="Avatar">
                                        <? endif; ?>
                                        <img id="AvatarBig" src="<?= $avatar ?>" alt="avatar">
                                        <? if ($isAvatar): ?>
                                    </a>
                                <? endif; ?>
                            </div>
                        </div>

                        <? if ($isEditPhotoAndVideo): ?>
                        <form id="AvatarForm" action="<?= base_url('employee/'.$employee['ID'].'/avatar') ?>" class="avatar-file" method="post">
                            <input type="file" id="addEmployeeAvatar" name="thumb">
                            <script>
                                $(function () {
                                    $("#addEmployeeAvatar").filestyle({
                                        input: false,
                                        buttonText: "Загрузить фотографию",
                                        buttonName: "btn assol-btn doc file",
                                        iconName: "glyphicon glyphicon-paperclip"
                                    });
                                });
                            </script>
                        </form>
                        <? endif ?>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="docs-video-block clear">
                                <div class="form-group passport-group">
                                    <label>Документы</label>
                                    <script id="passportTemplate" type="text/x-jquery-tmpl">
                                        <li id="Passport_${ID}">
                                            <div class="btn assol-btn doc">
                                                <a href="<?= base_url('employee/' . $employee['ID']) ?>/passport/${ID}/load" target="_blank" title="Скачать документ{{if Name}} '${Name}'{{/if}}">
                                                    <? if (IS_LOVE_STORY): ?>
                                                        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>scan-${ID}.${ext}</a>
                                                    <? else: ?>
                                                        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>{{if Name}}${Name}{{else}}scan-${ID}.${ext}{{/if}}</a>
                                                    <? endif ?>
                                                <span record="${ID}" class="glyphicon glyphicon-remove-circle action-remove-passport" aria-hidden="true" title="Удалить документ{{if Name}} '${Name}'{{/if}}"></span>
                                            </div>
                                        </li>
                                    </script>

                                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="PassportUploadLabel">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="PassportUploadLabel">Загрузка документов</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <iframe src="" frameborder="0"></iframe>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <style>
                                        .passport-group .modal-dialog {
                                            width: 90%;
                                            background: white;
                                            max-width: 1180px;
                                        }

                                        .passport-group .modal-title {
                                            float: left;
                                        }

                                        .passport-group iframe {
                                            width: 100%;
                                            height: 600px;
                                        }
                                    </style>

                                    <script>
                                        $(document).on('click', '#btnPassportUpload', function () {

                                            var form = $(this).closest('.form-group');

                                            var modal = form.find('.modal');
                                            var frame = form.find('iframe');
                                            var frameSrc = '<?=base_url('employee/'.$employee['ID'].'/passport/upload')?>';

                                            modal.on('show.bs.modal', function () {
                                                frame.attr("src", frameSrc);
                                            });
                                            modal.on('hidden.bs.modal', function () {
                                                $.EmployeeCard.ReloadPassportList();
                                            });
                                            modal.modal({show:true});
                                        });
                                    </script>

                                    <div>
                                        <div class="col">
                                            <ul id="passport" class="list-inline"></ul>
                                        </div>
                                        <div class="col">
                                            <? if ($isEditPhotoAndVideo): ?>
                                            <a href="javascript:void(0)" id="btnPassportUpload" class="btn assol-btn add" title="Загрузить документы">
                                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                            </a>
                                            <? endif ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group agreement-group">
                                    <label>Договор</label>

                                    <script id="agreementTemplate" type="text/x-jquery-tmpl">
                                        <li id="Agreement_${ID}">
                                            <div class="btn assol-btn doc">
                                                <a href="<?= base_url('employee/' . $employee['ID']) ?>/agreement/${ID}/load" target="_blank" title="Скачать документ{{if Name}} '${Name}'{{/if}}">
                                                    <? if (IS_LOVE_STORY): ?>
                                                        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>doc-${ID}.${ext}</a>
                                                    <? else: ?>
                                                        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>{{if Name}}${Name}{{else}}doc-${ID}.${ext}{{/if}}</a>
                                                    <? endif ?>
                                                <span record="${ID}" class="glyphicon glyphicon-remove-circle action-remove-agreement" aria-hidden="true" title="Удалить документ{{if Name}} '${Name}'{{/if}}"></span>
                                            </div>
                                        </li>
                                    </script>

                                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="AgreementUploadLabel">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="AgreementUploadLabel">Загрузка документов</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <iframe src="" frameborder="0"></iframe>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <style>
                                        .agreement-group .modal-dialog {
                                            width: 90%;
                                            background: white;
                                            max-width: 1180px;
                                        }

                                        .agreement-group .modal-title {
                                            float: left;
                                        }

                                        .agreement-group iframe {
                                            width: 100%;
                                            height: 600px;
                                        }
                                    </style>

                                    <script>
                                        $(document).on('click', '#btnAgreementUpload', function () {

                                            var form = $(this).closest('.form-group');

                                            var modal = form.find('.modal');
                                            var frame = form.find('iframe');
                                            var frameSrc = '<?=base_url('employee/'.$employee['ID'].'/agreement/upload')?>';

                                            modal.on('show.bs.modal', function () {
                                                frame.attr("src", frameSrc);
                                            });
                                            modal.on('hidden.bs.modal', function () {
                                                $.EmployeeCard.ReloadAgreementList();
                                            });
                                            modal.modal({show:true});
                                        });
                                    </script>

                                    <div>
                                        <div class="col">
                                            <ul id="agreement" class="list-inline"></ul>
                                        </div>
                                        <div class="col">
                                            <? if ($isEditPhotoAndVideo): ?>
                                                <a href="javascript:void(0)" id="btnAgreementUpload" class="btn assol-btn add" title="Загрузить документы">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </a>
                                            <? endif ?>
                                        </div>
                                    </div>
                                </div>
                                <? if (!IS_LOVE_STORY): ?>
                                <div class="form-group youtube-video-code">
                                    <label for="VideoConfirm">Видео подтверждение (поле для вставки кода с youtube)</label>
                                    <div>
                                        <input type="text" class="assol-input-style" id="VideoConfirm" placeholder="Код с youtube" value="<?= $employee['VideoConfirm'] ?>">
                                    </div>
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe id="videoConfirmFrame" class="embed-responsive-item" src="<?= $employee['VideoConfirm'] ?>"></iframe>
                                    </div>
                                </div>
                                <? else: ?>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="Login">Логин</label>
                                                <input type="text" class="assol-input-style" id="Login" placeholder="Логин" value="<?=$employee['ID']?>" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="Password">Пароль</label>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <input type="text" class="assol-input-style" id="Password" placeholder="Пароль" value="<?=$employee['Password']?>">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <? if ($isEditPhotoAndVideo): ?>
                                                        <button class="btn assol-btn" id="EmployeeSavePassword">изменить пароль</button>
                                                        <? endif ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <? endif ?>
                            </div>
                        </div>
                    </div>
                </div>
                <? if (!IS_LOVE_STORY): ?>
                <br>
                <button id="SavePhotoAndVideo" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
                <? endif ?>
            </div>
            <? endif ?>
        </div>

        <script>
            $(function (){
                $('[role="tab"]').first().click();
            });
        </script>

        <div style="margin: 15px">
            <div id="alertError" class="alert alert-danger" role="alert" style="display: none">
                <h4>Ошибка!</h4>
                <p id="alertErrorMessage"></p>
            </div>
            <div id="alertSuccess" class="alert alert-success" role="alert" style="display: none">
                <p id="alertSuccessMessage"></p>
            </div>
        </div>
    </div>
</div>

</div>