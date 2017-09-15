<?php
$sites = (!empty($sites)) ? toolIndexArrayBy($sites, 'ID') : array();
?>
<style>
    .pm-site-select > option {
        padding-top: 3px;
        padding-bottom: 3px;
    }
    .display-popover{
        max-width: 90px;
        max-height: 20px;
        text-decoration: underline;
    }
    .display-popover:hover{
        cursor: help;
    }
    .mens-blacklist-chb{
        width: 25px;
        height: 25px;
        cursor: pointer;
        vertical-align: middle;
    }
    .mens-men-photo img {
        border-radius: 25px;
        width: 50px;
        height: 50px;
    }
    .mens-bl-ok{
        font-size: 14px;
        color: #1A712C;
        visibility: hidden;
    }
    .man-view-label{
        width: 25%;
        text-align: right;
        font-weight: bold !important;
    }
    .man-view-value{
        width: 75%;
    }
    .man-view-img{
        margin: 0 auto 15px;
    }
</style>
<?php //if($isEditMens): ?>
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#myModalMenAdd">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить мужчину
        </button>
    </div>
</div>
<?php //endif; // ($isEditMens) ?>
<div class="row" style="margin-top: 20px; margin-bottom: 30px;">
<!--    <div class="col-md-12">-->
    <?php if(!empty($mensList)): ?>
        <script src="/public/tablesorter/jquery.tablesorter.min.js"></script>
        <link rel="stylesheet" href="/public/tablesorter/blue/style.css">

        <table class="table table-bordered table-striped tablesorter" id="tableMens">
            <thead>
            <tr>
                <th>Фото</th>
                <th class="sortable">Имя&nbsp;&nbsp;</th>
                <th>ID и Никнейм</th>
                <th class="sortable">Возраст&nbsp;&nbsp;</th>
                <th>Страна и город</th>
                <th class="sortable">Сайт&nbsp;&nbsp;</th>
                <th>Переводчик</th>
                <th>Черный список</th>
                <th style="min-width: 100px;"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($mensList as $men): ?>
            <tr id="tr_<?= $men['ID']; ?>">
                <!-- Men Info -->
                <td class="mens-men-photo">
                    <?php if(!empty($men['Photo'])): ?>
                        <a href="<?= base_url("thumb") ?>/?src=/files/images/<?=$men['Photo'];?>" data-lightbox="Men_Image_View_Modal_<?=$men['ID'];?>">
                            <img src="<?= base_url("thumb") ?>/?src=/files/images/<?=$men['Photo'];?>&w=100" alt="avatar">
                        </a><br/>
                    <?php endif; // (!empty($men['Photo'])) ?>
                </td>
<!--                <td>--><?//= $men['Name']; ?><!--</td>-->
                <td>
                    <div class="display-popover" data-content="<?= $men['Comment']; ?>" data-original-title="">
                        <?= $men['Name']; ?>
                    </div>
                </td>
                <td><?= $men['IDonSite'] . '<br>' . $men['Nickname']; ?></td>
                <td><?= $men['Age']; ?></td>
                <td><?= $men['FromWhere']; ?></td>
                <td><?= (!empty($sites[$men['SiteID']]['Name'])) ? $sites[$men['SiteID']]['Name'] : '&dash;'; ?></td>
                <td><?= $men['EmployeeName']; ?></td>
                <td>
                    <span class="glyphicon glyphicon-ok mens-bl-ok" id="mblok_<?= $men['ID'];?>"></span>
                    <input type="checkbox" id="mbchb_<?= $men['ID'];?>" class="mens-blacklist-chb" onclick="editBlacklist(<?= $men['ID'];?>);" <?= ($men['Blacklist'] > 0) ? 'checked="checked"' : ''; ?>>
                </td>
                <td>
                    <button class="btn btn-info btn-xs" title="Просмотр" onclick="viewMen(<?= $men['ID']; ?>);">
                        <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
                    </button>
                    <?php if($isEditMens): ?>
                    <button class="btn btn-primary btn-xs" title="Редактировать мужчину" onclick="editMen(<?= $men['ID']; ?>);">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button>
                    <?php endif; // ($isEditMens) ?>
                    <?php if($isDeleteMens): ?>
                    <button class="btn btn-danger btn-xs" title="Удалить мужчину" onclick="if(confirm('Вы уверены, что хотите удалить этого мужчину?')){ removeMenItem(<?= $men['ID']; ?>, <?= $customerID; ?>); }">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </button>
                    <?php endif; ?>
                </td>
                <!-- /Men Info -->
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; // (!empty($mensList)) ?>
<!--    </div>-->
</div>

