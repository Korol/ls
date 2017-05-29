<?php
//$mensList = array(
//    array(
//        'ID' => 1,
//        'Name' => 'Men 1',
//        'Photo' => '',
//        'Comment' => 'Some comment about Men 1',
//    ),
//    array(
//        'ID' => 2,
//        'Name' => 'Men 2',
//        'Photo' => '',
//        'Comment' => 'Some comment about Men 2',
//    ),
//    array(
//        'ID' => 3,
//        'Name' => 'Men 3',
//        'Photo' => '',
//        'Comment' => 'Some comment about Men 3',
//    ),
//);
?>
<div class="row">
    <div class="col-md-12">
        <button class="btn btn-success" data-toggle="modal" data-target="#myModalMenAdd">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Добавить мужчину
        </button>
    </div>
</div>
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
                    <button class="btn btn-primary" title="Редактировать мужчину" data-toggle="modal" data-target="#myModalMen<?=$men['ID'];?>">
                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                    </button>
                    <button class="btn btn-danger" title="Удалить мужчину" onclick="if(confirm('Вы уверены, что хотите удалить этого мужчину?')){ removeMenItem(<?= $men['ID']; ?>, <?= $customerID; ?>); }">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                    </button>
                    <?php endif; // ($isEditMens) ?>
                </td>
                <!-- /Men Info -->
                <!-- Men Modal -->
                <div class="modal fade" id="myModalMen<?=$men['ID'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalMen<?=$men['ID'];?>Label">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="<?= base_url('customer/mens/save') ?>" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="type" value="update"/>
                                <input type="hidden" name="ID" value="<?= $men['ID']; ?>"/>
                                <input type="hidden" name="CustomerID" value="<?=$customerID; ?>"/>
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalMeeting<?=$men['ID'];?>Label">Информация о мужчине #<?=$men['ID'];?>: <?= $men['Name']; ?></h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <?php if(!empty($men['Photo'])): ?>
                                                <a href="<?= base_url("thumb") ?>/?src=/files/images/<?=$men['Photo'];?>" data-lightbox="Men_Image_Modal_<?=$men['ID'];?>">
                                                    <img src="<?= base_url("thumb") ?>/?src=/files/images/<?=$men['Photo'];?>&w=150" alt="avatar">
                                                </a><br/>
                                            <?php endif; ?>
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
                                                <input type="text" name="Name" class="form-control" value="<?=$men['Name']; ?>" placeholder="Укажите имя"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="">Комментарий о мужчине:</label>
                                                <textarea name="Comment" class="form-control" rows="4"><?= $men['Comment']; ?></textarea>
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
                <!-- /Modal -->
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