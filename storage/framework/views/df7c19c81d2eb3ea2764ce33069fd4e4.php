<div class="row g-3">
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Code <span style="color:#ef4444">*</span></label>
        <input type="text" name="code" class="form-control form-control-sm" placeholder="VD: GIAM20" style="text-transform:uppercase" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Discount type <span style="color:#ef4444">*</span></label>
        <select name="type" class="form-select form-select-sm" required>
            <option value="percentage">Percentage (%)</option>
            <option value="fixed">Fixed (₫)</option>
        </select>
    </div>
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Value <span style="color:#ef4444">*</span></label>
        <input type="number" name="value" class="form-control form-control-sm" placeholder="20" step="0.01" min="0" required>
    </div>
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Min. Order (₫)</label>
        <input type="number" name="min_order_amount" class="form-control form-control-sm" placeholder="0" min="0">
    </div>
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Max. Discount (₫)</label>
        <input type="number" name="max_discount" class="form-control form-control-sm" placeholder="No limit" min="0">
    </div>
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Usage limit</label>
        <input type="number" name="usage_limit" class="form-control form-control-sm" placeholder="No limit" min="1">
    </div>
    <div class="col-sm-6">
        <label class="form-label" style="font-size:12px;font-weight:600;color:var(--muted)">Expiry date</label>
        <input type="date" name="expires_at" class="form-control form-control-sm">
    </div>
    <div class="col-sm-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active_chk" value="1" checked>
            <label class="form-check-label" for="is_active_chk" style="font-size:13px">Activate immediately</label>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\ADMIN\ecomerce\resources\views/admin/coupons/_form.blade.php ENDPATH**/ ?>