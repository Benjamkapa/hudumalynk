<?php
/** @var common\models\Listing $listing */
/** @var array $categories */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<style>
.form-group { margin-bottom: 20px; }
.form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text1); margin-bottom: 6px; }
.form-input { width: 100%; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--r-md); font-size: 13px; background: var(--bg); color: var(--text1); }
.form-input:focus { outline: none; border-color: var(--acc); box-shadow: 0 0 0 3px rgba(108,92,231,.1); }
.form-textarea { resize: vertical; min-height: 80px; }
.form-select { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e"); background-position: right 8px center; background-repeat: no-repeat; background-size: 16px; padding-right: 32px; }

.image-upload { border: 2px dashed var(--border); border-radius: var(--r-md); padding: 20px; text-align: center; margin-bottom: 16px; transition: border-color .2s; }
.image-upload:hover { border-color: var(--acc); }
.image-upload.dragover { border-color: var(--acc); background: rgba(108,92,231,.05); }
.upload-icon { font-size: 32px; margin-bottom: 8px; }
.upload-text { font-size: 13px; color: var(--text3); margin-bottom: 4px; }
.upload-subtext { font-size: 11px; color: var(--text4); }

.image-preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 12px; margin-top: 16px; }
.image-item { position: relative; border-radius: var(--r-md); overflow: hidden; background: var(--bg2); }
.image-item img { width: 100%; height: 60px; object-fit: cover; }
.image-remove { position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,.7); color: #fff; border: none; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; }

.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid var(--border); margin-top: 24px; }
.btn { padding: 10px 16px; border: none; border-radius: var(--r-md); font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; }
.btn-primary { background: var(--acc); color: #fff; }
.btn-primary:hover { background: var(--acc2); }
.btn-secondary { background: var(--bg2); color: var(--text2); border: 1px solid var(--border); }
.btn-secondary:hover { background: var(--bg3); }

.error-message { color: var(--rose); font-size: 12px; margin-top: 4px; }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'listing-form',
    'options' => ['enctype' => 'multipart/form-data'],
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
]); ?>

<div class="form-group">
    <?= $form->field($listing, 'name')->textInput([
        'class' => 'form-input',
        'placeholder' => 'Enter listing name'
    ])->label('Listing Name *') ?>
</div>

<div class="form-group">
    <?= $form->field($listing, 'category_id')->dropDownList($categories, [
        'class' => 'form-input form-select',
        'prompt' => 'Select a category'
    ])->label('Category *') ?>
</div>

<div class="form-group">
    <?= $form->field($listing, 'description')->textarea([
        'class' => 'form-input form-textarea',
        'placeholder' => 'Describe your product or service in detail...'
    ])->label('Description *') ?>
</div>

<div class="form-group">
    <?= $form->field($listing, 'price')->textInput([
        'class' => 'form-input',
        'type' => 'number',
        'step' => '0.01',
        'placeholder' => '0.00'
    ])->label('Price (KSh) *') ?>
</div>

<div class="form-group">
    <label class="form-label">Images (Max 5, First will be primary)</label>
    <div class="image-upload" id="imageUpload" onclick="document.getElementById('listing-imagefiles').click()">
        <div class="upload-icon">📷</div>
        <div class="upload-text">Click to upload images or drag and drop</div>
        <div class="upload-subtext">PNG, JPG, JPEG up to 5MB each</div>
    </div>
    <?= $form->field($listing, 'imageFiles[]')->fileInput([
        'multiple' => true,
        'accept' => 'image/*',
        'style' => 'display: none;'
    ])->label(false) ?>

    <div class="image-preview" id="imagePreview">
        <?php if (isset($listing) && !$listing->isNewRecord && $listing->images): ?>
            <?php foreach ($listing->images as $image): ?>
                <div class="image-item">
                    <img src="<?= Url::to(['/uploads/' . $image->image_path]) ?>" alt="Listing image">
                    <button type="button" class="image-remove" onclick="removeImage(this, <?= $image->id ?>)">×</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <?= $form->field($listing, 'status')->dropDownList([
        \common\models\Listing::STATUS_DRAFT => 'Draft (Not visible to buyers)',
        \common\models\Listing::STATUS_ACTIVE => 'Active (Visible to buyers)',
        \common\models\Listing::STATUS_INACTIVE => 'Inactive (Hidden from buyers)',
    ], [
        'class' => 'form-input form-select'
    ])->label('Status') ?>
