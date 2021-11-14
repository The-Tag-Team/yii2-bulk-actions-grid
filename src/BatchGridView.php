<?php

namespace thetagteam\batchactionsgrid;

use backend\assets\AppAsset;
use kartik\grid\GridView;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 *
 */
class BatchGridView extends GridView
{

    /**
     * @var string the template for rendering the grid within a bootstrap styled panel.
     * The following special tokens are recognized and will be replaced:
     * - `{prefix}`: _string_, the CSS prefix name as set in [[panelPrefix]]. Defaults to `panel panel-`.
     * - `{type}`: _string_, the panel type that will append the bootstrap contextual CSS.
     * - `{panelHeading}`: _string_, which will render the panel heading block.
     * - `{panelBefore}`: _string_, which will render the panel before block.
     * - `{panelAfter}`: _string_, which will render the panel after block.
     * - `{panelFooter}`: _string_, which will render the panel footer block.
     * - `{items}`: _string_, which will render the grid items.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content.
     */
    public $panelTemplate = <<< HTML
{panelBatchSelectedItemsCount}
{panelBatchActions}
{panelHeading}
{panelBefore}
{items}
{panelAfter}
{panelFooter}
HTML;

    /**
     * @var string the template for rendering the panel heading. The following special tokens are
     * recognized and will be replaced:
     * - `{title}`: _string_, which will render the panel heading title content.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content.
     */
    public $panelBatchActionsTemplate = <<< HTML
<div class="row">
    <div class="col-xs-12 batch-actions-container">
        <span>{batchActions}</span>
    </div>
</div>
HTML;

    /**
     * @var string the template for rendering the panel heading. The following special tokens are
     * recognized and will be replaced:
     * - `{title}`: _string_, which will render the panel heading title content.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content.
     */
    public $panelBatchSelectedItemsCountTemplate = <<< HTML
<div class="row">
    <div class="col-xs-12 batch-counter-container">
        <span>{batchSelectedItemsCount}</span>
    </div>
</div>
HTML;

    /**
     * Initializes and sets the grid panel layout based on the [[template]] and [[panel]] settings.
     * @throws InvalidConfigException
     */
    protected function initPanel()
    {
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $options = ArrayHelper::getValue($this->panel, 'options', []);
        $type = ArrayHelper::getValue($this->panel, 'type', 'default');
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $footer = ArrayHelper::getValue($this->panel, 'footer', '');
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $batchActions = ArrayHelper::getValue($this->panel, 'batchActions', '');
        $batchSelectedItemsCount = ArrayHelper::getValue($this->panel, 'batchSelectedItemsCount', '');
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $titleOptions = ArrayHelper::getValue($this->panel, 'titleOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $summaryOptions = ArrayHelper::getValue($this->panel, 'summaryOptions', []);
        $batchActionsOptions = ArrayHelper::getValue($this->panel, 'batchActionsOptions', []);
        $batchSelectedItemsCountOptions = ArrayHelper::getValue($this->panel, 'batchSelectedItemsCountOptions', []);
        $panelBatchActions = '';
        $panelbatchSelectedItemsCount = '';
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';
        $isBs4 = $this->isBs4();
        if (isset($this->panelPrefix)) {
            static::initCss($options, $this->panelPrefix . $type);
        } else {
            $this->addCssClass($options, self::BS_PANEL);
            Html::addCssClass($options, $isBs4 ? "border-{$type}" : "panel-{$type}");
        }
        static::initCss($summaryOptions, $this->getCssClass(self::BS_PULL_RIGHT));
        $titleTag = ArrayHelper::remove($titleOptions, 'tag', ($isBs4 ? 'h5' : 'h3'));
        static::initCss($titleOptions, $isBs4 ? 'm-0' : $this->getCssClass(self::BS_PANEL_TITLE));
        if ($heading !== false) {
            $color = $isBs4 ? ($type === 'default' ? ' bg-light' : " text-white bg-{$type}") : '';
            static::initCss($headingOptions, $this->getCssClass(self::BS_PANEL_HEADING) . $color);
            $panelHeading = Html::tag('div', $this->panelHeadingTemplate, $headingOptions);
        }
        if ($footer !== false) {
            static::initCss($footerOptions, $this->getCssClass(self::BS_PANEL_FOOTER));
            $content = strtr($this->panelFooterTemplate, ['{footer}' => $footer]);
            $panelFooter = Html::tag('div', $content, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, 'kv-panel-before');
            $content = strtr($this->panelBeforeTemplate, ['{before}' => $before]);
            $panelBefore = Html::tag('div', $content, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, 'kv-panel-after');
            $content = strtr($this->panelAfterTemplate, ['{after}' => $after]);
            $panelAfter = Html::tag('div', $content, $afterOptions);
        }
        if ($batchActions !== false) {
            static::initCss($batchActionsOptions, 'panel-batch-actions hidden');
            $actions = '';
            if (is_iterable($batchActions)) {
                foreach ($batchActions as $batchAction) {
                    $actions .= $batchAction;
                }
            } else {
                $actions = $batchActions;
            }
            $content = strtr($this->panelBatchActionsTemplate, ['{batchActions}' => $actions]);
            $panelBatchActions = Html::tag('div', $content, $batchActionsOptions);
        }
        if ($batchSelectedItemsCount !== false) {
            static::initCss($batchSelectedItemsCountOptions, 'panel-batch-counter hidden');
            $content = strtr($this->panelBatchSelectedItemsCountTemplate, ['{batchSelectedItemsCount}' => $batchSelectedItemsCount]);
            $panelbatchSelectedItemsCount = Html::tag('div', $content, $batchSelectedItemsCountOptions);
        }

        $out = strtr($this->panelTemplate, [
            '{panelHeading}' => $panelHeading,
            '{type}' => $type,
            '{panelFooter}' => $panelFooter,
            '{panelBefore}' => $panelBefore,
            '{panelAfter}' => $panelAfter,
            '{panelBatchActions}' => $panelBatchActions,
            '{panelBatchSelectedItemsCount}' => $panelbatchSelectedItemsCount,
        ]);

        $this->layout = Html::tag('div', strtr($out, [
            '{title}' => Html::tag($titleTag, $heading, $titleOptions),
            '{summary}' => Html::tag('div', '{summary}', $summaryOptions),
        ]), $options);


        echo $this->view->render('@vendor/thetagteam/yii2-batch-actions-grid/src/views/partials/_batch_progress_modal');
    }

    /**
     * Registers client assets for the [[GridView]] widget.
     * @throws \Exception
     */
    protected function registerAssets()
    {
        parent::registerAssets();
        $view = $this->getView();
        BatchGridAsset::register($view);
    }
}
