<?php
/**
 *
 */
?>

<table id="fin_table1" class="table table-bordered table-striped fin-table tablesorter">
    <thead>
    <tr>
        <th></th>
        <th colspan="8" class="big-th">Приход</th>
        <th colspan="5" class="big-th">Расход</th>
        <th></th>
    </tr>
    <tr>
        <th>Карта, валюта</th>
        <th class="th-info sortable">Поступление</th>
        <th class="sortable">Встреча</th>
        <th class="sortable">Вестерн</th>
        <th class="sortable">Квартира</th>
        <th class="sortable">Трансфер</th>
        <th class="sortable">Обмен</th>
        <th class="sortable">Резерв</th>
        <th class="th-grey sortable">Итого приход</th>
        <th class="sortable">Офис</th>
        <th class="sortable">Благо</th>
        <th class="sortable">Зарплата</th>
        <th class="sortable">Обмен</th>
        <th class="th-grey sortable">Итого расход</th>
        <th class="th-light-grey sortable">Итого</th>
    </tr>
    </thead>
    <tbody>
    <?php if(!empty($records)): ?>
        <?php foreach ($records as $record): ?>
        <tr>
            <td><?= $record['card_name']; ?></td>
            <td class="fin-td-info <?= ($record['income']['receipts'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="income" data-id="receipts">
                <?= $record['income']['receipts']; ?>
            </td>
            <td class="fin-td-info <?= ($record['income']['meeting'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="income" data-id="meeting">
                <?= $record['income']['meeting']; ?>
            </td>
            <td class="fin-td-info <?= ($record['income']['western'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="income" data-id="western">
                <?= $record['income']['western']; ?>
            </td>
            <td class="fin-td-info <?= ($record['income']['apartment'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="income" data-id="apartment">
                <?= $record['income']['apartment']; ?>
            </td>
            <td class="fin-td-info <?= ($record['income']['transfer'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="income" data-id="transfer">
                <?= $record['income']['transfer']; ?>
            </td>
            <td class="fin-td-info <?= ($record['income']['exchange_in'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="exchange" data-id="exchange_in">
                <?= $record['income']['exchange_in']; ?>
            </td>
            <td class="fin-td-info <?= ($record['income']['reserve'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="income" data-id="reserve">
                <?= $record['income']['reserve']; ?>
            </td>
            <td class="th-grey"><?= $record['income']['total']; ?></td>
            <td class="fin-td-info <?= ($record['outcome']['office'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="outcome" data-id="office">
                <?= $record['outcome']['office']; ?>
            </td>
            <td class="fin-td-info <?= ($record['outcome']['charity'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="outcome" data-id="charity">
                <?= $record['outcome']['charity']; ?>
            </td>
            <td class="fin-td-info <?= ($record['outcome']['salary'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="outcome" data-id="salary">
                <?= $record['outcome']['salary']; ?>
            </td>
            <td class="fin-td-info <?= ($record['outcome']['exchange_out'] == '0.00') ? 'hide-zeros' : ''; ?>" data-type="exchange" data-id="exchange_out">
                <?= $record['outcome']['exchange_out']; ?>
            </td>
            <td class="th-grey"><?= $record['outcome']['total']; ?></td>
            <td class="th-light-grey"><?= $record['total']; ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="15">
                <h5 class="text-center">Нет данных для отображения</h5>
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<script>
    $(document).ready(function()
        {
            $("#fin_table1").tablesorter({
                selectorHeaders: 'thead th.sortable' // <-- здесь указываем класс, который определяет те столбцы, по которым будет работать сортировка
//                selectorHeaders: 'span.fin-table-sort' // <-- здесь указываем класс, который определяет те столбцы, по которым будет работать сортировка
            });
        }
    );
</script>