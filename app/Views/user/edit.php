<h2>Edit User #<?= esc($user['ID']) ?></h2>
<form action="<?= site_url("user/{$user['ID']}") ?>" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="put">

  <label>First Name</label>
  <input type="text" name="firstname" value="<?= esc($user['FIRSTNAME']) ?>" required>

  <label>Last Name</label>
  <input type="text" name="lastname" value="<?= esc($user['LASTNAME']) ?>" required>

  <label>Middle Name</label>
  <input type="text" name="middlename" value="<?= esc($user['MIDDLENAME']) ?>">

  <label>Contact</label>
  <input type="text" name="contact" value="<?= esc($user['CONTACT']) ?>">

  <label>Address</label>
  <textarea name="address"><?= esc($user['ADDRESS']) ?></textarea>

  <label>Email</label>
  <input type="email" name="email" value="<?= esc($user['EMAIL']) ?>" required>

  <label>Password (leave blank to keep current)</label>
  <input type="password" name="password">

  <label>Type</label>
  <select name="type">
    <option value="1" <?= $user['TYPE']==1?'selected':'' ?>>Admin</option>
    <option value="2" <?= $user['TYPE']==2?'selected':'' ?>>Staff</option>
    <option value="3" <?= $user['TYPE']==3?'selected':'' ?>>Subscriber</option>
  </select>

  <button type="submit">Update</button>
</form>
