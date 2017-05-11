<?php if ($role['isDirector']): ?>
    <div class="panel assol-grey-panel">
        <div class="panel-body">

            <button class="btn assol-btn add right action-training-add" title="Создать статью" style="margin-left: 20px">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                Создать статью
            </button>
            <a href="<?=base_url('training/add_folder')?>" data-toggle="modal" data-target="#remoteDialog"
               class="" role="button" title="Добавить папку">
               <button class="btn assol-btn add right">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                Добавить папку
                </button>
            </a>
        </div>
    </div>
<?php endif ?>

<script id="trainingTemplate" type="text/x-jquery-tmpl">
    {{if bread}}
    <ol class="breadcrumb assol-grey-panel">
        <li><a href="#"><a href="#" record="0" class="action-folder-open">Обучение</a></li>
        {{tmpl($data.bread) "#trainingBreadTemplate"}}
    </ol>
    {{/if}}

    {{if AccessDenied}}
        Нет доступа к текущему каталогу
    {{else}}
        {{tmpl($data.data) "#trainingListTemplate"}}
    {{/if}}
</script>

<script id="trainingBreadTemplate" type="text/x-jquery-tmpl">
    <li><a href="#" record="${ID}" class="action-folder-open">${Name}</a></li>
</script>
<script id="trainingListTemplate" type="text/x-jquery-tmpl">
    <div class="document" record="${ID}">
        <div class="document-menu">
        <?php if ($role['isDirector']): ?>
            <span class="glyphicon glyphicon-remove-circle document-menu-btn remove {{if IsFolder>0}}action-folder-remove{{else}}action-document-remove{{/if}}" aria-hidden="true" title="Удалить"></span>

            {{if IsFolder>0}}
            <a href="<?=base_url('training/edit_folder')?>/${ID}" data-toggle="modal" data-target="#remoteDialog" role="button">
                <span class="glyphicon glyphicon-edit document-menu-btn blue" aria-hidden="true" title="Редактировать"></span>
            </a>
            {{/if}}

            {{if IsFolder==0}}
            <span class="glyphicon glyphicon-edit document-menu-btn blue action-training-edit" aria-hidden="true" title="Редактировать"></span>
            {{/if}}
        <?php endif ?>
        </div>
        <div>
            <a href="#" class="{{if IsFolder>0}}action-folder-open{{else}}action-training-open{{/if}}">
                <img src="<?=base_url('public/img')?>/{{if IsFolder>0}}<?= IS_LOVE_STORY ? 'folder-lovestory' : 'folder' ?>{{else}}training{{/if}}.png">
                <p class="document-name">${Name}</p>
            </a>
        </div>
    </div>
</script>

<div id="training"></div>

<script>
    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
    });

    var UrlParent = <?= $Parent ?>;
</script>

<style>
    .modal-dialog {
        width: 400px;
    }
</style>