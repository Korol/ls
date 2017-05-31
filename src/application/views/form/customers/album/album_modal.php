<?php
/**
 * @var $AlbumID
 * @var $ImageID
 * @var $images
 */
?>
<?php if(!empty($AlbumID) && !empty($ImageID) && !empty($images)): ?>
<style>
    .mac-opt{
        padding: 7px 15px;
    }
</style>
<div id="carousel-example-generic-album_<?=$AlbumID;?>" class="carousel slide" data-ride="carousel" data-interval="false">
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
        <?php foreach($images as $image): ?>
        <div class="img-item item <?= ($image['ImageID'] == $ImageID) ? 'active' : ''; ?>" id="mc_item_<?=$image['ImageID'];?>">
            <img src="<?= base_url("thumb") ?>?src=/files/images/<?=$image['ImageID'];?>.<?=$image['ext'];?>" class="img-responsive mac-image">
            <?php if(!empty($image['ToSites'])): ?>
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
            </div>
            <?php endforeach; // foreach($image['ToSites'] as $ts) ?>
            <?php endif; // (!empty($image['ToSites'])) ?>

            <?php if(!empty($image['ToMens'])): ?>
            <table class="table table-striped table-bordered" style="margin: 20px auto;">
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
                <?php foreach($image['ToMens'] as $tm): ?>
                <tr>
                    <td><?= $tm['MenID']; ?></td>
                    <td><?= $tm['MenName']; ?></td>
                    <td>
                        <a href="<?= base_url("thumb") ?>/?src=/files/images/<?= $tm['MenPhoto']; ?>" data-lightbox="Men_Image_Photo_<?=$image['ImageID'];?>_Modal_<?= $tm['MenID']; ?>">
                            <img src="<?= base_url("thumb") ?>/?src=/files/images/<?= $tm['MenPhoto']; ?>&w=100" alt="avatar" class="mn-avatar">
                        </a>
                    </td>
                    <td>
                        <input type="checkbox" value="1" id="sended_<?= $tm['MenID']; ?>_<?=$image['ImageID'];?>" <?= ($tm['MenConnect'] > 0) ? 'checked="checked" disabled="disabled"' : ''; ?> />
                    </td>
                    <td>
                        <textarea id="comment_<?= $tm['MenID']; ?>_<?=$image['ImageID'];?>" class="form-control" rows="2"><?= $tm['MenComment']; ?></textarea>
                    </td>
                    <td class="clearfix" style="width: 100px;">
                        <span class="glyphicon glyphicon-ok mac-ok pull-left hidden" id="okmen_<?= $tm['MenID']; ?>_<?=$image['ImageID'];?>" aria-hidden="true"></span>
                        <button class="btn btn-success save-men-info pull-right" id="savemeninfo_<?= $tm['MenID']; ?>_<?=$image['ImageID'];?>" title="Сохранить изменения">
                            <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span>
                        </button>
                    </td>
                </tr>
                <?php endforeach; // ($image['ToMens'] as $tm) ?>
                </tbody>
            </table>
            <?php endif; // (!empty($image['ToMens'])) ?>
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
<?php else: ?>
    <h4>Нет данных(</h4>
<?php endif; // (!empty($AlbumID) && !empty($ImageID) && !empty($images)) ?>