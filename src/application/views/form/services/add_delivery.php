<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">ДОБАВИТЬ ДОСТАВКУ</h4>
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
                    <label for="deliveryDate">Дата</label>
                    <div class="date-field">
                        <input type="text" class="assol-input-style" id="deliveryDate">
                    </div>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="deliverySite">Сайт</label>
                    <div class="btn-group assol-select-dropdown" id="deliverySite">
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
                    <label for="deliveryUserTranslate">Переводчик</label>
                    <div class="btn-group assol-select-dropdown" id="deliveryUserTranslate">
                        <div class="label-placement-wrap">
                            <button class="btn" data-label-placement=""><span class="data-label">Выбрать</span></button>
                        </div>
                        <button data-toggle="dropdown" class="btn dropdown-toggle">
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach($translators as $item): ?>
                                <li>
                                    <input type="radio" id="UserTranslate_<?=$item['ID']?>" name="UserTranslate" value="<?=$item['ID']?>">
                                    <label for="UserTranslate_<?=$item['ID']?>"><?= $item['SName'] ?> <?= $item['FName'] ?></label>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="deliveryMen">Мужчина</label>
                    <input type="text" class="assol-input-style" id="deliveryMen">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="deliveryGirl">Девушка</label>
                    <input type="text" class="assol-input-style" id="deliveryGirl">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="delivery">Доставка</label>
                    <input type="text" class="assol-input-style" id="delivery">
                </div>
            </div>
            <div>
                <div class="form-group">
                    <label for="deliveryGratitude">Благодарность</label>
                    <input type="text" class="assol-input-style" id="deliveryGratitude">
                </div>
            </div>
        </div>
        <div class="service-block-settings-btns">
            <button id="SaveDelivery" class="btn assol-btn save" title="Сохранить изменения">
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

    $('#SaveDelivery').click(function () {
        $('#alertError').hide();

        var data = {
            date: toServerDate($('#deliveryDate').val()),
            site: $('#deliverySite').find("input:radio:checked").val(),
            men: $('#deliveryMen').val(),
            girl: $('#deliveryGirl').val(),
            delivery: $('#delivery').val(),
            userTranslate: $('#deliveryUserTranslate').find("input:radio:checked").val(),
            gratitude: $('#deliveryGratitude').val()
        };

        $.post('<?= current_url() ?>', data, callback, 'json');
    });


    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>