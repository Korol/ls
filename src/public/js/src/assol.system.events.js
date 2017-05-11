$(document).ready(function(){
    /** Звуковой сигнал каждые 5 минут */
    var soundInterval = 5 * 60 * 1000;
    // var soundInterval = 3 * 1000;
    /** Текущее количество оповещений */
    var countCurrentEvent = Number.MAX_SAFE_INTEGER;
    /** Текущее количество оповещений для повтора */
    var countCurrentReplayEvent = Number.MAX_SAFE_INTEGER;

    // Таймер для подгрузки информации о количество новых событий
    var timerLoadCountEvents = {
        timer: null,
        start : function () {
            this.timer = setInterval($.SystemEvents.ReloadEvents, 5000);
        },
        stop: function () {
            clearInterval(this.timer);
        }
    };

    var timerSoundEvents = {
        timer: null,
        start : function () {
            this.timer = setInterval(function () {
                if (countCurrentReplayEvent > 0)
                    $.ionSound.play("ass");
            }, soundInterval);
        },
        stop: function () {
            clearInterval(this.timer);
        }
    };

    // Объект для публичного использования
    $.SystemEvents = {
        /** Инициализация объекта */
        Init: function() {
            this.InitActions();
            this.InitDynamicData();
            this.InitTemplate();
        },
        /** Инициализация событий */
        InitActions: function() {
            ion.sound({
                sounds: [{name: "ass"}],
                volume: 0.5,
                path: BaseUrl + "public/ion.sound/sounds/"
            });
        },
        /** Инициализация динамичных данных */
        InitDynamicData: function() {
            this.ReloadEvents();

            // Запуска таймера оповещения о новых событиях
            timerLoadCountEvents.start();
            // Запуска таймера звукового оповещения о новых событиях
            timerSoundEvents.start();
        },
        /** Предварительная компиляция шаблонов */
        InitTemplate: function() {

        },
        /**
         * Загрузка и отрисовка списка оповещений для пользователя системы
         */
        ReloadEvents: function() {
            $.get(BaseUrl + 'events', function(data) {
                // Счетчик оповещений
                var countEvent = 0;
                var countReplayEvent = 0;
                $.each(data, function(key, value) {
                    var replay = parseInt(value.replay) || 0;
                    var once = parseInt(value.once) || 0;
                    var count = replay + once;

                    var mark = $('#mark_'+key);
                    mark.html(count);
                    mark.css('display', (count > 0) ? 'block' : 'none');

                    if (key == 'tasks') {
                        var tasks = parseInt(value.tasks) || 0;
                        var undone = parseInt(value.undone) || 0;
                        var comment = parseInt(value.comment) || 0;

                        var mark_tasks = $('#mark_task_'+key);
                        mark_tasks.html(tasks);
                        mark_tasks.css('display', (tasks > 0) ? 'block' : 'none');

                        var mark_undone = $('#mark_undone_'+key);
                        mark_undone.html(undone);
                        mark_undone.css('display', (undone > 0) ? 'block' : 'none');

                        var mark_comment = $('#mark_comment_'+key);
                        mark_comment.html(comment);
                        mark_comment.css('display', (comment > 0) ? 'block' : 'none');

                        countReplayEvent += tasks;
                        countEvent += (tasks + comment + undone);
                    }

                    countReplayEvent += replay;
                    countEvent += count;
                });

                // Звуковой сигнал если есть новые оповещения
                if (countEvent > countCurrentEvent)
                    $.ionSound.play("ass");

                countCurrentEvent = countEvent;
                countCurrentReplayEvent = countReplayEvent;
            });
        }
    };

    // Инициализация объекта
    $.SystemEvents.Init();
});