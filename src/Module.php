<?php


/**
 * @package   yii2-batch-actions-grid
 * @author    Marco Piazza <piazza.m17@gmail.com>
 * @author    Nico Ratti <rattinico92@gmail.com>
 * @copyright Copyright &copy; The Tag Team, 2021
 * @version   0.0.1
 */

namespace thetagteam\batchactionsgrid;

use yii\base\InvalidConfigException;
use yii\base\BootstrapInterface;

/**
 * Base module class for TheTagTeam extensions
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 */
class Module extends \kartik\base\Module
{
    /**
     * The module name for TheTagTeam gridview
     */
    const MODULE = "batchactionsgrid";

    /**
     * @inheritdoc
     */
    protected $_msgCat = 'batchactionsgrid';
}