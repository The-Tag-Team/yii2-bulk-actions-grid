<?php

namespace thetagteam\batchactionsgrid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for the styling of the [[BatchGridView]] widget.
 *
 * @author Marco Piazza <piazza.m17@gmail.com>
 * @since 0.1
 */
class BatchGridAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->depends = array_merge(["yii\\grid\\GridViewAsset"], $this->depends);
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/batch-grid']);
        parent::init();
    }
}