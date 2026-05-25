<?php
$export_title = $export_title ?? (isset($page_export_title) ? $page_export_title : 'Export ANVDKO');
?>
<div class="anvdko-export-bar no-print d-flex flex-wrap align-items-center gap-2 mb-3" data-export-title="<?= htmlspecialchars($export_title) ?>">
  <span class="text-body-secondary small me-1"><i class="fas fa-download me-1"></i>Exporter :</span>
  <button type="button" class="btn btn-sm btn-success anvdko-export-btn" data-format="excel" title="Excel">
    <i class="fas fa-file-excel me-1"></i> Excel
  </button>
  <button type="button" class="btn btn-sm btn-primary anvdko-export-btn" data-format="word" title="Word">
    <i class="fas fa-file-word me-1"></i> Word
  </button>
  <button type="button" class="btn btn-sm btn-danger anvdko-export-btn" data-format="ppt" title="PowerPoint">
    <i class="fas fa-file-powerpoint me-1"></i> PowerPoint
  </button>
</div>
