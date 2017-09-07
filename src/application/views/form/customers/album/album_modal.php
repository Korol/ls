<?php
/**
 * @var $AlbumID
 * @var $ImageID
 * @var $images
 * @var $sites
 * @var $CustomerID
 */
?>
<?php if(!empty($AlbumID) && !empty($ImageID) && !empty($images) && !empty($CustomerID)): ?>
<style>
    .mac-opt{
        padding: 7px 15px;
    }
    .mac-checkbox{
        width: 25px;
        height: 25px;
        cursor: pointer;
    }
    .mac-ok {
        color: green;
        font-size: 30px;
    }
    .mac-textarea{
        padding-top: 5px !important;
        padding-bottom: 5px !important;
    }
    .mac-filter-dropdown{
        /*position: relative;*/
        z-index: 5000;
        max-height: 300px;
        overflow-y: scroll;
    }
</style>
<div id="carousel-example-generic-album_<?=$AlbumID;?>" class="carousel slide" data-ride="carousel" data-interval="false">
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
        <?php foreach($images as $image): ?>
        <div class="img-item item <?= ($image['ImageID'] == $ImageID) ? 'active' : ''; ?>" id="mc_item_<?=$image['ImageID'];?>">
            <img src="<?= base_url("thumb") ?>?src=/files/images/<?=$image['ImageID'];?>.<?=$image['ext'];?>" class="img-responsive mac-image">

            <?php /* фильтр по сайтам */ ?>
            <?php if(!empty($sites)): ?>
                <div class="row assol-grey-panel" style="padding-top: 10px; margin: 15px 0;">
                    <div class="col-md-6 col-md-offset-2">
                        <div class="form-group">
                            <label for="AlbumModalSite_<?=$image['ImageID'];?>">Сайт</label>
                            <div class="btn-group assol-select-dropdown" id="AlbumModalSite_<?=$image['ImageID'];?>">
                                <div class="label-placement-wrap">
                                    <button class="btn" data-label-placement>Выбрать</button>
                                </div>
                                <button data-toggle="dropdown" class="btn dropdown-toggle mac-dd-toggle" id="macddtoggle_<?=$image['ImageID'];?>">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu mac-filter-dropdown" id="AlbumModalSitesList_<?=$image['ImageID'];?>">
                                    <?php foreach($sites as $site_item): ?>
                                        <li>
                                            <input type="checkbox" id="AlbumModalSite_<?=$image['ImageID'];?>_<?= $site_item['ID']; ?>" value="<?= $site_item['ID']; ?>">
                                            <label for="AlbumModalSite_<?=$image['ImageID'];?>_<?= $site_item['ID']; ?>"><?= $site_item['Name']; ?></label>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-default" id="AlbumModalSearchBtn_<?=$image['ImageID'];?>" style="margin-top: 18px; padding: 9px 12px 8px;">
                            <span class="glyphicon glyphicon-search"></span> Поиск
                        </button>
                    </div>
                </div>

                <script type="text/javascript">
                    // поиск по сайтам
                    $(document).on('click', '#AlbumModalSearchBtn_<?=$image['ImageID'];?>', function(){
                        var amsb_ex = this.id.split('_'); // получаем ID картинки (amsb_ex[1])
                        loadAlbumImageData(amsb_ex[1]); // загружаем данные по выбранным сайтам
                    });
                </script>
            <?php endif; // sites ?>
            <?php /* /фильтр по сайтам */ ?>

            <?php /* результат работы фильтра – список сайтов + мужчины на сайтах */ ?>
            <div class="row" id="AlbumImageSitesMens_<?=$image['ImageID'];?>" style="margin: 15px 0;"></div>
            <?php /* /результат работы фильтра – список сайтов + мужчины на сайтах */ ?>

            <?php /*if(!empty($image['ToSites'])): ?>
            <?php foreach($image['ToSites'] as $ts): ?>
            <div class="row mac-site-row">
                <div class="col-md-3">
                    <div class="form-group work-sites-block mac-wsb">
                        <div class="site-item">
                            <span style="padding-left: 10px;"><?= $ts['SiteName']; ?></span>
                            <div class="arrow">
                                <div class="arrow-in"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 col-md-offset-5 clearfix">
                    <span class="glyphicon glyphicon-ok mac-ok hidden pull-right" id="oksite_<?=$ts['SiteID'];?>_<?=$image['ImageID'];?>" aria-hidden="true"></span>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="Connect_<?=$ts['SiteID'];?>_<?=$image['ImageID'];?>">В профайле</label>
                        <select class="form-control mac-site-connect" name="Connect_<?=$ts['SiteID'];?>_<?=$image['ImageID'];?>" id="Connect_<?=$ts['SiteID'];?>_<?=$image['ImageID'];?>">
                            <option class="mac-opt" value="0" <?=($ts['SiteConnect'] == 0) ? 'selected="selected"' : ''; ?>>Нет</option>
                            <option class="mac-opt" value="1" <?=($ts['SiteConnect'] == 1) ? 'selected="selected"' : ''; ?>>Есть</option>
                        </select>
                    </div>
                </div>
                <?php if(!empty($image['ToMens'][$ts['SiteID']])): ?>
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" style="margin: 0px auto 20px;">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Имя мужчины</th>
                            <th>Фото</th>
                            <th>Отправлено</th>
                            <th>Комментарий</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                    <?php foreach($image['ToMens'][$ts['SiteID']] as $itmsi): ?>
                        <tr>
                            <td><?= $itmsi['MenID']; ?></td>
                            <td><?= $itmsi['MenName']; ?></td>
                            <td>
                                <a href="<?= base_url("thumb") ?>/?src=/files/images/<?= $itmsi['MenPhoto']; ?>" data-lightbox="Men_Image_Photo_<?=$image['ImageID'];?>_Modal_<?= $itmsi['MenID']; ?>">
                                    Фото
    <!--                            <img src="--><?//= base_url("thumb") ?><!--/?src=/files/images/--><?//= $itmsi['MenPhoto']; ?><!--&w=100" alt="avatar" class="mn-avatar">-->
                                </a>
                            </td>
                            <td class="text-center">
                                <input class="mac-checkbox" type="checkbox" value="1" id="sended_<?= $itmsi['MenID']; ?>_<?=$image['ImageID'];?>" <?= ($itmsi['MenConnect'] > 0) ? 'checked="checked" disabled="disabled"' : ''; ?> />
                            </td>
                            <td>
                                <textarea id="comment_<?= $itmsi['MenID']; ?>_<?=$image['ImageID'];?>" class="form-control mac-textarea" rows="1"><?= $itmsi['MenComment']; ?></textarea>
                            </td>
                            <td class="clearfix text-center" style="width: 100px;">
                                <span class="glyphicon glyphicon-ok mac-ok pull-left hidden" id="okmen_<?= $itmsi['MenID']; ?>_<?=$image['ImageID'];?>" aria-hidden="true"></span>
                                <button class="btn btn-success save-men-info" id="savemeninfo_<?= $itmsi['MenID']; ?>_<?=$image['ImageID'];?>" title="Сохранить изменения">
                                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; // foreach($image['ToMens'][$ts['SiteID']] as $itmsi) ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; // (!empty($image['ToMens'][$ts['SiteID']])) ?>
            </div>
            <?php endforeach; // foreach($image['ToSites'] as $ts) ?>
            <?php endif; // (!empty($image['ToSites'])) */?>
        </div>
        <?php endforeach; // ($images as $image) ?>
        <!-- Controls -->
        <a class="left carousel-control album-carousel" href="#carousel-example-generic-album_<?=$AlbumID;?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control album-carousel" href="#carousel-example-generic-album_<?=$AlbumID;?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
        <!-- /Controls -->
    </div>
    <!-- /Wrapper for slides -->
