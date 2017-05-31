<div id="ReportGeneralCustomersStats" class="report-table">
    <div class="reports-title">Статистика по клиенткам</div>
<?php if(!empty($sites) && !empty($cs_customers)): ?>
<link rel="stylesheet" href="/public/stickytable/jquery.stickytable.min.css">
<script src="/public/stickytable/jquery.stickytable.min.js?v=1"></script>
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
    </style>
    <div class="row" style="margin-bottom: 50px;">
        <div class="col-md-12">
            <div class="sticky-table sticky-headers sticky-ltr-cells">
                <table class="table table-bordered table-striped editable-table">
                    <thead>
                        <tr class="sticky-row">
                            <th class="sticky-cell" nowrap="nowrap">ФИО</th>
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
                            <?php foreach($sites as $tb_site): ?>
                                <?php
                                $tb_text = '&dash;';
                                if(in_array($tb_site['ID'], array_keys($cs_item['CS']))){
                                    $tb_text = (!empty($cs_item['CS'][$tb_site['ID']])) ? $cs_item['CS'][$tb_site['ID']] : '';
                                }
                                $tdClass = ($tb_text !== '&dash;') ? 'editable-cell' : '';
                                $tdId = ($tb_text !== '&dash;') ? 'id="cell_' . $cs_item['ID'] . '_' . $tb_site['ID'] . '"' : '';
                                ?>
                                <td class="<?= $tdClass; ?>" <?= $tdId; ?> nowrap="nowrap"><?= $tb_text; ?></td>
                            <?php endforeach; // ($sites as $td_site) ?>
                        </tr>
                    <?php endforeach; // ($cs_customers as $cs_item) ?>
                    </tbody>
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
//                console.log(cellId, cellVal);
                $.post(
                    '/reports/savestat',
                    { cell: cellId, text: cellVal},
                    function(data){
//                        console.log(data);
                    },
                    'text'
                );
            }
        });
    </script>
<?php endif; // (!empty($sites) && !empty($cs_customers)) ?>
</div>
