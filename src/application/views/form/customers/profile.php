<?php

    $isDirector = $role['isDirector'];
    $isSecretary = $role['isSecretary'];
    $isTranslate = $role['isTranslate'];
    $isEmployee = $role['isEmployee'];

    /* Вкладку "Удаление" может видеть только директор */
    $isShowRemove                   = ($isDirector );

    // Настройка возможности редактирование вкладок
    $isEditPersonalData             = ($isDirector || $isSecretary);
    $isEditSelfDescription          = ($isDirector || $isSecretary);
    $isEditPhotoAndVideo            = ($isDirector || $isSecretary);
    $isEditSites                    = ($isDirector || $isSecretary);

    if (IS_LOVE_STORY) { /* На сайте Love story редактировать карточку клиенток могут только секретарь и директор */
        $isEditQuestions                = ($isDirector || $isSecretary);
        $isEditAdditionallyPane         = ($isDirector || $isSecretary);
        $isEditReservationContactPane   = ($isDirector || $isSecretary);
        $isEditStory                    = ($isDirector || $isSecretary || $isTranslate);
        $isEditVideo                    = ($isDirector || $isSecretary);
        $isEditPhoto                    = ($isDirector || $isSecretary);
        $isEditSitesDescription         = ($isDirector || $isSecretary);
        $isEditQuestionPhoto            = false;
        $isEditMens                     = ($isDirector || $isSecretary || $isTranslate);
        $isDeleteMens                   = ($isDirector || $isSecretary);

        $isShowPassportSection = ($isDirector || $isSecretary);
    } else { /* На сайте Assol переводчик может редактировать вкладки: дополнительно, история, заказ контактов, вопросы */
        $isEditQuestions                = ($isDirector || $isSecretary || $isTranslate);
        $isEditAdditionallyPane         = ($isDirector || $isSecretary || $isTranslate);
        $isEditReservationContactPane   = ($isDirector || $isSecretary || $isTranslate);
        $isEditStory                    = ($isDirector || $isSecretary || $isTranslate);
        $isEditQuestionPhoto            = ($isDirector || $isSecretary);
        $isEditVideo = false;
        $isEditPhoto = false;
        $isEditSitesDescription         = ($isDirector || $isSecretary || $isTranslate);

        $isShowPassportSection = true;
    }

    $employee_sites = (!empty($employee_sites)) ? $employee_sites : array();
?>

<div class="client-profile-page">

<ol class="breadcrumb assol-grey-panel">
  <li><a href="<?= base_url('customer') ?>">Клиенты</a></li>
  <li class="active"><?=$customer['SName']?> <?=$customer['FName']?></li>
</ol>

<script>
    var CustomerID = <?=$customer['ID']?>;

    var CustomerRecord = <?= json_encode($customer) ?>;
    var EmployeeRights = <?= json_encode($rights) ?>;

    var Sites = {};
    <?php foreach($sites as $site): ?>
    Sites["<?= $site['ID'] ?>"] = "<?= empty($site['Name']) ? $site['Domen'] : $site['Name'] ?>";
    <?php endforeach ?>

    $(function () {
        <? if (!$isEditPersonalData): ?>
            $('#PersonalData').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditSelfDescription): ?>
            $('#SelfDescription').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditPhotoAndVideo): ?>
            $('#PhotoAndVideo').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditQuestions): ?>
            $('#Questions').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditAdditionallyPane): ?>
        $('#AdditionallyPane').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
        <? if (!$isEditReservationContactPane): ?>
        $('#ReservationContactPane').find('input, select, textarea').attr('disabled', 'disabled');
        <? endif ?>
    });
</script>

<div class="employee-card clear">
    <div class="employee-img">
        <div class="employee-img-wrap">
            <div class="employee-img-in">
                <?php
                $isAvatar = !empty($customer['Avatar']);
                $avatar = $isAvatar
                    ? base_url("thumb/?src=/files/images/".$customer['FileName']."&w=152")
                    : base_url("public/img/avatar.jpeg")
                ?>
                <? if ($isAvatar): ?>
                    <a href="<?= base_url("thumb/?src=/files/images/".$customer['FileName']) ?>" data-lightbox="AvatarCard">
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
                    <div class="blue-style"><strong>ID:</strong> <span><?=sprintf("%'.07d", $customer['ID'])?></span></div>
                    <div><strong>Фамилия:</strong> <span> <?=$customer['SName']?></span></div>
                    <div><strong>Имя:</strong> <span> <?=$customer['FName']?></span></div>
                    <div><strong>Возраст:</strong> <span id="customerAge"> не указан</span></div>

                    <?php if($customer['DOB']): ?>
                        <script>
                            $(document).ready(function(){
                                var age = -(moment('<?=$customer['DOB']?>').diff(moment(), 'years'));
                                $('#customerAge').html(age + ' лет');
                            });
                        </script>
                    <?php endif ?>
                </td>
                <td>
                    <div><strong>Статус:</strong> <span class="blue-style"><?= ($customer['IsDeleted'] > 0 ? "удален" : "активный") ?></span></div>
                    <div><strong>Создана:</strong> <span><?= toClientDateTime($customer['DateCreate']) ?></span></div>
                </td>
            </tr>
        </table>
    </div>
</div>



<div class="tabs-info-fields">
    <div class="assol-tabs">
        <div class="assol-tabs-line">
            <div>
                <ul class="assol-tabs-btns nav nav-tabs nav-justified clear" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#PersonalData" aria-controls="PersonalData" role="tab" data-toggle="tab">
                            Анкета
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Questions" aria-controls="Questions" role="tab" data-toggle="tab">
                            Интервью
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#SelfDescription" aria-controls="SelfDescription" role="tab" data-toggle="tab">
                            О себе и избраннике
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Sites" aria-controls="Sites" role="tab" data-toggle="tab">
                            Сайты
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Mens" aria-controls="Mens" role="tab" data-toggle="tab">
                            Мужчины
                        </a>
                    </li>
                    <? if (IS_LOVE_STORY): ?>
                        <li role="presentation">
                            <a href="#Photo" aria-controls="Photo" role="tab" data-toggle="tab">
                                Фото
                            </a>
                        </li>
                        <li role="presentation">
                            <a href="#Video" aria-controls="Video" role="tab" data-toggle="tab">
                                Видео
                            </a>
                        </li>
                    <? else: ?>
                        <li role="presentation">
                            <a href="#PhotoAndVideo" aria-controls="PhotoAndVideo" role="tab" data-toggle="tab">
                                Фото / Видео
                            </a>
                        </li>
                    <? endif ?>
                    <li role="presentation">
                        <a href="#ReservationContactPane" aria-controls="ReservationContactPane" role="tab" data-toggle="tab">
                            Контакты
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#Story" aria-controls="Story" role="tab" data-toggle="tab">
                            <?= IS_LOVE_STORY ? 'Встречи' : 'История' ?>
                        </a>
                    </li>
                    <?php if(IS_LOVE_STORY): ?>
                        <li role="presentation">
                            <a href="#Delivery" aria-controls="Delivery" role="tab" data-toggle="tab">
                                Доставки
                            </a>
                        </li>
                    <?php endif; ?>
                    <li role="presentation">
                        <a href="#AdditionallyPane" aria-controls="AdditionallyPane" role="tab" data-toggle="tab">
                            <?= IS_LOVE_STORY ? 'Изменения' : 'Дополнительно' ?>
                        </a>
                    </li>
                    <?php if($isShowRemove): ?>
                        <li role="presentation">
                            <a href="#Remove" aria-controls="Remove" role="tab" data-toggle="tab">
                                Удаление
                            </a>
                        </li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
        
        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="PersonalData">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="SName">Фамилия</label>
                            <input type="text" class="assol-input-style" id="SName" placeholder="Фамилия" value="<?=$customer['SName']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="FName">Имя</label>
                            <input type="text" class="assol-input-style" id="FName" placeholder="Имя" value="<?=$customer['FName']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="MName">Отчество</label>
                            <input type="text" class="assol-input-style" id="MName" placeholder="Отчество" value="<?=$customer['MName']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="DOB">Дата рождения</label>
                            <div class="date-field">
                                <input type="text" class="assol-input-style" id="DOB" placeholder="Дата рождения" value="<?= toClientDate($customer['DOB']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="row sub-row">
                            <div class="col-md-7 sub-col">
                                <div class="form-group">
                                    <label for="City">Город</label>
                                    <input type="text" class="assol-input-style" id="City" placeholder="Город" value="<?=$customer['City']?>">
                                </div>
                            </div>
                            <div class="col-md-5 sub-col">
                                <div class="form-group">
                                    <label for="Postcode">Индекс</label>
                                    <input type="text" class="assol-input-style" id="Postcode" placeholder="Индекс" value="<?=$customer['Postcode']?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Country">Страна</label>
                            <input type="text" class="assol-input-style" id="Country" placeholder="Страна" value="<?=$customer['Country']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Address">Адрес проживания</label>
                            <input type="text" class="assol-input-style" id="Address" placeholder="Адрес проживания" value="<?=$customer['Address']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row sub-row">
                            <div class="col-md-6 sub-col">
                                <div class="form-group">
                                    <label for="Phone_1">Телефон 1</label>
                                    <input type="text" class="assol-input-style" id="Phone_1" placeholder="Телефон 1" value="<?=$customer['Phone_1']?>">
                                </div>
                            </div>
                            <div class="col-md-6 sub-col">
                                <div class="form-group">
                                    <label for="Phone_2">Телефон 2</label>
                                    <input type="text" class="assol-input-style" id="Phone_2" placeholder="Телефон 2" value="<?=$customer['Phone_2']?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <?php $CurrentWorkBlock = 3; ?>
                <? if (IS_LOVE_STORY): ?>
                    <?php if($isDirector || $isSecretary): ?>
                        <?php
                        $emailIsDisplayed = true;
                        $CurrentWorkBlock = 6;
                        ?>
