<?php
/** Buyer review form for a delivered order. */
$base = rtrim(base_url(), '/');
$o = $order;
?>
<div class="card" style="max-width:620px">
    <h3>Review <?= esc($seller['business_name'] ?? 'Seller') ?></h3>
    <p class="muted" style="margin-bottom:18px">Order <?= esc($o['order_number']) ?> was delivered. How was your experience?</p>

    <form method="post" action="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>/review">
        <label>Your Rating</label>
        <div style="display:flex;gap:8px;margin:8px 0 16px">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label style="flex:1;text-align:center;cursor:pointer;margin:0">
                    <input type="radio" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?> style="width:auto">
                    <div style="font-size:22px"><?= str_repeat('★', $i) ?><?= str_repeat('☆', 5 - $i) ?></div>
                </label>
            <?php endfor; ?>
        </div>

        <label>Review (optional)</label>
        <textarea name="review" rows="5" placeholder="Share your experience with quality, pricing, delivery and communication…"></textarea>

        <button class="btn btn-primary" type="submit" style="margin-top:18px">Submit Review</button>
    </form>
    <a class="store-link" href="<?= esc($base) ?>/account/orders/<?= (int)$o['id'] ?>">← Back to order</a>
</div>