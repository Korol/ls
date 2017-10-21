<?php
/**
 * @var $currencies
 * @var $sites
 * @var $cards
 * @var $employees
 */
// TODO: модальное окно для показа деталей по операциям за выбранный период

$sites = (!empty($sites)) ? $sites : array();
$cards = (!empty($cards)) ? $cards : array();
$employees = (!empty($employees)) ? $employees : array();
?>
<script src="/public/tablesorter/jquery.tablesorter.min.js"></script>
<link rel="stylesheet" href="/public/tablesorter/blue/style.css">
<style>
    #dateFromBlock,
    #dateToBlock {
        width: 150px;
    }
    #modalDateBlock {
        width: 200px;
    }
    #addOperation,
    #getData {
        position: relative;
        top: 30px;
    }
    .fin-table-block {
        margin: 25px 0 40px;
    }
    .fin-table-block .col-md-12 {
        padding: 0;
    }
    .fin-loader {
        width: 64px;
        height: 64px;
        margin: 0 auto;
        background: url('/public/img/25.gif') no-repeat;
    }
    .date-fields > .form-group,
    #getData {
        display: inline-block;
        margin-right: 15px;
    }
    .big-th {
        text-transform: uppercase;
        text-align: center;
    }
    .th-grey {
        background-color: #d7d7d7;
    }
    .th-light-grey {
        background-color: #f2f2f2;
    }
    .th-grey,
    .th-light-grey {
        font-weight: bold !important;
        padding-left: 8px !important;
        padding-right: 15px !important;
    }
    .fin-table > thead > tr > th {
        text-align: center;
        padding-right: 15px;
    }
    .fin-table > tbody > tr > td.fin-td-info {
        cursor: pointer;
    }
    #myAddOperation .nav-tabs.nav-justified > .active > a,
    .nav-tabs.nav-justified > .active > a:focus,
    .nav-tabs.nav-justified > .active > a:hover {
        border: 1px solid #ddd;
        border-bottom-color: #fff;
    }
    #myAddOperation .tab-content {
        margin: 20px 0;
    }
    #myAddOperation .tab-content .row {
        margin-top: 10px;
    }
    #myAddOperation option {
        padding: 5px;
    }
    #myAddOperation textarea {
        height: 75px;
    }
    .grey-help {
        color: #888;
        font-style: italic;
        font-weight: normal;
        margin-left: 25px;
    }
    #operationSuccess,
    #operationError,
    #formResponse {
        display: none;
        text-align: center;
    }
    #operationSuccess,
    #operationError {
        margin-top: 30px;
        padding: 10px 15px;
        margin-bottom: 0;
    }
    .table.fin-table > tbody > tr > td {
        border: 1px solid #ddd !important;
    }
    .hide-zeros {
        color: #DEDEE6 !important;
    }
</style>

<div class="row">
    <div class="col-md-5 date-fields clearfix">
        <div class="form-group">
            <label for="dateFrom">С</label>
            <div class="input-group date" id="dateFromBlock">
                <input type="text" name="dateFrom" class="form-control" id="dateFrom" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label for="dateTo">До</label>
            <div class="input-group date" id="dateToBlock">
                <input type="text" name="dateTo" class="form-control" id="dateTo" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <button class="btn btn-default" id="getData">
            <span class="glyphicon glyphicon-search"></span> Показать
        </button>
    </div>
    <div class="col-md-4">
        <div class="alert alert-success" role="alert" id="operationSuccess"></div>
        <div class="alert alert-danger" role="alert" id="operationError"></div>
    </div>
    <div class="col-md-3 clearfix">
        <button class="btn btn-default pull-right" id="addOperation">Добавить операцию</button>
    </div>
</div>

<div class="row fin-table-block">
    <div class="col-md-12">
        <div class="fin-loader"></div>
        <div id="finTable"></div>
    </div>
</div>

