<div id="ReportGeneralCustomersStats" class="report-table">
    <div class="reports-title">Статистика по клиенткам</div>

    <div class="panel assol-grey-panel">
        <div class="report-filter-wrap clear">
            <div class="date-filter-block">
                <div class="form-group">
                    <label for="general-salary-month">Месяц</label>
                    <select class="assol-btn-style" id="rgcs-month">
                        <option value="0">Январь</option>
                        <option value="1">Февраль</option>
                        <option value="2">Март</option>
                        <option value="3">Апрель</option>
                        <option value="4">Май</option>
                        <option value="5">Июнь</option>
                        <option value="6">Июль</option>
                        <option value="7">Август</option>
                        <option value="8">Сентябрь</option>
                        <option value="9">Октябрь</option>
                        <option value="10">Ноябрь</option>
                        <option value="11">Декабрь</option>
                    </select>
                </div>
            </div>

            <div class="date-filter-block">
                <div class="form-group calendar-block">
                    <label for="rgcs-year">Год</label>

                    <div class='input-group date' id='rgcs-year'>
                        <input type='text' class="assol-btn-style" id="rgcs-year-input" />
                        <span class="input-group-addon">
                                <span class="fa fa-calendar">
                                    <img src="<?= base_url() ?>/public/img/calendar-icon.png" alt="">
                                </span>
                            </span>
                    </div>
                </div>
            </div>

            <script>
                $(function() {
                    var years = $('#rgcs-year');
                    var months = $('#rgcs-month');

                    years.datetimepicker({
                        locale: 'ru',
                        format: 'YYYY',
                        viewMode: 'years',
                        defaultDate: 'now',
                        showTodayButton: true
                    }).on('dp.change', function () {
                        reloadRgcsTable();
                    });

                    months.change(function () {
                        reloadRgcsTable();
                    });

                    months.find("[value='" + moment().month() + "']").attr("selected", "selected");
                });

                function reloadRgcsTable(){
                    var m = $('#rgcs-month').val();
                    var y = $('#rgcs-year-input').val();
                    $.post(
                        '/reports/reload',
                        { month: m, year: y},
                        function(data){
                            $('#rgcs_table').html(data);
                        },
                        'html'
                    );
                }
            </script>

        </div>
    </div>

    <div id="rgcs_table">
        <?php
        $this->load->view('form/reports/general/rgcs_table',
            array(
                'sites' => $sites,
                'cs_customers' => $cs_customers,
            )
        );
        ?>
    </div>
</div>
