<?php $base = rtrim(base_url('admin'), '/'); ?>

<div class="card">
    <h3>Add Brand</h3>
    <form method="post" action="<?= esc($base) ?>/brands" class="toolbar" style="justify-content:flex-start;max-width:480px;margin:0">
        <input type="text" name="name" placeholder="Brand name" required style="max-width:280px">
        <button class="btn btn-primary" type="submit">Add Brand</button>
    </form>
</div>

<div class="card">
    <table>
        <thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($brands)): ?>
            <tr><td colspan="6" class="empty">No brands.</td></tr>
        <?php else: foreach ($brands as $b): ?>
            <tr>
                <td class="mono">#<?= (int)$b['id'] ?></td>
                <td><b><?= esc($b['name']) ?></b></td>
                <td class="mono"><?= esc($b['slug']) ?></td>
                <td><?= number_format($b['product_count']) ?></td>
                <td><?= $b['status'] ? '<span class="badge green">Active</span>' : '<span class="badge gray">Hidden</span>' ?></td>
                <td style="white-space:nowrap">
                    <button class="btn btn-sm btn-outline" onclick="editBrand(<?= (int)$b['id'] ?>, <?= htmlspecialchars(json_encode($b['name'])) ?>, <?= (int)$b['status'] ?>)">Edit</button>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/brands/delete/<?= (int)$b['id'] ?>" onclick="return confirm('Delete this brand?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="card" id="editBox" style="display:none">
    <h3>Edit Brand</h3>
    <form method="post" id="editForm" action="">
        <div class="row">
            <div><label>Name</label><input type="text" name="name" id="editName" required></div>
            <div><label>Status</label><select name="status" id="editStatus"><option value="1">Active</option><option value="0">Hidden</option></select></div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn" type="button" onclick="document.getElementById('editBox').style.display='none'">Cancel</button>
    </form>
</div>

<script>
function editBrand(id, name, status){
    document.getElementById('editForm').action = '<?= esc($base) ?>/brands/' + id + '/update';
    document.getElementById('editName').value = name;
    document.getElementById('editStatus').value = status;
    document.getElementById('editBox').style.display = 'block';
    document.getElementById('editBox').scrollIntoView({behavior:'smooth'});
}
</script>