<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">РЕДАКТИРОВАТЬ ВЕСТЕРН</h4>
</div>
<div class="modal-body">
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <div class="service-block" style="padding-top: 0">
        <div class="service-block-settings clear">
            <div>
                <div class="form-group">
                    <label for="westernDate">Дата</label>
                    <div class="date-field">
                        <input type="text" class="assol-input-style" id="westernDate" value="<?= toClientDate($record['Date']) ?>">
                    </div>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernGirl">Девушка</label>
                    <input type="text" class="assol-input-style" id="westernGirl" value="<?= $record['Girl'] ?>">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernMen">Мужчина</label>
                    <input type="text" class="assol-input-style" id="westernMen" value="<?= $record['Men'] ?>">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernSite">Сайт</label>
                    <div class="btn-group assol-select-dropdown" id="westernSite" style="padding-bottom: 2px">
                        <?php
                            $siteName = array_search($record['SiteID'], array_column($sites, 'ID', 'Name'));
                        ?>
                        <div class="label-placement-wrap">
                            <button class="btn" data-label-placement=""><span class="data-label"><?= $siteName ?></span></button>
                        </div>
                        <button data-toggle="dropdown" class="btn dropdown-toggle">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach($sites as $item): ?>
                                <li>
                                    <?php $isChecked = $item['ID']==$record['SiteID'] ?>
                                    <input type="radio" id="Site_<?=$item['ID']?>" name="Site" value="<?=$item['ID']?>" <?= $isChecked ? 'checked' : ''?>>
                                    <label for="Site_<?=$item['ID']?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernSum">Сумма</label>
                    <input type="text" class="assol-input-style" id="westernSum" value="<?= $record['Sum'] ?>">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernCode">Код</label>
                    <input type="text" class="assol-input-style" id="westernCode" value="<?= $record['Code'] ?>">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernIsSend">&nbsp;</label>
                    <div class="checkbox-line">
                        <label>
                            <?php $isChecked = $record['IsSend'] > 0 ?>
                            <input type="checkbox" id="westernIsSend" <?= $isChecked ? 'checked' : ''?>>
                            <mark></mark>
                            <? if (IS_LOVE_STORY): ?>
                                <span>% Кли-ки</span>
                            <? else: ?>
                                <span>выслали</span>
                            <? endif ?>
                        </label>
                    </div>
                </div>
            </div>
            <? if (IS_LOVE_STORY): ?>
                <div>
                    <div class="form-group">
                        <label for="westernIsPer">&nbsp;</label>
                        <div class="checkbox-line">
                            <label>
                                <?php $isChecked = $record['IsPer'] > 0 ?>
                                <input type="checkbox" id="westernIsPer" <?= $isChecked ? 'checked' : ''?>>
                                <mark></mark>
                                <span>% Пер-ка</span>
                            </label>
                        </div>
                    </div>
                </div>
            <? endif ?>
        </div>
        <div class="service-block-settings-btns">
            <button id="SaveWestern" class="btn assol-btn save" title="Сохранить изменения">
                <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                Сохранить
            </button>
        </div>
    </div>

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

    $('#SaveWestern').click(function () {
        $('#alertError').hide();

        var data = {
            date: toServerDate($('#westernDate').val()),
            girl: $('#westernGirl').val(),
            men: $('#westernMen').val(),
            site: $('#westernSite').find("input:radio:checked").val(),
            sum: $('#westernSum').val(),
            code: $('#westernCode').val(),
            isSend: $('#westernIsSend').prop('checked') ? 1 : 0,
            <? if (IS_LOVE_STORY): ?>
                isPer: $('#westernIsPer').prop('checked') ? 1 : 0
            <? endif ?>
        };

        $.post('<?= current_url() ?>', data, callback, 'json');
    });


    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>