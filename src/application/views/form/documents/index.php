<?php if ($role['isDirector'] || $role['isSecretary']): ?>
<div class="panel assol-grey-panel">
    <div class="panel-body">
            
            <div class="document-top-btns">
                <div>
                    <a href="javascript:void(0)" id="btnFileUpload" role="button" title="Загрузить файл">
                        <button class="btn assol-btn save right">
                            <span class="glyphicon glyphicon-upload" aria-hidden="true"></span>
                            Загрузить файл
                        </button>
                    </a>

                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="FileUploadLabel">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="FileUploadLabel">Загрузка файлов</h4>
                                </div>
                                <div class="modal-body">
                                    <iframe src="" frameborder="0"></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .document-top-btns .modal-dialog {
                            width: 90%;
                            background: white;
                            max-width: 1180px;
                        }

                        .document-top-btns .modal-title {
                            float: left;
                        }

                        .document-top-btns iframe {
                            width: 100%;
                            height: 600px;
                        }
                    </style>

                    <script>
                        $(document).on('click', '#btnFileUpload', function () {
                            var modal = $(this).parent().find('.modal');
                            var frame = $(this).parent().find('iframe');
                            var frameSrc = '<?= base_url('documents') ?>/' + $.AssolDocument.GetParent() + '/upload';

                            modal.on('show.bs.modal', function () {
                                frame.attr("src", frameSrc);
                            });
                            modal.on('hidden.bs.modal', function () {
                                $.AssolDocument.ReloadDocumentList();
                            });
                            modal.modal({show:true});
                        });
                    </script>
                </div>
                <div>
                    <a href="<?=current_url()?>/add_folder" data-toggle="modal" data-target="#remoteDialog"
                       class="" role="button" title="Добавить папку">
                       <button class="btn assol-btn add right">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        Добавить папку
                       </button>
                    </a>
                </div>
            </div>
    </div>
</div>
<?php endif ?>

<script id="documentsTemplate" type="text/x-jquery-tmpl">
    {{if bread}}
    <ol class="breadcrumb assol-grey-panel">
        <li><a href="#"><a href="#" record="0" class="action-folder-open">Документация</a></li>
        {{tmpl($data.bread) "#documentBreadTemplate"}}
    </ol>
    {{/if}}

    {{if AccessDenied}}
        Нет доступа к текущему каталогу
    {{else}}
        {{tmpl($data.data) "#documentListTemplate"}}
    {{/if}}
</script>

<script id="documentBreadTemplate" type="text/x-jquery-tmpl">
    <li><a href="#" record="${ID}" class="action-folder-open">${Name}</a></li>
</script>
<script id="documentListTemplate" type="text/x-jquery-tmpl">
    <div class="document" record="${ID}">
        <div class="document-menu">

            <?php if ($role['isDirector'] || $role['isSecretary']): ?>
            <span class="glyphicon glyphicon-remove-circle document-menu-btn remove {{if IsFolder>0}}action-folder-remove{{else}}action-document-remove{{/if}}" aria-hidden="true" title="Удалить"></span>

            {{if IsFolder>0}}
            <a href="<?=current_url()?>/edit_folder/${ID}" data-toggle="modal" data-target="#remoteDialog" role="button">
                <span class="glyphicon glyphicon-edit document-menu-btn blue" aria-hidden="true" title="Редактировать"></span>
            </a>
            {{/if}}
            <?php endif ?>

            {{if IsFolder==0}}
            <a href="<?=current_url()?>/load/${ID}" target="_blank">
                <span class="glyphicon glyphicon-download document-menu-btn blue" aria-hidden="true" title="Скачать"></span>
            </a>
            {{/if}}
        </div>
        <div>
            {{if IsFolder>0}}
            <a href="#" class="action-folder-open">
            {{/if}}
                <img src="<?=base_url('public/img')?>/{{if IsFolder>0}}<?= IS_LOVE_STORY ? 'folder-lovestory' : 'folder' ?>{{else}}file{{/if}}.png">
                <p class="document-name">${Name}</p>
            {{if IsFolder>0}}
            </a>
            {{/if}}
        </div>
    </div>
</script>

<div id="documents"></div>

<script>
    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
    });
</script>

<style>
    .modal-dialog {
        width: 400px;
    }
</style>