<!--                    Emails -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Email">E-Mail для мужчин</label>
                            <input type="text" class="assol-input-style" id="Email" placeholder="E-Mail для мужчин" value="<?=$customer['Email']?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Email_site">E-Mail для сайта</label>
                            <input type="text" class="assol-input-style" id="Email_site" placeholder="E-Mail для сайта" value="<?=$customer['Email_site']?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Email_private">E-Mail клиентки</label>
                            <input type="text" class="assol-input-style" id="Email_private" placeholder="E-Mail клиентки" value="<?=$customer['Email_private']?>">
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
<!--                    Соцсети -->
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
                <style>
                    .my-fa {
                        font-size: 22px;
                        padding-right: 5px;
                    }
                    .fa-skype {
                        color: #00aff0;
                    }
                    .fa-instagram {
                        color: #125688;
                    }
                    .fa-facebook {
                        color: #3B5998;
                    }
                </style>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Skype"><span class="my-fa fa fa-skype"></span>Skype</label>
                            <input type="text" class="assol-input-style" id="Skype" placeholder="Skype" value="<?=$customer['Skype']?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Instagram"><span class="my-fa fa fa-instagram"></span>Instagram</label>
                            <input type="text" class="assol-input-style" id="Instagram" placeholder="URL страницы в Instagram" value="<?=$customer['Instagram']?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="Facebook"><span class="my-fa fa fa-facebook"></span>Facebook</label>
                            <input type="text" class="assol-input-style" id="Facebook" placeholder="URL страницы в Facebook" value="<?=$customer['Facebook']?>">
                        </div>
                    </div>
                </div>
                    <?php endif; // isDirector || isSecretary ?>
                <? endif; // IS_LOVE_STORY ?>
                <div class="row">
                    <?php if(empty($emailIsDisplayed)): ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Email">E-Mail для мужчин</label>
                            <input type="text" class="assol-input-style" id="Email" placeholder="E-Mail для мужчин" value="<?=$customer['Email']?>">
                        </div>
                    </div>
                    <?php endif; // emailIsDisplayed ?>
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
                                        <?php $isSelected = $item['ID']== $customer['Forming']; ?>
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
                            <label for="ProfessionOfDiploma">Профессия (по диплому)</label>
                            <input type="text" class="assol-input-style" id="ProfessionOfDiploma" placeholder="Профессия (по диплому)" value="<?=$customer['ProfessionOfDiploma']?>">
                        </div>
                    </div>
                    <div class="col-md-<?= $CurrentWorkBlock; ?>">
                        <div class="form-group">
                            <label for="CurrentWork">Работа на данный момент</label>
                            <input type="text" class="assol-input-style" id="CurrentWork" placeholder="Работа на данный момент" value="<?=$customer['CurrentWork']?>">
                        </div>
                    </div>
                    <? if (!IS_LOVE_STORY): ?>
                        <div class="col-md-3"></div>
                    <? endif ?>
                </div>
                <? if (!IS_LOVE_STORY): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <script id="emailTemplate" type="text/x-jquery-tmpl">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="assol-input-style" id="Email_${ID}" placeholder="E-Mail" value="${Email}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <input type="text" class="assol-input-style" id="Note_${ID}" placeholder="Описание" value="${Note}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <? if ($isEditPersonalData): ?>
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
                <? endif ?>
                <? if (IS_LOVE_STORY): /* Грубо, но быстро :) */?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="Worship">Вероисповедание</label>
                                <input type="text" class="assol-input-style" id="Worship" placeholder="Вероисповедание" value="<?=$customer['Worship']?>">
                            </div>
                        </div>
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
                                            <?php $isSelected = $item['ID']== $customer['MaritalStatus']; ?>
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
                            <script id="languageTemplate" type="text/x-jquery-tmpl">
                                <div class="row">
                                    <div class="col-md-5">
                                        {{tmpl($data) "#languageSelectTemplate"}}
                                    </div>
                                    <div class="col-md-7">
                                        {{tmpl($data) "#languageLevelTemplate"}}
                                        <? if ($isEditPersonalData): ?>
                                        {{if ID}}
                                                <button record="${ID}" class="btn assol-btn save action-save-language" title="Сохранить запись">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                                <button record="${ID}" class="btn assol-btn remove action-remove-language" title="Удалить запись">
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </button>
                                        {{else}}
                                                <button record="0" class="btn assol-btn add action-save-language" title="Добавить запись">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </button>
                                        {{/if}}
                                        <? endif ?>
                                    </div>
                                </div>
                            </script>

                            <script id="languageSelectTemplate" type="text/x-jquery-tmpl">
                                <div class="form-group">
                                    <select class="assol-btn-style" id="Language_${ID}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                        <option value="0" selected disabled>Выбрать</option>
                                        <?php foreach($language as $item): ?>
                                            <option {{if (<?=$item['ID']?> == LanguageID)}} selected {{/if}} value="<?=$item['ID']?>"><?=$item['ReferenceValue']?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </script>

                            <script id="languageLevelTemplate" type="text/x-jquery-tmpl">
                                <div id="LevelLanguage_${ID}">
                                    <div class="rating-wrap">
                                        <div class="arrow"></div>
                                        <div class="stars">
                                            {{each [1,2,3,4,5]}}
                                                    <input type="radio" name="LevelLanguage_${ID}" class="star-${$value}" id="LevelLanguage_${ID}_${$value}" value="${$value}" {{if (Level==$value)}} checked {{/if}}>
                                                    <label class="star-${$value}" for="LevelLanguage_${ID}_${$value}">${$value}</label>
                                            {{/each}}
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </script>

                            <div class="form-group">
                                <label for="languageList">Иностранные языки</label>
                                <div id="languageList"></div>
                            </div>
                        </div>
                    </div>

                    <script id="childrenTemplate" type="text/x-jquery-tmpl">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="assol-input-style" id="ChildrenFIO_${ID}" placeholder="Дети ФИ" value="${FIO}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" class="assol-input-style" id="ChildrenReside_${ID}" placeholder="С кем проживает" value="${Reside}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="date-field">
                                        <input type="text" class="assol-input-style" id="ChildrenDOB_${ID}" placeholder="Дата рождения" value="${toClientDate(DOB)}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-7">
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

                                    <div class="col-md-5">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ChildrenFI">Дети ФИ</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ChildrenReside">С кем проживает</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="ChildrenDOB">Дата рождения</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ChildrenSex">Пол</label>
                                </div>
                            </div>
                        </div>

                        <div id="childrenList"></div>
                    </div>
                <? else: ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="Worship">Вероисповедание</label>
                                <input type="text" class="assol-input-style" id="Worship" placeholder="Вероисповедание" value="<?=$customer['Worship']?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="DateRegister">Дата регистрации</label>
                                <div class="date-field">
                                    <input type="text" class="assol-input-style" id="DateRegister" placeholder="Дата регистрации" value="<?= toClientDate($customer['DateRegister']) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <script id="languageTemplate" type="text/x-jquery-tmpl">
                                <div class="row">
                                    <div class="col-md-5">
                                        {{tmpl($data) "#languageSelectTemplate"}}
                                    </div>
                                    <div class="col-md-7">
                                        {{tmpl($data) "#languageLevelTemplate"}}
                                        <? if ($isEditPersonalData): ?>
                                        {{if ID}}
                                                <button record="${ID}" class="btn assol-btn save action-save-language" title="Сохранить запись">
                                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                                </button>
                                                <button record="${ID}" class="btn assol-btn remove action-remove-language" title="Удалить запись">
                                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                </button>
                                        {{else}}
                                                <button record="0" class="btn assol-btn add action-save-language" title="Добавить запись">
                                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                </button>
                                        {{/if}}
                                        <? endif ?>
                                    </div>
                                </div>
                            </script>

                                <script id="languageSelectTemplate" type="text/x-jquery-tmpl">
                                <div class="form-group">
                                    <select class="assol-btn-style" id="Language_${ID}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                        <option value="0" selected disabled>Выбрать</option>
                                        <?php foreach($language as $item): ?>
                                            <option {{if (<?=$item['ID']?> == LanguageID)}} selected {{/if}} value="<?=$item['ID']?>"><?=$item['ReferenceValue']?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </script>

                            <script id="languageLevelTemplate" type="text/x-jquery-tmpl">
                                <div id="LevelLanguage_${ID}">
                                    <div class="rating-wrap">
                                        <div class="arrow"></div>
                                        <div class="stars">
                                            {{each [1,2,3,4,5]}}
                                                    <input type="radio" name="LevelLanguage_${ID}" class="star-${$value}" id="LevelLanguage_${ID}_${$value}" value="${$value}" {{if (Level==$value)}} checked {{/if}}>
                                                    <label class="star-${$value}" for="LevelLanguage_${ID}_${$value}">${$value}</label>
                                            {{/each}}
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                            </script>

                            <div class="form-group">
                                <label for="languageList">Иностранные языки</label>
                                <div id="languageList"></div>
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
                                            <?php $isSelected = $item['ID']== $customer['MaritalStatus']; ?>
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
                        <div class="col-md-9">

                            <script id="childrenTemplate" type="text/x-jquery-tmpl">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="text" class="assol-input-style" id="ChildrenFIO_${ID}" placeholder="Дети ФИ" value="${FIO}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="date-field">
                                            <input type="text" class="assol-input-style" id="ChildrenDOB_${ID}" placeholder="Дата рождения" value="${toClientDate(DOB)}" <? if (!$isEditPersonalData): ?> disabled="disabled" <? endif ?>>
                                        </div>
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
                    </div>
                <? endif ?>

                <? if ($isDocAccess): ?>
                    <? if ($isShowPassportSection): ?>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row sub-row">
                                    <div class="col-md-4 sub-col">
                                        <div class="form-group">
                                            <label for="PassportSeries">Серия</label>
                                            <input type="text" class="assol-input-style" id="PassportSeries" placeholder="xxxx" value="<?=$customer['PassportSeries']?>">
                                        </div>
                                    </div>
                                    <div class="col-md-8 sub-col">
                                        <div class="form-group">
                                            <label for="PassportNumber">Номер паспорта</label>
                                            <input type="text" class="assol-input-style" id="PassportNumber" placeholder="xxxxxx" value="<?=$customer['PassportNumber']?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group passport-group">
                                    <label>Паспорт (изображение)</label>

                                    <script id="passportTemplate" type="text/x-jquery-tmpl">
                                        {{if ID > 0}}
                                            {{tmpl($data) "#passportTemplateRecord"}}
                                        {{else limit()}}
                                            {{tmpl($data) "#passportTemplateAppend"}}
                                        {{/if}}
                                    </script>

                                    <script id="passportTemplateRecord" type="text/x-jquery-tmpl">
                                        <li id="div-passport-${ID}">
                                            <div class="btn assol-btn doc">
                                                <a href="<?= base_url('customer/'.$customer['ID'].'/passport') ?>/${ID}/load" target="_blank" title="Скачать документ{{if Name}} '${Name}'{{/if}}">
                                                    <? if (IS_LOVE_STORY): ?>
                                                        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>doc-${ID}.${ext}</a>
                                                    <? else: ?>
                                                        <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>{{if Name}}${Name}{{else}}doc-${ID}.${ext}{{/if}}</a>
                                                    <? endif ?>
                                                <? if ($isEditPersonalData): ?>
                                                <span record="${ID}" class="glyphicon glyphicon-remove-circle action-remove-passport" aria-hidden="true" title="Удалить документ{{if Name}} '${Name}'{{/if}}"></span>
                                                <? endif ?>
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
                                            var frameSrc = '<?=base_url('customer/'.$customer['ID'].'/passport/upload')?>';

                                            modal.on('show.bs.modal', function () {
                                                frame.attr("src", frameSrc);
                                            });
                                            modal.on('hidden.bs.modal', function () {
                                                $.CustomerCard.ReloadPassportList();
                                            });
                                            modal.modal({show:true});
                                        });
                                    </script>

                                    <script id="passportTemplateAppend" type="text/x-jquery-tmpl">
                                        <? if ($isEditPersonalData): ?>
                                        <li>
                                            <a href="javascript:void(0)" id="btnPassportUpload" class="btn assol-btn add" title="Загрузить документы">
                                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                            </a>
                                        </li>
                                        <? endif ?>
                                    </script>

                                    <script>
                                        function limit() {
                                            <? if (!IS_LOVE_STORY): ?>
                                            return $('.action-remove-passport').length < 4; // Для assol максимум 4 скана
                                            <? else: ?>
                                            return true;
                                            <? endif ?>
                                        }
                                    </script>

                                    <ul id="passport" class="list-inline"></ul>
                                </div>
                            </div>
                        </div>

                    <? endif ?>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group agreement-group">
                                <label>Договор</label>

                                <script id="agreementTemplate" type="text/x-jquery-tmpl">
                                {{if ID}}
                                    {{tmpl($data) "#agreementRecordTemplate"}}
                                {{else}}
                                    {{tmpl($data) "#agreementAppendTemplate"}}
                                {{/if}}
                            </script>

                                <script id="agreementRecordTemplate" type="text/x-jquery-tmpl">
                                    <li id="Agreement_${ID}">
                                        <div class="btn assol-btn doc">
                                            <a href="<?= base_url('customer/'.$customer['ID']) ?>/agreement/${ID}/load" target="_blank" title="Скачать документ{{if Name}} '${Name}'{{/if}}">
                                                <? if (IS_LOVE_STORY): ?>
                                                    <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>doc-${ID}.${ext}</a>
                                                <? else: ?>
                                                    <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>{{if Name}}${Name}{{else}}doc-${ID}.${ext}{{/if}}</a>
                                                <? endif ?>
                                            <? if ($isEditPersonalData): ?>
                                            <span record="${ID}" class="glyphicon glyphicon-remove-circle action-remove-agreement" aria-hidden="true" title="Удалить документ{{if Name}} '${Name}'{{/if}}"></span>
                                            <? endif ?>
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
                                        var frameSrc = '<?=base_url('customer/'.$customer['ID'].'/agreement/upload')?>';

                                        modal.on('show.bs.modal', function () {
                                            frame.attr("src", frameSrc);
                                        });
                                        modal.on('hidden.bs.modal', function () {
                                            $.CustomerCard.ReloadAgreementList();
                                        });
                                        modal.modal({show:true});
                                    });
                                </script>

                                <script id="agreementAppendTemplate" type="text/x-jquery-tmpl">
                                    <? if ($isEditPersonalData): ?>
                                    <li>
                                        <a href="javascript:void(0)" id="btnAgreementUpload" class="btn assol-btn add" title="Загрузить документы">
                                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                        </a>
                                    </li>
                                    <? endif ?>
                                </script>

                                <ul id="agreement" class="list-inline list-agreement"></ul>
                            </div>
                        </div>
                    </div>
                <? endif; ?>

                <div class="row">
                    <div class="col-md-3">
                        <div class="row sub-row">
                            <div class="col-md-7 sub-col">
                                <div class="form-group">
                                    <label for="Height">Рост</label>
                                    <input type="text" class="assol-input-style" id="Height" placeholder="Рост" value="<?=$customer['Height']?>">
                                </div>
                            </div>
                            <div class="col-md-5 sub-col">
                                <div class="form-group">
                                    <label for="Weight">Вес</label>
                                    <input type="text" class="assol-input-style" id="Weight" placeholder="Вес" value="<?=$customer['Weight']?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="HairColor">Цвет волос</label>
                            <input type="text" class="assol-input-style" id="HairColor" placeholder="Цвет волос" value="<?=$customer['HairColor']?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="EyeColor">Цвет глаз</label>
                            <select class="assol-btn-style" id="EyeColor">
                                <option value="0" selected>Выбрать</option>
                                <?php foreach($eye_color as $item): ?>
                                    <?php $isSelected = $item['ID']== $customer['EyeColor']; ?>
                                    <option <?= $isSelected ? 'selected':'' ?> value="<?=$item['ID']?>"><?=$item['ReferenceValue']?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>

                    <? if (IS_LOVE_STORY): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="BodyBuild">Строение тела</label>
                                <input type="text" class="assol-input-style" id="BodyBuild" placeholder="Строение тела" value="<?=$customer['BodyBuild']?>">
                            </div>
                        </div>
                    <? else: ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="BodyBuildID">Строение тела</label>
                                <select class="assol-btn-style" id="BodyBuildID">
                                    <option value="0" selected>Выбрать</option>
                                    <?php foreach($body_build as $item): ?>
                                        <?php $isSelected = $item['ID']== $customer['BodyBuildID']; ?>
                                        <option <?= $isSelected ? 'selected':'' ?> value="<?=$item['ID']?>"><?=$item['ReferenceValue']?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    <? endif ?>
                </div>
                <? if (IS_LOVE_STORY): ?>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="SizeFoot">Размер Ноги</label>
                                <input type="text" class="assol-input-style" id="SizeFoot" placeholder="Размер Ноги" value="<?=$customer['SizeFoot']?>">
                            </div>
                        </div>
                        <div class="col-md-9"></div>
                    </div>
                <? else: ?>
                    <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Smoking">Курение</label>
                            <div id="Smoking">
                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Smoking" id="Smoking_1" value="1" <?=($customer['Smoking']==1)?'checked':''?>>
                                        <mark></mark>
                                        <span>
                                            <strong> Да:</strong>
                                        </span>
                                    </label>
                                </div>
                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Smoking" id="Smoking_2" value="2" <?=($customer['Smoking']==2)?'checked':''?>>
                                        <mark></mark>
                                        <span>
                                            <strong> Нет:</strong>
                                        </span>
                                    </label>
                                </div>
                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Smoking" id="Smoking_2" value="2" <?=($customer['Smoking']==3)?'checked':''?>>
                                        <mark></mark>
                                        <span>
                                            <strong> Иногда:</strong>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="Alcohol">Алкоголь</label>
                            <div id="Alcohol">


                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Alcohol" id="Alcohol_1" value="1" <?=($customer['Alcohol']==1)?'checked':''?>>
                                        <mark></mark>
                                        <span>
                                            <strong> Да:</strong>
                                        </span>
                                    </label>
                                </div>

                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Alcohol" id="Alcohol_2" value="2" <?=($customer['Alcohol']==2)?'checked':''?>>
                                        <mark></mark>
                                        <span>
                                            <strong> Нет:</strong>
                                        </span>
                                    </label>
                                </div>

                                <div class="radio-line">
                                    <label>
                                        <input type="radio" name="Alcohol" id="Alcohol_3" value="3" <?=($customer['Alcohol']==3)?'checked':''?>>
                                        <mark></mark>
                                        <span>
                                            <strong> Иногда:</strong>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                    <div class="col-md-3"></div>
                </div>
                <? endif ?>

                <? if ($isEditPersonalData): ?>
                    <button id="SavePersonalData" class="btn assol-btn save" title="Сохранить изменения">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                        Сохранить
                    </button>
                <? endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="Mens">
                <div class="row assol-grey-panel" style="padding-top: 10px; margin-bottom: 15px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="MensSite">Сайт</label>
                            <div class="btn-group assol-select-dropdown" id="MensSite">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" id="MensSitesList">
                                    <?php foreach($employee_sites as $item): ?>
                                        <li>
                                            <input type="checkbox" id="MensSite_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                                            <label for="MensSite_<?= $item['ID'] ?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-default" id="MensSearchBtn" style="margin-top: 18px; padding: 9px 12px 8px;">
                            <span class="glyphicon glyphicon-search"></span> Поиск
                        </button>
                    </div>
                </div>

                <div id="MensTabContent">
                    <?php /*
                    $this->load->view('form/customers/profile_mens',
                        array(
                            'customerID' => $customer['ID'],
                            'isEditMens' => $isEditMens,
                            'isDeleteMens' => $isDeleteMens,
                            'mensList' => (!empty($mensList)) ? $mensList : array(),
                            'mensSitesList' => (!empty($mensSitesList)) ? $mensSitesList : array(),
                            'sites' => $employee_sites,
                        )
                    );*/
                    ?>
                </div>

                <script type="text/javascript">
                    // клик по табу Мужчины
                    $(document).on('click', 'a[aria-controls=Mens]', function(){
                        fillMensContent();
                    });
                    // поиск по сайтам
                    $(document).on('click', '#MensSearchBtn', function(){
                        fillMensContent();
                    });
                    // загрузка контента после добавления нового Мужчины (или после редактирования)
                    if(window.location.hash == '#Mens') {
                        fillMensContent();
                    }
                    // загрузка данных
                    function fillMensContent(){
                        // учитываем фильтр по сайтам
                        var mensListInputs = $('#MensSitesList').find('input[type=checkbox]:checked'); // отмеченные чекбоксы
                        var mensListIds = []; // массив для ID выбранных сайтов
                        $.each(mensListInputs, function(key, item){
                            mensListIds[key] = $(item).val(); // собираем ID выбранных сайтов
                        });

                        $.post(
                            '/Customer_Mens/filter',
                            {
                                CustomerID: <?= $customer['ID']; ?>,
                                SiteIDs: ((mensListIds.length > 0) ? mensListIds.join() : '')
                            },
                            function(data){
                                if(data !== ''){
                                    $('#MensTabContent').html('');
                                    $('#MensTabContent').html(data);
                                }
                                else{
                                    $('#MensTabContent').html('');
                                    $('#MensTabContent').html('<h4 class="text-center">Нет данных для отображения</h4>');
                                }
                            },
                            'html'
                        );
                    }
                </script>
            </div>
            <div role="tabpanel" class="tab-pane" id="SelfDescription">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Temper">Характер</label>
                            <textarea class="assol-input-style" id="Temper" rows="6" placeholder="Характер"><?=$customer['Temper']?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Interests">Интересы</label>
                            <textarea class="assol-input-style" id="Interests" rows="6" placeholder="Интересы"><?=$customer['Interests']?></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span class="h4">Пожелания к мужчине:</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Возраст</label>
                            <div class="row">
                                <div class="col-md-6" style="padding-right: 5px">
                                    <input type="text" class="assol-input-style" id="WishesForManAgeMin" placeholder="от" value="<?=$customer['WishesForManAgeMin']?>">
                                </div>
                                <div class="col-md-6" style="padding-left: 5px">
                                    <input type="text" class="assol-input-style" id="WishesForManAgeMax" placeholder="до" value="<?=$customer['WishesForManAgeMax']?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="WishesForManWeight">Вес</label>
                            <input type="text" class="assol-input-style" id="WishesForManWeight" placeholder="Вес" value="<?=$customer['WishesForManWeight']?>">
                        </div>
                    </div>
                    <? if (IS_LOVE_STORY): ?>
                        <input type="hidden" id="WishesForManHeight" value="<?=$customer['WishesForManHeight']?>">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="WishesForManNationality">Национальность</label>
                                <input type="text" class="assol-input-style" id="WishesForManNationality" placeholder="Национальность" value="<?=$customer['WishesForManNationality']?>">
                            </div>
                        </div>
                    <? else: ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="WishesForManHeight">Рост</label>
                                <input type="text" class="assol-input-style" id="WishesForManHeight" placeholder="Рост" value="<?=$customer['WishesForManHeight']?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="WishesForManNationality">Национальность</label>
                                <input type="text" class="assol-input-style" id="WishesForManNationality" placeholder="Национальность" value="<?=$customer['WishesForManNationality']?>">
                            </div>
                        </div>
                    <? endif ?>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea class="assol-input-style" id="WishesForManText" rows="6"><?=$customer['WishesForManText']?></textarea>
                        </div>
                    </div>
                </div>

                <?php if ($isEditSelfDescription): ?>
                    <button id="SaveSelfDescription" class="btn assol-btn save" title="Сохранить изменения">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                        Сохранить
                    </button>
                <?php endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="Questions">
                <div id="react-customer-profile-page"></div>
                <br />
                <br />
                <? if (!IS_LOVE_STORY): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group question-group">
                                <label>Изображение</label>

                                <script id="questionPhotoTemplate" type="text/x-jquery-tmpl">
                                    {{if ID > 0}}
                                        {{tmpl($data) "#questionPhotoTemplateRecord"}}
                                    {{else}}
                                        {{tmpl($data) "#questionPhotoTemplateAppend"}}
                                    {{/if}}
                                </script>

                                <script id="questionPhotoTemplateRecord" type="text/x-jquery-tmpl">
                                        <li id="div-question-photo-${ID}">
                                            <div class="btn assol-btn doc">
                                                <a href="<?= base_url('customer/'.$customer['ID'].'/question/photo') ?>/${ID}/load" target="_blank" title="Скачать изображение{{if Name}} '${Name}'{{/if}}">
                                                    <span class="glyphicon glyphicon-paperclip" aria-hidden="true"></span>{{if Name}}${Name}{{else}}doc-${ID}.${ext}{{/if}}</a>
                                                <? if ($isEditQuestionPhoto): ?>
                                                <span record="${ID}" class="glyphicon glyphicon-remove-circle action-remove-question-photo" aria-hidden="true" title="Удалить изображение{{if Name}} '${Name}'{{/if}}"></span>
                                                <? endif ?>
                                            </div>
                                        </li>
                                    </script>

                                <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="QuestionPhotoUploadLabel">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title" id="QuestionPhotoUploadLabel">Загрузка изображения</h4>
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
                                    .question-group .modal-dialog {
                                        width: 90%;
                                        background: white;
                                        max-width: 1180px;
                                    }

                                    .question-group .modal-title {
                                        float: left;
                                    }

                                    .question-group iframe {
                                        width: 100%;
                                        height: 600px;
                                    }

                                    .question-group li {
                                        padding-top: 10px;
                                        padding-bottom: 10px;
                                    }
                                </style>

                                <script>
                                    $(document).on('click', '#btnQuestionPhoto', function () {

                                        var form = $(this).closest('.form-group');

                                        var modal = form.find('.modal');
                                        var frame = form.find('iframe');
                                        var frameSrc = '<?=base_url('customer/'.$customer['ID'].'/question/photo/upload')?>';

                                        modal.on('show.bs.modal', function () {
                                            frame.attr("src", frameSrc);
                                        });
                                        modal.on('hidden.bs.modal', function () {
                                            $.CustomerCard.ReloadQuestionPhotoList();
                                        });
                                        modal.modal({show:true});
                                    });
                                </script>

                                <script id="questionPhotoTemplateAppend" type="text/x-jquery-tmpl">
                                    <? if ($isEditQuestionPhoto): ?>
                                    <li>
                                        <a href="javascript:void(0)" id="btnQuestionPhoto" class="btn assol-btn add" title="Загрузить изображение">
                                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                        </a>
                                    </li>
                                    <? endif ?>
                                </script>

                                <ul id="questionPhoto" class="list-inline"></ul>
                            </div>
                        </div>
                    </div>
                <? endif; ?>

            </div>
            <? if (IS_LOVE_STORY): ?>
                <div role="tabpanel" class="tab-pane" id="Photo">

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
                            $isAvatar = !empty($customer['Avatar']);
                            $avatar = $isAvatar
                                ? base_url("thumb/?src=/files/images/".$customer['FileName']."&w=221")
                                : base_url('public/img/avatar-example.png')
                            ?>
                            <? if ($isAvatar): ?>
                            <a href="<?= base_url("thumb/?src=/files/images/".$customer['FileName']) ?>" data-lightbox="Avatar">
                            <? endif; ?>
                                <img id="AvatarBig" src="<?= $avatar ?>" alt="avatar">
                            <? if ($isAvatar): ?>
                            </a>
                            <? endif; ?>
                        </div>
                    </div>

                    <form id="AvatarForm" action="<?= base_url('customer/'.$customer['ID'].'/avatar') ?>" class="avatar-file" method="post">
                        <input type="file" id="addClientAvatar" name="thumb" tabindex="-1" style="display: none;">
                        <div class="bootstrap-filestyle input-group">
                            <span class="group-span-filestyle" tabindex="0">
                                <label for="addClientAvatar" class="btn btn assol-btn doc file ">
                                    <span class="icon-span-filestyle glyphicon glyphicon-paperclip"></span>
                                    <span class="buttonText">Загрузить фотографию</span>
                                </label>
                            </span>
                        </div>
                    </form>

                    <hr>

                    <div class="last-photo-session">
                        <div class="form-group">
                            <label for="DateLastPhotoSession">Дата последней фотосессии</label><br>
                            <div class="date-field" style="width: 200px">
                                <input type="text" class="assol-input-style" id="DateLastPhotoSession" placeholder="Дата последней фотосессии" value="<?= toClientDate($customer['DateLastPhotoSession']) ?>">
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div>
                        <? if ($isEditPhoto): ?>
                            <a href="<?=base_url('customer/'.$customer['ID'].'/album/add')?>" data-toggle="modal" data-target="#remoteDialog" class="btn assol-btn add" role="button" title="Создать альбом">
                                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                СОЗДАТЬ АЛЬБОМ
                            </a>
                        <? endif ?>
                    </div>
                    <?php $showNewModal = true; ?>
                    <script id="albumTemplate" type="text/x-jquery-tmpl">

                        <div class="album-item-wrap">
                            <input type="checkbox" id="album_num_${ID}" class="album-block-checkbox">
                            <label for="album_num_${ID}" class="album-block-label">${Name} <span class="arrow"></span></label>
                            <div class="album-item">
                                <div record="${ID}" class="">
                                    <div class="pseudo-table-wrap">
                                        <div class="pseudo-table">
                                            <div class="pseudo-td width50">
                                                <label for="Album_${ID}" class="album-label">Название альбома</label>

                                                <div class="form-group">
                                                    <input type="text" class="assol-input-style" id="Album_${ID}" placeholder="Название альбома" value="${Name}">
                                                </div>
                                            </div>
                                            <div class="pseudo-td width50">
                                                <div class="pseudo-table-wrap">
                                                    <div class="pseudo-table">
                                                        <div class="pseudo-td">
                                                            <strong>Дата создания:</strong> ${toClientDate(DateCreate)}
                                                        </div>
                                                        <div class="pseudo-td content-width">
                                                            <a href="javascript:void(0)" class="action-remove-album nobr">Удалить альбом</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        {{each images}}
                                            <div class="col-md-2 album-image" id-cross="${ID}">
                                                <span class="glyphicon glyphicon-remove-circle image-remove-btn remove" aria-hidden="true" title="Удалить"></span>
                                                <a href="<?= base_url("thumb") ?>?src=/files/images/${ImageID}.${ext}&sia=${ImageID}.${ext}" target="_blank" download>
                                                    <span class="glyphicon glyphicon-download image-download-btn" aria-hidden="true" title="Скачать"></span>
                                                </a>
                                                <?php if(empty($showNewModal)): ?>
                                                <a href="<?= base_url("thumb") ?>?src=/files/images/${ImageID}.${ext}" data-lightbox="Album_${AlbumID}">
                                                    <img src="<?= base_url("thumb") ?>?src=/files/images/${ImageID}.${ext}&w=138" class="thumbnail">
                                                </a>
                                                <?php else: ?>
                                                <img src="<?= base_url("thumb") ?>?src=/files/images/${ImageID}.${ext}&w=138" class="thumbnail launch-album-modal" id="img_${AlbumID}_${ImageID}">
                                                <?php endif; ?>
                                            </div>
                                        {{/each}}

                                        <? if ($isEditPhoto): ?>
                                            <div class="col-md-2 open-album">
                                                <a href="javascript:void(0)" data-url="<?=base_url('customer/'.$customer['ID'])?>/album/${ID}/upload" class="thumbnail" role="button" title="Добавить фото">
                                                    <img src="<?=base_url('public/img/album.png')?>">
                                                    ДОБАВИТЬ<br>ФОТО
                                                </a>

                                                <div class="modal fade upload-modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="albumUpload_${ID}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="albumUpload_${ID}">Добавить фото</h4>
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
                                            </div>
                                        <? endif ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>

                    <style>
                        .mac-image {
                            max-width: 100%;
                            max-height: 700px;
                            margin: 0 auto 10px;
                        }
                        .album-carousel {
                            width: 100px;
                            height: 100px;
                            top: 180px;
                            border: 1px solid #ddd;
                            border-radius: 50px;
                        }
                        .album-carousel .glyphicon-chevron-right{
                            margin-right: -15px;
                            margin-top: -15px;
                        }
                        .album-carousel .glyphicon-chevron-left{
                            margin-left: -15px;
                            margin-top: -15px;
                        }
                        .launch-album-modal{
                            cursor: pointer;
                        }
                        .mn-avatar {
                            border-radius: 25px;
                            width: 50px;
                            height: 50px;
                        }
                        .mac-wsb {
                            margin-top: 10px;
                        }
                        .mac-site-row {
                            margin-bottom: 15px;
                            margin-top: 15px;
                            border-bottom: 1px solid #ddd;
                        }
                        .mac-asd {
                            width: 98%;
                        }



                        .album-item-wrap {

                        }
                        .album-block-label {
                            font-weight: normal;
                            border: 1px solid #535c69;
                            display: block;
                            padding: 10px 10px;
                            border-radius: 5px;
                            position: relative;
                            font-size: 15px;
                            cursor: pointer;
                        }
                        .album-block-label .arrow {
                            position: absolute;
                            top: 2px;
                            right: 10px;
                            overflow: hidden;
                        }
                        .album-block-label .arrow:after {
                            content: '';
                            display: block;
                            width: 30px;
                            height: 30px;
                            position: relative;
                            top: -12px;
                            border: 2px solid #535c69;
                            transform: rotate(-45deg);
                        }
                        .album-block-label.album-block {
                            position: relative;
                        }
                        .album-block-checkbox {
                            display: none;
                        }
                        .album-item {
                            display: none;
                            margin-top: 20px;
                        }
                        .album-block-checkbox:checked~.album-item{
                            display: block;
                        }
                        .album-block-checkbox:checked~.album-block-label .arrow{
                            transform: rotate(180deg);
                        }
                    </style>

                    <style>
                        .open-album .modal-dialog {
                            width: 90%;
                            background: white;
                            max-width: 1180px;
                        }

                        .open-album iframe {
                            width: 100%;
                            height: 600px;
                        }
                    </style>

                    <div id="album-list"></div>

