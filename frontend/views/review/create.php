<?php
/** @var common\models\Order $order */
/** @var common\models\Review $review */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<div style="padding-top:var(--navbar-h);background:var(--surface-2);min-height:100vh;">
<div class="hl-section" style="max-width:640px;padding-top:3rem;">

  <div class="hl-card">
    <div class="hl-card-header text-center" style="padding-bottom:0;border:none;">
      <h3 style="margin-bottom:0.5rem;">Rate Your Experience</h3>
      <p style="color:var(--text-muted);font-size:0.9rem;">
        Order Ref: <?= Html::encode($order->reference) ?>
      </p>
    </div>

    <div class="hl-card-body" style="padding:2rem;">
      <!-- Provider summary -->
      <div class="d-flex align-items-center gap-3 mb-4 p-3" style="background:var(--surface-2);border-radius:var(--radius);border:1px solid var(--border);">
        <div style="width:50px;height:50px;border-radius:50%;background:var(--hl-gradient);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">
          <?= strtoupper(substr($order->provider->business_name, 0, 2)) ?>
        </div>
        <div>
          <div style="font-weight:700;"><?= Html::encode($order->provider->business_name) ?></div>
          <div style="font-size:0.82rem;color:var(--text-muted);">
            <?php foreach ($order->items as $i) echo Html::encode($i->name) . '<br>'; ?>
          </div>
        </div>
      </div>

      <?php $form = ActiveForm::begin(); ?>

      <!-- Star Rating -->
      <div class="hl-form-group text-center mb-4">
        <label class="hl-label" style="font-size:1rem;">How would you rate this provider?</label>

        <div class="rating-input mt-2">
          <?php for ($i = 5; $i >= 1; $i--): ?>
            <input
              type="radio"
              name="Review[rating]"
              id="star<?= $i ?>"
              value="<?= $i ?>"
              <?= ($review->rating == $i) ? 'checked' : '' ?>
              <?= ($i === 5) ? 'required' : '' ?>
            >
            <label for="star<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
          <?php endfor; ?>
        </div>

        <!-- Live hint -->
        <div id="starHint" style="margin-top:0.4rem;font-size:0.8rem;color:var(--text-muted);min-height:1.2em;">
          <?php
            $hints = [1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent!'];
            echo $review->rating ? $hints[$review->rating] : 'Tap a star to rate';
          ?>
        </div>

        <?= Html::error($review, 'rating', ['class' => 'help-block text-danger mt-1']) ?>
      </div>

      <style>
      .rating-input {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 0.5rem;
        font-size: 2.5rem;
        line-height: 1;
      }
      .rating-input input[type="radio"] {
        display: none;
      }
      .rating-input label {
        cursor: pointer;
        color: #d1d5db;
        transition: color 0.15s ease, transform 0.15s ease;
      }
      /* Fill selected star + all siblings after it (= visually to its left, due to row-reverse) */
      .rating-input input:checked ~ label {
        color: #F59E0B;
      }
      /* Hover fill */
      .rating-input label:hover,
      .rating-input label:hover ~ label {
        color: #FCD34D;
        transform: scale(1.15);
      }
      </style>

      <div class="hl-form-group">
        <label class="hl-label">Review Title <span style="font-weight:400;color:var(--text-muted);">(optional)</span></label>
        <?= $form->field($review, 'title', ['template' => '{input}{error}'])->textInput(['class' => 'hl-input', 'placeholder' => 'Summarize your experience']) ?>
      </div>

      <div class="hl-form-group">
        <label class="hl-label">Detailed Review <span style="font-weight:400;color:var(--text-muted);">(optional)</span></label>
        <?= $form->field($review, 'comment', ['template' => '{input}{error}'])->textarea(['class' => 'hl-textarea', 'rows' => 4, 'placeholder' => 'What did you like or dislike? Would you recommend them?']) ?>
      </div>

      <div class="mt-4 text-center">
        <?= Html::submitButton('<i class="bi bi-star-fill"></i> Submit Review', ['class' => 'btn-hl-primary w-100', 'style' => 'justify-content:center;padding:0.85rem;']) ?>
        <a href="<?= Url::to(['/order/view', 'id' => $order->id]) ?>" class="btn btn-link mt-2" style="color:var(--text-muted);font-size:0.85rem;text-decoration:none;">Cancel</a>
      </div>

      <?php ActiveForm::end(); ?>
    </div>
  </div>

</div>
</div>

<script>
(function () {
    var hints  = { 1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Very Good', 5: 'Excellent!' };
    var hintEl = document.getElementById('starHint');
    var inputs = document.querySelectorAll('.rating-input input[type="radio"]');
    inputs.forEach(function (input) {
        input.addEventListener('change', function () {
            if (hintEl) hintEl.textContent = hints[this.value] || '';
        });
    });
})();
</script>