<script>
    $(function () {
        // настройки для datepicker-ов
        var dpObj = {
            locale: 'ru',
            format: 'DD-MM-YYYY',
            defaultDate: 'now',
            showTodayButton: true
        };
        // from
        $('#dateFromBlock').datetimepicker(dpObj);
        // to
        $('#dateToBlock').datetimepicker(dpObj);
        // modal
        $('#modalDateBlock').datetimepicker(dpObj);

        // обновить данные в таблице
        $('#getData').click(function () {
            $('#finTable').html('');
            $('.fin-loader').css('display', 'block');
            fillFinanceTable();
        });

        // показать модальное окно добавления новой операции
        $('#addOperation').click(function () {
            $('#formResponse').html('').hide();
            $('#myAddOperation').modal('show');
        });

        // добавление новой операции
        $('#addOperationBtn').click(function () {
            $('#formResponse').html('').hide();
            $.post(
                '/finance/add/',
                {
                    form: $('#addOperationForm').serialize()
                },
                function (data) {
                    console.log(data);
                    if(data.status){
                        $('#myAddOperation').modal('hide');
                        $('#operationSuccess').html(data.message);
                        $('#operationSuccess').show().delay(4000).fadeOut();
                        $('#addOperationForm')[0].reset();
                        fillFinanceTable();
                    }
                    else{
                        $('#formResponse').html(data.message).show();
                    }
                },
                'json'
            );
        });

    });

    // запрос и отображение данных по операциям этого типа за данный период
    $(document).on('click','td.fin-td-info',function(){
        $('#myViewOperation').modal('hide');
        var dataType = $(this).attr('data-type');
        var dataId = $(this).attr('data-id');
        $(function () {
            var fromD = $('#dateFrom').val();
            var toD = $('#dateTo').val();
            console.log(dataType, dataId, fromD, toD);
            // $.post();
            $('#myViewOperation').modal('show');

        });
    });

    // заполняем данными таблицу
    function fillFinanceTable(){
        $(function () {
            var from = $('#dateFrom').val();
            var to = $('#dateTo').val();
            $.post(
                '/finance/data/',
                {
                    from: from,
                    to: to
                },
                function (data) {
//                    if(data.status){
                    if(data !== ''){
                        $('.fin-loader').css('display', 'none');
                        $('#finTable').html('');
                        $('#finTable').html(data);
                    }
                    else{
                        $('.fin-loader').css('display', 'none');
                        $('#finTable').html('<h5 class="text-center">Нет данных для отображения</h5>');
                    }
                },
                'html'
            );
        });
    }

    // фиксация изменений типа операции через клики по табам
    function setOperationType(type) {
        $('#modalOperationType').val(type);
    }

    // загружаем таблицу с данными за выбранный период времени
    fillFinanceTable();

</script>

<?php /* MODALS */ ?>