<!--New modal with carousel-->
<div class="modal fade" id="myModalAlbum" tabindex="-1" role="dialog" aria-labelledby="myModalAlbumLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body" id="myModalAlbumBody">
                Loading...
            </div>
        </div>
    </div>
</div>
<!--New modal with carousel-->

                    <script>
                        $(function () {
                            lightbox.option({
                                'maxWidth': 900,
                                'maxHeight': 600,
                                'albumLabel': "Изображение %1 из %2"
                            });
                        });

                        $(document).on('click', '.open-album a[data-url]', function () {
                            var modal = $(this).parent().find('.upload-modal');
                            var frame = $(this).parent().find('iframe');
                            var frameSrc = $(this).attr('data-url');

                            modal.on('show.bs.modal', function () {
                                frame.attr("src", frameSrc);
                            });
                            modal.on('hidden.bs.modal', function () {
                                $.CustomerCard.ReloadAlbumList();
                            });
                            modal.modal({show:true});
                        });

                        $(document).on('click', '.launch-album-modal', function () {
                            var thisId = this.id.split('_');
                            $.post(
                                '/customer/album/datamodal',
                                {
                                    AlbumID: thisId[1],
                                    ImageID: thisId[2],
                                    CustomerID: CustomerID
                                },
                                function(data){
                                    $('#myModalAlbumBody').html('Loading...'); // очистили модальное окно
                                    $('#myModalAlbum').modal(); // вызвали модальное окно
                                    $('#myModalAlbumBody').html(data); // поместили контент в модальное окно
                                },
                                'html'
                            );
                        });

                        // новая обработка селектов связи фото<->сайт
                        $(document).on('change', '.mac-site-connect', function () {
                            var macSelectId = this.id.split('_');
                            var macSelectValue = $('#'+this.id).val();
                            $.post(
                                '/customer/album/connect',
                                {
                                    SiteID: macSelectId[1],
                                    ImageID: macSelectId[2],
                                    Value: macSelectValue,
                                    Type: 'site'
                                },
                                function(data){
                                    if(data*1 === 1){
                                        // показываем ОК
                                        $('#oksite_'+macSelectId[1]+'_'+macSelectId[2]).removeClass('hidden').addClass('show');
                                        setTimeout(function() { $('#oksite_'+macSelectId[1]+'_'+macSelectId[2]).removeClass('show').addClass('hidden'); }, 2000);
                                    }
                                },
                                'text'
                            );
                        });

                        // обработка селектов связи фото<->сайт
                        $(document).on('click', '.mac-radio', function () {
                            var macRadioId = this.id.split('_');
                            $.post(
                                '/customer/album/connect',
                                {
                                    SiteID: macRadioId[1],
                                    ImageID: macRadioId[2],
                                    Value: macRadioId[3],
                                    Type: 'site'
                                },
                                function(data){
                                    if(data*1 === 1){
                                        // показываем ОК
                                        $('#oksite_'+macRadioId[1]+'_'+macRadioId[2]).removeClass('hidden').addClass('show');
                                        setTimeout(function() { $('#oksite_'+macRadioId[1]+'_'+macRadioId[2]).removeClass('show').addClass('hidden'); }, 2000);
                                    }
                                },
                                'text'
                            );
                        });

                        // обработка строки из таблицы связи фото<->мужчина
                        $(document).on('click', '.save-men-info', function () {
                            var macMenBtn = this.id.split('_');
                            var macMenSend = ($('#sended_'+macMenBtn[1]+'_'+macMenBtn[2]).is(':checked')) ? 1 : 0;
                            var macMenComment = $('#comment_'+macMenBtn[1]+'_'+macMenBtn[2]).val();
                            if(macMenSend === 1) {
                                $.post(
                                    '/customer/album/connect',
                                    {
                                        MenID: macMenBtn[1],
                                        ImageID: macMenBtn[2],
                                        Value: macMenSend,
                                        Comment: macMenComment,
                                        Type: 'men'
                                    },
                                    function(data){
                                        if(data*1 === 1){
                                            // закрываем чекбокс от редактирования
                                            $('#sended_'+macMenBtn[1]+'_'+macMenBtn[2]).prop('disabled', 'disabled');
                                            // показываем ОК
                                            $('#okmen_'+macMenBtn[1]+'_'+macMenBtn[2]).removeClass('hidden').addClass('show');
                                            setTimeout(function() { $('#okmen_'+macMenBtn[1]+'_'+macMenBtn[2]).removeClass('show').addClass('hidden'); }, 2000);
                                        }
                                    },
                                    'text'
                                );
                            }
                            else{
                                alert('Для сохранения изменений обязательно должен быть отмечен чекбокс «Отправлено»!');
                            }
                        });
                    </script>

                    <? if ($isEditPhoto): ?>
                        <div>
                            <button id="SavePhoto" class="btn assol-btn save" title="Сохранить изменения">
                                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                Сохранить
                            </button>
                        </div>
                    <? endif ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="Video">
                    <div class="form-group" style="width: 350px">
                        <label for="VideoSite">Сайт</label>
                        <div class="btn-group assol-select-dropdown" id="VideoSite">
                            <div class="label-placement-wrap">
                                <button class="btn" data-label-placement>Выбрать</button>
                            </div>
                            <button data-toggle="dropdown" class="btn dropdown-toggle">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <?php foreach($sites as $item): ?>
                                    <li>
                                        <input type="checkbox" id="VideoSite_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                                        <label for="VideoSite_<?= $item['ID'] ?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                    </li>
                                <?php endforeach ?>
                            </ul>
                            <script>
                                $.each(<?= json_encode($video_sites) ?>, function(key, site) {
                                    $('#VideoSite_'+site.SiteID).click();
                                });
                            </script>
                        </div>
                    </div>

                    <script id="videoSiteTemplate" type="text/x-jquery-tmpl">

                        <div class="work-sites-block" video-site="${ID}" id-site="${SiteID}">


                            <div class="pseudo-table-wrap">
                                <div class="pseudo-table">
                                    <div class="pseudo-td content-width">
                                        <div class="form-group">
                                            <div class="site-item">
                                                <? if ($isEditVideo): ?>
                                                <span class="glyphicon glyphicon-remove-circle action-remove-video-site" aria-hidden="true" title="Удалить сайт"></span>
                                                <? endif ?>
                                                <span <? if (!$isEditVideo): ?>style="padding-left: 10px;"<? endif ?>>${Sites[SiteID]}</span>
                                                <div class="arrow">
                                                    <div class="arrow-in"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pseudo-td">
                                        <div class="pseudo-table-wrap">
                                            <div class="pseudo-table">
                                                <div class="pseudo-td width33">
                                                    <div class="form-group">
                                                        <label>Подтверждение</label>
                                                        <div class="pseudo-table-wrap">
                                                            <div class="pseudo-table">
                                                                <div class="pseudo-td">
                                                                    <input type="text" id="VideoSiteVerificationLink_${ID}" class="assol-input-style">
                                                                </div>
                                                                <div class="pseudo-td content-width">
                                                                    <button video-type="0" class="btn assol-btn add action-append-video-site">
                                                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="VideoSiteVerificationList_${ID}"></div>
                                                </div>
                                                <div class="pseudo-td width33">
                                                    <div class="form-group">
                                                        <label>Любительское</label>

                                                        <div class="pseudo-table-wrap">
                                                            <div class="pseudo-table">
                                                                <div class="pseudo-td">
                                                                    <input type="text" id="VideoSiteAmateurLink_${ID}" class="assol-input-style">
                                                                </div>
                                                                <div class="pseudo-td content-width">
                                                                    <button video-type="1" class="btn assol-btn add action-append-video-site">
                                                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="VideoSiteAmateurList_${ID}"></div>
                                                </div>
                                                <div class="pseudo-td width33">
                                                    <div class="form-group">
                                                        <label>Видеописьмо</label>

                                                        <div class="pseudo-table-wrap">
                                                            <div class="pseudo-table">
                                                                <div class="pseudo-td">
                                                                    <input type="text" id="VideoSiteMailLink_${ID}" class="assol-input-style">
                                                                </div>
                                                                <div class="pseudo-td content-width">
                                                                    <button video-type="2" class="btn assol-btn add action-append-video-site">
                                                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="VideoSiteMailList_${ID}"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>

                    <script id="videoSiteLinkTemplate" type="text/x-jquery-tmpl">
                        <div class="form-group">
                            <div class="pseudo-table-wrap">
                                <div class="pseudo-table">
                                    <div class="pseudo-td">
                                        <input type="text" class="assol-input-style" value="${Link}">
                                    </div>
                                    <div class="pseudo-td content-width">
                                        <button video-link="${ID}" video-type="${Type}" class="btn assol-btn remove action-remove-video-site-link" title="Удалить запись">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </script>

                    <div id="videoSiteList"></div>

                    <? if ($isEditVideo): ?>
                        <button id="SaveVideo" class="btn assol-btn save" title="Сохранить изменения">
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                            Сохранить
                        </button>
                    <? endif ?>
                </div>
            <? else: ?>
                <div role="tabpanel" class="tab-pane" id="PhotoAndVideo">
                <script id="videoTemplate" type="text/x-jquery-tmpl">
                    <ul class="list-inline">
                        <li style="width: 90%">
                            <input type="text" class="assol-input-style" value="${Link}" disabled>
                        </li>
                        <? if ($isEditPhotoAndVideo): ?>
                        <li>
                            <button record="${ID}" class="btn assol-btn remove action-remove-video" title="Удалить запись">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                            </button>
                        </li>
                        <? endif ?>
                    </ul>
                    <div class="embed-responsive embed-responsive-16by9">
                      <iframe class="embed-responsive-item" src="${Link}"></iframe>
                    </div>
                </script>

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
                                $avatar = empty($customer['Avatar'])
                                    ? base_url('public/img/avatar-example.png')
                                    : base_url("thumb/?src=/files/images/".$customer['FileName']."&w=221")
                                ?>
                                <img id="AvatarBig" src="<?= $avatar ?>" alt="avatar">
                            </div>
                        </div>

                        <? if ($isEditPhotoAndVideo): ?>
                        <form id="AvatarForm" action="<?= base_url('customer/'.$customer['ID'].'/avatar') ?>" class="avatar-file" method="post">
                            <input type="file" id="addClientAvatar" name="thumb" tabindex="-1" style="display: none;">
                            <div class="bootstrap-filestyle input-group">
                                <span class="group-span-filestyle " tabindex="0">
                                    <label for="addClientAvatar" class="btn btn assol-btn doc file ">
                                        <span class="icon-span-filestyle glyphicon glyphicon-paperclip"></span>
                                        <span class="buttonText">Загрузить фотографию</span>
                                    </label>
                                </span>
                            </div>
                        </form>
                        <? endif ?>
                    </div>

                    <div class="col-md-8 customer-video">

                        <div class="last-photo-session">
                            <div class="form-group">
                                <label for="DateLastPhotoSession">Дата последней фотосессии</label><br>
                                <div class="date-field">
                                    <input type="text" class="assol-input-style" id="DateLastPhotoSession" placeholder="Дата последней фотосессии" value="<?= toClientDate($customer['DateLastPhotoSession']) ?>">
                                </div>
                            </div>
                        </div>

                        <? if ($isEditPhotoAndVideo): ?>
                        <div class="form-group">
                            <label for="VerificationLink">Видео подтверждение (поле для вставки кода с youtube)</label>
                            <ul class="list-inline">
                                <li style="width: 90%">
                                    <input type="text" id="VerificationLink" class="assol-input-style">
                                </li>
                                <li>
                                    <button id="SaveVerificationLink" class="btn assol-btn add">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <? endif ?>
                        <div id="VideoVerificationList"></div>

                        <? if ($isEditPhotoAndVideo): ?>
                        <div class="form-group">
                            <label for="AmateurLink">Любительское видео (поле для вставки кода с youtube)</label>
                            <ul class="list-inline">
                                <li style="width: 90%">
                                    <input type="text" id="AmateurLink" class="assol-input-style">
                                </li>
                                <li>
                                    <button id="SaveAmateurLink" class="btn assol-btn add">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <? endif ?>
                        <div id="VideoAmateurList"></div>
                    </div>
                </div>

                <br>

                <? if ($isEditPhotoAndVideo): ?>
                <button id="SavePhotoAndVideo" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
                <? endif ?>
            </div>
            <? endif ?>
            <div role="tabpanel" class="tab-pane" id="Sites">
                <? if ($isEditDocumentAccess): ?>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="Name">Доступ к договорам и паспортным данным</label>

                                <div class="row sub-row">
                                    <div class="col-md-11 sub-col">
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
                                    <div class="col-md-1 sub-col">
                                        <button id="SaveEmployeeAccess" class="btn assol-btn save" title="Сохранить изменения">
                                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5"></div>
                    </div>
                <? endif ?>
                <? if ($isEditSites): ?>
                <div class="row">
                    <div class="col-md-7">
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
                    <div class="col-md-5"></div>
                </div>
                <? endif ?>

                <script id="siteTemplate" type="text/x-jquery-tmpl">
                    <div class="work-sites-block" record="${SiteID}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="site-item">
                                        <? if ($isEditSites): ?>
                                        <span class="glyphicon glyphicon-remove-circle action-remove-site" record="${ID}" aria-hidden="true" title="Удалить сайт"></span>
                                        <? endif ?>
                                        <span <? if (!$isEditSites): ?>style="padding-left: 10px;"<? endif ?>>${Sites[SiteID]}</span>
                                        <div class="arrow">
                                            <div class="arrow-in"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="form-group">
                                    <textarea class="assol-input-style note-site1" record="${ID}" <? if (!$isEditSitesDescription): ?> disabled="disabled" <? endif ?>>${Note}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </script>

                <?php
                // новый вариант таблицы с сайтами клиентки
                $this->load->view('form/customers/sites', array(
                        'isEditSites' => $isEditSites,
                        'CustomerID' => $customer['ID'],
                ));
                ?>

                <div id="siteList" class="work-sites-block-wrap" style="display: none;"></div>

                <? if ($isEditSitesDescription): ?>
                <button id="SaveSites" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
                <? endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="AdditionallyPane">

                <? if (IS_LOVE_STORY): ?>
                    <div id="react-customer-history-page"></div>
                    <br />
                    <br />
                <? endif; ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea class="assol-input-style" id="Additionally" rows="6" placeholder="Дополнительно"><?=$customer['Additionally']?></textarea>
                        </div>
                    </div>
                </div>

                <? if ($isEditAdditionallyPane): ?>
                    <button id="SaveAdditionally" class="btn assol-btn save" title="Сохранить изменения">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                        Сохранить
                    </button>
                <? endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="ReservationContactPane">

                <?php
                // новая таблица Заказа контактов
                $this->load->view('form/customers/contacts',
                        array(
                            'isEdit' => $isEditReservationContactPane,
                            'CustomerID' => $customer['ID'],
                            'employee_sites' => $employee_sites,
                        )
                );
                ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <textarea class="assol-input-style" id="ReservationContacts" rows="6" placeholder="Заказ контактов"><?=$customer['ReservationContacts']?></textarea>
                        </div>
                    </div>
                </div>

                <? if ($isEditReservationContactPane): ?>
                    <button id="SaveReservationContact" class="btn assol-btn save" title="Сохранить изменения">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                        Сохранить
                    </button>
                <? endif ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="Story">
                <style>
                    .story-avatar {
                        width: 50px;
                        height: 50px;
                        background-image: url(<?= base_url('public/img/avatar-example.png') ?>);
                        background-size: cover;
                        border-radius: 25px;
                        padding: 0;
                    }

                    .story-avatar .badge {
                        width: 100%;
                        height: 100%;
                        border-radius: 25px;
                        font-size: 25px;
                        padding-top: 12px;
                    }

                    #storyList img {
                        border-radius: 25px;
                        width: 50px;
                        height: 50px;
                    }
                </style>

                <div class="row assol-grey-panel" style="padding-top: 10px; margin-bottom: 15px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="StorySite">Сайт</label>
                            <div class="btn-group assol-select-dropdown" id="StorySite">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" id="StorySitesList">
                                    <?php foreach($employee_sites as $item): ?>
                                        <li>
                                            <input type="checkbox" id="StorySite_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                                            <label for="StorySite_<?= $item['ID'] ?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-default" id="StorySearchBtn" style="margin-top: 18px; padding: 9px 12px 8px;">
                            <span class="glyphicon glyphicon-search"></span> Поиск
                        </button>
                    </div>
                </div>

                <script type="text/javascript">
                    // поиск по сайтам
                    $(document).on('click', '#StorySearchBtn', function(){
                        $.CustomerCard.ReloadStoryList();
                    });
                </script>

                <script id="storyTemplate" type="text/x-jquery-tmpl">
                <form role="form" action="<?= base_url('customer/'.$customer['ID'].'/story/save') ?>" enctype="multipart/form-data" method="post">
                    <input type="hidden" name="RecordID" value="${ID}">
                    <div class="row">
                        <div class="col-md-1">
                            {{if Avatar>0}}
                            <a href="<?= base_url("thumb") ?>/?src=/files/images/${FileName}" data-lightbox="Story_Image_${ID}">
                                <img src="<?= base_url("thumb") ?>/?src=/files/images/${FileName}&w=50" alt="avatar">
                            </a>
                            {{else}}
                            <input type="file" name="thumb">
                            {{/if}}
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="Story_Date_${ID}">Дата</label>
                                <div class="date-field">
                                    <input type="text" class="assol-input-style" id="Story_Date_${ID}" name="Date" placeholder="Дата" value="${toClientDate(Date)}" <? if (!$isEditStory): ?> disabled="disabled" <? endif ?>>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="StorySite_${ID}">Сайт</label>
                                <div class="btn-group assol-select-dropdown" id="StorySite_${ID}">
                                    <div class="label-placement-wrap">
                                        <button class="btn" data-label-placement=""><span class="data-label">Все</span></button>
                                    </div>
                                    <button data-toggle="dropdown" class="btn dropdown-toggle">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <? foreach($sites as $item): ?>
                                            <li>
                                                <input type="radio" id="StorySite_${ID}_<?=$item['ID']?>" {{if (<?=$item['ID']?> == SiteID)}} checked="checked" {{/if}} name="StorySite" value="<?=$item['ID']?>">
                                                <label for="StorySite_${ID}_<?=$item['ID']?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                            </li>
                                        <? endforeach ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="Story_Name_${ID}">Имя</label>
                                <input type="text" class="assol-input-style" id="Story_Name_${ID}" name="Name" placeholder="Имя" value="${Name}" <? if (!$isEditStory): ?> disabled="disabled" <? endif ?>>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="Story_Note_${ID}"><?= IS_LOVE_STORY ? 'Описание' : 'Дополнительно' ?></label>
                                <input
                                    type="text"
                                    class="assol-input-style"
                                    id="Story_Note_${ID}"
                                    name="Note"
                                    placeholder="<?= IS_LOVE_STORY ? 'Описание' : 'Дополнительно' ?>"
                                    value="${Note}"
                                    <? if (!$isEditStory): ?> disabled="disabled" <? endif ?>
                                    data-toggle="story-popover"
                                    data-placement="left"
                                    data-trigger="focus"
                                    data-content="${Note}"
                                />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <? if ($isEditStory): ?>
                            <div class="form-group">
                                <label>&nbsp</label>
                                {{if ID > 0}}
                                    <div>
                                        <button type="submit" record="${ID}" class="btn assol-btn save action-save-story" title="Сохранить запись">
                                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                        </button>
                                        <button record="${ID}" class="btn assol-btn remove action-remove-story" title="Удалить запись">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </button>
                                    </div>
                                {{else}}
                                    <button type="submit" record="${ID}" class="btn assol-btn add action-save-story" title="Добавить запись">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                                        ДОБАВИТЬ
                                    </button>
                                {{/if}}
                            </div>
                            <? endif ?>
                        </div>
                    </div>
                </form>
                </script>

                <div id="storyList"></div>

                <script>
                    $(document).on('click', '#storyList button[type!=submit]', function (event) {
                        return false;
                    });
                </script>
            </div>

            <div role="tabpanel" class="tab-pane" id="Delivery">

                <style>
                    .dph-modal .modal-dialog {
                        width: 90%;
                        background: white;
                        max-width: 1180px;
                    }

                    .dph-modal iframe {
                        width: 100%;
                        height: 600px;
                    }
                    .site-name {
                        color: #2067b0;
                        font-weight: 700;
                    }
                </style>

                <div class="row assol-grey-panel" style="padding-top: 10px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="DeliverySite">Сайт</label>
                            <div class="btn-group assol-select-dropdown" id="DeliverySite">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" id="DeliverySitesList">
                                    <?php foreach($employee_sites as $item): ?>
                                        <li>
                                            <input type="checkbox" id="DeliverySite_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                                            <label for="DeliverySite_<?= $item['ID'] ?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-default" id="DeliverySearchBtn" style="margin-top: 18px; padding: 9px 12px 8px;">
                            <span class="glyphicon glyphicon-search"></span> Поиск
                        </button>
                    </div>
                </div>
                <div class="row">
                    <h5 style="text-transform: uppercase; margin-bottom: 0px; margin-top: 25px;">Выполненные доставки</h5>
                </div>
                <div class="row">
                    <div class="service-block-info-table">
                        <table>
                            <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th>Дата</th>
                                <th>Сайт</th>
                                <th>Переводчик</th>
                                <th>Мужчина</th>
                                <th>Девушка</th>
                                <th>Доставка</th>
                                <th>Благодарность</th>
                                <th>Фото</th>
                            </tr>
                            </thead>
                            <tbody id="DeliveryTableContent"></tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade dph-modal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Фото доставки</h4>
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

                <script type="text/javascript">
                    // клик по табу Доставки
                    $(document).on('click', 'a[aria-controls=Delivery]', function(){
                        loadDeliveryData();
                    });
                    // поиск по сайтам
                    $(document).on('click', '#DeliverySearchBtn', function(){
                        loadDeliveryData();
                    });
                    // загрузка данных
                    function loadDeliveryData(){
                        // учитываем фильтр по сайтам
                        var deliveryListInputs = $('#DeliverySitesList').find('input[type=checkbox]:checked'); // отмеченные чекбоксы
                        var deliveryListIds = []; // массив для ID выбранных сайтов
                        $.each(deliveryListInputs, function(key, item){
                            deliveryListIds[key] = $(item).val(); // собираем ID выбранных сайтов
                        });

                        $.post(
                            '/customer/getdelivery',
                            {
                                CustomerID: <?= $customer['ID']; ?>,
                                SiteIDs: ((deliveryListIds.length > 0) ? deliveryListIds.join() : '')
                            },
                            function(data){
                                if(data !== ''){
                                    $('#DeliveryTableContent').html('');
                                    $('#DeliveryTableContent').html(data);
                                }
                                else{
                                    $('#DeliveryTableContent').html('');
                                    $('#DeliveryTableContent').html('<tr><td colspan="9"><p class="text-center">Нет данных для отображения</p></td></tr>');
                                }
                            },
                            'html'
                        );
                    }
                    // модальное окно с фото Доставки
                    $(document).on('click', '.open-delivery-modal a[data-url]', function () {
                        var modal = $('.dph-modal');
                        var frame = modal.find('iframe');
                        var frameSrc = $(this).attr('data-url');
                        var delId = $(this).attr('data-delid');

                        modal.on('show.bs.modal', function () {
                            frame.attr("src", frameSrc);
                        });
                        modal.on('hidden.bs.modal', function () {
                            reloadBtnIcon(delId);
                            frame.html('');
                        });
                        modal.modal({show:true});
                    });
                    // обновить иконку на кнопке вызова модального окна с фото
                    function reloadBtnIcon(id){
                        $.post(
                            '/customer/cntimages',
                            {
                                DeliveryID: id
                            },
                            function(data){
                                if(data.cnt*1 > 0){
                                    $('#gl_icon_'+data.delivery).removeClass('glyphicon-plus');
                                    $('#gl_icon_'+data.delivery).addClass('glyphicon-folder-open');
                                }
                                else{
                                    $('#gl_icon_'+data.delivery).removeClass('glyphicon-folder-open');
                                    $('#gl_icon_'+data.delivery).addClass('glyphicon-plus');
                                }
                            },
                            'json'
                        );
                    }
                </script>

            </div>

            <? if($isShowRemove): ?>
                <div role="tabpanel" class="tab-pane" id="Remove">
                    <div class="row">
                        <div class="col-md-2">
                            <button id="CustomerMarkRemove" class="btn assol-btn remove form-control">УДАЛИТЬ</button>
                        </div>
                        <div class="col-md-4">
                            <button id="CustomerRemove" class="btn assol-btn remove form-control">УДАЛИТЬ БЕЗВОЗВРАТНО</button>
                        </div>
                        <div class="col-md-3">
                            <?php if($customer['IsDeleted']): ?>
                                Удалено: <?= date_format(date_create($customer['DateRemove']), 'Y-m-d') ?>
                            <?php endif ?>
                        </div>
                        <div class="col-md-3">
                            <button id="CustomerRestore" class="btn assol-btn add form-control">ВОССТАНОВИТЬ</button>
                        </div>
                    </div>

                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="ReasonForDeleted">Причина удаления</label>
                                <textarea class="assol-input-style" id="ReasonForDeleted" rows="3" placeholder="Причина удаления"><?=$customer['ReasonForDeleted']?></textarea>
                            </div>
                        </div>
                    </div>

                    <button id="SaveRemove" class="btn assol-btn save" title="Сохранить изменения">
                        <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                        Сохранить
                    </button>
                </div>
            <? endif ?>
        </div>

    </div>
</div>


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