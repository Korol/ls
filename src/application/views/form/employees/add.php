<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">Добавить нового сотрудника</h4>
</div>
<div class="modal-body">
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="sName">Фамилия</label>
        <input type="text" class="form-control" id="sName" placeholder="Фамилия">
    </div>
    <div class="form-group">
        <label for="fName">Имя</label>
        <input type="text" class="form-control" id="fName" placeholder="Имя">
    </div>
    <div class="form-group">
        <label for="mName">Отчество</label>
        <input type="text" class="form-control" id="mName" placeholder="Отчество">
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
            window.location = '<?= base_url('employee') ?>/' + data.id + '/profile';
        } else {
            showErrorAlert(data.message)
        }
    }

    $('#bSubmit').click(function () {
        $('#alertError').hide();

        var data = {
            sName: $('#sName').val(),
            fName: $('#fName').val(),
            mName: $('#mName').val()
        };

        $.post('<?= current_url() ?>', data, callback, 'json');
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>