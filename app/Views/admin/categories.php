<?php $base = rtrim(base_url('admin'), '/'); $cat = function($c) use (&$cat) { return $c; }; ?>

<div class="card">
    <h3>Add Category</h3>
    <form method="post" action="<?= esc($base) ?>/categories" class="toolbar" style="justify-content:flex-start;max-width:640px;margin:0">
        <input type="text" name="name" placeholder="New category name" required style="max-width:280px">
        <button class="btn btn-primary" type="submit">Add Category</button>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>ID</th><th>Name</th><th>Slug</th><th>Parent</th><th>Products</th><th>Status</th><th></th></tr>
        </thead>
        <tbody>
        <?php if (empty($categories)): ?>
            <tr><td colspan="7" class="empty">No categories.</td></tr>
        <?php else: foreach ($categories as $c): ?>
            <tr>
                <td class="mono">#<?= (int)$c['id'] ?></td>
                <td><b><?= esc($c['name']) ?></b></td>
                <td class="mono"><?= esc($c['slug']) ?></td>
                <td class="muted"><?= esc($c['parent_name'] ?? '—') ?></td>
                <td><?= number_format($c['product_count']) ?></td>
                <td><?= $c['status'] ? '<span class="badge green">Active</span>' : '<span class="badge gray">Hidden</span>' ?></td>
                <td>
                    <button class="btn btn-sm btn-outline" onclick="editCat(<?= (int)$c['id'] ?>, <?= htmlspecialchars(json_encode($c['name'])) ?>, <?= (int)($c['parent_id'] ?? 0) ?>, <?= (int)$c['status'] ?>)">Edit</button>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/categories/delete/<?= (int)$c['id'] ?>" onclick="return confirm('Delete this category?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
    </div>
</div>

<div class="card" id="editBox" style="display:none">
    <h3>Edit Category</h3>
    <form method="post" id="editForm" action="">
        <div class="row">
            <div><label>Name</label><input type="text" name="name" id="editName" required></div>
            <div><label>Parent</label>
                <select name="parent_id" id="editParent">
                    <option value="0">None (top-level)</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= esc($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div><label>Active</label>
                <select name="status" id="editStatus"><option value="1">Active</option><option value="0">Hidden</option></select>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn" type="button" onclick="document.getElementById('editBox').style.display='none'">Cancel</button>
    </form>
</div>

<script>
function editCat(id, name, parent, status){
    document.getElementById('editForm').action = '<?= esc($base) ?>/categories/' + id + '/update';
    document.getElementById('editName').value = name;
    document.getElementById('editParent').value = parent;
    document.getElementById('editStatus').value = status;
    document.getElementById('editBox').style.display = 'block';
    document.getElementById('editBox').scrollIntoView({behavior:'smooth'});
}
</script>