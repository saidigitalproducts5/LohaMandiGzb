<?php
/** Warehouses list. */
$base = rtrim(base_url(), '/');
?>
<div class="toolbar">
    <div>
        <h3>Warehouses</h3>
        <p class="muted">Storage locations for your stock. The default warehouse is auto-suggested for new records.</p>
    </div>
    <a class="btn btn-primary" href="<?= esc($base) ?>/account/inventory/warehouses/new">+ Add Warehouse</a>
</div>

<?php if (empty($warehouses)): ?>
    <div class="card"><p class="empty">No warehouses yet. Add your first warehouse to begin tracking stock.</p></div>
<?php else: ?>
    <div class="card">
        <table>
            <tr><th>Name</th><th>Address</th><th>City</th><th>Contact</th><th>Default</th><th></th></tr>
            <?php foreach ($warehouses as $w): ?>
                <tr>
                    <td><b><?= esc($w['name']) ?></b></td>
                    <td class="muted"><?= esc($w['address'] ?? '-') ?></td>
                    <td><?= esc($w['city'] ?? '-') ?></td>
                    <td class="mono"><?= esc($w['contact_phone'] ?? '-') ?></td>
                    <td><?= $w['is_default'] ? '<span class="badge green">Default</span>' : '<a class="btn btn-sm btn-outline" href="' . esc($base) . '/account/inventory/warehouses/' . (int)$w['id'] . '/default">Set default</a>' ?></td>
                    <td>
                        <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/inventory/warehouses/<?= (int)$w['id'] ?>/edit">Edit</a>
                        <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/inventory/warehouses/<?= (int)$w['id'] ?>/delete" onclick="return confirm('Delete this warehouse? Stock records are kept.')">✕</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>