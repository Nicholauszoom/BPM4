<?php

use app\models\Analysis;
use app\models\CommentRequest;
use app\models\Project;
use app\models\Request;
use app\models\User;
use yii\bootstrap5\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Task $model */
/** @var yii\widgets\ActiveForm $form */

$userId = Yii::$app->user->id;


$assignedProject=Project::findOne($projectid_request);


// Modal pop-up
Modal::begin([
    'id' => 'createModal',
    'title' => 'Create',
]);

// Header
echo '<div class="modal-header">';
echo '</div>';

// Form
$form = ActiveForm::begin([
   
]);

// echo $form->field($model, 'payment')->textInput();
echo $form->field($model, 'payment')->dropDownList(
  [
      1 => 'Cheque',
      2 => 'Cash',
      
  ],
  ['prompt' => 'Payment Mode']
);


// echo $form->field($model, 'item')->dropDownList(
// \yii\helpers\ArrayHelper::map($analysis, 'id', 'item'),
// ['prompt' => 'Select Item for this project']
// ) ;


echo $form->field($model, 'item')->textInput(['value' => $analysis->item,'readonly' => true,'id' => 'ref-input']);

echo $form->field($model, 'department')->dropDownList(
  \yii\helpers\ArrayHelper::map($department, 'id', 'name'),
  ['prompt' => 'Select department']
  ) ;

// echo $form->field($model, 'ref')->textInput(['type' => 'number', 'value'=>$analysis->quantity,'max' => $analysis->quantity,'id' => 'ref-input']);
// $request_qty=Request::findOne(condition)

$rem_qty=$analysis->quantity - $existingQuantity;
echo $form->field($model, 'ref')->dropDownList(
  range(0, $rem_qty),
  ['prompt' => 'Select', 'id' => 'ref-input']
);


echo $form->field($model, 'analysis_id')->hiddenInput(['value' => $analysisId])->label(false);


// echo $form->field($model, 'ref')->textInput(['type'=>'number','value'=>$analysis->]) ;

echo $form->field($model, 'amount')->textInput(['type'=>'number']) ;


// Add remaining form fields...

echo '<div class="modal-footer">';
echo Html::submitButton('Save', ['class' => 'btn btn-success']);
echo '</div>';

ActiveForm::end();

Modal::end();



?>

<div class="task-form">


<table class="table">
  <thead>
    <tr style="background-color: #f2f2f2;">
  
      <th scope="col">item</th>
      <th scope="col">ref no</th>
      <th scope="col">Amount</th>
      <th scope="col">Created</th>
      <th scope="col">Updated</th>
      <th scope="col">Created By</th>
      <th scope="col">Approved/Not</th>
      <th scope="col"></th>
    </tr>
    </thead>
<tbody>
<?php foreach ($request as $request): ?>
    <?php if (Yii::$app->user->can('admin') && $request->status != 1) continue; ?>
    <tr>
        <?php 
        $analysis = Analysis::findOne($request->analysis_id);
        $item = $analysis->item;
        $wrappedItem = wordwrap($item, 70, "<br>", true);
        ?>
        <td><?= $wrappedItem ?></td>
        <td><?= $request->ref ?></td>
        <td><?= $request->amount ?></td>
        <td><?= Yii::$app->formatter->asDatetime($request->created_at) ?></td>
        <td><?= Yii::$app->formatter->asDatetime($request->updated_at) ?></td>
        <?php 
        $user = User::findOne($request->created_by);
        ?>
        <td><?= $user->username ?></td>
        <td><?= getStatusLabel($request->status) ?></td>
        <td>
            <?php if (Yii::$app->user->can('author')) : ?>
                <?= Html::a('<span class="glyphicon glyphicon-edit"></span>', ['update', 'id' => $request->id], [
                    'title' => 'Update',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]) ?>
            <?php endif; ?>

            <?php if (Yii::$app->user->can('admin')) : ?>
                <?= Html::a('<span class="fa fa-commenting" style="font-size: 20px;"></span>', ['update', 'id' => $request->id], [
                    'title' => 'for Approve',
                    'data-method' => 'post',
                    'data-pjax' => '0',
                ]) ?>
            <?php endif; ?>

            <?= Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $request->id], [
                'title' => 'Delete',
                'data-confirm' => 'Are you sure you want to delete this updates',
                'data-method' => 'post',
                'data-pjax' => '0',
            ]) ?>
        </td>
    </tr>
<?php endforeach; ?>
<tr>
    <td>
    <?php if (Yii::$app->user->can('author') ) : ?>
            <?= Html::a('+ create request', '#', ['data-toggle' => 'modal', 'data-target' => '#createModal']) ?>
        <?php endif; ?>
    </td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>

<tr>
    <td></td>
    <td style="background-color: #f2f2f2;">Total Item Budget:TSH <?= $analysis->cost ?></td>
    <?php
    $rem_budget = $analysis->cost - $existingAmount;
    ?>
    <td style="background-color: #f2f2f2;">Remain Item Budget:TSH <?= $rem_budget ?></td>
    <td style="background-color: #f2f2f2;"></td>
    <td style="background-color: #f2f2f2;"></td>
    <td style="background-color: #f2f2f2;"></td>
    <td style="background-color: #f2f2f2;"></td>
    <td style="background-color: #f2f2f2;"></td>
</tr>
</tbody>
</table>
<!--
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn bt<th scope="col">ref no</th>n-success']) ?>
    </div>
                -->

    <?php
function getStatusLabel($status)
{
    $statusLabels = [
      1 => '<span class="badge badge-success">Accepted</span>',
      2 => '<span class="badge badge-danger">Reject</span>',
      3 => '<span class="badge badge-warning">PM Accept</span>',
      0 => '<span class="badge badge-secondary">Pending..</span>',
    ];

    return isset($statusLabels[$status]) ? $statusLabels[$status] : '';
}

function getStatusClass($status)
{
    $statusClasses = [
       
        1 => 'status-active',
        2 => 'status-inactive',
        3 => 'status-onhold',
    ];

    return isset($statusClasses[$status]) ? $statusClasses[$status] : '';
}
?>
</div>








