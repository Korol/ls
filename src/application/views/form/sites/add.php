<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">Добавить новый сайт</h4>
</div>
<div class="modal-body">
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="iName">Название сайта</label>
        <input type="text" class="form-control" id="iName" placeholder="Название сайта">
    </div>
    <div class="form-group">
        <label for="iDomen">Домен сайта</label>
        <input type="text" class="form-control" id="iDomen" placeholder="Домен сайта">
    </div>
    <div class="form-group">
        <label for="iNote">Примечание</label>
        <textarea class="form-control" id="iNote" rows="3" placeholder="Примечание"></textarea>
    </div>
    <div class="checkbox-line">
        <label>
            <input type="checkbox" id="iIsDealer">
            <mark></mark>
            <span>Дилерские</span>
        </label>
    </div>

    <div id="alertError" class="alert alert-danger" role="alert" style="display: none">
        <h4>Ошибка!</h4>
        <p id="alertErrorMessage"></p>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" id="bSubmit">Сохранить</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
</div>

<script>
    function callback(data) {
        if (data.status) {
            $('#remoteDialog').modal('hide')
        } else {
            showErrorAlert(data.message)
        }
    }

    $('#bSubmit').click(function () {
        $('#alertError').hide();

        var data = {
            name: $('#iName').val(),
            domen: $('#iDomen').val(),
            note: $('#iNote').val(),
            IsDealer: $('#iIsDealer').prop("checked") ? 1 : 0
        };

        $.post('<?= current_url() ?>', data, callback, 'json');
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>