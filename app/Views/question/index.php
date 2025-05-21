<h2>All Questions</h2>
<a href="<?= site_url('question/new') ?>">+ New Question</a>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Question</th>
    <th>Type</th>
    <th>Actions</th>
  </tr>
  <?php foreach($questions as $q): ?>
  <tr>
    <td><?= esc($q['ID']) ?></td>
    <td><?= esc($q['QUESTION']) ?></td>
    <td><?= esc($q['TYPE']) ?></td>
    <td>
      <a href="<?= site_url("question/{$q['ID']}") ?>">View</a> |
      <a href="<?= site_url("question/{$q['ID']}/edit") ?>">Edit</a> |
      <a href="<?= site_url("question/{$q['ID']}") ?>" data-method="delete">Delete</a>
    </td>
  </tr>
  <?php endforeach ?>
</table>


<!-- DataTables init for Questions -->
<script>
  $(function () {
    $("#questionTable").DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      language: {
        search: "",
        searchPlaceholder: "Search…"
      }
    });
  });
</script>

<?= view('templates/footer') ?>
