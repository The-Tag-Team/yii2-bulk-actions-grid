<?php

namespace thetagteam\batchactionsgrid\models;

/**
 * ExtraForm is a model class that is used for extra data in batchGridVidw.
 * It extends base DynamicModel to allow developers to define "dynamic attributes" using its constructor or
 * [[defineAttribute()]].
 *
 * @author Nico Ratti <rattinico92@gmail.com>
 */
class ExtraForm extends \yii\base\DynamicModel
{
    public function formName()
    {
        return 'extra';
    }
}