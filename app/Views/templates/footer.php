</div><!-- /.container-fluid -->
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->

  <!-- Footer -->
  <footer class="main-footer text-center">
    <strong>© <?= date('Y') ?> UMT Happiness Index System</strong>
  </footer>
</div><!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery (loaded automatically by auto-include below, but you may leave this to ensure order) -->
<script src="<?= base_url('assets/plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<!-- OverlayScrollbars -->
<script src="<?= base_url('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/dist/js/adminlte.min.js') ?>"></script>

<!-- DataTables & Responsive -->
<script src="<?= base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>

<!-- Auto-include all other plugin JS -->
<?php
  $pluginJsDir = FCPATH . 'assets/plugins';
  if (is_dir($pluginJsDir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pluginJsDir));
    foreach ($iterator as $file) {
      if ($file->isFile() && strtolower($file->getExtension()) === 'js') {
        $relative = str_replace([FCPATH, '\\'], ['', '/'], $file->getPathname());
        // Skip ones already loaded above
        if (preg_match('#/(jquery|bootstrap|overlayScrollbars|adminlte|datatables)/#', $relative)) {
          continue;
        }
        echo '<script src="' . base_url($relative) . '"></script>' . "\n";
      }
    }
  }
?>

<script>
$(function(){
  // … your other page-specific scripts …

  // Mark all unread notifications as read when bell icon is clicked
  $('.main-header .fa-bell').closest('a').on('click', function() {
    fetch("<?= site_url('notifications/mark-read') ?>", {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(() => {
      // remove the little badge count
      $(this).find('.navbar-badge').remove();
    });
  });
});
</script>
</body>
</html>
