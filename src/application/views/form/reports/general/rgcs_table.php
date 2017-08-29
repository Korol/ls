<?php if(!empty($sites) && !empty($cs_customers)): ?>
    <link rel="stylesheet" href="/public/stickytable/jquery.stickytable.min.css">
    <script src="/public/stickytable/jquery.stickytable.min.js?v=1"></script>
    <script src="/public/tablesorter/jquery.tablesorter.min.js"></script>
    <link rel="stylesheet" href="/public/tablesorter/blue/style.css">
    <style>
        .sticky-table table td.sticky-cell, .sticky-table table th.sticky-cell,
        .sticky-table table tr.sticky-row td, .sticky-table table tr.sticky-row th {
            outline: #ddd solid 1px !important;
        }
        .site-table .table > thead > tr > th, .table > tbody > tr > td{
            border: 1px solid #ddd;
        }
        td.sticky-cell{
            font-weight: bold !important;
            background-color: #ecf0f3 !important;
        }
        tr.sticky-row > th{
            background-color: #ecf0f3 !important;
        }
        .thVal{
            max-width: 100%;
        }
        .editable-table>thead>tr>th{
            border-bottom-width: 1px !important;
        }
        .summary-col{
            background-color: #ecf0f3 !important;
            font-weight: bold;
        }
        .rgcs-row-data{
            text-align: center !important;
        }
    </style>
    <div class="row" style="margin-bottom: 50px;">
        <div class="col-md-12">
            <div class="sticky-table sticky-headers sticky-ltr-cells">
                <table id="rgcs_table" class="table table-bordered table-striped editable-table tablesorter">
                    <thead>
                    <tr class="sticky-row">
                        <th class="sticky-cell sortable" nowrap="nowrap">ФИО</th>
                        <th class="sticky-cell sortable" nowrap="nowrap" style="min-width: 70px;">Итого</th>
                        <?php foreach($sites as $th_site): ?>
                            <th nowrap="nowrap"><?= $th_site['Name']; ?></th>
                        <?php endforeach; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($cs_customers as $cs_item): ?>
                        <?php
                        $sname_ex = explode(' ', trim($cs_item['SName']));
                        $sname = $sname_ex[0];
                        $fname = mb_substr($cs_item['FName'], 0, 1, 'UTF-8');
                        $mname = mb_substr($cs_item['MName'], 0, 1, 'UTF-8');
                        ?>
                        <tr>
                            <td class="sticky-cell" nowrap="nowrap">
                                <a href="/customer/<?=$cs_item['ID']; ?>/profile" target="_blank">
                                    <?= $sname . ' ' . $fname . '.' . $mname . '.'; ?>
                                </a>
                            </td>
                            <td class="sticky-cell rgcs-row-total" nowrap="nowrap">
                                0.00
                            </td>
                            <?php foreach($sites as $tb_site): ?>
                                <?php
                                $tb_text = '&dash;';
                                if(in_array($tb_site['ID'], array_keys($cs_item['CS']))){
                                    $tb_text = (!empty($cs_item['CS'][$tb_site['ID']])) ? $cs_item['CS'][$tb_site['ID']] : '0';
                                }
                                $tdClass = ($tb_text !== '&dash;') ? 'editable-cell' : '';
                                $tdId = ($tb_text !== '&dash;') ? 'id="cell_' . $cs_item['ID'] . '_' . $tb_site['ID'] . '"' : '';
                                ?>
                                <td class="<?= $tdClass; ?> rgcs-row-data site-col-<?= $tb_site['ID']; ?>" <?= $tdId; ?> nowrap="nowrap"><?= $tb_text; ?></td>
                            <?php endforeach; // ($sites as $td_site) ?>
                        </tr>
                    <?php endforeach; // ($cs_customers as $cs_item) ?>
                    </tbody>
                    <tfoot>
                        <tr id="rgcs_summary">
                            <td class="sticky-cell" nowrap="nowrap">ИТОГО</td>
                            <td class="sticky-cell" id="rgcs_total" nowrap="nowrap">0.00</td>
                            <?php foreach($sites as $tf_site): ?>
                                <td class="summary-col" id="col-<?= $tf_site['ID']; ?>" nowrap="nowrap">0.00</td>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.editable-cell').click(function(e){
                var id=$(this).attr('id');
                e.stopPropagation();      //<-------stop the bubbling of the event here
                var checkInput = $('#'+id).find('input.thVal').length;
                if(checkInput > 0){
                    var value = $('input.thVal').val().trim();
                }
                else{
                    var value = $('#'+id).text();
                }
//                var value = $('#'+id).html();
                updateVal(id, value);

            });
            // update cell value
            function updateVal(currentId, value) {
                $('#'+currentId).html('<input class="thVal" onfocus="this.select()" type="text" value="'+value+'" />').select();
                $(".thVal").focus();
                $(".thVal").keyup(function (event) {
                    if (event.keyCode == 13) {
                        var newValue = $(".thVal").val().trim();
                        $('#'+currentId).html(newValue);
                        if(newValue !== value){
                            saveVal(currentId, newValue);
                        }
                    }
                });

                $(".thVal").focusout(function () { // you can use $('html')
                    var nnewVal = $(".thVal").val().trim();
                    $('#'+currentId).html(nnewVal);
                    if(nnewVal !== value){
                        saveVal(currentId, nnewVal);
                    }
                });
            }
            // save new cell value
            function saveVal(cellId, cellVal){
                var m = $('#rgcs-month').val();
                var y = $('#rgcs-year-input').val();
                $.post(
                    '/reports/savestat2',
                    {
                        cell: cellId,
                        text: cellVal,
                        month: m,
                        year: y
                    },
                    function(data){
                        if(data*1 > 0){
                            var newVal = parseFloat($('#'+cellId).html()) || 0.00;
                            $('#'+cellId).html(newVal.toFixed(2));
                            countRowsTotals();
                        }
                    },
                    'text'
                );
            }
        });

        function countRowsTotals() {
            // считаем суммы по строкам таблицы
            $('#rgcs_table tbody tr').each(function () {
                var rowSum = 0;
                $(this).find('td.rgcs-row-data').each(function () {
                    rowSum += parseFloat($(this).html()) || 0.00;
                });
                $(this).find('td.rgcs-row-total').html(rowSum.toFixed(2));
            });
            // считаем суммы по столбцам таблицы
            $('#rgcs_summary td.summary-col').each(function () {
                var tID = this.id;
                var colSum = 0;
                $('#rgcs_table tbody td.site-'+tID).each(function () {
                    colSum += parseFloat($(this).html()) || 0.00;
                });
                $(this).html(colSum.toFixed(2));
            });
            // считаем общую сумму по таблице
            var total = 0;
            $('#rgcs_summary td.summary-col').each(function () {
                total += parseFloat($(this).html()) || 0.00;
            });
            $('#rgcs_total').html(total.toFixed(2));
            // sortable
            $("#rgcs_table").tablesorter({
                selectorHeaders: 'thead th.sortable'
            });
        }
        countRowsTotals();
    </script>
<?php else: ?>
    <h3 class="text-center">Нет данных для отображения</h3>
<?php endif; // (!empty($sites) && !empty($cs_customers)) ?>