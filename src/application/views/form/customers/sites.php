<?php
/**
 * @var $isEditSites
 * @var $CustomerID
 */
?>
<style>
    #newSitesTable > thead > tr > th {
        text-align: center;
    }
    #newSitesTable > tbody > tr > td {
        border: 1px solid #ddd !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped table-hover" id="newSitesTable">
            <thead>
            <tr>
                <th>Сайт</th>
                <th>ID клиентки на сайте</th>
                <th>Переводчик</th>
                <th>Удалить</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">

    // получение и заполнение таблицы Сайтов клиентки
    function fillSitesTable() {
        $.post(
            '/Customer_Site/sites',
            {
                CustomerID: <?= $CustomerID; ?>,
                isEditSites: <?= (!empty($isEditSites)) ? 1 : 0; ?>
            },
            function (data) {
                if(data !== ''){
                    $('#newSitesTable tbody').html('').append(data);
                }
                else{
                    $('#newSitesTable tbody').html('').append('<tr><td colspan="4" align="center">Нет данных для отображения</td></tr>');
                }
            },
            'html'
        );
    }

    // «удаляем» связь клиентки с сайтом
    function removeSiteConnection(connectionID) {
        // customer/(:num)/site/(:num)/remove
        $.post(
            '/customer/<?= $CustomerID; ?>/site/'+connectionID+'/remove',
            function (data) {
                if(data.status*1 > 0){
                    // удаляем строку из таблицы
                    $('#str_'+connectionID).remove();
                }
            },
            'json'
        );
    }

    // снимаем чекбокс с «удалённого» сайта в списке
    function recheckSite(siteID) {
        $('#WorkSite_'+siteID).click();
    }

    fillSitesTable(); // заполняем таблицу данными
</script>