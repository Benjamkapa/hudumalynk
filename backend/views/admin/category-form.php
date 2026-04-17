<?php
/** @var yii\web\View $this */
/** @var common\models\Category $category */
/** @var common\models\Category[] $parents */
use yii\helpers\Html;
use yii\helpers\Url;
$isNew = $category->isNewRecord;
$this->title = $isNew ? 'New Category' : 'Edit: ' . $category->name;
?>
<style>
.form-section{background:var(--bg2);border:1px solid var(--border);border-radius:var(--r-lg);padding:20px;margin-bottom:12px;}
.form-section-title{font-size:12.5px;font-weight:700;color:var(--text1);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border);}
.form-grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.fg{display:flex;flex-direction:column;gap:4px;}
.fg label{font-size:10.5px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.08em;}
.fg input,.fg select,.fg textarea{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:var(--r-md);padding:8px 11px;font-size:13px;color:var(--text1);font-family:var(--font);outline:none;transition:border-color .13s;}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--acc);}
.fg textarea{min-height:80px;resize:vertical;}
.field-hint{font-size:10.5px;color:var(--text3);margin-top:3px;}
@media(max-width:600px){.form-grid2{grid-template-columns:1fr;}}
</style>

<div class="hl-pg-head">
    <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?= Url::to(['/admin/categories']) ?>" class="hl-btn-g" style="padding:6px 10px;font-size:11px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="15 18 9 12 15 6"/></svg> Categories
        </a>
        <h1 class="hl-pg-title"><?= $isNew ? 'New Category' : 'Edit Category' ?></h1>
    </div>
</div>

<?php $form = \yii\widgets\ActiveForm::begin([
    'id'      => 'category-form',
    'options' => ['novalidate' => true],
]); ?>

<div class="form-section">
    <div class="form-section-title">Category Details</div>
    <div class="fg" style="margin-bottom:12px;">
        <label>Name <span style="color:var(--rose);">*</span></label>
        <?= $form->field($category, 'name', ['template' => '{input}{error}'])->textInput(['placeholder' => 'e.g. Home Cleaning, Electronics…']) ?>
    </div>
    <div class="form-grid2">
        <div class="fg">
            <label>Parent Category</label>
            <?= $form->field($category, 'parent_id', ['template' => '{input}{error}'])->dropDownList(
                array_merge(['— None (top level) —'], array_column($parents, 'name', 'id')),
                ['prompt' => '— None (top level) —']
            ) ?>
        </div>
        <div class="fg">
            <label>Type</label>
            <?= $form->field($category, 'type', ['template' => '{input}{error}'])->dropDownList([
                'both'    => 'Both (service & product)',
                'service' => 'Service only',
                'product' => 'Product only',
            ]) ?>
        </div>
        <div class="fg">
            <label>Status</label>
            <?= $form->field($category, 'status', ['template' => '{input}{error}'])->dropDownList([
                'active'   => 'Active — visible to vendors',
                'inactive' => 'Inactive — hidden',
            ]) ?>
        </div>
        <div class="fg">
            <label>Sort Order</label>
            <?= $form->field($category, 'sort_order', ['template' => '{input}{error}'])->input('number', ['min' => 0, 'placeholder' => '0']) ?>
            <span class="field-hint">Lower numbers appear first</span>
        </div>
        <div class="fg">
            <label>Icon class</label>
            <?= $form->field($category, 'icon', ['template' => '{input}{error}'])->textInput(['placeholder' => 'e.g. broom, laptop…']) ?>
            <span class="field-hint">Used for display on the frontend</span>
        </div>
    </div>
    <div class="fg" style="margin-top:12px;">
        <label>Description</label>
        <?= $form->field($category, 'description', ['template' => '{input}{error}'])->textarea(['rows' => 3, 'placeholder' => 'Brief description of this category…']) ?>
    </div>
</div>

<div style="display:flex;justify-content:flex-end;gap:8px;">
    <a href="<?= Url::to(['/admin/categories']) ?>" class="hl-btn-g">Cancel</a>
    <button type="submit" class="hl-btn-p">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
        <?= $isNew ? 'Create Category' : 'Save Changes' ?>
    </button>
</div>

<?php \yii\widgets\ActiveForm::end(); ?>
