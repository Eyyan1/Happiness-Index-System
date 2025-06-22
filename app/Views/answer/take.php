<?= view('templates/header') ?>

<div class="container mt-4">
    <h3><?= esc($survey['TITLE']) ?></h3>
    <p><?= nl2br(esc($survey['DESCRIPTION'])) ?></p>

    <form action="<?= site_url('answer/'.$survey['ID']) ?>" method="post">
        <?= csrf_field() ?>

        <!-- Section Container -->
        <div id="section-container">
            <?php foreach ($survey['SECTIONS'] as $index => $section): ?>
                <div class="section mb-3"
                     data-index="<?= $index ?>"
                     style="display: <?= $index === 0 ? 'block' : 'none' ?>;">
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <strong><?= esc($section['NAME']) ?></strong>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($section['DESCRIPTION'])): ?>
                                <p><?= nl2br(esc($section['DESCRIPTION'])) ?></p>
                            <?php endif; ?>

                            <?php foreach ($section['QUESTIONS'] as $q): ?>
                                <div class="mb-3">
                                    <label class="form-label">
                                      <strong><?= esc($q['QUESTION']) ?></strong>
                                    </label>

                                    <?php if (str_contains($q['TYPE'], 'Radio')): ?>
                                        <?php foreach ($q['OPTIONS'] as $opt): ?>
                                            <div>
                                                <input
                                                  type="radio"
                                                  name="answers[<?= $q['ID'] ?>]"
                                                  value="<?= esc($opt['OPTION_TEXT']) ?>"
                                                  required>
                                                <?= esc($opt['OPTION_TEXT']) ?>
                                            </div>
                                        <?php endforeach; ?>

                                    <?php elseif (str_contains($q['TYPE'], 'Check')): ?>
                                        <?php foreach ($q['OPTIONS'] as $opt): ?>
                                            <div>
                                                <input
                                                  type="checkbox"
                                                  name="answers[<?= $q['ID'] ?>][]"
                                                  value="<?= esc($opt['OPTION_TEXT']) ?>">
                                                <?= esc($opt['OPTION_TEXT']) ?>
                                            </div>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <textarea
                                          name="answers[<?= $q['ID'] ?>]"
                                          class="form-control"
                                          placeholder="Your answer"
                                        ></textarea>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <button
              type="button"
              id="prev-section"
              class="btn btn-outline-secondary"
              disabled
            >Previous</button>

            <div id="section-progress" class="align-self-center small text-muted"></div>

            <button
              type="button"
              id="next-section"
              class="btn btn-outline-primary"
            >Next</button>
        </div>

        <div class="text-end mt-3">
            <button
              id="submit-btn"
              type="submit"
              class="btn btn-success"
              style="display: none;"
            >Submit Survey</button>
        </div>
    </form>
</div>

<?= view('templates/footer') ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sections = document.querySelectorAll('.section');
  const prevBtn  = document.getElementById('prev-section');
  const nextBtn  = document.getElementById('next-section');
  const progress = document.getElementById('section-progress');
  const submitBtn= document.getElementById('submit-btn');
  let idx = 0;

  function update() {
    sections.forEach((s,i)=> s.style.display = i===idx ? 'block' : 'none');
    prevBtn.disabled = idx===0;
    nextBtn.style.display = idx===sections.length-1? 'none':'inline-block';
    submitBtn.style.display = idx===sections.length-1? 'inline-block':'none';
    progress.textContent = `Page ${idx+1} of ${sections.length}`;
  }
  prevBtn.onclick = ()=> { if(idx>0) idx--, update(); };
  nextBtn.onclick = ()=> { if(idx<sections.length-1) idx++, update(); };
  update();
});
</script>
