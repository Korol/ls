<ol class="breadcrumb assol-grey-panel">
    <li><a href="<?= base_url('training') ?>">Обучение</a></li>
    <? if (!empty($bread)): ?>
        <? foreach ($bread as $item): ?>
            <li><a href="<?= base_url('training/'.$item['ID']) ?>"><?= $item['Name'] ?></a></li>
        <? endforeach ?>
    <? endif ?>
    <li class="active"><?= $record['Name'] ?></li>
</ol>

<div class="edit-training-article-page">
<p><b>РЕДАКТИРОВАТЬ СТАТЬЮ</b></p>
<br>

<div class="form-group">
    <label for="TrainingName">Название статьи:</label>
    <input id="TrainingName" type="text" class="assol-input-style fullwidth defaultheight" placeholder="Название статьи" value="<?= $record['Name'] ?>">
</div>

<div class="form-group">
    <label for="TrainingContent">Текст статьи:</label>
    <textarea id="TrainingContent" class="tinymce-editor"><?= $record['Content'] ?></textarea>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group">
            <label>Родительская категория</label>
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
    </div>
    <div class="col-md-9">
        <div class="form-group">
            <label for="employeeAccess">Доступ</label>
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div class="form-group">
                        <button type="button" class="btn assol-btn add form-control" id="bSubmit">СОХРАНИТЬ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="alertError" class="alert alert-danger" role="alert" style="display: none">
    <h4>Ошибка!</h4>
    <p id="alertErrorMessage"></p>
</div>

<script>
    // 1. Устанавливаем родительский каталог
    $('#CurrentCategory')
        .val(<?= $Parent ?>)
        .prop("checked", true);

    $.each(<?= json_encode($rights) ?>, function(key, right) {
        $('#ex'+right.ID).click();
    });

    var CurrentURL = '<?= current_url() ?>';
    
    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
</script>
</div>