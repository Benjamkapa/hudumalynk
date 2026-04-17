<?php if (!Yii::$app->request->isAjax): ?>
<div class="modal fade" id="providerModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content hl-card" style="border:none;box-shadow:var(--shadow-lg);border-radius:var(--radius-xl);">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" id="modalTitle">Loading...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" id="modalBody">
        <div class="text-center py-4" id="modalLoading">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2 text-muted">Loading provider details...</p>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
