<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title ?></title>

    <script>
        var BaseUrl = '<?= base_url() ?>';
        <? if (IS_LOVE_STORY): ?>
        var IsLoveStory = true;
        <? else: ?>
        var IsLoveStory = false;
        <? endif ?>
    </script>

    <script src="<?= base_url('public/jquery/jquery-1.11.2.min.js') ?>"></script>

    <link rel="stylesheet" href="<?=base_url()?>public/react-bootstrap-table/react-bootstrap-table-all.min.css">

    <?php if(isset($isTable)): ?>
        <!-- Bootstrap Table -->
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="<?=base_url()?>public/bootstrap/table/bootstrap-table.min.css">
        <!-- Latest compiled and minified JavaScript -->
        <script src="<?=base_url()?>public/bootstrap/table/bootstrap-table.min.js"></script>
        <!-- Latest compiled and minified Locales -->
        <script src="<?=base_url()?>public/bootstrap/table/locale/bootstrap-table-ru-RU.min.js"></script>
    <?php endif ?>

    <!-- Bootstrap -->
    <link href="<?=base_url()?>public/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Calendar style -->
    <? if(isset($isCalendar)): ?>
        <link href='<?=base_url()?>public/fullcalendar/fullcalendar.css' rel='stylesheet' />
        <link href='<?=base_url()?>public/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
    <? endif ?>

    <!-- Assol style -->
    <link href="<?=base_url()?>public/build/assol.min.css" rel="stylesheet">

    <?php if(isset($css_array)): ?>
        <?php foreach($css_array as $css): ?>
            <link href="<?=base_url($css)?>" rel="stylesheet">
        <?php endforeach ?>
    <?php endif ?>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="<?=base_url()?>public/js/html5shiv.min.js"></script>
	<script src="<?=base_url()?>public/js/respond.min.js"></script>
	<![endif]-->
    <!--[if lte IE 8]>
        <script>
            var oldiesURL = BaseUrl + 'public/oldies';
        </script>
        <script src="<?=base_url()?>public/oldies/oldies.js" charset="utf-8"></script>
    <![endif]-->
    <style>
        ul.wide-list {
            margin-bottom: 0;
        }
        ul.wide-list .nums {
            margin-left: 4px;
        }
        ul.wide-list > a {
            display: inline-block;
        }
        #switchMenu {
            background-color: #fff;
        }
        .acc-wide {
            padding-left: 0 !important;
        }
        #wideMenu {
            display: none;
        }
    </style>
</head>
<body <?= IS_LOVE_STORY ? 'class="lovestory"' : '' ?>>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="pseudo-table-wrap">
                <div class="pseudo-table">
                    <div class="pseudo-td content-width">
                        <a href="<?=base_url()?>">
                            <img src="<?=base_url()?>public/img/<?= IS_LOVE_STORY ? 'logo-header-lovestory' : 'logo-header' ?>.png" alt="Логотип">
                        </a>
                    </div>
                    <div class="pseudo-td">
                        <ul class="user-block">
                            <li>
                                <?php
                                $avatar = empty($user['Avatar'])
                                    ? base_url('public/img/photo-profit.jpg')
                                    : base_url("thumb/?src=/files/images/".$user['Avatar']."&w=39")
                                ?>
                                <div class="user-img-wrap">
                                    <div class="user-img">
                                        <img src="<?= $avatar ?>" alt="Фото пользователя">
                                    </div>
                                    <div class="status online"></div>
                                </div>
                            </li>
                            <li class="nick">
                                <span><?= $user['FName'] ?> <?= $user['SName'] ?></span>
                                <span class="type-user"> (<?= $user['role_description'][$user['role']]?>)</span>
                            </li>
                        </ul>
                    </div>
                    <? if (IS_LOVE_STORY): ?>
                    <div class="pseudo-td content-width">
                        <button class="btn btn-default btn-profile" id="switchMenu">
                            <span class="glyphicon glyphicon-menu-hamburger"></span> Меню
                        </button>
                    </div>
                    <div class="pseudo-td content-width">
                        <a class="" href="<?=base_url('employee/'.$user['ID'].'/profile')?>" role="button">
                            <button class="btn btn-default btn-profile">
                                Профиль
                            </button>
                        </a>
                    </div>
                    <? endif ?>
                    <div class="pseudo-td content-width">
                        <a class="" href="<?=base_url('logout')?>" role="button">
                            <button class="btn btn-default btn-logout">
                                <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span> Выйти
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <script>
        $('#switchMenu').on('click', function () {
//            $('#wideMenu').toggleClass('hide', 'show');
            $('#wideMenu').slideToggle('slow');
        });
    </script>
	<div class="container-fluid" style="max-width: 1600px;">
        <div class="row-fluid">
            <!-- Modal -->
            <div class="modal fade remoteModal" id="remoteDialog" tabindex="-1" role="dialog" aria-labelledby="remoteDialogLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content"></div>
                </div>
            </div>

            <div id="react-chat"></div>

            <script src="<?= base_url('public/build/bundle.js') ?>"></script>

            <div class="assol-main-content">
<!--                <div class="assol-col-1">-->
                    <div id="wideMenu" class="assol-grey-panel assol-menu">
                        <div class="panel-body" style="padding-bottom: 12px;">
                            <ul class="list-group wide-list">
                                <?php
                                    $uri = $this->uri->uri_string();
                                ?>
                                <? foreach($menu as $item): ?>
                                    <?php
                                    $isNews = empty($uri) && $item['controller']=='news';
                                    $isStartUri = uriStartsWith($uri, $item['controller']);
                                    $active = ($isStartUri || $isNews) ? 'active':'';
                                    ?>

                                    <? if ($item['controller']=='messages'): ?>
                                        <a href="#" id="reactMessageDialog" class="list-group-item"><?=$item['description']?>
                                            <mark id="mark_<?=$item['controller']?>" class="nums">0</mark>
                                        </a>
                                    <? else: ?>
                                        <a href="<?=base_url($item['controller'])?>" class="list-group-item <?=$active?>"><?=$item['description']?>
                                            <? if ($item['controller']=='tasks'): ?>
                                                <mark id="mark_<?=$item['controller']?>" title="Задачи на подтверждение" class="nums">0</mark>
                                                <mark id="mark_task_<?=$item['controller']?>" title="Новые задачи" class="nums task">0</mark>
                                                <mark id="mark_undone_<?=$item['controller']?>" title="Невыполненные задачи" class="nums undone">0</mark>
                                                <mark id="mark_comment_<?=$item['controller']?>" title="Новые комментарии к задачам" class="nums comment">0</mark>
                                            <? else: ?>
                                                <mark id="mark_<?=$item['controller']?>" class="nums">0</mark><mark id="mark_<?=$item['controller']?>" class="nums">0</mark>
                                            <? endif ?>
                                        </a>
                                    <? endif ?>
                                <? endforeach ?>
                            </ul>
                        </div>

<!--                        <div class="sidebar-search">-->
<!--                            <div class="search-block">-->
<!--                                <input class="search-field" type="search" placeholder="поиск">-->
<!--                                <button type="button" class="search-btn">-->
<!--                                    <span class="glyphicon glyphicon-search"></span>-->
<!--                                </button>-->
<!--                            </div>-->
<!--                        </div>-->
                        <!-- /input-group -->
                    </div>
<!--                </div>-->
            </div><!-- /.assol-main-content -->
            <div class="assol-main-content">
                <div class="assol-col-content acc-wide">
                    <?php if (isset($errorMessage)): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Ошибка!</strong> <?= $errorMessage ?>
                        </div>
                    <?php endif; ?>