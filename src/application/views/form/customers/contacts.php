<?php
/**
 * @var $isEdit
 * @var $CustomerID
 */
?>
<?php /* шаблон таблицы */ ?>
<script id="contactsTableTmpl" type="text/x-jquery-tmpl">
    {{if records.length > 0}}
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Дата</th>
                <th>ID на сайте</th>
                <th>Сайт</th>
                <th>Имя</th>
                <th>Комментарий</th>
                <?php if(!empty($isEdit)): ?>
                <th>Действия</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            {{tmpl(records) '#contactsRowTmpl'}}
        </tbody>
    </table>
    {{else}}
        <h5 class="text-center">Нет данных для отображения</h5>
    {{/if}}
</script>
<?php /* шаблон строки в таблице */ ?>
<script id="contactsRowTmpl" type="text/x-jquery-tmpl">
    <tr id="contactsTableRow_${ID}">
        <td>${Date}</td>
        <td>${IDonSite}</td>
        <td>${SiteName}</td>
        <td>${Name}</td>
        <td>${Comment}</td>
        <?php if(!empty($isEdit)): ?>
        <td>
            <button class="btn btn-primary btn-xs" onclick="getContactToEdit(${ID});">
                <span class="glyphicon glyphicon-pencil"></span>
            </button>
            <button class="btn btn-danger btn-xs" onclick="if(confirm('Вы действительно хотите удалить этот запрос контактов?')){ removeContact(${ID}); }">
                <span class="glyphicon glyphicon-trash"></span>
            </button>
        </td>
        <?php endif; ?>
    </tr>
</script>

<script type="text/javascript">
    // клик по табу Контакты
    $(document).on('click', 'a[aria-controls=ReservationContactPane]', function(){
        fillContactsInfo();
    });
    // поиск по сайтам
    $(document).on('click', '#RCPSearchBtn', function(){
        fillContactsInfo();
    });
    // загрузка контента после обновления страницы с #ReservationContactPane в URL
    if(window.location.hash == '#ReservationContactPane') {
        fillContactsInfo();
    }
    
    // заполнение вкладки
    function fillContactsInfo() {
        // учитываем фильтр по сайтам
        var contactsListInputs = $('#RCPSitesList').find('input[type=checkbox]:checked'); // отмеченные чекбоксы
        var contactsListIds = []; // массив для ID выбранных сайтов
        $.each(contactsListInputs, function(key, item){
            contactsListIds[key] = $(item).val(); // собираем ID выбранных сайтов
        });
        $.post(
            '/Customer_Contacts/data',
            {
                CustomerID: <?= $CustomerID; ?>,
                SiteIDs: ((contactsListIds.length > 0) ? contactsListIds.join() : '')
            },
            function (data) {
                if(data.status){
                    $('#contactsTabInfo').html('');
                    $(function () {
                        $('#contactsTableTmpl').tmpl(data).appendTo('#contactsTabInfo');
                    });
                }
                else{
                    $('#contactsTabInfo').html('<h5>Нет данных для отображения. Status 0</h5>');
                }
            },
            'json'
        );
    }

    // добавление контакта
    $(document).on('click', '#addNewContactBtn', function () {
        $.post(
            '/Customer_Contacts/save',
            $('#addNewContact').serialize(),
            function(data){
                if(data.status){
                    $('#myModalContactAdd').modal('hide');
                    fillContactsInfo();
                }
            },
            'json'
        );
    });

    // запрос контакта на редактирование
    function getContactToEdit(id){
        $.post(
            '/Customer_Contacts/get',
            {
                ContactID: id
            },
            function(data){
                if(data.status){
                    if(data.contact){
                        console.log('test');
                        $('#editMName').val(data.contact.Name);
                        $('#editMDate').val(data.contact.Date);
                        $('#editMSiteID').val(data.contact.SiteID);
                        $('#editMIDonSite').val(data.contact.IDonSite);
                        $('#editMComment').val(data.contact.Comment);
                        $('#editMContactID').val(data.contact.ID);
                        $('#myModalContactEdit').modal('show');
                    }
                }
            },
            'json'
        );
    }

    // редактирование контакта
    $(document).on('click', '#editContactBtn', function () {
        $.post(
            '/Customer_Contacts/save',
            $('#editContact').serialize(),
            function(data){
                if(data.status){
                    $('#myModalContactEdit').modal('hide');
                    fillContactsInfo();
                }
            },
            'json'
        );
    });

    // удаление контакта
    function removeContact(id) {
        $.post(
            '/Customer_Contacts/remove',
            {
                ContactID: id
            },
            function(data){
                if(data.status){
                    $('#contactsTableRow_'+id).remove();
                }
            },
            'json'
        );
    }

    // datetimepickers
    $(function() {
        $('#contactNewDate').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY',
            viewMode: 'days'
        });

        $('#editMDate').datetimepicker({
            locale: 'ru',
            format: 'DD.MM.YYYY',
            viewMode: 'days'
        });
    });
