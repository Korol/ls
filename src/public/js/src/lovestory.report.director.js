$(document).ready(function(){
    "use strict";

    // Объект для публичного использования
    $.ReportDirector = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitTemplate();
            this.InitDynamicData();
        },
        /** Инициализация событий */
        InitActions: function() {

            //region ReportOverallSalary

            $(document).on('click', '#ReportOverallSalary_data a.confirm', function () {
                var td = $(this).closest('td');

                var data = {
                    SiteID: $('#overlaySalarySite').find("input:radio:checked").val(),
                    employee: $(this).closest('tr').attr('id-employee'),
                    year: $('#overlay-salary-year').data("DateTimePicker").date().year(),
                    month: $('#overlay-salary-month').val()
                };

                function callback(data) {
                    if (data.status) {
                        td.html('подтверждено');
                        alert('Данные успешно отправлены в общую таблицу з/п');
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                }

                $.post(BaseUrl + 'reports/overlay/salary/close', data, callback, 'json');
            });

            $(document).on("click", "#overlaySalarySite input:radio", function (e) {
                $.ReportDirector.ReloadReportOverallSalary($(e.target).val());
            });

            $(document).on('click', '#ReportOverallSalary_data>tbody>tr>td[type]>div', function () {
                var type = $(this).parent().hasClass('decimal') ? 'decimal' : 'number';
                updateCellModel($(this).parent(), $(this).html(), type);
            });

            $(document).on('focusout', '#ReportOverallSalary_data td[type]>input', function () {
                var cell = $(this).parent();
                var isDecimal = cell.hasClass('decimal');

                var lastValue = isDecimal
                    ? parseFloat($(this).attr('last-value') || 0.00)
                    : parseInt($(this).attr('last-value') || 0);
                var newValue = isDecimal
                    ? parseFloat($(this).val() || 0.00)
                    : parseInt($(this).val() || 0);

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);

                        var original = cell.attr('original') || 0;

                        if (newValue != original) {
                            cell.addClass('editable-cell');
                        } else {
                            cell.removeClass('editable-cell');
                        }

                        var td = cell.closest('tr').find('.status');
                        if (td.html() == 'подтверждено') {
                            td.html('<a href="#" class="confirm">подтвердить</a>');
                        }

                        $.ReportDirector.RefreshReportOverallSalaryDataSummary();
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка: ' + data.message);
                    }
                }

                if (lastValue != newValue) {
                    var year = $('#overlay-salary-year').data("DateTimePicker").date().year();
                    var month = $('#overlay-salary-month').val();
                    var type = cell.attr('type');

                    var data = {
                        SiteID: $('#overlaySalarySite').find("input:radio:checked").val(),
                        idEmployee: cell.closest('tr').attr('id-employee'),
                        year: year,
                        month: month,
                        type: type,
                        value: newValue
                    };

                    $.post(BaseUrl + 'reports/overlay/salary/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }

            });

            //endregion

            //region ReportGeneralSalary

            $(document).on('click', '#ReportGeneralSalary_data thead td[id-site]>div', function () {
                updateCellModel($(this).parent(), $(this).html());
            });

            $(document).on('focusout', '#ReportGeneralSalary_data thead td[id-site]>input', function () {
                var cell = $(this).parent();

                var isDecimal = cell.hasClass('decimal');

                var lastValue = isDecimal
                    ? parseFloat($(this).attr('last-value') || 0.00)
                    : parseInt($(this).attr('last-value') || 0);
                var newValue = isDecimal
                    ? parseFloat($(this).val() || 0.00)
                    : parseInt($(this).val() || 0);

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);

                        $.ReportDirector.ReloadReportGeneralSalaryDataSummary();
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка: ' + data.message);
                    }
                }

                if (lastValue != newValue) {
                    var year = $('#general-salary-year').data("DateTimePicker").date().year();
                    var month = $('#general-salary-month').val();

                    var data = {
                        idEmployee: cell.closest('tr').attr('id-employee'),
                        idSite: cell.attr('id-site'),
                        year: year,
                        month: month,
                        value: newValue
                    };

                    $.post(BaseUrl + 'reports/general/salary/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }

            });

            $(document).on('click', '#ReportGeneralSalary_data .is-repay-tooltip .yes', function () {
                var td = $(this).closest('td');

                function callback(data) {
                    if (data.status) {
                        td.addClass('repay');
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                }

                var data = {idRecord: td.find('div[id-record]').attr('id-record'), paid: 1};

                $.post(BaseUrl + 'reports/general/salary/paid', data, callback, 'json');
            });

            $(document).on('click', '#ReportGeneralSalary_data .is-repay-tooltip .no', function () {
                var td = $(this).closest('td');

                function callback(data) {
                    if (data.status) {
                        td.removeClass('repay');
                    } else {
                        alert('Ошибка: ' + data.message);
                    }
                }

                var data = {idRecord: td.find('div[id-record]').attr('id-record'), paid: 0};

                $.post(BaseUrl + 'reports/general/salary/paid', data, callback, 'json');
            });

            //endregion

            //region ReportGeneralOfEmployees
            $(document).on('click', '#ReportGeneralOfEmployees_data .is-edit>div', function () {
                updateCellModel($(this).parent(), $(this).html(), 'decimal');
            });

            $(document).on("click", "#generalSite input:radio", function () {
                $.ReportDirector.ReloadGeneralOfEmployeesMeta($(this).val());
            });

            $(document).on('focusout', '#ReportGeneralOfEmployees_data .is-edit>input', function () {
                var cell = $(this).parent();

                var isDecimal = cell.hasClass('decimal');

                var lastValue = isDecimal
                    ? parseFloat($(this).attr('last-value') || 0.00)
                    : parseInt($(this).attr('last-value') || 0);
                var newValue = isDecimal
                    ? parseFloat($(this).val() || 0.00)
                    : parseInt($(this).val() || 0);

                function hideInput(value) {
                    cell.html($('<div/>', {text: value}));
                }

                function callback(data) {
                    if (data.status) {
                        hideInput(newValue);

                        $.ReportDirector.RefreshReportGeneralOfEmployeesDataSummary();
                    } else {
                        hideInput(lastValue);
                        alert('Ошибка: ' + data.message);
                    }
                }

                if (lastValue != newValue) {
                    var year = $('#general-year').data("DateTimePicker").date().year();
                    var month = $('#general-month').val();

                    var data = {
                        idSite: $('#generalSite').find("input:radio:checked").val(),
                        idEmployee: $(this).closest('[id-employee]').attr('id-employee'),
                        year: year,
                        month: month,
                        value: newValue
                    };

                    $.post(BaseUrl + 'reports/lovestory/daily/plan/agency/save', data, callback, 'json');
                } else {
                    hideInput(lastValue);
                }

            });
            //endregion

            $(document).on("click", "#overallAllocationSite input:radio", function (e) {
                $.ReportDirector.ReloadReportOverallAllocation($(e.target).val());
            });

            function updateCellModel(cell, value, type) {
                var lastValue = value;

                switch ((type || 'number')) {
                    case 'number':
                        value = (value > 0 ? value : '');
                        break;
                    case 'decimal':
                        value = (value > 0.00 ? value : '');
                        break;
                }

                if ($(cell).contentEditable != null) {
                    $(cell).attr("contentEditable", isEdit);
                } else {
                    var input = $('<input/>', {type: 'text', min: 0, value: value}).attr('last-value', lastValue);

                    switch ((type || 'number')) {
                        case 'number':
                            input.numeric();
                            break;
                        case 'decimal':
                            input.numeric();
                            input.keydown(function (e) {
                                if(e.keyCode == '188' || e.charCode == '188' || e.keyCode == '110' || e.charCode == '110'){
                                    e.preventDefault();

                                    var input = $(e.target);
                                    var isExistDecimal = input.val().indexOf('.');
                                    if (isExistDecimal > 0) return;

                                    input.val(input.val() + '.');
                                }
                            });
                            //input.numeric({decimalPlaces: 2});
                            break;
                    }

                    $(cell).html(input);

                    input.focus();
                }
            }

        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {

        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#reportGeneralOfEmployees_data_Template").template('reportGeneralOfEmployees_data_Template');
            $("#reportGeneralOfEmployees_total_Template").template('reportGeneralOfEmployees_total_Template');
            $("#reportOverallSalaryTemplate").template('reportOverallSalaryTemplate');
            $("#reportGeneralSalarySumTemplate").template('reportGeneralSalarySumTemplate');
            $("#reportOverallAllocation_Template").template('reportOverallAllocation_Template');
        },
        ReloadReportOverallSalary: function (SiteID) {
            SiteID = SiteID || $('#overlaySalarySite').find("input:radio:checked").val();

            var report_data = $('#ReportOverallSalary_data').find('>tbody');
            report_data.empty();

            var data = {
                SiteID: SiteID,
                year: $('#overlay-salary-year').data("DateTimePicker").date().year(),
                month: $('#overlay-salary-month').val()
            };

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportOverallSalaryTemplate', data.records).appendTo(report_data);
                    $.ReportDirector.RefreshReportOverallSalaryDataSummary();
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/overlay/salary/data', data, callback, 'json');
        },
        RefreshReportOverallSalaryDataSummary: function () {
            var reportData = $('#ReportOverallSalary_data'); // Таблица с данными

            // Очищаем сумму по переводчикам и итого
            $(reportData).find('tr').find('td:eq(9)').html(0);

            // Заполнение суммы по сайтам
            $(reportData).find('tbody>tr').each(function() {
                var emailAmount     = parseFloat($(this).find('td:eq(2) div').html()) || 0.00;
                var chatAmount      = parseFloat($(this).find('td:eq(4) div').html()) || 0.00;
                var deliveryAmount  = parseFloat($(this).find('td:eq(6) div').html()) || 0.00;
                var dealerAmount    = parseFloat($(this).find('td:eq(8) div').html()) || 0.00;

                $(this).find('td:eq(9)').html((emailAmount + chatAmount + deliveryAmount + dealerAmount).toFixed(2));
            });

            // Заполнение общей суммы по сайтам
            var total = $(reportData).find('tfoot td:eq(9)');
            $(reportData).find('tbody>tr').find('td:eq(9)').each(function() {
                var employeeAmount = parseFloat($(this).html()) || 0.00;
                var totalAmount = parseFloat(total.html()) || 0.00;

                total.html((totalAmount + employeeAmount).toFixed(2));
            });
        },
        ReloadReportGeneralSalary: function () {
            $('#ReportGeneralSalary_data').find('thead tr[id-employee] td[id-site] div').empty();
            $('#ReportGeneralSalary_data').find('tbody tr[id-employee] td[id-site]').empty().removeClass('repay');

            var data = {
                year: $('#general-salary-year').data("DateTimePicker").date().year(),
                month: $('#general-salary-month').val()
            };

            function callback(data) {
                if (data.status) {
                    // Ставим "-" для всех
                    $('#ReportGeneralSalary_data tbody tr[id-employee] td[id-site]').html("-");

                    // Убираем "-" для сайтов за которыми закреплены сотрудники
                    $(data.cross).each(function (key, cross) {
                        $('#ReportGeneralSalary_data tbody tr[id-employee="'+cross.EmployeeID+'"] td[id-site="'+cross.SiteID+'"]').html("");
                    });

                    // Заполнение зарплаты
                    $(data.records).each(function (key, record) {
                        if (record.EmployeeID > 0) {
                            var tbody_selector = '#ReportGeneralSalary_data tbody tr[id-employee="'+record.EmployeeID+'"] td[id-site="'+record.SiteID+'"]';

                            $(tbody_selector).attr('sum', record.value);

                            if (record.paid == 1) {
                                $(tbody_selector).closest('td').addClass('repay');
                            }

                            $.tmpl('reportGeneralSalarySumTemplate', record).appendTo(tbody_selector);
                        } else {
                            var thead_selector = '#ReportGeneralSalary_data thead tr[id-employee="'+record.EmployeeID+'"] td[id-site="'+record.SiteID+'"]';
                            $(thead_selector).attr('sum', record.value);
                            $(thead_selector).find('div').html(record.value);

                            var tdHeadTotal = $('#ReportGeneralSalary_data').find('thead th[data-total]');
                            tdHeadTotal.html(((parseFloat(tdHeadTotal.html()) || 0.00) + parseFloat(record.value)).toFixed(2));
                        }
                    });


                    $.ReportDirector.ReloadReportGeneralSalaryDataSummary();
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/general/salary/data', data, callback, 'json');
        },
        ReloadReportGeneralSalaryDataSummary: function () {
            // Заполнение итого в разрезе сайтов в футоре
            $('.total-salary-report-table thead td[id-site]').each(function () {
                var SiteID = $(this).attr('id-site');
                var InputValue = parseFloat($(this).find('div').html()) || 0.00;

                $('.total-salary-report-table tbody td[id-site="'+SiteID+'"]').each(function () {
                    InputValue -= parseFloat($(this).find('div>a').html()) || 0.00;
                });

                $('.total-salary-report-table tfoot td[id-site="'+SiteID+'"]').html(InputValue.toFixed(2));
            });

            // Заполнение колонки итого в разрезе выданных денег
            var headTotal = parseFloat($('#ReportGeneralSalary_data').find('thead th[data-total]').html()) || 0;
            var tdFootTotal = $('#ReportGeneralSalary_data').find('tfoot td[data-total]');
            tdFootTotal.html(headTotal.toFixed(2));
            $('.total-salary-report-table tbody tr').each(function () {
                var tr = $(this);
                var total = 0.00;

                $(this).find('td[id-site][sum]').each(function () {
                    total += parseFloat($(this).attr('sum'));
                });

                tr.find('td[data-total]').html(total.toFixed(2));

                tdFootTotal.html(((parseFloat(tdFootTotal.html()) || 0.00) - total).toFixed(2))
            });
        },
        ReloadReportGeneralOfCustomers: function () {
            $.ReportDirector.ReloadGeneralOfCustomersMeta();
        },
        ReloadReportOverallAllocation: function (SiteID) {
            SiteID = SiteID || $('#overallAllocationSite').find("input:radio:checked").val();

            var report_data = $('#ReportOverallAllocation_data').find('>tbody');
            report_data.empty();
            $('#ReportOverallNotAllocation_data').find('td').empty();

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportOverallAllocation_Template', data.records).appendTo(report_data);
                    $('#ReportOverallNotAllocation_data').find('td').html(data.records.freeAllSitesCustomers);
                } else {
                    alert('Ошибка получения данных');
                }
            }

            $.post(BaseUrl + 'reports/overall/allocation/data', {SiteID: SiteID}, callback, 'json');
        },
        ReloadGeneralOfEmployeesMeta: function (Site) {
            $('#ReportGeneralOfEmployees_data').empty();
            $('#ReportGeneralOfEmployees_total').empty();

            var years = $('#general-year');
            var months = $('#general-month');

            function getYear() {
                return years.data("DateTimePicker").date().year();
            }

            function callback(data) {
                if (data.status) {
                    var lYear = getYear();
                    var lMonth = months.val();
                    var lastDay = moment([lYear, lMonth]).endOf('month').date();

                    data.records.days = [];
                    for (var day = 1; day <= lastDay; day++) {
                        data.records.days.push({
                            day: day,
                            date: moment([lYear, lMonth, day]).format('YYYY-MM-DD')
                        });
                    }

                    $.tmpl('reportGeneralOfEmployees_data_Template', data.records).appendTo('#ReportGeneralOfEmployees_data');
                    $.tmpl('reportGeneralOfEmployees_total_Template', data.records).appendTo('#ReportGeneralOfEmployees_total');

                    $.ReportDirector.ReloadReportGeneralOfEmployeesData();
                }
            }

            var data = {
                site: Site || $('#generalSite').find("input:radio:checked").val()
            };

            $.post(BaseUrl + 'reports/lovestory/general/meta', data, callback, 'json');
        },
        ReloadReportGeneralOfEmployeesData: function () {
            var table = $('#ReportGeneralOfEmployees_data');

            // Очистка данных
            table.find('.plan, .emails, .chat').html(0);

            var data = {
                year: $('#general-year').data("DateTimePicker").date().year(),
                month: $('#general-month').val(),
                site: $('#generalSite').find("input:radio:checked").val()
            };

            function callback(data) {
                if (data.status) {
                    // Заполняем данные
                    $.each(data.records.plans, function(key, value) {
                        table.find('tr[id-employee="'+ value.EmployeeID +'"] .plan').html(value.plan);
                    });

                    $.each(data.records.report, function(key, value) {
                        table.find('#ger_'+ value.EmployeeID +'_'+ value.date).html((parseFloat(value.emails) + parseFloat(value.chat)).toFixed(2));
                    });

                    table.find('tfoot tr:eq(1)>td>div').html(0);

                    $.each(data.records.agent, function () {
                        $('#apm_' + this.EmployeeID).find('div').html(this.value);
                    });

                    $.ReportDirector.RefreshReportGeneralOfEmployeesDataSummary();
                }
            }

            $.post(BaseUrl + 'reports/lovestory/general/data', data, callback, 'json');
        },
        RefreshReportGeneralOfEmployeesDataSummary: function () {
            var table = $('#ReportGeneralOfEmployees_data');

            // Очистка данных
            table.find('tfoot tr:eq(0)>td:gt(0)').html(0);
            $('#ReportGeneralOfEmployees_total').find('tbody td').html(0);

            var totalSumBal = $('#rge_total_bal');
            totalSumBal.html(0);

            // Заполняем сводные данные
            $(table).find('tbody .value').each(function() {
                var idEmployee = $(this).closest('[id-employee]').attr('id-employee');
                var total = $('#rge_total_' + idEmployee);
                total.html((parseFloat(total.html()) + parseFloat($(this).html())).toFixed(2));

                // Заполнение строчки "Итог сумм переводчиков"
                var day = $(this).attr('day');
                var totalSum = $('#rge_sum_total_' + day);
                totalSum.html((parseFloat(totalSum.html()) + parseFloat($(this).html())).toFixed2);

                // Заполнение колонки "Итого" в строчке "Итог сумм переводчиков"
                totalSumBal.html((parseFloat(totalSumBal.html()) + parseFloat($(this).html())).toFixed(2));
            });

            var planSumTotal = $('#rge_plan_sum_total');

            $(table).find('tbody .plan').each(function() {
                var idEmployee = $(this).closest('[id-employee]').attr('id-employee');
                var planTotal = $('#rge_plan_' + idEmployee);
                var total = $('#rge_total_' + idEmployee);

                planTotal.html((parseFloat($(this).html()) - parseFloat(total.html())).toFixed(2));

                // Заполнение колонки "План месяца" в строчке "Итог сумм переводчиков"
                planSumTotal.html((parseFloat(planSumTotal.html()) + parseFloat($(this).html())).toFixed(2));
            });

            var planAgentSumTotal = $('#rge_agent_plan_sum_total');

            $(table).find('tbody .is-edit>div').each(function() {
                // Заполнение колонки "План агенства" в строчке "Итог сумм переводчиков"
                planAgentSumTotal.html((parseFloat(planAgentSumTotal.html()) + parseFloat($(this).html())).toFixed(2));
            });

        }
    };

    // Инициализация объекта
    $.ReportDirector.Init();
});