</div>

<div class="form-actions">
    <button type="button" class="btn btn-secondary" onclick="closeOffcanvas()">Cancel</button>
    <button type="submit" class="btn btn-primary">
        <?= isset($listing) && !$listing->isNewRecord ? 'Update Listing' : 'Create Listing' ?>
    </button>
</div>

<?php ActiveForm::end(); ?>

<script>
let selectedFiles = [];
let imageCount = document.querySelectorAll('.image-item').length;

function updateImagePreview() {
    const preview = document.getElementById('imagePreview');
    const upload = document.getElementById('imageUpload');

    // Clear existing previews for new files
    const existingItems = preview.querySelectorAll('.image-item');
    const newPreview = document.createElement('div');
    newPreview.id = 'imagePreview';
    newPreview.className = 'image-preview';

    // Keep existing images
    existingItems.forEach(item => newPreview.appendChild(item));

    // Add new file previews
    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'image-item';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="image-remove" onclick="removeFile(${index})">×</button>
            `;
            newPreview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });

    preview.parentNode.replaceChild(newPreview, preview);

    // Update upload area visibility
    const totalImages = existingItems.length + selectedFiles.length;
    upload.style.display = totalImages >= 5 ? 'none' : 'block';
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateImagePreview();
    updateFileInput();
}

function removeImage(button, imageId) {
    if (confirm('Remove this image?')) {
        button.closest('.image-item').remove();
        // You might want to add AJAX call here to delete from server
    }
}

function updateFileInput() {
    const input = document.getElementById('listing-imagefiles');
    const dt = new DataTransfer();

    selectedFiles.forEach(file => {
        dt.items.add(file);
    });

    input.files = dt.files;
}

function closeOffcanvas() {
    // Close the offcanvas modal
    document.getElementById('hlOcBackdrop').classList.remove('open');
    document.getElementById('hlOffcanvas').classList.remove('open');
}

// Handle file selection
document.getElementById('listing-imagefiles').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const maxFiles = 5 - document.querySelectorAll('.image-item').length;

    if (files.length > maxFiles) {
        alert(`You can only upload ${maxFiles} more image(s)`);
        return;
    }

    // Validate file types and sizes
    const validFiles = files.filter(file => {
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image file`);
            return false;
        }
        if (file.size > 5 * 1024 * 1024) { // 5MB
            alert(`${file.name} is too large. Maximum size is 5MB`);
            return false;
        }
        return true;
    });

    selectedFiles = selectedFiles.concat(validFiles);
    updateImagePreview();
});

// Drag and drop functionality
const uploadArea = document.getElementById('imageUpload');

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');

    const files = Array.from(e.dataTransfer.files);
    const maxFiles = 5 - document.querySelectorAll('.image-item').length;

    if (files.length > maxFiles) {
        alert(`You can only upload ${maxFiles} more image(s)`);
        return;
    }

    const validFiles = files.filter(file => {
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image file`);
            return false;
        }
        if (file.size > 5 * 1024 * 1024) { // 5MB
            alert(`${file.name} is too large. Maximum size is 5MB`);
            return false;
        }
        return true;
    });

    selectedFiles = selectedFiles.concat(validFiles);
    updateImagePreview();
    updateFileInput();
});

// Form submission handling
document.getElementById('listing-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-Token': document.querySelector('[name="_csrf"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeOffcanvas();
            // Reload the listings page
            if (window.location.href.includes('/listings')) {
                window.location.reload();
            } else {
                window.location.href = '<?= Url::to(['/provider/listings']) ?>';
            }
        } else {
            // Handle validation errors
            console.error('Validation errors:', data.errors);
            alert('Please check the form for errors');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>