<?php
/** @var common\models\Listing $listing */
/** @var array $categories */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<div class="hl-pg-head">
    <div>
        <h1 class="hl-pg-title"><?= $listing->isNewRecord ? 'Add New Listing' : 'Edit Listing' ?></h1>
        <div class="hl-pg-sub">Fill out the details for your marketplace listing</div>
    </div>
</div>

<div class="hl-card" style="max-width:800px; border:none; box-shadow:none; padding-top:10px;">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    
    <div class="row" style="display:flex; flex-wrap:wrap; gap:16px;">
        <div style="flex: 1 1 60%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Listing Name *</label>
            <?= $form->field($listing, 'name', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);', 'placeholder' => 'e.g. Broken Screen Repair']) ?>
        </div>
        
        <div style="flex: 1 1 30%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Type *</label>
            <?= $form->field($listing, 'type', ['template' => '{input}{error}'])->dropDownList([
                'service' => 'Service',
                'product' => 'Product'
            ], ['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']) ?>
        </div>

        <div style="flex: 1 1 45%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Category *</label>
            <?= $form->field($listing, 'category_id', ['template' => '{input}{error}'])->dropDownList(
                $categories,
                ['prompt' => 'Select a category...', 'class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);']
            ) ?>
        </div>

        <div style="flex: 1 1 45%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Price (KES) *</label>
            <?= $form->field($listing, 'price', ['template' => '{input}{error}'])->textInput(['type' => 'number', 'step' => '0.01', 'class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);', 'placeholder' => '0.00']) ?>
        </div>

        <div style="flex: 1 1 100%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Description *</label>
            <?= $form->field($listing, 'description', ['template' => '{input}{error}'])->textarea(['rows' => 5, 'class' => 'hl-search-input', 'style' => 'width:100%; border:1px solid var(--border); border-radius:var(--r-sm); padding:10px 12px; background:var(--bg2);', 'placeholder' => 'Describe your offering in detail...']) ?>
        </div>

        <div style="flex: 1 1 45%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Stock Quantity (Products)</label>
            <?= $form->field($listing, 'stock_quantity', ['template' => '{input}{error}'])->textInput(['type' => 'number', 'class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);', 'placeholder' => 'Leave blank if service']) ?>
        </div>

        <div style="flex: 1 1 45%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Availability (Services)</label>
            <?= $form->field($listing, 'availability', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);', 'placeholder' => 'e.g. Mon-Fri 9am-5pm']) ?>
        </div>

        <div style="flex: 1 1 45%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Location</label>
            <?= $form->field($listing, 'location', ['template' => '{input}{error}'])->textInput(['class' => 'hl-search-input', 'style' => 'width:100%; height:38px; border:1px solid var(--border); border-radius:var(--r-sm); padding:0 12px; background:var(--bg2);', 'placeholder' => 'e.g. CBD, Nairobi']) ?>
        </div>

        <div style="flex: 1 1 45%;">
            <label style="display:block; font-size:11px; font-weight:700; color:var(--text2); margin-bottom:6px; text-transform:uppercase; letter-spacing:0.05em;">Images (Up to 5)</label>
            <?= $form->field($listing, 'imageFiles[]', ['template' => '{input}{error}'])->fileInput(['multiple' => true, 'class' => 'hl-search-input', 'style' => 'width:100%;', 'accept' => 'image/*']) ?>
            <div style="font-size:10px; color:var(--text3); margin-top:4px;">First image will be primary. Max 5 images, 2MB each.</div>
        </div>

        <div style="flex: 1 1 100%; margin-top:16px; border-top:1px solid var(--border); padding-top:20px;">
            <button type="submit" class="hl-btn-p">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <?= $listing->isNewRecord ? 'Publish Listing' : 'Save Changes' ?>
            </button>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
