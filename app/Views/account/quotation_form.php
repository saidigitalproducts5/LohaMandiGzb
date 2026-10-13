<?php
/** Seller quotation form — sent against a matched buyer RFQ. */
$base = rtrim(base_url(), '/');
$r = $rfq;
$errors = $errors ?? [];
?>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start">
    <div class="card">
        <h3>RFQ Summary</h3>
        <table>
            <tbody>
                <tr><th style="width:40%">Product</th><td><b><?= esc($r['product_name']) ?></b></td></tr>
                <tr><th>Quantity</th><td><?= rtrim(rtrim(number_format((float)$r['quantity'], 3), '0'), '.') ?> <?= esc($r['unit']) ?></td></tr>
                <tr><th>Required by</th><td><?= esc($r['required_date'] ?? '-') ?></td></tr>
                <tr><th>City</th><td><?= esc($r['city'] ?? '-') ?></td></tr>
                <tr><th>Specification</th><td><?= nl2br(esc($r['specification'] ?? '-')) ?></td></tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h3>Build Your Quotation</h3>
        <form id="qform" method="post" action="<?= esc($base) ?>/account/quotations/new/<?= (int)$r['id'] ?>">
            <label>Unit Price (₹ / <?= esc($r['unit']) ?>) *</label>
            <input type="number" name="unit_price" id="unit_price" step="0.01" min="0.01" required value="<?= esc($_POST['unit_price'] ?? '') ?>">
            <?php if (isset($errors['unit_price'])): ?><div class="err alert error"><?= esc($errors['unit_price']) ?></div><?php endif; ?>

            <div class="row" style="margin-top:14px">
                <div>
                    <label>GST Rate (%)</label>
                    <input type="number" name="gst_rate" id="gst_rate" step="0.01" min="0" max="30" value="<?= esc($_POST['gst_rate'] ?? $gstRate) ?>">
                </div>
                <div>
                    <label>Discount (₹)</label>
                    <input type="number" name="discount" id="discount" step="0.01" min="0" value="<?= esc($_POST['discount'] ?? 0) ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label>Transport (₹)</label>
                    <input type="number" name="transport_charge" id="transport" step="0.01" min="0" value="<?= esc($_POST['transport_charge'] ?? 0) ?>">
                </div>
                <div>
                    <label>Other Charges (₹)</label>
                    <input type="number" name="other_charges" id="other" step="0.01" min="0" value="<?= esc($_POST['other_charges'] ?? 0) ?>">
                </div>
            </div>

            <div class="row">
                <div>
                    <label>Valid Until</label>
                    <input type="date" name="valid_until" value="<?= esc($_POST['valid_until'] ?? '') ?>">
                </div>
            </div>

            <label>Payment Terms</label>
            <input type="text" name="payment_terms" value="<?= esc($_POST['payment_terms'] ?? '') ?>" placeholder="e.g. 30% advance, balance on delivery">

            <div style="margin:14px 0">
                <label>Notes</label>
                <textarea name="notes" rows="3"><?= esc($_POST['notes'] ?? '') ?></textarea>
            </div>

            <div style="background:#f3f4f6;border-radius:10px;padding:14px;margin-bottom:16px">
                <div style="display:flex;justify-content:space-between"><span class="muted">Subtotal</span><b>₹<span id="s_subtotal">0.00</span></b></div>
                <div style="display:flex;justify-content:space-between"><span class="muted">GST</span><b>₹<span id="s_gst">0.00</span></b></div>
                <div style="display:flex;justify-content:space-between"><span class="muted">Discount</span><b>-₹<span id="s_disc">0.00</span></b></div>
                <div style="display:flex;justify-content:space-between"><span class="muted">Transport + Other</span><b>₹<span id="s_tn">0.00</span></b></div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid #dbe2ea;padding-top:10px;margin-top:10px"><b>Total</b><b>₹<span id="s_total">0.00</span></b></div>
            </div>

            <button class="btn btn-primary" type="submit">Send Quotation</button>
            <a class="btn" href="<?= esc($base) ?>/account/rfqs">Cancel</a>
        </form>
    </div>
</div>
<script>
(function(){
    const qty = <?= json_encode((float)$r['quantity']) ?>;
    const $ = id => document.getElementById(id);
    function calc(){
        const up = parseFloat($('unit_price').value) || 0;
        const gst = parseFloat($('gst_rate').value) || 0;
        const disc = parseFloat($('discount').value) || 0;
        const tr = parseFloat($('transport').value) || 0;
        const ot = parseFloat($('other').value) || 0;
        const base = up * qty;
        const gstAmt = base * gst / 100;
        const total = base - disc + tr + ot + gstAmt;
        $('s_subtotal').textContent = base.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
        $('s_gst').textContent = gstAmt.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
        $('s_disc').textContent = disc.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
        $('s_tn').textContent = (tr+ot).toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
        $('s_total').textContent = total.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2});
    }
    ['unit_price','gst_rate','discount','transport','other'].forEach(id => $(id).addEventListener('input', calc));
    calc();
})();
</script>