<?php
/**
 * @var \yii\web\View $this
 * @var string $id
 * @var string[] $values
 */

use yii\bootstrap\Html;

?>
<datalist id="<?= Html::encode($id) ?>">
    <?php foreach ($values as $value): ?>
        <option><?= Html::encode($value) ?></option>
    <?php endforeach; ?>
</datalist>