<script>
    // удаление мужчины
    function removeMenItem(id, customerID){
        $.post(
            '/customer/mens/remove',
            {ID : id, CustomerID : customerID},
            function(data){
                if(data*1 == 1){
                    $('#tr_'+id).remove();
                }
            },
            'text'
        );
    }
    // черный список
    function editBlacklist(id) {
        var bList = 1;
        if(!$('#mbchb_'+id).is(':checked')){
            bList = 0;
        }
        $.post(
            '/Customer_Mens/blacklist',
            {
                MenID: id,
                Blacklist: bList
            },
            function (data) {
                if(data.status){
                    $("#mblok_"+data.id).css('visibility', 'visible');
                    setTimeout(function() { $("#mblok_"+data.id).css('visibility', 'hidden'); }, 2000);
                }
            },
            'json'
        );
    }
    // редактируем мужчину в модальном окне
    function editMen(id) {
        $.post(
            '/Customer_Mens/getman',
            {
                ManID: id
            },
            function(data){
//                console.log(data);
                if(data.man){
                    $('#editH4ID').html(data.man.ID); // заголовок попапа
                    $('#editID').val(data.man.ID); // ID мужчины
                    $('#editH4Name').html(data.man.Name); // заголовок попапа
                    $('#editName').val(data.man.Name); // Имя
                    if(data.man.Photo){ // Фото
                        $('#editPhoto').css('display', 'block');
                        $('#editPhoto').attr('src', '<?= base_url("thumb") ?>/?src=/files/images/'+data.man.Photo+'&w=150');
                    }
                    else{
                        $('#editPhoto').css('display', 'none');
                    }
                    $('#editComment').val(data.man.Comment); // Комментарий
                    $('#editSiteID').val(data.man.SiteID); // Сайт
                    $('#editBlacklist').val(data.man.Blacklist); // Черный список
                    $('#editAge').val(data.man.Age); // Возраст
                    $('#editFromWhere').val(data.man.FromWhere); // Страна и город
                    $('#editNickname').val(data.man.Nickname); // Никнейм на сайте
                    $('#editEmployeeID').val(data.man.EmployeeID); // Создал сотрудник
//                    $('#editAdded').val(data.man.Added); // Дата добавления
                    $('#editIDonSite').val(data.man.IDonSite); // ID мужчины на Сайте
                    $('#myModalMenEdit').modal();
                }
            },
            'json'
        );
    }
    // просматриваем мужчину в модальном окне
    function viewMen(id) {
        $.post(
            '/Customer_Mens/getman',
            {
                ManID: id
            },
            function(data){
//                console.log(data);
                if(data.man){
                    $('#viewH4ID').html(data.man.ID); // заголовок попапа
                    $('#viewID').html(data.man.ID); // ID мужчины
                    $('#viewH4Name').html(data.man.Name); // заголовок попапа
                    $('#viewName').html(data.man.Name); // Имя
                    if(data.man.Photo){ // Фото
                        $('#viewPhoto').css('display', 'block');
                        $('#viewPhoto').attr('src', '<?= base_url("thumb") ?>/?src=/files/images/'+data.man.Photo+'&w=150');
                    }
                    else{
                        $('#viewPhoto').css('display', 'none');
                    }
                    $('#viewComment').html(data.man.Comment); // Комментарий
                    $('#viewSiteID').html(data.man.SiteName); // Сайт
                    $('#viewBlacklist').html((data.man.Blacklist*1 > 0) ? '<span class="text-danger">Да</span>' : '<span class="text-success">Нет</span>'); // Черный список
                    $('#viewAge').html(data.man.Age); // Возраст
                    $('#viewFromWhere').html(data.man.FromWhere); // Страна и город
                    $('#viewNickname').html(data.man.Nickname); // Никнейм на сайте
                    $('#viewEmployeeName').html(data.man.EmployeeName); // Создал сотрудник
                    $('#viewAdded').html(data.man.Added); // Дата добавления
                    $('#viewIDonSite').html(data.man.IDonSite); // ID мужчины на Сайте
                    $('#myModalMenView').modal();
                }
            },
            'json'
        );
    }

    jQuery(document).ready(function ($) {
        $("#tableMens").tablesorter({
            selectorHeaders: 'thead th.sortable' // <-- здесь указываем класс, который определяет те столбцы, по которым будет работать сортировка
        });

        // popover комментариев
        $('.display-popover').popover({
            placement: 'right',
            trigger: 'hover'
        });
    });
</script>

