<?php $base = rtrim(base_url('admin'), '/'); ?>

<div class="card">
    <h3>Add Location</h3>
    <form method="post" action="<?= esc($base) ?>/locations" class="toolbar" style="justify-content:flex-start;max-width:640px;margin:0">
        <input type="text" name="city" placeholder="City" required style="max-width:180px">
        <input type="text" name="state" placeholder="State" required style="max-width:180px">
        <input type="text" name="pincode" placeholder="Pincode (optional)" style="max-width:150px">
        <button class="btn btn-primary" type="submit">Add Location</button>
    </form>
</div>

<div class="card">
    <table>
        <thead><tr><th>ID</th><th>City</th><th>State</th><th>Pincode</th><th>Sellers</th><th>Active</th><th></th></tr></thead>
        <tbody>
        <?php if (empty($locations)): ?>
            <tr><td colspan="7" class="empty">No locations. Add cities/states served by the marketplace.</td></tr>
        <?php else: foreach ($locations as $l): ?>
            <tr>
                <td class="mono">#<?= (int)$l['id'] ?></td>
                <td><b><?= esc($l['city']) ?></b></td>
                <td><?= esc($l['state']) ?></td>
                <td class="mono"><?= esc($l['pincode']) ?></td>
                <td><?= number_format($l['seller_count']) ?></td>
                <td><?= $l['is_active'] ? '<span class="badge green">Active</span>' : '<span class="badge gray">Hidden</span>' ?></td>
                <td style="white-space:nowrap">
                    <button class="btn btn-sm btn-outline" onclick="editLoc(<?= (int)$l['id'] ?>, <?= htmlspecialchars(json_encode($l['pincode'])) ?>, <?= (int)$l['is_active'] ?>)">Edit</button>
                    <a class="btn btn-sm btn-danger" href="<?= esc($base) ?>/locations/delete/<?= (int)$l['id'] ?>" onclick="return confirm('Delete this location?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

<div class="card" id="editBox" style="display:none">
    <h3>Edit Location</h3>
    <form method="post" id="editForm" action="">
        <div class="row">
            <div><label>Pincode</label><input type="text" name="pincode" id="editPincode"></div>
            <div><label>Active</label><select name="is_active" id="editActive"><option value="1">Active</option><option value="0">Hidden</option></select></div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
        <button class="btn" type="button" onclick="document.getElementById('editBox').style.display='none'">Cancel</button>
    </form>
</div>

<script>
function editLoc(id, pincode, active){
    document.getElementById('editForm').action = '<?= esc($base) ?>/locations/' + id + '/update';
    document.getElementById('editPincode').value = pincode || '';
    document.getElementById('editActive').value = active;
    document.getElementById('editBox').style.display = 'block';
    document.getElementById('editBox').scrollIntoView({behavior:'smooth'});
}
</script>