</div>
    <script type="text/javascript" src="<?= base_url('public/autosize/autosize.min.js'); ?>"></script>
    <script>
        autosize($('.mac-textarea'));

        // загрузка данных по фильтру сайтов
        function loadAlbumImageData(id) {
            // учитываем фильтр по сайтам
            var albumModalListInputs = $('#AlbumModalSitesList_'+id).find('input[type=checkbox]:checked'); // отмеченные чекбоксы
            var albumModalListIds = []; // массив для ID выбранных сайтов
            $.each(albumModalListInputs, function(key, item){
                albumModalListIds[key] = $(item).val(); // собираем ID выбранных сайтов
            });

            $.post(
                '/Customer_Album/getimageinfo',
                {
                    ImageID: id,
                    CustomerID: <?= $CustomerID;?>,
                    SiteIDs: ((albumModalListIds.length > 0) ? albumModalListIds.join() : '')
                },
                function(data){
                    if(data !== ''){
                        $('#AlbumImageSitesMens_'+id).html('');
                        $('#AlbumImageSitesMens_'+id).html(data);
                    }
                    else{
                        $('#AlbumImageSitesMens_'+id).html('');
                        $('#AlbumImageSitesMens_'+id).html('<h5 class="text-center">Нет данных для отображения, измените параметры фильтра</h5>');
                    }
                },
                'html'
            );
        }

        // разворот выпадающего списка в фильтре наверх - если блок с сайтами пуст или в нём менее 3-х сайтов
        // (без этого список прячется внутри модального окна – и пунктов не видно)
        $(document).on('click', '.mac-dd-toggle', function(){
            var exid = this.id.split('_');
            var fnd = $('#AlbumImageSitesMens_'+exid[1]).find('.mac-site-row');
            if(fnd.length > 3){
                $('#AlbumModalSite_'+exid[1]).removeClass('dropup');
            }
            else{
                $('#AlbumModalSite_'+exid[1]).addClass('dropup');
            }
        });
    </script>
<?php else: ?>
    <h4>Нет данных(</h4>
<?php endif; // (!empty($AlbumID) && !empty($ImageID) && !empty($images) && !empty($CustomerID)) ?>