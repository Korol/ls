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

	<form class="form-signin" method="post" style="float: none">
		<img src="<?=base_url()?>public/img/<?= IS_LOVE_STORY ? "logo-lovestory" : "logo" ?>.png" alt="Логотип">

        <input type="hidden" name="site" value="<?= IS_LOVE_STORY ? 1 : 0 ?>">

		<div class="form-group">
			<input type="text" class="form-control" id="inUsername" name="username" placeholder="Логин" required="true">
		
			<input type="password" class="form-control" id="inPassword" name="password" placeholder="Пароль" required="true">
		</div>

		<button class="btn btn-lg btn-primary btn-block" type="submit" style="">ВОЙТИ В СИСТЕМУ</button>
	</form>

</div> <!-- /container -->

<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="<?=base_url()?>public/bootstrap/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>