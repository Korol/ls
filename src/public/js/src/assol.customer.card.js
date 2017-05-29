$(document).ready(function(){

    /**
     * Функция сбора измененных данных
     *
     * @param fields массив полей, среди которых необходимо искать измененные данные
     */
    function getChangeData(fields) {
        var data = {};

        $.each(fields, function (index, field){
            // Получаем элемент содержащий значение
            var source = $('#'+field);

            // Получаем значение элемента
            var currentValue = source.is('div')
                ? $(source).find('input:checked').val()
                : $(source).val();

            // Получаем оригинальное значение элемента
            var originalValue = CustomerRecord[field];

            // Добавляем в результат если новое или оригинальное значение указано и они отличаются
            if (!($.isBlank(currentValue) && $.isBlank(originalValue)) && (currentValue != originalValue)) {
                // Дополнительная обработка даты
                data[field] = isDateField(source) ? toServerDate(currentValue) : currentValue;
                CustomerRecord[field] = currentValue;
            }
        });

        return data;
    }

    // Объект для публичного использования
    $.CustomerCard = {
        /**
         * Получить URL для сохранения данных клиента
         *
         * @return {string}
         */
        GetCustomerUpdateUrl: function() {
            return BaseUrl+'customer/'+CustomerID+'/update';
        },
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            $('#SavePersonalData').click(this.SavePersonalData);
            $('#SaveSelfDescription').click(this.SaveSelfDescription);
            $('#QuestionDescription').click(this.QuestionDescription);
            $('#SaveAdditionally').click(this.SaveAdditionally);
            $('#SaveRemove').click(this.SaveRemove);
            $('#SaveReservationContact').click(this.SaveReservationContact);
            $('#SaveSites').click(this.SaveSites);
            $('#SaveVideo').click(this.SaveVideoSites);
            $('#SavePhoto').click(this.SavePhoto);
            $('#SavePhotoAndVideo').click(this.SavePhotoAndVideo);

            $('#CustomerRemove').click(this.CustomerRemove);
            $('#CustomerMarkRemove').click(this.CustomerMarkRemove);
            $('#CustomerRestore').click(this.CustomerRestore);

            $('#SaveVerificationLink').click(function(){
                var link = $('#VerificationLink').val();
                if (link) {
                    $.CustomerCard.SaveVideoLink(link, 0);
                }
            });
            $('#SaveAmateurLink').click(function(){
                var link = $('#AmateurLink').val();
                if (link) {
                    $.CustomerCard.SaveVideoLink(link, 1);
                }
            });

            $(document).on("click", ".action-remove-passport", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemovePassportRecord($(e.target).attr('record'));
                });
            });
            $(document).on("click", ".action-remove-agreement", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveAgreementRecord($(e.target).attr('record'));
                });
            });
            $(document).on("click", ".action-remove-question-photo", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveQuestionPhotoRecord($(e.target).attr('record'));
                });
            });

            $(document).on("click", ".action-remove-site", function (e) {
                var siteID = $(e.target).closest('.work-sites-block').attr('record');
                var recordID = $(e.target).attr('record');
                confirmRemove(function(){
                    $.CustomerCard.RemoveSiteRecord(siteID, recordID);
                });
            });

            $(document).on("click", ".action-append-video-site", function (e) {
                var videoSiteID = $(e.target).closest('.work-sites-block').attr('video-site');
                var videoLink = $(e.target).closest('.form-group').find('input').val();
                var videoType = $(e.target).closest('[video-type]').attr('video-type');

                $.CustomerCard.SaveVideoSiteLink(videoSiteID, videoLink, videoType);
            });

            $(document).on("click", ".action-remove-question", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveQuestionRecord($(e.target).closest('[record]').attr('record'));
                });
            });
            $(document).on("click", ".action-save-answer", function (e) {
                $.CustomerCard.SaveQuestionAnswer($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-save-language", function (e) {
                $.CustomerCard.SaveLanguageRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-language", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveLanguageRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-children", function (e) {
                $.CustomerCard.SaveChildrenRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-children", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveChildrenRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-remove-story", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveStoryRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-save-email", function (e) {
                $.CustomerCard.SaveEmailRecord($(e.target).closest('button').attr('record'));
            });
            $(document).on("click", ".action-remove-email", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveEmailRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-remove-video", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveVideoRecord($(e.target).closest('button').attr('record'));
                });
            });
            $(document).on("click", ".action-remove-video-site", function (e) {
                var siteID = $(e.target).closest('.work-sites-block').attr('id-site');
                var videoSiteID = $(e.target).closest('.work-sites-block').attr('video-site');
                confirmRemove(function(){
                    $.CustomerCard.RemoveVideoSiteRecord(siteID, videoSiteID);
                });
            });
            $(document).on("click", ".action-remove-video-site-link", function (e) {
                var videoSiteID = $(e.target).closest('.work-sites-block').attr('video-site');

                var button = $(e.target).closest('button');
                var videoSiteLink = button.attr('video-link');
                var videoType = button.attr('video-type');

                confirmRemove(function(){
                    $.CustomerCard.RemoveVideoSiteLinkRecord(videoSiteID, videoSiteLink, videoType);
                });
            });

            $(document).on("click", ".action-remove-album", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveAlbumRecord($(e.target).closest('div[record]').attr('record'));
                });
            });

            $(document).on("click", ".image-remove-btn", function (e) {
                confirmRemove(function(){
                    $.CustomerCard.RemoveImageAlbumRecord($(e.target).closest('div[id-cross]').attr('id-cross'));
                });
            });

            $(document).on("change", "#addClientAvatar", function (e) {
                $('#AvatarForm').submit();
            });
            $('#AvatarForm').ajaxForm(function(data) {
                if (data.status) {
                    $.CustomerCard.RefreshAvatar(data.id, data.FileName);
                } else {
                    showErrorAlert(data.message)
                }
            });

            // Права доступа
            $.each(EmployeeRights, function(key, right) {
                $('#employeeAccess_'+right.EmployeeID).click();
            });

            $('#SaveEmployeeAccess').click(function () {
                function callback(data) {
                    if (data.status) {
                        showSuccessAlert('Права пользователя успешно сохранены');
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var employees = [];
                $('#employeeAccess').find("input:checked").each(function(){
                    employees.push($(this).val());
                });

                var data = {
                    Customer:  CustomerID,
                    Employees: employees
                };

                $.post(BaseUrl + '/customer/'+CustomerID+'/rights', data, callback, 'json');
            });

            $('a[href="#Questions"]').on('shown.bs.tab', function () {
                //$.CustomerCard.ReloadQuestionList();

                $.each($('textarea[data-autoresize]'), function() {
                    var offset = this.offsetHeight - this.clientHeight;

                    var resizeTextarea = function(el) {
                        $(el).css('height', 'auto').css('height', el.scrollHeight + offset);
                    };

                    resizeTextarea($(this));

                    $(this).on('keyup input', function() { resizeTextarea(this); }).removeAttr('data-autoresize');
                });
            });
            $('a[href="#PhotoAndVideo"]').on('shown.bs.tab', function () {
                $.CustomerCard.ReloadVideoVerificationList();
                $.CustomerCard.ReloadVideoAmateurList();
            });
            $('a[href="#Photo"]').on('shown.bs.tab', function () {
                $.CustomerCard.ReloadAlbumList();
            });
            $('a[href="#Video"]').on('shown.bs.tab', function () {
                $.CustomerCard.ReloadVideoSiteList();
            });
            $('a[href="#Story"]').on('shown.bs.tab', function () {
                $.CustomerCard.ReloadStoryList();
            });
            $('a[href="#Sites"]').on('shown.bs.tab', function () {
                $.CustomerCard.ReloadSiteList();
            });
        },
        /** Инициализация динамичных данных клиента */
        InitDynamicData: function() {
            // Обновление списка сканов паспорта
            this.ReloadPassportList();
            // Загрузка списка договоров
            this.ReloadAgreementList();
            // Обновление списка изображений на вкладке вопросов
            this.ReloadQuestionPhotoList();
            // Обновление списка языков
            this.ReloadLanguageList();
            // Обновление списка детей
            this.ReloadChildrenList();

            if (!IsLoveStory) {
                // Обновление списка E-Mail
                this.ReloadEmailList();
            }
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {
            $("#agreementTemplate").template('agreementTemplate');
            $("#albumTemplate").template('albumTemplate');
            $("#emailTemplate").template('emailTemplate');
            $("#passportTemplate").template('passportTemplate');
            $("#questionTemplate").template('questionTemplate');
            $("#questionPhotoTemplate").template('questionPhotoTemplate');
            $("#videoTemplate").template('videoTemplate');
            $("#languageTemplate").template('languageTemplate');
            $("#childrenTemplate").template('childrenTemplate');
            $("#storyTemplate").template('storyTemplate');
            $("#siteTemplate").template('siteTemplate');
            $("#videoSiteTemplate").template('videoSiteTemplate');
            $("#videoSiteLinkTemplate").template('videoSiteLinkTemplate');
        },
        /** Удаление пользователя */
        CustomerRemove: function () {
            confirmRemove(function(){
                hideAlerts();

                function callback(data) {
                    if (data.status) {
                        alert('Запись успешно удалена');
                        window.location = BaseUrl + 'customer';
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var url = BaseUrl + 'customer/'+CustomerID+'/remove';
                $.post(url, {IsFull: true}, callback, 'json');
            }, "Вы действительно хотите БЕЗВОЗВРАТНО удалить запись?");
        },
        /** Метка удаления пользователя */
        CustomerMarkRemove: function () {
            confirmRemove(function(){
                hideAlerts();

                function callback(data) {
                    if (data.status) {
                        showSuccessAlert('Запись успешно удалена');
                    } else {
                        showErrorAlert(data.message)
                    }
                }

                var url = BaseUrl + 'customer/'+CustomerID+'/remove';
                $.post(url, {}, callback, 'json');
            });
        },
        /** Восстановление пользователя */
        CustomerRestore: function () {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно восстановлена');
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/restore';
            $.post(url, {}, callback, 'json');
        },
        RefreshAvatar: function(id, FileName) {
            var src = (id > 0)
                ? BaseUrl + 'thumb/?src=/files/images/' + FileName
                : BaseUrl + 'public/img/avatar-example.png';

            $('#AvatarBig').attr('src', src);
            $('#AvatarCard').attr('src', src);
        },
        /** Загрузка списка договоров */
        ReloadAgreementList: function () {
            this.ReloadData('#agreement', 'agreement', 'agreementTemplate', true);
        },
        /** Обновление списка альбомов */
        ReloadAlbumList: function() {
            this.ReloadData('#album-list', 'album', 'albumTemplate');
        },
        /** Обновление списка языков */
        ReloadLanguageList: function() {
            this.ReloadData('#languageList', 'language', 'languageTemplate', {ID:0, CustomerID:CustomerID, LanguageID:0, Level:0});
        },
        /** Обновление списка детей */
        ReloadChildrenList: function() {
            this.ReloadData('#childrenList', 'children', 'childrenTemplate', {ID:0, CustomerID:CustomerID, SexID:0, FIO:'', DOB:''});
        },
        /** Обновление списка сканов паспорта */
        ReloadPassportList: function() {
            this.ReloadData('#passport', 'passport', 'passportTemplate', {ID:0});
        },
        /** Обновление списка сканов паспорта */
        ReloadQuestionPhotoList: function() {
            this.ReloadData('#questionPhoto', 'question/photo', 'questionPhotoTemplate', {ID:0});
        },
        /** Загрузка списка вопросов */
        ReloadQuestionList: function () {
            this.ReloadData('#QuestionList', 'question', 'questionTemplate');
        },
        /** Загрузка списка видеоподтверждения */
        ReloadVideoVerificationList: function () {
            this.ReloadData('#VideoVerificationList', 'video_0', 'videoTemplate');
        },
        /** Загрузка списка любительского видео */
        ReloadVideoAmateurList: function () {
            this.ReloadData('#VideoAmateurList', 'video_1', 'videoTemplate');
        },
        /** Загрузка списка видеоподтверждения */
        ReloadVideoSiteVerificationList: function (VideoSite) {
            this.ReloadData('#VideoSiteVerificationList_' + VideoSite, 'video/site/'+VideoSite+'/video_0', 'videoSiteLinkTemplate');
        },
        /** Загрузка списка любительского видео */
        ReloadVideoSiteAmateurList: function (VideoSite) {
            this.ReloadData('#VideoSiteAmateurList_' + VideoSite, 'video/site/'+VideoSite+'/video_1', 'videoSiteLinkTemplate');
        },
        /** Загрузка списка видеописем */
        ReloadVideoSiteMailList: function (VideoSite) {
            this.ReloadData('#VideoSiteMailList_' + VideoSite, 'video/site/'+VideoSite+'/video_2', 'videoSiteLinkTemplate');
        },
        /** Обновление списка сайтов */
        ReloadVideoSiteList: function() {
            this.ReloadData('#videoSiteList', 'video/site', 'videoSiteTemplate', null, function() {
                $(this).each(function() {
                    $.CustomerCard.ReloadVideoSiteVerificationList(this.ID);
                    $.CustomerCard.ReloadVideoSiteAmateurList(this.ID);
                    $.CustomerCard.ReloadVideoSiteMailList(this.ID);
                });
            });
        },
        /** Обновление списка E-Mail */
        ReloadEmailList: function() {
            this.ReloadData('#emailList', 'email', 'emailTemplate', {ID:0, CustomerID:CustomerID, Email:'', Note:''});
        },
        /** Обновление списка сайтов */
        ReloadSiteList: function() {
            this.ReloadData('#siteList', 'site', 'siteTemplate');
        },
        /** Загрузка истории встреч */
        ReloadStoryList: function () {
            function callback() {
                $("#storyList").find('input[type="file"]').filestyle({
                    input: false,
                    buttonText: "",
                    buttonName: "story-avatar",
                    iconName: ""
                });

                $("#storyList").find('form').ajaxForm(function(data) {
                    if (data.status) {
                        showSuccessAlert('Запись успешно сохранена');
                        $.CustomerCard.ReloadStoryList();
                    } else {
                        showErrorAlert(data.message)
                    }
                });

                $(function () {
                    $('[data-toggle="story-popover"]').popover()
                })
            }

            this.ReloadData('#storyList', 'story', 'storyTemplate', {ID:0, CustomerID:CustomerID, SiteID:0, Date:"", Name:"", Note:"", Avatar:0}, callback);
        },
        /**
         * Загрузка и рендер данных
         *
         * @param TargetSelector селектор контейнера для загрузки данных
         * @param TargetSegment сегмент для загрузки данных
         * @param TemplateName имя шаблона для рендера
         * @param EmptyRecord пустая запись
         *
         */
        ReloadData: function(TargetSelector, TargetSegment, TemplateName, EmptyRecord, callbackData){
            $(TargetSelector).html('Загрузка данных...');

            function callback(data) {
                if (data.status) {
                    if (data.records){
                        $(TargetSelector).empty();
                        $.tmpl(TemplateName, data.records).appendTo(TargetSelector);
                        if (EmptyRecord){
                            $.tmpl(TemplateName, EmptyRecord).appendTo(TargetSelector);
                        }
                        $('input:checked').change();

                        if (typeof(callbackData) === "function") {
                            callbackData.call(data.records);
                        }
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            var urlData = BaseUrl + 'customer/'+CustomerID+'/'+TargetSegment+'/data';

            $.post(urlData, {}, callback, 'json');
        },
        /** Удаление договора по ID */
        RemoveAgreementRecord: function(AgreementID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Документ успешно удален');
                    $.CustomerCard.ReloadAgreementList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/agreement/'+AgreementID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление фотоальбома по ID */
        RemoveAlbumRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadAlbumList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/album/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление фото из альбома по ID связки фото с альбом */
        RemoveImageAlbumRecord: function(CrossID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadAlbumList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/album/cross/'+CrossID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление языка по ID */
        RemoveLanguageRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Язык успешно удален');
                    $.CustomerCard.ReloadLanguageList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/language/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление ребенка по ID */
        RemoveChildrenRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadChildrenList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/children/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление E=Mail по ID */
        RemoveEmailRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadEmailList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/email/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление записи истории по ID */
        RemoveStoryRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadStoryList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/story/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /**
         * Удаление скана паспорта
         *
         * @param PassportIndex Индекс страницы паспорта(1-4)
         **/
        RemovePassportRecord: function(PassportIndex) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Документ успешно удален');
                    $.CustomerCard.ReloadPassportList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/passport/'+PassportIndex+'/remove';

            $.post(url, {}, callback, 'json');
        },
        /**
         * Удаление изображения
         *
         * @param id id изображения
         **/
        RemoveQuestionPhotoRecord: function(id) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Изображение успешно удалено');
                    $.CustomerCard.ReloadQuestionPhotoList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/question/photo/'+id+'/remove';

            $.post(url, {}, callback, 'json');
        },
        /** Удаление сайта по ID */
        RemoveSiteRecord: function(SiteID, RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadSiteList();
                    $('#WorkSite_'+SiteID).click();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/site/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление видео-сайта по ID */
        RemoveVideoSiteRecord: function(siteID, VideoSiteID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadVideoSiteList();
                    $('#VideoSite_'+siteID).click();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/video/site/'+VideoSiteID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление ссылки из видео-сайта по ID */
        RemoveVideoSiteLinkRecord: function(VideoSite, VideoSiteLinkID, VideoType) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    switch (parseInt(VideoType)) {
                        case 0:
                            $.CustomerCard.ReloadVideoSiteVerificationList(VideoSite);
                            break;
                        case 1:
                            $.CustomerCard.ReloadVideoSiteAmateurList(VideoSite);
                            break;
                        case 2:
                            $.CustomerCard.ReloadVideoSiteMailList(VideoSite);
                            break;
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/video/site/link/'+VideoSiteLinkID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Удаление записи видео по ID */
        RemoveVideoRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadVideoAmateurList();
                    $.CustomerCard.ReloadVideoVerificationList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/video/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        RemoveQuestionRecord: function(RecordID) {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Запись успешно удалена');
                    $.CustomerCard.ReloadQuestionList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            var url = BaseUrl + 'customer/'+CustomerID+'/question/'+RecordID+'/remove';
            $.post(url, {}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Личные данные" */
        SavePersonalData: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Личные данные успешно сохранены')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['SName', 'FName', 'MName', 'DOB', 'DateRegister', 'City', 'Postcode','Country', 'Address', 'Phone_1',
                'Phone_2', 'Email', 'Forming', 'ProfessionOfDiploma', 'CurrentWork', 'Worship', 'MaritalStatus',
                'PassportSeries', 'PassportNumber', 'Height', 'Weight', 'HairColor', 'EyeColor', 'BodyBuild', 'BodyBuildID',
                'SizeFoot', 'Smoking', 'Alcohol', 'Email_site', 'Email_private', 'Skype', 'Instagram', 'Facebook']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');

        },
        /** Сохранение статичных полей с вкладки "Самоописание" */
        SaveSelfDescription: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Самоописание успешно сохранено')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['Temper', 'Interests', 'WishesForManAgeMin', 'WishesForManAgeMax',
                'WishesForManWeight', 'WishesForManHeight', 'WishesForManText', 'WishesForManNationality']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Вопросы" */
        QuestionDescription: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Вопросы успешно сохранены')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['Question']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Дополнительно" */
        SaveAdditionally: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Дополнительная информация успешно сохранена')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['Additionally']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Удаление" */
        SaveRemove: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Причина удаления успешно сохранена')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['ReasonForDeleted']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        },
        /** Сохранение ответа на вопрос с вкладки "Вопросы" */
        SaveQuestionAnswer: function(QuestionID) {
            hideAlerts();

            var data = {
                Answer: $('#Answer_' + QuestionID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Ответ на вопрос успешно сохранен');
                    $.CustomerCard.ReloadQuestionList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/question/' + QuestionID + '/update', {data: data}, callback, 'json');
        },
        /** Сохранение языка */
        SaveLanguageRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                LanguageID: $('#Language_' + RecordID).val(),
                Level: $('#LevelLanguage_' + RecordID).find('input:radio:checked').val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Язык успешно сохранен');
                    $.CustomerCard.ReloadLanguageList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/language/save', data, callback, 'json');
        },
        /** Сохранение E-Mail */
        SaveEmailRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                Email: $('#Email_' + RecordID).val(),
                Note: $('#Note_' + RecordID).val()
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('E-Mail успешно сохранен');
                    $.CustomerCard.ReloadEmailList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/email/save', data, callback, 'json');
        },
        /** Сохранение ребенка */
        SaveChildrenRecord: function(RecordID) {
            hideAlerts();

            var data = {
                RecordID: RecordID,
                SexID: $('#ChildrenSex_' + RecordID).find('input:radio:checked').val(),
                FIO: $('#ChildrenFIO_' + RecordID).val(),
                Reside: IsLoveStory ? $('#ChildrenReside_' + RecordID).val() : "",
                DOB: toServerDate($('#ChildrenDOB_' + RecordID).val())
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Ребенок успешно сохранен');
                    $.CustomerCard.ReloadChildrenList();
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/children/save', data, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Фото" */
        SavePhoto: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Данные успешно сохранены')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['DateLastPhotoSession']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Фото/Видео" */
        SavePhotoAndVideo: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Данные успешно сохранены')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['DateLastPhotoSession']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        },
        /** Добавление ссылки на видео - Assol */
        SaveVideoLink: function(VideoLink, VideoType) {
            hideAlerts();

            var data = {
                Type: VideoType,
                Link: VideoLink
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Видео успешно добавлено');
                    VideoType
                        ? $.CustomerCard.ReloadVideoAmateurList()
                        : $.CustomerCard.ReloadVideoVerificationList();

                    $(VideoType ? '#AmateurLink' : '#VerificationLink').val('');
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/video/add', data, callback, 'json');
        },
        /** Добавление ссылки на видео - LoveStory */
        SaveVideoSiteLink: function(VideoSite, VideoLink, VideoType) {
            hideAlerts();

            var data = {
                Site: VideoSite,
                Type: VideoType,
                Link: VideoLink
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Видео успешно добавлено');
                    switch (parseInt(VideoType)) {
                        case 0:
                            $.CustomerCard.ReloadVideoSiteVerificationList(VideoSite);
                            $('#VideoSiteVerificationLink_' + VideoSite).val('');
                            break;
                        case 1:
                            $.CustomerCard.ReloadVideoSiteAmateurList(VideoSite);
                            $('#VideoSiteAmateurLink_' + VideoSite).val('');
                            break;
                        case 2:
                            $.CustomerCard.ReloadVideoSiteMailList(VideoSite);
                            $('#VideoSiteMailLink_' + VideoSite).val('');
                            break;
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/video/site/link/add', data, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Сайты" */
        SaveSites: function() {
            hideAlerts();

            var sites = [];
            $('#WorkSite').find("input:checked").each(function(){
                sites.push($(this).val());
            });

            var notes = [];
            $('.note-site').each(function(){
                var note = $(this).val();
                var id = $(this).attr('record');

                notes.push({id: id, note: note});
            });

            var data = {
                sites: sites,
                notes: notes
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Список сайтов успешно сохранен');
                    if (data.insert) {
                        $.CustomerCard.ReloadSiteList();
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/site/save', {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Видео" - LoveStory */
        SaveVideoSites: function() {
            hideAlerts();

            var sites = [];
            $('#VideoSite').find("input:checked").each(function(){
                sites.push($(this).val());
            });

            var data = {
                sites: sites
            };

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Список сайтов успешно сохранен');
                    if (data.insert) {
                        $.CustomerCard.ReloadVideoSiteList();
                    }
                } else {
                    showErrorAlert(data.message)
                }
            }

            $.post(BaseUrl + 'customer/'+CustomerID+'/video/site/save', {data: data}, callback, 'json');
        },
        /** Сохранение статичных полей с вкладки "Заказ контактов" */
        SaveReservationContact: function() {
            hideAlerts();

            function callback(data) {
                if (data.status) {
                    showSuccessAlert('Информация о контактах успешно сохранена')
                } else {
                    showErrorAlert(data.message)
                }
            }

            var data = getChangeData(['ReservationContacts']);

            if (!$.isBlank(data))
                $.post($.CustomerCard.GetCustomerUpdateUrl(), {data: data}, callback, 'json');
        }
    };

    // Инициализация объекта
    $.CustomerCard.Init();



    // Hide alerts when changing tabs
    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        hideAlerts();
    });

    $('body').on('hidden.bs.modal', '.remoteModal', function () {
        $(this).removeData('bs.modal');
    });

    function showErrorAlert(message) {
        $('#alertErrorMessage').text(message);
        $('#alertError').slideDown();
    }
    function showSuccessAlert(message) {
        $('#alertSuccessMessage').text(message);
        $('#alertSuccess').slideDown();
    }
    function hideAlerts() {
        $('#alertError').hide();
        $('#alertSuccess').hide();
    }

    if(window.location.hash) {
        var mhash = window.location.hash;
        $('a[href="'+mhash+'"]').tab('show');
    }
});