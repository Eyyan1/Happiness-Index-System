<h2>All Answers</h2>
<a href="<?= site_url('answer/new') ?>">+ New Answer</a>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Survey ID</th>
    <th>User ID</th>
    <th>Question ID</th>
    <th>Answer</th>
    <th>Actions</th>
  </tr>
  <?php foreach($answers as $a): ?>
  <tr>
    <td><?= esc($a['ID']) ?></td>
    <td><?= esc($a['SURVEY_ID']) ?></td>
    <td><?= esc($a['USER_ID']) ?></td>
    <td><?= esc($a['QUESTION_ID']) ?></td>
    <td>
  <?= esc(
        is_object($a['ANSWER'])
        ? $a['ANSWER']->load()   // load CLOB into a string
        : $a['ANSWER']
      ) ?>
</td>
    <td>
      <a href="<?= site_url("answer/{$a['ID']}") ?>">View</a> |
      <a href="<?= site_url("answer/{$a['ID']}/edit") ?>">Edit</a> |
      <a href="<?= site_url("answer/{$a['ID']}") ?>" data-method="delete">Delete</a>
    </td>
  </tr>
  <?php endforeach ?>
</table>



<!-- DataTables init for Answers -->
<script>
  $(function () {
    $("#answerTable").DataTable({
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

