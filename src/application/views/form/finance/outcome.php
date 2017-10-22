<?php
$records = (!empty($records)) ? $records : array();
$header = (!empty($header)) ? $header : '';
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title" id="ViewOperationLabel"><?= $header; ?></h4>
</div>
<div class="modal-body">
<?php if(!empty($records)): ?>
    <table class="table table-bordered table-striped operation-table">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Описание</th>
            <th>Сайт</th>
            <th>Сумма</th>
            <th>Валюта</th>
            <th>Карта/нал.</th>
            <th>Комментарий</th>
            <th>Удаление</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($records as $record): ?>
            <tr id="operation_<?= $record['id']; ?>">
                <td><?= date('d-m-Y', strtotime($record['created_date'])); ?></td>
                <td><?= (!empty($record['employee_name'])) ? $record['employee_name'] : ''; ?></td>
                <td><?= $record['site_name']; ?></td>
                <td><?= $record['sum']; ?></td>
                <td><?= $record['currency']; ?></td>
                <td><?= $record['card_name']; ?></td>
                <td><?= $record['comment']; ?></td>
                <td class="rm-operation">
                    <button class="btn btn-default btn-sm" onclick="removeOperation(<?= $record['id']; ?>, 'outcome');">
                        <span class="glyphicon glyphicon-trash"></span>
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <h5 class="text-center">Нет данных для отображения</h5>
<?php endif; ?>
</div>
<div class="modal-footer"></div>