</script>

<style>
    .contacts-tab-row{
        margin: 20px -15px;
    }
    .add-contact-tab-btn{
        margin-top: 20px;
        padding-top: 7px;
        padding-bottom: 7px;
    }
    .contacts-modal-select{
        max-width: 300px;
    }
    .contacts-modal-select option{
        padding: 7px 10px;
    }
</style>

<div class="row assol-grey-panel" style="padding-top: 10px;">
    <div class="col-md-4">
        <div class="form-group">
            <label for="RCPSite">Сайт</label>
            <div class="btn-group assol-select-dropdown" id="RCPSite">
                <div class="label-placement-wrap">
                    <button class="btn" data-label-placement>Выбрать</button>
                </div>
                <button data-toggle="dropdown" class="btn dropdown-toggle">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" id="RCPSitesList">
                    <?php foreach($employee_sites as $item): ?>
                        <li>
                            <input type="checkbox" id="RCPSite_<?= $item['ID'] ?>" value="<?= $item['ID'] ?>">
                            <label for="RCPSite_<?= $item['ID'] ?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <button class="btn btn-default" id="RCPSearchBtn" style="margin-top: 18px; padding: 9px 12px 8px;">
            <span class="glyphicon glyphicon-search"></span> Поиск
        </button>
    </div>
    <?php if(!empty($isEdit)): ?>
    <div class="col-md-6 clearfix">
        <button class="add-contact-tab-btn btn btn-success pull-right" data-toggle="modal" data-target="#myModalContactAdd">+ Добавить заказ контактов</button>
    </div>
    <?php endif; ?>
</div>

<div class="row contacts-tab-row" id="contactsTabInfo">Loading...</div>

<?php /* Add modal */ ?>
<div class="modal fade" id="myModalContactAdd" tabindex="-1" role="dialog" aria-labelledby="myModalContactAddLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ModalContactAddLabel">Добавить новый заказ контактов</h4>
            </div>
            <div class="modal-body">
                <form id="addNewContact">
                    <input type="hidden" name="CustomerID" value="<?= $CustomerID; ?>">
                    <input type="hidden" name="type" value="add">
                    <div class="form-group">
                        <label for="Name">Имя мужчины:</label>
                        <input type="text" name="Name" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="Date">Дата:</label>
                        <input type="text" name="Date" id="contactNewDate" class="form-control" />
                    </div>
                    <div class="form-group contacts-modal-select">
                        <label for="SiteID">Сайт:</label>
                        <select name="SiteID" class="form-control">
                        <?php foreach ($employee_sites as $site): ?>
                            <option value="<?= $site['ID']; ?>"><?= $site['Name']; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="IDonSite">ID мужчины на сайте:</label>
                        <input type="text" name="IDonSite" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="Comment">Комментарий:</label>
                        <textarea name="Comment" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                <button type="submit" class="btn btn-primary" id="addNewContactBtn">Сохранить</button>
            </div>
        </div>
    </div>
</div>
<?php /* /Add modal */ ?>

<?php /* Edit modal */ ?>
<div class="modal fade" id="myModalContactEdit" tabindex="-1" role="dialog" aria-labelledby="myModalContactEditLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ModalContactEditLabel">Редактировать заказ контактов</h4>
            </div>
            <div class="modal-body">
                <form id="editContact">
                    <input type="hidden" name="CustomerID" value="<?= $CustomerID; ?>">
                    <input type="hidden" name="type" value="edit">
                    <input type="hidden" name="ContactID" id="editMContactID" value="">
                    <div class="form-group">
                        <label for="Name">Имя мужчины:</label>
                        <input type="text" name="Name" id="editMName" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="Date">Дата:</label>
                        <input type="text" name="Date" id="editMDate" class="form-control" />
                    </div>
                    <div class="form-group contacts-modal-select">
                        <label for="SiteID">Сайт:</label>
                        <select name="SiteID" id="editMSiteID" class="form-control">
                            <?php foreach ($employee_sites as $site): ?>
                                <option value="<?= $site['ID']; ?>"><?= $site['Name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="IDonSite">ID мужчины на сайте:</label>
                        <input type="text" name="IDonSite" id="editMIDonSite" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label for="Comment">Комментарий:</label>
                        <textarea name="Comment" id="editMComment" class="form-control"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                <button type="submit" class="btn btn-primary" id="editContactBtn">Редактировать</button>
            </div>
        </div>
    </div>
</div>
<?php /* /Edit modal */ ?>