<!-- Add Modal -->
<div class="modal fade" id="myModalMenAdd" tabindex="-1" role="dialog" aria-labelledby="myModalMenAddLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('customer/mens/save') ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="CustomerID" value="<?=$customerID; ?>"/>
                <input type="hidden" name="type" value="add"/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalMenAddLabel">Добавить мужчину</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Фото мужчины:</label>
                                <input type="file" name="Photo"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Name">Имя мужчины:</label>
                                <input type="text" name="Name" class="form-control" placeholder="Укажите имя"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Age">Возраст мужчины:</label>
                                <input type="text" name="Age" class="form-control" placeholder="Укажите возраст мужчины"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="FromWhere">Страна и город:</label>
                                <input type="text" name="FromWhere" class="form-control" placeholder="Укажите страну и город мужчины"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="SiteID">Сайт:</label>
                                <select name="SiteID" class="form-control pm-site-select">
                                <?php
                                foreach($mensSitesList as $msla){
                                    echo '<option value="' . $msla['ID'] . '">' . $msla['Name'] . '</option>';
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="IDonSite">ID мужчины на сайте:</label>
                                <input type="text" name="IDonSite" class="form-control" placeholder="Укажите ID мужчины на сайте"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Nickname">Никнейм мужчины на сайте:</label>
                                <input type="text" name="Nickname" class="form-control" placeholder="Укажите никнейм мужчины на сайте"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="EmployeeID">Переводчик:</label>
                                <select name="EmployeeID" class="form-control pm-site-select">
                                    <?php
                                    foreach($translators as $t_key => $trans){
                                        echo '<option value="' . $t_key . '">' . $trans . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Комментарий о мужчине:</label>
                                <textarea name="Comment" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Modal -->

<!--Edit Modal-->
<div class="modal fade" id="myModalMenEdit" tabindex="-1" role="dialog" aria-labelledby="myModalMenEditLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('customer/mens/save'); ?>" name="editMan" method="post" enctype="multipart/form-data">
                <input type="hidden" name="type" value="update"/>
                <input type="hidden" name="ID" id="editID" value=""/>
                <input type="hidden" name="CustomerID" id="editCustomerID" value="<?= $customerID; ?>"/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalMeetingEditLabel">Информация о мужчине #<span id="editH4ID"></span>: <span id="editH4Name"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <img src="" id="editPhoto" alt="avatar">
                            <div class="form-group">
                                <label for="">Фото мужчины:</label>
                                <input type="file" name="Photo"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Name">Имя мужчины:</label>
                                <input type="text" name="Name" class="form-control" id="editName" value="" placeholder="Укажите имя"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Age">Возраст мужчины:</label>
                                <input type="text" name="Age" id="editAge" class="form-control" placeholder="Укажите возраст мужчины"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="FromWhere">Страна и город:</label>
                                <input type="text" name="FromWhere" id="editFromWhere" class="form-control" placeholder="Укажите страну и город мужчины"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="SiteID">Сайт:</label>
                                <select name="SiteID" id="editSiteID" class="form-control pm-site-select">
                                    <?php
                                    foreach($mensSitesList as $msl){
                                        echo '<option value="' . $msl['ID'] . '">' . $msl['Name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="IDonSite">ID мужчины на сайте:</label>
                                <input type="text" name="IDonSite" id="editIDonSite" class="form-control" placeholder="Укажите ID мужчины на сайте"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Nickname">Никнейм мужчины на сайте:</label>
                                <input type="text" name="Nickname" id="editNickname" class="form-control" placeholder="Укажите никнейм мужчины на сайте"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="Blacklist">Мужчина в Черном списке:</label>
                                <select name="Blacklist" id="editBlacklist" class="form-control pm-site-select">
                                    <option value="0">Нет</option>
                                    <option value="1">Да</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="EmployeeID">Переводчик:</label>
                                <select name="EmployeeID" id="editEmployeeID" class="form-control pm-site-select">
                                    <?php
                                    foreach($translators as $t_key => $trans){
                                        echo '<option value="' . $t_key . '">' . $trans . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Комментарий о мужчине:</label>
                                <textarea name="Comment" id="editComment" class="form-control" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить изменения</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--/Edit Modal-->

<!--View Modal-->
<div class="modal fade" id="myModalMenView" tabindex="-1" role="dialog" aria-labelledby="myModalMenViewLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalMenViewLabel">Информация о мужчине #<span id="viewH4ID"></span>: <span id="viewH4Name"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <img src="" id="viewPhoto" alt="avatar" class="man-view-img">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped" cellpadding="10" cellspacing="0">
                            <tbody>
                            <tr>
                                <td class="man-view-label">Имя мужчины:</td>
                                <td class="man-view-value" id="viewName"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Возраст мужчины:</td>
                                <td class="man-view-value" id="viewAge"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Страна и город:</td>
                                <td class="man-view-value" id="viewFromWhere"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Сайт:</td>
                                <td class="man-view-value" id="viewSiteID"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">ID мужчины на сайте:</td>
                                <td class="man-view-value" id="viewIDonSite"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Никнейм:</td>
                                <td class="man-view-value" id="viewNickname"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Переводчик:</td>
                                <td class="man-view-value" id="viewEmployeeName"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Дата добавления:</td>
                                <td class="man-view-value" id="viewAdded"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">В Черном списке:</td>
                                <td class="man-view-value" id="viewBlacklist"></td>
                            </tr>
                            <tr>
                                <td class="man-view-label">Комментарий:</td>
                                <td class="man-view-value" id="viewComment"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!--/View Modal-->