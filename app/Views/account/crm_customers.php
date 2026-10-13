<?php
/** CRM customer database. */
$base = rtrim(base_url(), '/');
?>
<div class="card">
    <div class="toolbar">
        <form method="get" action="<?= esc($base) ?>/account/crm/customers">
            <input type="text" name="q" value="<?= esc($q ?? '') ?>" placeholder="Search name, company, mobile…" style="max-width:320px">
            <button class="btn btn-primary" type="submit">Search</button>
            <?php if (!empty($q)): ?><a class="btn" href="<?= esc($base) ?>/account/crm/customers">Clear</a><?php endif; ?>
        </form>
        <a class="btn btn-primary" href="<?= esc($base) ?>/account/crm/customer/new">+ Add Customer</a>
    </div>

    <table>
        <thead><tr><th>Customer</th><th>Contact</th><th>Source</th><th>Deals</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($customers)): ?>
            <tr><td colspan="5" class="empty">No customers yet. Add your first customer or create a deal.</td></tr>
        <?php else: foreach ($customers as $c): ?>
            <tr>
                <td>
                    <b><?= esc($c['name']) ?></b>
                    <?php if ($c['company']): ?><br><span class="muted"><?= esc($c['company']) ?></span><?php endif; ?>
                </td>
                <td class="mono"><?= esc($c['mobile'] ?? '—') ?><?= $c['email'] ? '<br>' . esc($c['email']) : '' ?></td>
                <td><span class="badge gray"><?= esc($c['source']) ?></span></td>
                <td class="mono"><?= (int)$c['deal_count'] ?> deal<?= (int)$c['deal_count'] === 1 ? '' : 's' ?></td>
                <td style="white-space:nowrap">
                    <a class="btn btn-sm btn-outline" href="<?= esc($base) ?>/account/crm/customers/<?= (int)$c['id'] ?>">Open</a>
                    <a class="btn btn-sm" href="<?= esc($base) ?>/account/crm/customers/<?= (int)$c['id'] ?>/edit">Edit</a>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/account/crm/customers/<?= (int)$c['id'] ?>/delete" onclick="return confirm('Delete this customer? Related deals/tasks stay.')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>