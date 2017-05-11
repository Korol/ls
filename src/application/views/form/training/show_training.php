<ol class="breadcrumb assol-grey-panel">
    <li><a href="<?= base_url('training') ?>">Обучение</a></li>
    <? if (!empty($bread)): ?>
        <? foreach ($bread as $item): ?>
        <li><a href="<?= base_url('training/'.$item['ID']) ?>"><?= $item['Name'] ?></a></li>
        <? endforeach ?>
    <? endif ?>
    <li class="active"><?= $record['Name'] ?></li>
</ol>

<p><b><?= $record['Name'] ?></b></p>
<br>

<div style="max-width: 937px">
    <?= $record['Content'] ?>
</div>