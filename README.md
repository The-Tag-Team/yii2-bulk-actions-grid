# yii2-batch-actions-grid
An extension of Kartik yii2-grid to add batch actions when selecting rows via the CheckboxColumn

## Usage

### View:

```
<?= BatchGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => '#myPageSize',
    'columns' => $gridColumns,
    'pjax' => true,
    'panel' => [
        'heading' => true,
        'footer' => true,
        'batchActions' => [
            Html::a('Add Code', ['#'], [
                'class' => 'btn btn-sm btn-info',
                'title' => 'Add a code to the selected users',
                'data-akt' => 'assign-code',
                'rel' => 'tooltip',
                'data-placement' => 'top',
                'data-html' => 'true',
                'data-toggle' => 'modal',
                'data-target' => '#add-code',
                'data-reload' => '1',
                'data-url' => Url::to(['/item-batch/batch']),
            ]),
            Html::a('Delete', ['#'], [
                'class' => 'btn btn-sm btn-info batch_process',
                'title' => "<b>Delete the selected users.</b><br />Warning: this action cannot be undone.",
                'data-akt' => 'delete',
                'rel' => 'tooltip',
                'data-placement' => 'top',
                'data-html' => 'true',
                'data-confirmMsg' => 'Are you sure you want to delete the selected users?',
                'data-reload' => '1',
                'data-url' => Url::to(['/item-batch/batch']),
            ]),
        ]
    ],
    'options' => [
        'id' => 'batch-grid',
        'class' => 'grid-view'
    ],
    'panelTemplate' => <<<HTML
{panelHeading}
<div class="card-top-absolute"><h4 class="card-title">{summary}</h4></div>
{panelBatchSelectedItemsCount}
{panelBatchActions}
<div class="clearfix"></div>
{items}
{panelAfter}
{panelFooter}
HTML
,
]); ?>

```

### Single action controller (using data-akt):

``` 

/**
 * Class ItemBatchController
 */
class ItemBatchController
{

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionBatch()
    {
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $ids = json_decode(Yii::$app->request->post('ids', ''));
            $akt = Yii::$app->request->post('akt', '');
            $extras = Yii::$app->request->post('extras');

            $model = DynamicModel::validateData(compact('ids', 'akt'), [
                [['akt'], 'string', 'max' => 128],
                ['ids', 'each', 'rule' => ['integer']],
            ]);

            $response = new ObjectResponse;
            if ($model->hasErrors()) {
                $response->setError('Error during the process');
                $response->add('error', $model->getErrors());
            } else {
                $success = $this->getServiceModel()->process($akt, $ids, $extras);
                $response->setSuccess('Success', $model->toArray());
                $response->add('issued', count($ids));
                $response->add('issues', count($ids) - $success);
            }
            return $response->get();
        }
        throw new BadRequestHttpException;
    }
```