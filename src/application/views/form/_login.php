<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= TITLE_TEXT ?></title>

    <script src="<?=base_url()?>public/jquery/jquery-1.11.2.min.js"></script>

	<!-- Bootstrap core CSS -->
	<link href="<?=base_url()?>public/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom styles for this template -->
	<link href="<?=base_url()?>public/css/signin.css" rel="stylesheet">

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="<?=base_url()?>public/js/html5shiv.min.js"></script>
	<script src="<?=base_url()?>public/js/respond.min.js"></script>
	<![endif]-->
</head>

<body class="sign-in">

<div class="container">
	<?php if (isset($errorMessage)): ?>
		<div class="alert alert-danger" role="alert">
			<strong>Ошибка!</strong> <?= $errorMessage ?>
		</div>
	<?php endif; ?>

	<form class="form-signin" method="post">
		<img src="<?=base_url()?>public/img/logo.png" alt="Логотип">

		<div class="form-group">
			<input type="text" class="form-control" id="inUsername" name="username" placeholder="Логин" required="true">
		
			<input type="password" class="form-control" id="inPassword" name="password" placeholder="Пароль" required="true">
		</div>

        <div style="text-align: left">
            <div class="radio">
                <label>
                    <input type="radio" name="site" id="site1" value="0" checked>
                    Assol
                </label>
            </div>
            <div class="radio">
                <label>
                    <input type="radio" name="site" id="site2" value="1">
                    Love store
                </label>
            </div>
        </div>

        <script>
            $('#site1').click(function () {
                $('.form-signin').find('img').attr('src', '<?=base_url()?>public/img/logo.png');
            });

            $('#site2').click(function () {
                $('.form-signin').find('img').attr('src', '<?=base_url()?>public/img/logo-lovestory.png');
            });
        </script>

		<button class="btn btn-lg btn-primary btn-block" type="submit" style="">ВОЙТИ В СИСТЕМУ</button>
	</form>

    <div class="alert alert-info" role="alert" style="width: 500px">
        <strong>Набор тестовых пользователей!</strong>
        <hr />
        <style>
            .users td {
                padding: 5px;
                border: 1px solid;
            }
        </style>

        <table class="users">
            <tr>
                <td width="100"><strong>Логин</strong></td>
                <td width="150"><strong>Пароль</strong></td>
                <td width="150"><strong>Роль</strong></td>
            </tr>
            <?php foreach($employees as $employee): ?>
                <tr>
                    <td><?=$employee['ID']?></td>
                    <td><?=$employee['Password']?></td>
                    <td><?=$role_d[$employee['UserRole']]?></td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>

</div> <!-- /container -->

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?=base_url()?>public/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>