<?php /** @var array $survey */ ?>
<?php $hideNavbar = true; ?>
<?= view('templates/header') ?>

<div class="container-fluid mt-4">
  <div class="row">
    <!-- Survey Details -->
    <div class="col-md-4">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <strong>Survey Details</strong>
        </div>
        <div class="card-body">
          <p><strong>Title:</strong> <?= esc($survey['TITLE']) ?></p>
          <p><strong>Description:</strong><br><?= nl2br(esc($survey['DESCRIPTION'])) ?></p>
          <p><strong>Start:</strong> <?= esc($survey['START_DATE']) ?></p>
          <p><strong>End:</strong> <?= esc($survey['END_DATE']) ?></p>
          <p><strong>Have Taken:</strong> 0</p>
        </div>
      </div>
    </div>

    <!-- Survey Questionnaire -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Survey Questionnaire</strong>
          <button class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#addQuestionModal">
            + Add New Question
          </button>
        </div>
        <div class="card-body" id="question-list">
          <?php if (!empty($questions)): ?>
            <?php foreach ($questions as $q): ?>
              <div class="card mb-3 shadow-sm" id="question-card-<?= $q['ID'] ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <strong><?= esc($q['QUESTION']) ?></strong>
                  <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                      <a class="dropdown-item text-primary" href="<?= site_url('question/edit/' . $q['ID']) ?>">
                        <i class="fas fa-edit"></i> Edit
                      </a>
                      <button class="dropdown-item text-danger" onclick="deleteQuestion(<?= $q['ID'] ?>)">
                        <i class="fas fa-trash-alt"></i> Delete
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <?php if (str_contains($q['TYPE'], 'Radio')): ?>
                    <?php foreach ($q['OPTIONS'] as $opt): ?>
                      <div><input type="radio" disabled> <?= esc($opt['OPTION_TEXT']) ?></div>
                    <?php endforeach; ?>
                  <?php elseif (str_contains($q['TYPE'], 'Check')): ?>
                    <?php foreach ($q['OPTIONS'] as $opt): ?>
                      <div><input type="checkbox" disabled> <?= esc($opt['OPTION_TEXT']) ?></div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <input type="text" class="form-control" placeholder="Text field" disabled>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="text-muted">No questions yet.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FontAwesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />



<?= view('templates/footer') ?>
<!-- Modal: Add New Question -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Question</label>
              <textarea id="question-text" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label>Question Answer Type</label>
              <select id="question-type" class="form-control">
                <option>Single Answer/Radio Button</option>
                <option>Multiple Answer/Check Boxes</option>
                <option>Text Field/ Text Area</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <label>Preview</label>
            <div id="question-preview" class="border p-3">Select Question Answer type first.</div>
          </div>
        </div>
        <div class="form-group mt-3" id="options-wrapper" style="display: none;">
          <label>Options</label>
          <div id="option-list">
            <input type="text" class="form-control mb-2" placeholder="Option 1">
          </div>
          <button id="add-option-btn" class="btn btn-sm btn-outline-primary">+ Add</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="save-question-btn" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const questionType = document.getElementById('question-type');
  const optionsWrapper = document.getElementById('options-wrapper');
  const preview = document.getElementById('question-preview');
  const optionList = document.getElementById('option-list');
  const addBtn = document.getElementById('add-option-btn');

  function addOption() {
    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control mb-2';
    input.placeholder = 'Option';
    input.addEventListener('input', updatePreview);
    optionList.appendChild(input);
    updatePreview();
  }

  function updatePreview() {
    const type = questionType.value;
    const inputs = optionList.querySelectorAll('input');
    preview.innerHTML = '';

    if (type.includes('Radio')) {
      inputs.forEach((input, i) => {
        const label = input.value.trim() || `Option ${i + 1}`;
        preview.innerHTML += `<div><input type="radio" disabled> ${label}</div>`;
      });
    } else if (type.includes('Check')) {
      inputs.forEach((input, i) => {
        const label = input.value.trim() || `Option ${i + 1}`;
        preview.innerHTML += `<div><input type="checkbox" disabled> ${label}</div>`;
      });
    } else {
      preview.innerHTML = '<input class="form-control" placeholder="User input field" disabled>';
    }
  }

  questionType.addEventListener('change', () => {
    optionList.innerHTML = '';
    preview.innerHTML = '';
    if (questionType.value.includes('Radio') || questionType.value.includes('Check')) {
      optionsWrapper.style.display = 'block';
      for (let i = 0; i < 2; i++) addOption();
    } else {
      optionsWrapper.style.display = 'none';
      updatePreview();
    }
  });

  addBtn.addEventListener('click', (e) => {
    e.preventDefault();
    addOption();
  });

  $('#addQuestionModal').on('shown.bs.modal', () => {
    questionType.dispatchEvent(new Event('change'));
  });

  document.getElementById('save-question-btn').addEventListener('click', async () => {
    const surveyId = <?= json_encode($survey['ID']) ?>;
    const question = document.getElementById('question-text').value.trim();
    const type = questionType.value;
    const options = Array.from(optionList.querySelectorAll('input'))
      .map(input => input.value.trim())
      .filter(Boolean);

    const payload = { question, type, options, survey_id: surveyId };

    try {
      const response = await fetch('<?= site_url('question/create') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const text = await response.text();
      let result;
      try {
        result = JSON.parse(text);
      } catch (err) {
        alert('❌ Failed to parse JSON.');
        return;
      }

      if (result.success) {
        alert('✅ Question saved!');
        location.reload();
      } else {
        alert(result.message || '❌ Failed to save.');
      }
    } catch (err) {
      alert('⚠️ Could not connect to server.');
    }
  });
});

function deleteQuestion(id) {
  if (!confirm('Are you sure you want to delete this question?')) return;

  fetch('<?= site_url('question/delete/') ?>' + id, {
    method: 'DELETE',
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(res => res.json())
  .then(result => {
    if (result.success) {
      alert('✅ Question deleted.');
      document.getElementById('question-card-' + id).remove();
    } else {
      alert(result.message || '❌ Failed to delete question.');
    }
  })
  .catch(err => {
    console.error(err);
    alert('⚠️ Server error occurred.');
  });
}
</script>

<!-- Fix for Dropdown -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

<?= view('templates/footer') ?>
