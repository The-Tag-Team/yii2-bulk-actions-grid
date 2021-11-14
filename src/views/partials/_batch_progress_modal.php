<?php

use thetagteam\batchactionsgrid\Module;
$module = \Yii::$app->getModule('batchactionsgrid');
if($module->isBs4()) {
    $modal = 'yii\bootstrap4\Modal';
    $progress = 'yii\bootstrap\Progress';
} else {
    $modal = 'yii\bootstrap\Modal';
    $progress = 'yii\bootstrap\Progress';
}

$modal::begin(['header' => '<h5 id="progress">0% '.\Yii::t('batchactionsgrid', 'Completed').'</h5>', 'id' => 'progress-modal', 'closeButton' => false]);

?>

<?= $progress::widget([
    'percent' => 0,
    'options' => ['class' => 'progress-success active progress-striped'],
]); ?>

    <div class="hidden alert alert-danger" id="batch-action-errors"></div>

<?php $modal::end(); ?>