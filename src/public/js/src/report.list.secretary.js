$(document).ready(function(){
    "use strict";

    /** Список отчетов */
    const REPORT_INDIVIDUAL_LIST = {Level: 0};
    /** Индивидуальные отчеты -> Ежедневный отчет */
    const REPORT_OVERALL_ALLOCATION = {Level: 1, Name: 'Сводная таблица распределения'};

    var pathLevel = REPORT_INDIVIDUAL_LIST.Level;

    function getIindividualBread() {
        switch (pathLevel) {
            case REPORT_OVERALL_ALLOCATION.Level:
                return {Name: REPORT_OVERALL_ALLOCATION.Name, IsLast: true};
            default:
                return [];
        }
    }

    var path = {
        individual: {
            data: [
                {Level: REPORT_OVERALL_ALLOCATION.Level, Name: REPORT_OVERALL_ALLOCATION.Name, IsDoc: true}
            ]
        }
    };

    // Объект для публичного использования
    $.ReportListTranslate = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitTemplate();
            this.InitDynamicData();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on('click', '.report-folder>a, .report-bread', function (e) {
                var record = $(e.target).closest('[level]');
                pathLevel = parseInt(record.attr('level'));

                $.ReportListTranslate.ReloadReportList();
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            $.ReportListTranslate.ReloadReportList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#reportsTemplate").template('reportsTemplate');
        },
        /** Загрузка списка папок и отчетов */
        ReloadReportList: function () {
            $("#reports").html('Загрузка данных...');

            function render(records) {
                $("#reports").empty();
                $.tmpl('reportsTemplate', records).appendTo("#reports");
            }

            switch (pathLevel) {
                case REPORT_INDIVIDUAL_LIST.Level:
                    render(path.individual);
                    break;
                case REPORT_OVERALL_ALLOCATION.Level:
                    render({bread: getIindividualBread()});
                    break;
                default:
                    alert('Ошибка загрузки данных');
            }

            $.ReportListTranslate.ShowReport(pathLevel);
        },
        ShowReport: function (level) {
            $('#ReportOverallAllocation').toggle(level == REPORT_OVERALL_ALLOCATION.Level);

            switch (level) {
                case REPORT_OVERALL_ALLOCATION.Level:
                    $('#overallAllocationSite').find('input:first').click();
                    break;
            }
        }
    };

    // Инициализация объекта
    $.ReportListTranslate.Init();
});