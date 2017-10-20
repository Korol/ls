<?php
/**
 * @var $cards
 * @var $currencies
 */
$isEdit = true;
?>
<style>
    #Currency option,
    #editCurrency option,
    #editActive option {
        padding: 5px;
    }
    .card-table-block {
        margin: 20px 0;
    }
    #cardTable {
        padding: 0;
    }
    #editCurrency,
    #editActive{
        width: 150px;
    }
    .card-messages .alert {
        padding: 9px;
        margin-bottom: 0;
        margin-top: 0;
        display: none;
        text-align: center;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <h4>Кредитные карты</h4>
    </div>
</div>
<div class="row">
    <div class="col-md-4 card-messages">
        <div id="formError" class="alert alert-danger alert-dismissible" role="alert">
            Карта НЕ добавлена! Заполните все поля!
        </div>
        <div id="formSuccess" class="alert alert-success alert-dismissible" role="alert">
            Новая карта успешно добавлена!
        </div>
        <div id="editSuccess" class="alert alert-success alert-dismissible" role="alert">
            Карта успешно обновлена!
        </div>
    </div>
    <div class="col-md-8">
        <form class="form-inline pull-right" id="addCard">
            <input type="hidden" name="isNew" value="1">
            <div class="form-group">
                <label class="sr-only" for="Name">Название карты</label>
                <input name="Name" type="text" class="form-control" id="Name" placeholder="Название карты *">
            </div>
            <div class="form-group">
                <label class="sr-only" for="Number">Номер карты</label>
                <input name="Number" type="text" class="form-control" id="Number" placeholder="Номер карты *">
            </div>
            <div class="form-group">
                <label class="sr-only" for="Currency">Валюта</label>
                <select name="Currency" class="form-control" id="Currency">
                <?php
                if(!empty($currencies)){
                    foreach ($currencies as $c_key => $currency) {
                ?>
                    <option value="<?= $c_key; ?>"><?= $currency; ?></option>
                <?php
                    }
                }
                ?>
                </select>
            </div>
            <button type="button" id="addNewCard" class="btn btn-default">Добавить карту</button>
        </form>
    </div>
</div>

<div class="row card-table-block">
    <div class="col-md-12" id="cardTable"></div>
</div>


<script>
    jQuery(document).ready(function ($) {
        // добавляем новую карту
        $('#addNewCard').click(function () {
            $.post(
                '/cards/save/',
                $('#addCard').serialize(),
                function(data){
                    if(data.status){
                        $('#formSuccess').show().delay(4000).fadeOut();
                        loadCardTable();
                        $('#addCard')[0].reset();
                    }
                    else{
                        showFormError();
                    }
                },
                'json'
            );
        });

        // редактирование карты
        $('#editCardBtn').click(function () {
            $.post(
                '/cards/save/',
                $('#editCard').serialize(),
                function(data){
                    if(data.status){
                        $('#myModalCardEdit').modal('hide');
                        $('#editSuccess').show().delay(4000).fadeOut();
                        loadCardTable();
                    }
                },
                'json'
            );
        });
    });

    function showFormError() {
        $('#formError').show().delay(4000).fadeOut();
    }

    function loadCardTable(){
        $.post(
            '/cards/data/',
            function (data) {
                if(data.status){
                    $('#cardTable').html('');
                    $(function () {
                        $('#cardTableTmpl').tmpl(data).appendTo('#cardTable');
                    });
                }
                else{
                    $('#cardTable').html('<h5>Нет данных для отображения</h5>');
                }
            },
            'json'
        );
    }

    loadCardTable();

    // запрос карты на редактирование
    function getCardToEdit(id){
        $.post(
            '/cards/get/',
            {
                ID: id
            },
            function(data){
                if(data.status){
                    if(data.card){
                        $('#editID').val(data.card.ID);
                        $('#editName').val(data.card.Name);
                        $('#editNumber').val(data.card.Number);
                        $('#editCurrency').val(data.card.Currency);
                        $('#editActive').val(data.card.Active);
                        $('#myModalCardEdit').modal('show');
                    }
                }
            },
            'json'
        );
    }

    // удаление карты
    function removeCard(id) {
        $.post(
            '/cards/remove/',
            {
                ID: id
            },
            function(data){
                if(data.status){
                    $('#cardTableRow_'+id).remove();
                }
            },
            'json'
        );
    }
</script>

<?php /* шаблон таблицы */ ?>
<script id="cardTableTmpl" type="text/x-jquery-tmpl">
    {{if records.length > 0}}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название карты</th>
                <th>Номер карты</th>
                <th>Валюта карты</th>
                <th>Активна</th>
                <?php if(!empty($isEdit)): ?>
                <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            {{tmpl(records) '#cardRowTmpl'}}
        </tbody>
    </table>
    {{else}}
        <h5 class="text-center">Нет данных для отображения</h5>
    {{/if}}
</script>
<?php /* шаблон строки в таблице */ ?>
<script id="cardRowTmpl" type="text/x-jquery-tmpl">
    <tr id="cardTableRow_${ID}">
        <td>${ID}</td>
        <td>${Name}</td>
        <td>${Number}</td>
        <td>${Currency}</td>
        <td>{{if $data.Active > 0}}Да{{else}}Нет{{/if}}</td>
        <?php if(!empty($isEdit)): ?>
        <td>
            <button class="btn btn-default btn-xs" onclick="getCardToEdit(${ID});">
                <span class="glyphicon glyphicon-pencil"></span>
            </button>
            <button class="btn btn-default btn-xs" onclick="if(confirm('Вы действительно хотите удалить эту карту?')){ removeCard(${ID}); }">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </td>
        <?php endif; ?>
    </tr>
</script>

<?php /* Edit modal */ ?>
    <div class="modal fade" id="myModalCardEdit" tabindex="-1" role="dialog" aria-labelledby="myModalCardEditLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="ModalCardEditLabel">Редактировать карту</h4>
                </div>
                <div class="modal-body">
                    <form id="editCard">
                        <input type="hidden" name="isNew" value="0">
                        <input type="hidden" name="ID" id="editID">
                        <div class="form-group">
                            <label for="editName">Название карты</label>
                            <input name="Name" type="text" class="form-control" id="editName" placeholder="Название карты">
                        </div>
                        <div class="form-group">
                            <label for="editNumber">Номер карты</label>
                            <input name="Number" type="text" class="form-control" id="editNumber" placeholder="Номер карты">
                        </div>
                        <div class="form-group">
                            <label for="editCurrency">Валюта</label>
                            <select name="Currency" class="form-control" id="editCurrency">
                                <?php
                                if(!empty($currencies)){
                                    foreach ($currencies as $c_key => $currency) {
                                        ?>
                                        <option value="<?= $c_key; ?>"><?= $currency; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editActive">Активна</label>
                            <select name="Active" class="form-control" id="editActive">
                                <option value="0">Нет</option>
                                <option value="1">Да</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                    <button type="submit" class="btn btn-primary" id="editCardBtn">Редактировать</button>
                </div>
            </div>
        </div>
    </div>
<?php /* /Edit modal */ ?>