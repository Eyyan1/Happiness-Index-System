<?php /** @var array $survey */ ?>
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
        </div>
      </div>
    </div>

    <!-- Sectioned Survey Questionnaire -->
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Survey Questionnaire</strong>
          <div>
            <button class="btn btn-outline-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addSectionModal">
              + Add Section
            </button>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
              + Add Question
            </button>
          </div>
        </div>
        <div class="card-body">

          <?php if (! empty($survey['SECTIONS'])): ?>
            <div id="section-container">
              <?php foreach ($survey['SECTIONS'] as $i => $section): ?>
                <div class="survey-section mb-4 border rounded p-3 bg-light" data-index="<?= $i ?>" style="display: <?= $i === 0 ? 'block' : 'none' ?>">
                  <div class="d-flex justify-content-between align-items-center">
                    <h5>
                      <button class="btn btn-link text-decoration-none p-0" type="button" data-bs-toggle="collapse" data-bs-target="#section-desc-<?= $i ?>">
                        <strong><?= esc($section['NAME']) ?></strong>
                      </button>
                    </h5>
                    <!-- Actions Widget -->
                    <div class="dropdown">
                      <button class="btn btn-sm btn-secondary" type="button" id="secMenuBtn<?= $section['ID'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="secMenuBtn<?= $section['ID'] ?>">
                        <li>
                          <a class="dropdown-item edit-section-btn" href="#"
                             data-id="<?= $section['ID'] ?>"
                             data-name="<?= esc($section['NAME'], 'attr') ?>"
                             data-desc="<?= esc($section['DESCRIPTION'], 'attr') ?>">
                            Edit Section
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <a class="dropdown-item text-danger delete-section-btn" href="#" data-id="<?= $section['ID'] ?>">
                            Delete Section
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <?php if (! empty($section['DESCRIPTION'])): ?>
                    <div class="collapse show" id="section-desc-<?= $i ?>">
                      <p class="text-muted small"><?= nl2br(esc($section['DESCRIPTION'])) ?></p>
                    </div>
                  <?php endif; ?>

                  <?php if (! empty($section['QUESTIONS'])): ?>
                    <?php foreach ($section['QUESTIONS'] as $q): ?>
                      <div class="mb-3">
                        <strong><?= esc($q['QUESTION']) ?></strong>
                        <div class="ms-3 mt-1">
                          <?php if (str_contains($q['TYPE'], 'Radio')): ?>
                            <?php foreach ($q['OPTIONS'] as $opt): ?>
                              <div><input type="radio" disabled> <?= esc($opt['OPTION_TEXT']) ?></div>
                            <?php endforeach; ?>
                          <?php elseif (str_contains($q['TYPE'], 'Check')): ?>
                            <?php foreach ($q['OPTIONS'] as $opt): ?>
                              <div><input type="checkbox" disabled> <?= esc($opt['OPTION_TEXT']) ?></div>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <input type="text" class="form-control" placeholder="Text input" disabled>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <p class="text-muted">No questions in this section.</p>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="d-flex justify-content-between mt-4">
              <button id="prev-section" class="btn btn-outline-secondary" disabled>Previous</button>
              <div id="section-progress" class="align-self-center small text-muted"></div>
              <button id="next-section" class="btn btn-outline-primary">Next</button>
            </div>
            <div class="text-end mt-3">
              <button id="submit-btn" class="btn btn-success" style="display: none;">Submit</button>
            </div>
          <?php else: ?>
            <p class="text-muted">No sections or questions found.</p>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="section-form">
        <div class="modal-header">
          <h5 class="modal-title">Add Section</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Section Name</label>
            <input type="text" class="form-control" id="section-name" required>
          </div>
          <div class="form-group mb-2">
            <label>Description (optional)</label>
            <textarea class="form-control" id="section-description" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Section</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="question-form">
        <div class="modal-header">
          <h5 class="modal-title">Add Question</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Section</label>
            <select class="form-control" id="section-id" required>
              <option value="">-- Select Section --</option>
              <?php foreach ($survey['SECTIONS'] as $section): ?>
                <option value="<?= $section['ID'] ?>"><?= esc($section['NAME']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group mb-2">
            <label>Question</label>
            <textarea class="form-control" id="question-text" rows="3" required></textarea>
          </div>
          <div class="form-group mb-2">
            <label>Answer Type</label>
            <select class="form-control" id="question-type" required>
              <option>Single Answer/Radio Button</option>
              <option>Multiple Answer/Check Boxes</option>
              <option>Text Field/ Text Area</option>
            </select>
          </div>
          <div class="form-group" id="option-wrapper" style="display: none;">
            <label>Options</label>
            <div id="option-list">
              <input type="text" class="form-control mb-2" placeholder="Option 1" required>
              <input type="text" class="form-control mb-2" placeholder="Option 2" required>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="add-option">+ Add Option</button>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save Question</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="edit-section-form" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-section-id">
        <div class="mb-3">
          <label for="edit-section-name" class="form-label">Section Name</label>
          <input type="text" id="edit-section-name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="edit-section-description" class="form-label">Description</label>
          <textarea id="edit-section-description" class="form-control" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- JS Dependencies -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Survey Show.js — pagination plus Add/Edit/Delete Section & Add Question handlers
document.addEventListener('DOMContentLoaded', () => {
  // ── Section Pagination ─────────────────────────────────────────
  const sections = document.querySelectorAll('.survey-section');
  const prevBtn   = document.getElementById('prev-section');
  const nextBtn   = document.getElementById('next-section');
  const progress  = document.getElementById('section-progress');
  const submitBtn = document.getElementById('submit-btn');
  let currentIndex = 0;

  function updateSectionView() {
    sections.forEach((s,i) => s.style.display = i === currentIndex ? 'block' : 'none');
    prevBtn.disabled = currentIndex === 0;
    nextBtn.style.display = currentIndex === sections.length - 1 ? 'none' : 'inline-block';
    submitBtn.style.display = currentIndex === sections.length - 1 ? 'inline-block' : 'none';
    progress.textContent = `Page ${currentIndex+1} of ${sections.length}`;
  }

  prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) { currentIndex--; updateSectionView(); }
  });
  nextBtn.addEventListener('click', () => {
    if (currentIndex < sections.length - 1) { currentIndex++; updateSectionView(); }
  });
  updateSectionView();

  // ── Add Section ───────────────────────────────────────────────
  document.getElementById('section-form').addEventListener('submit', async e => {
    e.preventDefault();
    const name = document.getElementById('section-name').value.trim();
    const desc = document.getElementById('section-description').value.trim();
    const res  = await fetch('<?= site_url('section/create') ?>', {
      method: 'POST',
      headers: { 
        'Content-Type':'application/json',
        'X-Requested-With':'XMLHttpRequest' 
      },
      body: JSON.stringify({
        survey_id: <?= (int)$survey['ID'] ?>,
        name, description: desc
      })
    });
    const json = await res.json();
    if (json.success) {
      location.reload();
    } else {
      alert(json.message || '❌ Failed to add section');
    }
  });

 // ── Edit Section ──────────────────────────────────────────────
  const editModal = new bootstrap.Modal(document.getElementById('editSectionModal'));
  document.querySelectorAll('.edit-section-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      document.getElementById('edit-section-id').value          = btn.dataset.id;
      document.getElementById('edit-section-name').value        = btn.dataset.name;
      document.getElementById('edit-section-description').value = btn.dataset.desc;
      editModal.show();
    });
  });

  document.getElementById('edit-section-form').addEventListener('submit', async e => {
    e.preventDefault();
    const id   = document.getElementById('edit-section-id').value;
    const name = document.getElementById('edit-section-name').value.trim();
    const desc = document.getElementById('edit-section-description').value.trim();
    const res  = await fetch(`<?= site_url('section/update') ?>/${id}`, {
      method: 'POST',               // ← changed from PUT
      headers: {
        'Content-Type':'application/json',
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify({ name, description: desc })
    });
    const json = await res.json();
    if (json.success) location.reload();
    else alert(json.message || '❌ Failed to update section');
  });

    // ── Delete Section ────────────────────────────────────────────
  document.querySelectorAll('.delete-section-btn').forEach(btn => {
  btn.addEventListener('click', async e => {
    e.preventDefault();
    if (!confirm('Delete this section?')) return;

    const res = await fetch(`<?= site_url('section/delete') ?>/${btn.dataset.id}`, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const json = await res.json();
    if (json.success) {
      location.reload();
    } else {
      alert(json.message || '❌ Failed to delete section');
    }
  });
});

  // ── Question Options UI ────────────────────────────────────────
  const questionType  = document.getElementById('question-type');
  const optionWrapper = document.getElementById('option-wrapper');
  const optionList    = document.getElementById('option-list');
  document.getElementById('add-option').addEventListener('click', () => {
    const inp = document.createElement('input');
    inp.type = 'text';
    inp.className = 'form-control mb-2';
    inp.placeholder = 'Option';
    optionList.appendChild(inp);
  });
  questionType.addEventListener('change', () => {
    const t = questionType.value;
    optionWrapper.style.display = t.includes('Radio') || t.includes('Check') ? 'block' : 'none';
  });

  // ── Add Question ──────────────────────────────────────────────
  document.getElementById('question-form').addEventListener('submit', async e => {
    e.preventDefault();
    const payload = {
      section_id: document.getElementById('section-id').value,
      survey_id:  <?= (int)$survey['ID'] ?>,
      question:   document.getElementById('question-text').value.trim(),
      type:       questionType.value,
      options:    Array.from(optionList.querySelectorAll('input'))
                         .map(i => i.value.trim()).filter(Boolean)
    };
    const res  = await fetch('<?= site_url('question/create') ?>', {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-Requested-With':'XMLHttpRequest'
      },
      body: JSON.stringify(payload)
    });
    const json = await res.json();
    if (json.success) {
      location.reload();
    } else {
      alert(json.message || '❌ Failed to add question');
    }
  });
});
</script>

<?= view('templates/footer') ?>

