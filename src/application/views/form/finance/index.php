<?php
/**
 * @var $currencies
 * @var $sites
 * @var $cards
 * @var $employees
 */
// TODO: сообщения успех|ошибка в шапке страницы
// TODO: модальное окно для показа деталей по операциям за выбранный период
// TODO: выяснить вопрос по сортировке: если по клику на заголовок открывается модалка – то какая нах тут сортировка???

$types_in = array(
    'receipts' => 'Поступление',
    'meeting' => 'Встреча',
    'western' => 'Вестерн',
    'apartment' => 'Квартира',
    'transfer' => 'Трансфер',
    'reserve' => 'Резерв',
);
$types_out = array(
    'office' => 'Офис',
    'charity' => 'Благотворительность',
    'salary' => 'Зарплата',
    'exchange' => 'Обмен',
);
$sites = (!empty($sites)) ? $sites : array();
$cards = (!empty($cards)) ? $cards : array();
$employees = (!empty($employees)) ? $employees : array();
?>
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
    .fin-table > thead > tr > th {
        text-align: center;
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
    <div class="col-md-4">messages here!</div>
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

        // добавить новую операцию
        $('#addOperation').click(function () {
            $('#myAddOperation').modal('show');
        });

    });

    $(document).on('click','th.th-info',function(){
        var dataType = $(this).attr('data-type');
        var dataId = $(this).attr('data-id');
        $(function () {
            var fromD = $('#dateFrom').val();
            var toD = $('#dateTo').val();
            console.log(dataType, dataId, fromD, toD);
            // запрос и отображение данных по операциям этого типа за данный период

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
                    if(data.status){
                        $('.fin-loader').css('display', 'none');
                        $('#finTable').html('');
                        $(function () {
                            $('#finTableTmpl').tmpl(data).appendTo('#finTable');
                        });
                    }
                    else{
                        $('.fin-loader').css('display', 'none');
                        $('#finTable').html('<h5 class="text-center">Нет данных для отображения</h5>');
                    }
                },
                'json'
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

<?php /* ШАБЛОНЫ */ ?>

<?php /* шаблон таблицы */ ?>
<script id="finTableTmpl" type="text/x-jquery-tmpl">
    <table class="table table-bordered table-striped fin-table">
        <thead>
            <tr>
                <th></th>
                <th colspan="8" class="big-th">Приход</th>
                <th colspan="5" class="big-th">Расход</th>
                <th></th>
            </tr>
            <tr>
                <th>Карта, валюта</th>
                <th class="th-info" data-type="income" data-id="receipts">Поступление</th>
                <th>Встреча</th>
                <th>Вестерн</th>
                <th>Квартира</th>
                <th>Трансфер</th>
                <th>Обмен</th>
                <th>Резерв</th>
                <th class="th-grey">Итого приход</th>
                <th>Офис</th>
                <th>Благотворительность</th>
                <th>Зарплата</th>
                <th>Обмен</th>
                <th class="th-grey">Итого расход</th>
                <th class="th-light-grey">Итого</th>
            </tr>
        </thead>
        <tbody>
        {{if records.length > 0}}
            {{tmpl(records) '#finRowTmpl'}}
        {{else}}
            <tr>
                <td colspan="15">
                    <h5 class="text-center">Нет данных для отображения</h5>
                </td>
            </tr>
        {{/if}}
        </tbody>
    </table>
</script>
<?php /* шаблон строки в таблице */ ?>
<script id="finRowTmpl" type="text/x-jquery-tmpl">
    <tr">
        <td>${CardName}</td>
        <td>${Receipts}</td>
        <td>${Meeting}</td>
        <td>${Western}</td>
        <td>${Apartment}</td>
        <td>${Transfer}</td>
        <td class="th-grey">${ExchangeIn}</td>
        <td>${Reserve}</td>
        <td>${TotalIn}</td>
        <td>${Office}</td>
        <td>${Charity}</td>
        <td>${Salary}</td>
        <td>${ExchangeOut}</td>
        <td class="th-grey">${TotalOut}</td>
        <td class="th-dark-grey">${Total}</td>
    </tr>
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
                                                            <option value="<?= $to_k; ?>"><?= $to_v; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="modalOutCustomer">Сотрудник: <span class="grey-help">(это имеет смысл только если Категория расходов = Зарплата)</span></label>
                                                    <select name="modalOutCustomer" id="modalOutCustomer" class="form-control">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отменить и закрыть</button>
                    <button type="submit" class="btn btn-primary" id="addOperationBtn">Добавить операцию</button>
                </div>
            </div>
        </div>
    </div>
<?php /* /Add modal */ ?>