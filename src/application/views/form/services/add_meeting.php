<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">ДОБАВИТЬ ВСТРЕЧУ</h4>
</div>
<div class="modal-body">
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <? if (IS_LOVE_STORY): ?>
        <div class="service-block" style="padding-top: 0">
            <div class="service-block-settings clear">
                <div>
                    <div class="form-group">
                        <label for="meetingDateIn">Дата приезда</label>
                        <div class="date-field">
                            <input type="text" class="assol-input-style" id="meetingDateIn">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingDateOut">Дата отъезда</label>
                        <div class="date-field">
                            <input type="text" class="assol-input-style" id="meetingDateOut">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingGirl">Девушка</label>
                        <input type="text" class="assol-input-style" id="meetingGirl">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingMen">Мужчина</label>
                        <input type="text" class="assol-input-style" id="meetingMen">
                    </div>
                </div>
                <div style="width: 50%">
                    <div class="form-group">
                        <label for="meetingSite">Сайт</label>
                        <div class="btn-group assol-select-dropdown" id="meetingSite">
                            <div class="label-placement-wrap">
                                <button class="btn" data-label-placement=""><span class="data-label">Выбрать</span></button>
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
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingCity">Город</label>
                        <input type="text" class="assol-input-style" id="meetingCity">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingTransfer">Трансфер</label>
                        <input type="text" class="assol-input-style" id="meetingTransfer">
                    </div>
                </div>
                    <div class="form-group" style="width: 50%">
                        <label for="meetingUserTranslateOrganizer">Переводчик создавший встречу</label>
                        <input type="text" class="assol-input-style" id="meetingUserTranslateOrganizer"
                               disabled="disabled" value="<?= $employee['SName'] ?> <?= $employee['FName'] ?>">
                    </div>
                    <div class="form-group" style="width: 50%">
                        <label for="meetingUserTranslateDuring">Переводчик во время встречи</label>
                        <input type="text" class="assol-input-style" id="meetingUserTranslateDuring">
                    </div>
                <div>
                    <div class="form-group">
                        <label for="meetingHousing">Жилье</label>
                        <input type="text" class="assol-input-style" id="meetingHousing">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingTranslate">Перевод</label>
                        <input type="text" class="assol-input-style" id="meetingTranslate">
                    </div>
                </div>
            </div>
            <div class="service-block-settings-btns">
                <button id="SaveMeeting" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
            </div>
        </div>
    <? else: ?>
        <div class="service-block" style="padding-top: 0">
            <div class="service-block-settings clear">
                <div>
                    <div class="form-group">
                        <label for="meetingDateIn">Дата приезда</label>
                        <div class="date-field">
                            <input type="text" class="assol-input-style" id="meetingDateIn">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingDateOut">Дата отъезда</label>
                        <div class="date-field">
                            <input type="text" class="assol-input-style" id="meetingDateOut">
                        </div>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingGirl">Девушка</label>
                        <input type="text" class="assol-input-style" id="meetingGirl">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingMen">Мужчина</label>
                        <input type="text" class="assol-input-style" id="meetingMen">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingSite">Сайт</label>
                        <div class="btn-group assol-select-dropdown" id="meetingSite">
                            <div class="label-placement-wrap">
                                <button class="btn" data-label-placement=""><span class="data-label">Выбрать</span></button>
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
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingUserTranslate">Переводчик</label>
                        <input type="text" class="assol-input-style" id="meetingUserTranslate">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingCity">Город</label>
                        <input type="text" class="assol-input-style" id="meetingCity">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingTransfer">Трансфер</label>
                        <input type="text" class="assol-input-style" id="meetingTransfer">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingHousing">Жилье</label>
                        <input type="text" class="assol-input-style" id="meetingHousing">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="meetingTranslate">Перевод</label>
                        <input type="text" class="assol-input-style" id="meetingTranslate">
                    </div>
                </div>
            </div>
            <div class="service-block-settings-btns">
                <button id="SaveMeeting" class="btn assol-btn save" title="Сохранить изменения">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                    Сохранить
                </button>
            </div>
        </div>
    <? endif ?>

    <div id="alertError" class="alert alert-danger" role="alert" style="display: none; margin-top: 20px">
        <h4>Ошибка!</h4>
        <p id="alertErrorMessage"></p>
    </div>
</div>

<script>
    function callback(data) {
        if (data.status) {
            $('#remoteDialog').modal('hide');
            $.AssolServices.ReloadServiceLists();
        } else {
            showErrorAlert(data.message)
        }
    }

    $('#SaveMeeting').click(function () {
        $('#alertError').hide();

        var data = {
            dateIn: toServerDate($('#meetingDateIn').val()),
            dateOut: toServerDate($('#meetingDateOut').val()),
            girl: $('#meetingGirl').val(),
            men: $('#meetingMen').val(),
            site: $('#meetingSite').find("input:radio:checked").val(),
            city: $('#meetingCity').val(),
            transfer: $('#meetingTransfer').val(),
            housing: $('#meetingHousing').val(),
            translate: $('#meetingTranslate').val()
        };

        <? if (IS_LOVE_STORY): ?>
            data['userTranslateDuring'] = $('#meetingUserTranslateDuring').val();
        <? else: ?>
            data['userTranslate'] = $('#meetingUserTranslate').val();
        <? endif ?>

        $.post('<?= current_url() ?>', data, callback, 'json');
    });


    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>