<?php /* Add modal */ ?>
    <div class="modal fade" id="myAddOperation" tabindex="-1" role="dialog" aria-labelledby="myAddOperationLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="AddOperationLabel">Добавление новой операции</h4>
                </div>
                <div class="modal-body">
                    <form id="addOperationForm">
                        <input type="hidden" name="operationType" id="modalOperationType" value="income">
                        <div class="form-group">
                            <div class="input-group date" id="modalDateBlock">
                                <input type="text" name="modalDate" class="form-control" id="modalDate" />
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs nav-justified" role="tablist">
                                    <li role="presentation" class="active">
                                        <a onclick="setOperationType('income');" href="#income" aria-controls="income" role="tab" data-toggle="tab">Приход</a>
                                    </li>
                                    <li role="presentation">
                                        <a onclick="setOperationType('outcome');" href="#outcome" aria-controls="outcome" role="tab" data-toggle="tab">Расход</a>
                                    </li>
                                    <li role="presentation">
                                        <a onclick="setOperationType('exchange');" href="#exchange" aria-controls="exchange" role="tab" data-toggle="tab">Обмен</a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="income">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalInSite">Сайт:</label>
                                                    <select name="modalInSite" id="modalInSite" class="form-control">
                                                    <?php foreach ($sites as $site): ?>
                                                        <option value="<?= $site['ID']; ?>"><?= $site['Name']; ?></option>
                                                    <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalInType">Категория поступления:</label>
                                                    <select name="modalInType" id="modalInType" class="form-control">
                                                    <?php foreach($types_in as $ti_k => $ti_v): ?>
                                                        <?php if($ti_k == 'exchange_in') continue; ?>
                                                        <option value="<?= $ti_k; ?>"><?= $ti_v; ?></option>
                                                    <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalInCard">Карта/наличные:</label>
                                                    <select name="modalInCard" id="modalInCard" class="form-control">
                                                    <?php foreach ($cards as $card): ?>
                                                        <option value="<?= $card['ID']; ?>">
                                                            <?= $card['Name']; ?>, <?= $card['Currency']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalInSum">Сумма:</label>
                                                    <input type="text" name="modalInSum" id="modalInSum" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="outcome">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalOutSite">Сайт:</label>
                                                    <select name="modalOutSite" id="modalOutSite" class="form-control">
                                                        <?php foreach ($sites as $site): ?>
                                                            <option value="<?= $site['ID']; ?>"><?= $site['Name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalOutType">Категория расходов:</label>
                                                    <select name="modalOutType" id="modalOutType" class="form-control">
                                                        <?php foreach($types_out as $to_k => $to_v): ?>
                                                            <?php if($to_k == 'exchange_out') continue; ?>
                                                            <option value="<?= $to_k; ?>"><?= $to_v; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="modalOutEmployee">Сотрудник: <span class="grey-help">(это имеет смысл только если Категория расходов = Зарплата)</span></label>
                                                    <select name="modalOutEmployee" id="modalOutEmployee" class="form-control">
                                                        <option value="0">--- Выберите сотрудника ---</option>
                                                        <?php foreach ($employees as $employee): ?>
                                                            <option value="<?= $employee['ID']; ?>"><?= $employee['SName']; ?> <?= $employee['FName']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalOutCard">Карта/наличные:</label>
                                                    <select name="modalOutCard" id="modalOutCard" class="form-control">
                                                        <?php foreach ($cards as $card): ?>
                                                            <option value="<?= $card['ID']; ?>">
                                                                <?= $card['Name']; ?>, <?= $card['Currency']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="modalOutSum">Сумма:</label>
                                                    <input type="text" name="modalOutSum" id="modalOutSum" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="exchange">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="modalExSumOut">Сумма к обмену:</label>
                                                    <input type="text" name="modalExSumOut" id="modalExSumOut" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="modalExCard">Карта/наличные:</label>
                                                    <select name="modalExCard" id="modalExCard" class="form-control">
                                                        <?php foreach ($cards as $card): ?>
                                                            <?php if($card['Currency'] == 'UAH') continue; ?>
                                                            <option value="<?= $card['ID']; ?>">
                                                                <?= $card['Name']; ?>, <?= $card['Currency']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="modalExRate">Курс обмена:</label>
                                                    <input type="text" name="modalExRate" id="modalExRate" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="modalExSumUah">Итого в UAH:</label>
                                                    <input type="text" name="modalExSumUah" id="modalExSumUah" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="modalComment">Комментарий:</label>
                                    <textarea name="modalComment" id="modalComment" cols="30" rows="10" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="formResponse" class="alert alert-danger" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                    <button type="submit" class="btn btn-primary" id="addOperationBtn">Добавить операцию</button>
                </div>
            </div>
        </div>
    </div>
<?php /* /Add modal */ ?>

<?php /* View modal */ ?>
<div class="modal fade" id="myViewOperation" tabindex="-1" role="dialog" aria-labelledby="myViewOperationLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="ViewOperationLabel">Просмотр операции</h4>
            </div>
            <div class="modal-body" id="ViewOperationBody">
                Hello!
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<?php /* /View modal */ ?>
