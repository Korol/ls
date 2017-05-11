<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="remoteDialogLabel">Редактирование новости</h4>
</div>

<div class="modal-body">
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <form id="addNewsForm" role="form" class="addNews" action="<?=current_url()?>" enctype="multipart/form-data" method="post">
        <div class="form-group sites">
            <label>Категория:</label>
            <div class="btn-group assol-select-dropdown" id="Site">
                <?php
                    $domen = array_search($record['SiteID'], array_column($sites, 'ID', 'Domen'));
                ?>
                <div class="label-placement-wrap">
                    <button class="btn" data-label-placement><?= $domen ?></button>
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

        <? if (IS_LOVE_STORY): ?>
            <div class="form-group sites" style="margin-left: 6px;">
                <label>Клиентка:</label>
                <div class="btn-group assol-select-dropdown" id="NewsCustomer">
                    <?php
                        $SName = array_search($record['CustomerID'], array_column($customers, 'ID', 'SName'));
                        $FName = array_search($record['CustomerID'], array_column($customers, 'ID', 'FName'));
                        $name = empty($SName) ? "Все клиентки" : $SName.' '.$FName;
                    ?>
                    <div class="label-placement-wrap">
                        <button class="btn" data-label-placement><?= $name ?></button>
                    </div>
                    <button data-toggle="dropdown" class="btn dropdown-toggle">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <input type="radio" id="Customer_0" name="Customer" value="0">
                            <label for="Customer_0">Все клиентки</label>
                        </li>
                        <? foreach($customers as $item): ?>
                            <li>
                                <?php $isChecked = $item['ID']==$record['CustomerID'] ?>
                                <input type="radio" id="Customer_<?=$item['ID']?>" name="Customer" value="<?=$item['ID']?>" <?= $isChecked ? 'checked' : ''?>>
                                <label for="Customer_<?=$item['ID']?>"><?= $item['SName'] ?> <?= $item['FName'] ?></label>
                            </li>
                        <? endforeach ?>
                    </ul>
                </div>
            </div>
        <? endif ?>

        <div class="form-group nameNews" style="<?= IS_LOVE_STORY ? 'width: 340px' : '' ?>">
            <label for="">Заголовок новости:</label>
            <input id="addNewsTitle" name="Title" type="text" class="form-control" value="<?= $record['Title'] ?>">
        </div>

        <div class="form-group">
            <label for="NewsText">Текст новости:</label>
            <textarea id="NewsText" class="tinymce-editor" name="Text"><?= $record['Text'] ?></textarea>
        </div>

        <div style="display: inline-block">
            <input type="file" id="addNewsFile" name="thumb">
            <script>
                $("#addNewsFile").filestyle({
                    input: false,
                    buttonText: "Прикрепить миниатюру новости",
                    buttonName: "btn assol-btn doc file",
                    iconName: "glyphicon glyphicon-paperclip"
                });
            </script>
        </div>

        <button id="submitEditNewsForm" type="submit" class="btn assol-btn save right" id="bSubmit" title="Сохранить изменения">
            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Сохранить изменения
        </button>
    </form>

    <div id="alertError" class="alert alert-danger" role="alert" style="display: none; margin-top: 20px">
        <h4>Ошибка!</h4>
        <p id="alertErrorMessage"></p>
    </div>
</div>

<div class="modal-footer">
</div>

<script>
    initTinymce('<?=base_url()?>');

    $('#addNewsForm').ajaxForm(function(data) {
        if (data.status) {
            $('#remoteDialog').modal('hide')
            $.News.ReloadNewsList();
        } else {
            showErrorAlert(data.message)
        }
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>