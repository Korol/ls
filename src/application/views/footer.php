                </div>
            </div>
        </div>
    </div>

	<footer class="footer">
		<div class="container">
            <div class="row-fluid">
                <div class="assol-col-1">
                    <p class="text-muted"><?= FOOTER_TEXT ?></p>
                </div>
                <div class="assol-col-2">
                    <p class="text-muted font-size-12">
                        <a href="#" class="footer-link">правила конфиденциальности</a>
                        <a href="#" class="footer-link docs">документация</a>
                    </p>
                </div>
                <div class="assol-col-3">
                    <p class="text-muted pull-right font-size-12">разработчики системы: <a href="http://webcase.com.ua/" class="a-webcase" target="_blank">webcase</a></p>
                </div>
            </div>
		</div>
	</footer>

    <script src="<?= base_url('public/build/assol.min.js') ?>"></script>

    <?php if(isset($isWysiwyg)): ?>
        <script src="<?= base_url('public/tinymce/tinymce.min.js') ?>"></script>
        <script>
            initTinymce('<?=base_url()?>');
        </script>
    <?php endif ?>

    <?php if(isset($js_array)): ?>
        <?php foreach($js_array as $js): ?>
            <script src="<?=base_url($js)?>"></script>
        <?php endforeach ?>
    <?php endif ?>

</body>
</html>