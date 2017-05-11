<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">ДОБАВИТЬ ВЕСТЕРН</h4>
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
                        <input type="text" class="assol-input-style" id="westernDate">
                    </div>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernGirl">Девушка</label>
                    <input type="text" class="assol-input-style" id="westernGirl">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernMen">Мужчина</label>
                    <input type="text" class="assol-input-style" id="westernMen">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernSite">Сайт</label>
                    <div class="btn-group assol-select-dropdown" id="westernSite" style="padding-bottom: 2px">
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
                    <label for="westernSum">Сумма</label>
                    <input type="text" class="assol-input-style" id="westernSum">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernCode">Код</label>
                    <input type="text" class="assol-input-style" id="westernCode">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="westernIsSend">&nbsp;</label>
                    <div class="checkbox-line">
                        <label>
                            <input type="checkbox" id="westernIsSend">
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
                                <input type="checkbox" id="westernIsPer">
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