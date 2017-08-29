$(document).ready(function(){

    /** Количество записей на страницу */
    var pageRecordLimit = 10;
    /** Количество страниц */
    var pageCount = 1;
    /** Текущая страница */
    var currentPage = 1;

    // Объект для публичного использования
    $.News = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $(document).on("click", ".action-remove-news", function (e) {
                $.News.RemoveNewsRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", "#newsCategory input:radio", function (e) {
                var nCustomer = $('#newsCustomer input:radio:checked').val();
                $.News.ReloadNewsList($(e.target).val(), nCustomer);
                // во второй список фильтра выбираем только тех клиенток, которые связаны с выбранным сайтом
                $.News.ReloadCustomerList($(e.target).val());
            });
            $(document).on("click", "#newsCustomer input:radio", function (e) {
                var nCategory = $('#newsCategory input:radio:checked').val();
                $.News.ReloadNewsList(nCategory, $(e.target).val());
            });

            $('.assol-pagination-arrs .next').click(function () {
                $('#CurrentPage').val((parseInt($('#CurrentPage').val()) || 0) + 1);
                $.News.ReloadNewsList();
            });

            $('.assol-pagination-arrs .prev').click(function () {
                $('#CurrentPage').val((parseInt($('#CurrentPage').val()) || 0) - 1);
                $.News.ReloadNewsList();
            });

            $(document).on("keyup", ".filter-input", function (e) {
                delay(function(){
                    $.News.ReloadNewsList();
                }, 500);
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            this.ReloadNewsList();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#newsTemplate").template('newsTemplate');
        },
        /** Загрузка списка новостей */
        ReloadNewsList: function (category, customer) {
            category = category || $('#newsCategory input:radio:checked').val();
            customer = customer || $('#newsCustomer input:radio:checked').val(); // input[name=NewsCustomer]:checked
            this.ReloadData('#news', 'newsTemplate', category, customer);
        },
        /** формируем список клиенток для данного сайта */
        ReloadCustomerList: function (category) {
            category = category || $('#newsCategory input:radio:checked').val();
            $.post(
                BaseUrl + 'news/customerlist',
                {
                    siteID: (category || 0)
                },
                function(data){
                    if(data !== ''){
                        $('#newsCustomer > ul.dropdown-menu').html('').append(data);
                    }
                },
                'html'
            );
        },
        /**
         * Загрузка и рендер данных
         *
         * @param TargetSelector селектор контейнера для загрузки данных
         * @param TemplateName имя шаблона для рендера
         * @param category
         * @param customer
         */
        ReloadData: function(TargetSelector, TemplateName, category, customer){
            $(TargetSelector).html('Загрузка данных...');

            function callback(data) {
                if (data.status) {
                    if (data.records){
                        $(TargetSelector).empty();
                        $.tmpl(TemplateName, data.records).appendTo(TargetSelector);
                    }

                    // Устанавливаем количество страниц
                    pageCount = Math.ceil(data.count / pageRecordLimit);
                    // Сброс текущего значения при привышение лимита
                    currentPage = parseInt($('#CurrentPage').val()) || 1;
                    if (currentPage > pageCount)
                        $('#CurrentPage').val(1);

                    $('#CountPage').html(pageCount);
                } else {
                    showErrorAlert(data.message)
                }
            }

            currentPage = parseInt($('#CurrentPage').val()) || 0;
            if (currentPage < 1)
                $('#CurrentPage').val(1);
            if (currentPage > pageCount)
                $('#CurrentPage').val(pageCount);

            var data = {
                category: (category || 0),
                customer: (customer || 0),
                Limit: pageRecordLimit,
                Offset: (($('#CurrentPage').val() || 1) - 1) * pageRecordLimit
            };

            var urlData = BaseUrl + 'news/data';

            $.post(urlData, {data: data}, callback, 'json');
        },
        /** Удаление новости по ID */
        RemoveNewsRecord: function(RecordID) {
            //hideAlerts();

            function callback(data) {
                if (data.status) {
                    //showSuccessAlert('Язык успешно удален');
                    $.News.ReloadNewsList();
                } else {
                    //showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'news/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        }
    };

    // Инициализация объекта
    $.News.Init();

});