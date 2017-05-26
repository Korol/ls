<?php
// TODO: получение данных из БД, и заполнение таблицы реальными значениями
?>
<div id="ReportGeneralCustomersStats" class="report-table">
<link rel="stylesheet" href="/public/stickytable/jquery.stickytable.min.css">
<script src="/public/stickytable/jquery.stickytable.min.js"></script>
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
    </style>
    <div class="row" style="margin-bottom: 50px;">
        <div class="col-md-12">
            <div class="sticky-table sticky-headers sticky-ltr-cells">
                <table class="table table-bordered table-striped editable-table">
                    <thead>
                        <tr class="sticky-row">
                            <?php
                            for($i = 1; $i <= 30; $i++){
                                $thClass = ($i == 1) ? ' class="sticky-cell"' : '';
                                echo '<th' . $thClass . ' nowrap="nowrap"> Header ' . $i . '</th>';
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php for($i = 1; $i <= 100; $i++): ?>
                        <tr>
                            <?php for($k = 1; $k <= 30; $k++): ?>
                                <?php $tdClass = ($k == 1) ? 'sticky-cell' : 'editable-cell'; ?>
                                <td class="<?=$tdClass; ?>" nowrap="nowrap" id="cll_<?=$i;?>_<?=$k;?>">Value <?= $i . ' - ' . $k; ?></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.editable-cell').click(function(e){
                var i=0;
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
            function updateVal(currentEle, value) {
                $('#'+currentEle).html('<input class="thVal" onfocus="this.select()" type="text" width="2" value="'+value+'" />').select();
                $(".thVal").focus();
                $(".thVal").keyup(function (event) {
                    if (event.keyCode == 13) {
                        var newValue = $(".thVal").val().trim();
                        $('#'+currentEle).html(newValue);
                        saveVal(currentEle, newValue);
                    }
                });

                $(".thVal").focusout(function () { // you can use $('html')
                    var nnewVal = $(".thVal").val().trim();
                    $('#'+currentEle).html(nnewVal);
                    saveVal(currentEle, nnewVal);
                });
            }
            // save new cell value
            function saveVal(cellId, cellVal){
                console.log(cellId, cellVal);
                // TODO: сохранение изменений в БД
            }

        });
//        $(function(){
//            $('td.editable-cell').click(function(){
//                var currentVal = $(this).html();
//                var currentId = $(this).attr('id');
//                console.log(currentId, currentVal);
//                $(this).html('<input type="text" id="tf'+currentId+'" value="'+currentVal+'"/>');
//            });
//        });
//        $(function () {
//            $("td").dblclick(function () {
//                var OriginalContent = $(this).text();
//
//
//                var inputNewText = prompt("Enter new content for:", OriginalContent);
//
//                if (inputNewText!=null)
//                {
//                    $(this).text(inputNewText)
//                }
//
//
//            });
//        }); // end function
    </script>
</div>
