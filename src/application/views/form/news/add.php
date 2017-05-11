<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="remoteDialogLabel">Добавить новость</h4>
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
                <div class="label-placement-wrap">
                    <button class="btn" data-label-placement>Все новости</button>
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


        <? if (IS_LOVE_STORY): ?>
            <div class="form-group sites" style="margin-left: 6px;">
                <label>Клиентка:</label>
                <div class="btn-group assol-select-dropdown" id="NewsCustomer">
                    <div class="label-placement-wrap">
                        <button class="btn" data-label-placement>Все клиентки</button>
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
                                <input type="radio" id="Customer_<?=$item['ID']?>" name="Customer" value="<?=$item['ID']?>">
                                <label for="Customer_<?=$item['ID']?>"><?= $item['SName'] ?> <?= $item['FName'] ?></label>
                            </li>
                        <? endforeach ?>
                    </ul>
                </div>
            </div>
        <? endif ?>

        <div class="form-group nameNews" style="<?= IS_LOVE_STORY ? 'width: 340px' : '' ?>">
            <label for="addNewsTitle">Заголовок новости:</label>
            <input id="addNewsTitle"  type="text" name="Title" class="form-control">
        </div>

        <div class="form-group">
            <label for="NewsText">Текст новости:</label>
            <textarea id="NewsText" class="tinymce-editor" name="Text"></textarea>
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

        <button id="submitAddNewsForm" type="submit" class="btn assol-btn add right" title="Добавить новость">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить новость
        </button>

    </form>

    <div id="alertError" class="alert alert-danger" role="alert" style="display: none; margin-top: 20px">
        <h4>Ошибка!</h4>
        <p id="alertErrorMessage"></p>
    </div>
</div>

<script>
    initTinymce('<?=base_url()?>');

    $('#addNewsForm').ajaxForm(function(data) {
        if (data.status) {
            $('#remoteDialog').modal('hide');
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