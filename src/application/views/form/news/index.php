<script>
    var Sites = {};
    <?php foreach($sites as $site): ?>
        Sites["<?= $site['ID'] ?>"] = "<?= empty($site['Name']) ? $site['Domen'] : $site['Name'] ?>";
    <?php endforeach ?>
</script>

<div class="panel assol-grey-panel">
    <div class="panel-body">
        <div class="btn-group assol-select-dropdown" id="newsCategory" style="width: 200px">
            <div class="label-placement-wrap">
                <button class="btn" data-label-placement>Все новости</button>
            </div>
            <button data-toggle="dropdown" class="btn dropdown-toggle">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <?php foreach($sites as $item): ?>
                    <li>
                        <input type="radio" id="NewsCategory_<?=$item['ID']?>" name="NewsCategory" value="<?=$item['ID']?>">
                        <label for="NewsCategory_<?=$item['ID']?>"><?= empty($item['Name']) ? $item['Domen'] : $item['Name'] ?></label>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>

        <?php if ($role['isDirector'] || $role['isSecretary']): ?>
            <a href="<?=base_url('news/add')?>" data-toggle="modal" data-target="#remoteDialog"
               class="" role="button" title="Добавить новость">
               <button class="btn assol-btn add right">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                Добавить новость
               </button>
            </a>
        <?php endif ?>
    </div>
</div>

<script id="newsTemplate" type="text/x-jquery-tmpl">
  <div class="assol-grey-panel dateNews">
    {{if isCurrentDay}}<strong>Новости за сегодня</strong> -{{/if}} ${date}
  </div>
  {{tmpl(news, {isCurrentDay: isCurrentDay}) "#newsDayTemplate"}}
</script>

<style>
    .user span {
        font-weight: normal;
        color: #2fc6f7;
        text-decoration: underline;
    }
</style>

<script id="newsDayTemplate" type="text/x-jquery-tmpl">
  <div class="singleNews">
        <div class="bodyNews" style="position: relative;">
            <div class="title-wrap">
                <h5>${Title}</h5>
            </div>

            <div id="news_${ID}" class="collapse {{if $item.isCurrentDay}}in{{else}}collapsed{{/if}}">
                <div class="bodyNewsContent" style="min">
                    <div class="bodyNewsContent-in">
                        <div class="bodyNewsThumb">
                            {{if ImageID}}
                                <a href="<?= base_url("thumb") ?>/?src=/files/images/${FileName}" data-lightbox="ThumbsNews_${ID}">
                                    <img id="ThumbsNews_${ID}" src="<?= base_url("thumb") ?>/?src=/files/images/${FileName}&w=215">
                                </a>
                            {{/if}}
                        </div>
                        {{html Text}}
                        <div class="show-full-news">Показать всю новость</div>
                    </div>
                </div>
                <div class="nav-wrap">
                    <div class="nav">
                        <?php if ($role['isDirector'] || $role['isSecretary']): ?>
                            <div>
                                <a href="<?=base_url('news')?>/${ID}/edit" data-toggle="modal" data-target="#remoteDialog" class="" role="button" title="Редактировать новость">
                                    <button class="btn">
                                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> редактировать
                                    </button>
                                </a>
                            </div>
                            <div>
                                <button record="${ID}" class="btn action-remove-news" role="button" title="Удалить новость">
                                    <span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span>
                                    удалить
                                </button>
                            </div>
                        <?php endif ?>
                        <div>
                            <span class="site"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> ${Sites[SiteID]}</span>
                        </div>
                        <div>
                            <span class="user">Опубликовано: <span>${FName} ${SName}</span></span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="btn-wrap">
                <button type="button" class="collapseNews {{if $item.isCurrentDay}}{{else}}collapsed{{/if}}" data-toggle="collapse" data-target="#news_${ID}" aria-expanded="{{if $item.isCurrentDay}}true{{else}}false{{/if}}" ><span class="glyphicon glyphicon-chevron-up"></span>
                  Свернуть
                </button>
            </div>
        </div>
    </div>
</script>

<div id="news"></div>

<div class="assol-pagination assol-grey-panel">
    <div class="assol-pagination-in clear">

        <div class="assol-pagination-left">
            <input type="number" class="assol-input-style now-page-input filter-input" id="CurrentPage" value="1">
            <span class="assol-pagination-all">из <span id="CountPage">1</span></span>
        </div>
        <div class="assol-pagination-right">
            <div class="assol-pagination-arrs">
                <button class="prev">
                    <span class="glyphicon glyphicon-chevron-left"></span>
                </button>
                <button class="next">
                    <span class="glyphicon glyphicon-chevron-right"></span>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    $(function () {
        // Запуск обновления информации о прочтение
        setTimeout(function () {
            $.post(BaseUrl + 'news/read');
        }, 10000);

        $('body').on('hidden.bs.modal', '.remoteModal', function () {
            // Очистка редактора для корректного открытия
            tinymce.EditorManager.editors = [];

            $(this).removeData('bs.modal');
        });

        $('.glyphicon').click(function () {
            $(this).toggleClass('click');
        });
    });
</script>