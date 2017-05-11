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
            $("#reportGeneralOfCustomersTemplate").template('reportGeneralOfCustomersTemplate');
            $("#reportGeneralOfCustomers_fixedWrapBody_Template").template('reportGeneralOfCustomers_fixedWrapBody_Template');
            $("#reportGeneralOfCustomers_total_Template").template('reportGeneralOfCustomers_total_Template');
            $("#reportOverallSalaryTemplate").template('reportOverallSalaryTemplate');
            $("#reportGeneralSalarySumTemplate").template('reportGeneralSalarySumTemplate');
            $("#reportOverallAllocation_Template").template('reportOverallAllocation_Template');
        },
        RefreshReportGeneralOfCustomersDate: function (year) {
            var years = $('#general-customers-year');
            var months = $('#general-customers-month');
            var days = $('#general-customers-day');

            function getYear() {
                return years.data("DateTimePicker").date().year();
            }

            var lastDay = moment([year || getYear(), months.val()]).endOf('month').date();

            days.empty();
            days.append($("<option/>", {value: 0, text: 'за месяц'}));
            for (var i = 1; i <= lastDay; i++)
                days.append($("<option/>", {value: i, text: i}));

            if (months.val() == moment().month())
                days.find("[value='" + moment().date() + "']").attr("selected", "selected");

            $.ReportDirector.ReloadReportGeneralOfCustomersData();
        },
        RefreshReportGeneralOfCustomersDataSummary: function () {
            var reportData = $('#ReportGeneralOfCustomers_data');                  // Таблица с данными
            var summaryData = $('#ReportGeneralOfCustomers_total');                // Таблица с сводными данными
            var fixedSummaryData = $('#ReportGeneralOfCustomers_fixedWrapBody');   // Фиксированная таблица с сводными данными

            // Очищаем данные и сводные данные
            $(reportData).find('tfoot td div').html(0);
            $(summaryData).find('tbody td, tfoot td').html(0);
            $(fixedSummaryData).find('tbody td, tfoot td').html(0);

            // Заполняем сводные данные по письмам в разрезе сайта + в разрезе клиента
            $(reportData).find('tbody .mail').each(function() {
                // Подсчет количества email в разрезе сайта
                var idEmployeeSite = $(this).attr('id-site');
                var footMail = $('#gc_foot_mail_' + idEmployeeSite);
                footMail.html(parseFloat(footMail.html()) + parseFloat($(this).find('div').html()));

                // Подсчет количества email в разрезе клиента
                var idCustomer = $(this).attr('id-customer');
                var totalMail = $('#gc_total_' + idCustomer).find('td:eq(0)');
                totalMail.html(parseFloat(totalMail.html()) + parseFloat($(this).find('div').html()));
            });

            // Заполняем сводные данные по чатам в разрезе сайта + в разрезе клиента + сумма писем и чатов
            $(reportData).find('tbody .chat').each(function() {
                // Подсчет количества сообщений чата в разрезе сайта
                var idEmployeeSite = $(this).attr('id-site');
                var footChat = $('#gc_foot_chat_' + idEmployeeSite);
                footChat.html(parseFloat(footChat.html()) + parseFloat($(this).find('div').html()));

                // Подсчет количества сообщений чата в разрезе клиента
                var idCustomer = $(this).attr('id-customer');
                var total = $('#gc_total_' + idCustomer);
                var totalMail = total.find('td:eq(0)');
                var totalChat = total.find('td:eq(1)');
                var totalAll = total.find('td:eq(2)');

                totalChat.html(parseFloat(totalChat.html()) + parseFloat($(this).find('div').html()));
                totalAll.html(parseFloat(totalChat.html()) + parseFloat(totalMail.html()));
                $('#gc_slide_total_' + idCustomer).find('td').html(totalAll.html());
            });

            // Заполняем футер для сводных данных в разрезе клиента
            var totalFooter = $(summaryData).find('tfoot>tr');
            var totalFooterMail = totalFooter.find('td:eq(0)');
            var totalFooterChat = totalFooter.find('td:eq(1)');
            var totalFooterAll = totalFooter.find('td:eq(2)');

            $(summaryData).find('tbody>tr').each(function () {
                totalFooterMail.html(parseFloat(totalFooterMail.html()) + parseFloat($(this).find('td:eq(0)').html()));
                totalFooterChat.html(parseFloat(totalFooterChat.html()) + parseFloat($(this).find('td:eq(1)').html()));
                totalFooterAll.html(parseFloat(totalFooterAll.html()) + parseFloat($(this).find('td:eq(2)').html()));
            });

            $(fixedSummaryData).find('tfoot>tr>td').html(totalFooterAll.html());
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

                            if (record.paid == 1) {
                                $(tbody_selector).closest('td').addClass('repay');
                            }

                            $.tmpl('reportGeneralSalarySumTemplate', record).appendTo(tbody_selector);
                        } else {
                            var thead_selector = '#ReportGeneralSalary_data thead tr[id-employee="'+record.EmployeeID+'"] td[id-site="'+record.SiteID+'"]';
                            $(thead_selector).find('div').html(record.value);
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
            $('.total-salary-report-table thead td[id-site]').each(function () {
                var SiteID = $(this).attr('id-site');
                var InputValue = parseFloat($(this).find('div').html()) || 0.00;

                $('.total-salary-report-table tbody td[id-site="'+SiteID+'"]').each(function () {
                    InputValue -= parseFloat($(this).find('div>a').html()) || 0.00;
                });

                $('.total-salary-report-table tfoot td[id-site="'+SiteID+'"]').html(InputValue.toFixed(2));
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
        ReloadGeneralOfCustomersMeta: function () {
            $('#ReportGeneralOfCustomers_data').empty();
            $('#ReportGeneralOfCustomers_fixedWrapBody').find('>tbody').empty();
            $('#ReportGeneralOfCustomers_total').find('>tbody').empty();

            function callback(data) {
                if (data.status) {
                    $.tmpl('reportGeneralOfCustomersTemplate', data.records).appendTo('#ReportGeneralOfCustomers_data');
                    $.tmpl('reportGeneralOfCustomers_fixedWrapBody_Template', data.records.customers).appendTo('#ReportGeneralOfCustomers_fixedWrapBody>tbody');
                    $.tmpl('reportGeneralOfCustomers_total_Template', data.records.customers).appendTo('#ReportGeneralOfCustomers_total>tbody');

                    $.ReportDirector.RefreshReportGeneralOfCustomersDate();
                }
            }

            $.getJSON(BaseUrl + 'reports/general/customers/meta', callback);
        },
        ReloadReportGeneralOfCustomersData: function () {
            var data = {
                year: $('#general-customers-year').data("DateTimePicker").date().year(),
                month: $('#general-customers-month').val(),
                day: $('#general-customers-day').val()
            };

            function callback(data) {
                function initEditCell(cell, typeClass, idCustomer, idSite, value) {
                    // Установка свойств
                    cell.addClass(typeClass)
                        .attr('id-customer', idCustomer)
                        .attr('id-site', idSite)
                        .find('div').html(value || 0);
                }

                // Очищаем данные
                $('#ReportGeneralOfCustomers_data').find('.mail div, .chat div').html(0);

                // Заполняем данные
                $.each(data.records, function(key, value) {
                    var suffix = value.CustomerID + '_' + value.SiteID;

                    initEditCell($('#gc_mail_' + suffix), 'mail', value.CustomerID, value.SiteID, value.emails);
                    initEditCell($('#gc_chat_' + suffix), 'chat', value.CustomerID, value.SiteID, value.chat);
                });

                $.ReportDirector.RefreshReportGeneralOfCustomersDataSummary();
            }

            $.post(BaseUrl + 'reports/general/customers/data', data, callback, 'json');
        }
    };

    // Инициализация объекта
    $.ReportDirector.Init();
});