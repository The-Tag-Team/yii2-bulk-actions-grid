<?php

namespace thetagteam\batchactionsgrid;

use Exception;
use kartik\dynagrid\DynaGrid;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 *
 */
class BatchDynaGrid extends DynaGrid
{
    /**
     * @inheritdoc
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function run()
    {
        $this->initWidget();
        echo Html::tag('div', BatchGridView::widget($this->gridOptions), $this->options);
    }
}
