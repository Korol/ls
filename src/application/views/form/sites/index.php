<div class="sites-page">

<div class="site-table">
    <table id="site-table"
           data-toggle="table"
           data-url="<?= current_url_build('data') ?>"
           data-side-pagination="server"
           class="table">
        <thead>
        <tr>
            <th data-field="Name" data-formatter="inputFieldFormatter">Название сайта</th>
            <th data-field="Domen" data-formatter="inputFieldFormatter">Домен сайта</th>
            <th data-field="Note" data-formatter="inputFieldFormatter">Примечание</th>
            <th data-field="IsDealer" data-formatter="dealerFormatter" class="dealer">Дилерские</th>
            <th data-field="ID" data-formatter="editFormatter" class="table-action"></th>
            <th data-field="ID" data-formatter="removeFormatter" class="table-action"></th>
        </tr>
        </thead>
    </table>
</div>

<style>
    .site-table  table td.dealer {
        padding-top: 15px;
        text-align: center;
    }
</style>

<div style="padding-top: 40px">
    <a href="<?=current_url_build('add')?>" data-toggle="modal" data-target="#remoteDialog" class="" role="button" title="Добавить сайт">
        <button class="btn assol-btn add right site">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            ДОБАВИТЬ САЙТ
        </button>
    </a>
</div>

<script>
    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
        $('#site-table').bootstrapTable('refresh', {});
    });

    function inputFieldFormatter(value) {
        return '<input type="text" class="assol-input-style fullwidth defaultheight" value="'+value+'" readonly>';
    }

    function dealerFormatter(value) {
        if (value > 0)
            return '<div class="checkbox-line"><label><input type="checkbox" disabled="disabled" checked="checked"><mark></mark></label></div>';
        else
            return '<div class="checkbox-line"><label><input type="checkbox" disabled="disabled"><mark></mark></label></div>';
    }

    function removeFormatter(value) {
        return  '<a href="' + '<?= current_url_build("remove") ?>/' + value + '" data-toggle="modal" data-target="#remoteDialog" class="btn btn-remove-site" role="button" title="Удалить сайт">' +
                    '<span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>' +
                '</a>';
    }

    function editFormatter(value) {
        return  '<a href="' + '<?= current_url_build("edit") ?>/' + value + '" data-toggle="modal" data-target="#remoteDialog" class="btn btn-remove-site" style="color: green" role="button" title="Редактировать сайт">' +
            '<span class="glyphicon glyphicon glyphicon-edit" aria-hidden="true"></span>' +
            '</a>';
    }
</script>

</div>