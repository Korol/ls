<?php
$sites = (!empty($sites)) ? toolIndexArrayBy($sites, 'ID') : array();
?>
<style>
    .pm-site-select > option {
        padding-top: 3px;
        padding-bottom: 3px;
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
    <div class="col-md-12">
    <?php if(!empty($mensList)): ?>
        <style>
            .mens-men-photo img {
                border-radius: 25px;
                width: 50px;
                height: 50px;
            }
        </style>
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Сайт</th>
                <th>Фото</th>
                <th>Комментарий</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($mensList as $men): ?>
            <tr id="tr_<?= $men['ID']; ?>">
                <!-- Men Info -->
                <td><?= $men['ID']; ?></td>
                <td><?= $men['Name']; ?></td>
                <td><?= (!empty($sites[$men['SiteID']]['Name'])) ? $sites[$men['SiteID']]['Name'] : '&dash;'; ?></td>
                <td class="mens-men-photo">
                <?php if(!empty($men['Photo'])): ?>
                    <a href="<?= base_url("thumb") ?>/?src=/files/images/<?=$men['Photo'];?>" data-lightbox="Men_Image_View_Modal_<?=$men['ID'];?>">
                        <img src="<?= base_url("thumb") ?>/?src=/files/images/<?=$men['Photo'];?>&w=100" alt="avatar">
                    </a><br/>
                <?php endif; // (!empty($men['Photo'])) ?>
                </td>
                <td><?= $men['Comment']; ?></td>
                <td>
                    <?php if($isEditMens): ?>
                    <button class="btn btn-primary" title="Редактировать мужчину" onclick="editMen(<?= $men['ID']; ?>);">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button>
                    <?php endif; // ($isEditMens) ?>
                    <?php if($isDeleteMens): ?>
                    <button class="btn btn-danger" title="Удалить мужчину" onclick="if(confirm('Вы уверены, что хотите удалить этого мужчину?')){ removeMenItem(<?= $men['ID']; ?>, <?= $customerID; ?>); }">
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
    </div>
</div>

<script>
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
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="SiteID">Сайт:</label>
                                <select name="SiteID" id="editSiteID" class="form-control pm-site-select">
                                    <?php
                                    foreach($mensSitesList as $msl){
//                                        $selected = ($msl['ID'] == $men['SiteID']) ? 'selected="selected"' : '';
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
<script type="text/javascript">
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
                    $('#editH4ID').html(data.man.ID);
                    $('#editID').val(data.man.ID);
                    $('#editH4Name').html(data.man.Name);
                    $('#editName').val(data.man.Name);
                    if(data.man.Photo){
                        $('#editPhoto').css('display', 'block');
                        $('#editPhoto').attr('src', '<?= base_url("thumb") ?>/?src=/files/images/'+data.man.Photo+'&w=150');
                    }
                    else{
                        $('#editPhoto').css('display', 'none');
                    }
                    $('#editComment').val(data.man.Comment);
                    $('#editSiteID').val(data.man.SiteID);
                    $('#myModalMenEdit').modal();
                }
            },
            'json'
        );
    }
</script>