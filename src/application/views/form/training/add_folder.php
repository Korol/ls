<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="remoteDialogLabel">Добавить папку</h4>
</div>
<div class="modal-body">
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger" role="alert">
            <strong>Ошибка!</strong> <?= $errorMessage ?>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="Name">Название папки</label>
        <input type="text" class="form-control" id="Name" placeholder="Название папки">
    </div>

    <div class="form-group">
        <label>Категория</label>
        <div class="btn-group assol-select-dropdown" id="Parent">
            <div class="label-placement-wrap">
                <button class="btn" data-label-placement>Текущая категория</button>
            </div>
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <input type="radio" id="CurrentCategory" name="Parent" value="0">
                    <label for="CurrentCategory" class="fixed">Текущая категория</label>
                </li>
                <li>
                    <input type="radio" id="Parent_0" name="Parent" value="0">
                    <label for="Parent_0" class="fixed">Без категорий</label>
                </li>

                <?php foreach($folders as $item): ?>
                    <li>
                        <input type="radio" id="Parent_<?=$item['ID']?>" name="Parent" value="<?=$item['ID']?>">
                        <label for="Parent_<?=$item['ID']?>"><?=$item['Name']?></label>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>

    <div class="form-group">
        <label for="Name">Доступ</label>
        <div class="btn-group assol-select-dropdown" id="employeeAccess">
            <div class="label-placement-wrap">
                <button class="btn" data-label-placement>Доступна всем</button>
            </div>
            <button class="btn dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
            <ul class="dropdown-menu">
                <?php foreach($employees as $item): ?>
                    <li>
                        <input type="checkbox" id="ex<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                        <label for="ex<?= $item['ID'] ?>"><?= $item['SName'] ?> <?= $item['FName'] ?></label>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>

    <div id="alertError" class="alert alert-danger" role="alert" style="display: none">
        <h4>Ошибка!</h4>
        <p id="alertErrorMessage"></p>
    </div>
</div>
<div class="modal-footer">
    <div class="form-group">
        <button type="button" class="btn assol-btn add form-control" id="bSubmit">Добавить папку</button>
    </div>
</div>

<script>
    // Устанавливаем родительский каталог
    $('#CurrentCategory')
        .val($.AssolTraining.GetParent())
        .prop("checked", true);

    function callback(data) {
        if (data.status) {
            $('#remoteDialog').modal('hide');
            $.AssolTraining.SetParent($('#Parent').find("input:radio:checked").val());
            $.AssolTraining.ReloadTrainingList();
        } else {
            showErrorAlert(data.message)
        }
    }

    $('#bSubmit').click(function () {
        $('#alertError').hide();

        var employees = [];
        $('#employeeAccess').find("input:checked").each(function(){
            employees.push($(this).val());
        });

        var data = {
            Name: $('#Name').val(),
            Parent: $('#Parent').find("input:radio:checked").val(),
            Employees: employees
        };

        $.post('<?= current_url() ?>', data, callback, 'json');
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>