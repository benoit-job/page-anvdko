<?php

/**
 * Upload et sélection d'images côté administration (remplace les champs « URL image »).
 */

function anvdko_admin_upload_image_file($fieldName)
{
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }
    if ($_FILES[$fieldName]['size'] > 5 * 1024 * 1024) {
        return null;
    }

    $dest = createPathFile('../fichiers/uploads/') . uniqid('img_') . '.' . $ext;
    if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $dest)) {
        return ltrim(str_replace(['../', '\\'], ['', '/'], $dest), '/');
    }
    return null;
}

function anvdko_admin_resolve_image_path($fileField, $hiddenField = 'image_courante', $required = false)
{
    $uploaded = anvdko_admin_upload_image_file($fileField);
    if ($uploaded !== null && $uploaded !== '') {
        return $uploaded;
    }
    $current = trim((string) ($_POST[$hiddenField] ?? ''));
    if ($current !== '') {
        return $current;
    }
    if ($required) {
        return null;
    }
    return '';
}

function anvdko_admin_image_sql($bdd, $fileField, $hiddenField = 'image_courante', $required = false)
{
    $path = anvdko_admin_resolve_image_path($fileField, $hiddenField, $required);
    if ($path === null) {
        return null;
    }
    return mysqli_real_escape_string($bdd, $path);
}

function anvdko_admin_preview_src($storedPath)
{
    $path = trim((string) $storedPath);
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isLocal = (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
    $base = $isLocal ? '/anvdko' : '';

    if ($path === '') {
        return $base . '/assets/img/LOGO.jpg';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (strpos($path, 'fichiers/') === 0) {
        return $base . '/' . $path;
    }
    if (strpos($path, 'assets/') === 0) {
        return $base . '/' . $path;
    }
    if ($isLocal && strpos($path, '/') !== 0) {
        return $base . '/fichiers/uploads/' . $path;
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * @param string $fileInputName name de l'input file
 * @param string $storedPath chemin enregistré en BDD
 * @param string $hiddenName champ hidden pour conserver l'image actuelle
 */
function anvdko_admin_image_picker_html($fileInputName, $storedPath = '', $hiddenName = 'image_courante', $label = 'Image', $required = false, $sm = false)
{
    $src = htmlspecialchars(anvdko_admin_preview_src($storedPath), ENT_QUOTES, 'UTF-8');
    $hid = htmlspecialchars($hiddenName, ENT_QUOTES, 'UTF-8');
    $path = htmlspecialchars((string) $storedPath, ENT_QUOTES, 'UTF-8');
    $fname = htmlspecialchars($fileInputName, ENT_QUOTES, 'UTF-8');
    $req = ($required && $storedPath === '') ? ' required' : '';
    $cls = $sm ? 'form-control form-control-sm' : 'form-control';
    $lblCls = $sm ? 'form-label small mb-1' : 'form-label';

    return '<div class="anvdko-img-picker mb-2" data-picker>
        <label class="' . $lblCls . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</label>
        <input type="hidden" name="' . $hid . '" value="' . $path . '">
        <div class="d-flex align-items-start gap-2 flex-wrap">
            <div class="border rounded overflow-hidden flex-shrink-0" style="width:100px;height:72px;background:#f8f9fa;">
                <img src="' . $src . '" alt="" class="anvdko-img-preview w-100 h-100" style="object-fit:cover;" data-fallback="/anvdko/assets/img/LOGO.jpg" onerror="this.onerror=null;this.src=this.dataset.fallback||\'/anvdko/assets/img/LOGO.jpg\';">
            </div>
            <div class="flex-grow-1" style="min-width:180px;">
                <input type="file" name="' . $fname . '" class="' . $cls . ' anvdko-img-file" accept="image/jpeg,image/png,image/gif,image/webp"' . $req . '>
                <small class="text-muted d-block mt-1">Choisir une image (JPG, PNG, GIF, WebP — max. 5 Mo).</small>
            </div>
        </div>
    </div>';
}

function anvdko_admin_image_picker_script()
{
    return <<<'JS'
<script>
document.addEventListener('change', function(e) {
  if (!e.target.classList.contains('anvdko-img-file')) return;
  const file = e.target.files[0];
  if (!file) return;
  const picker = e.target.closest('[data-picker]');
  if (!picker) return;
  const img = picker.querySelector('.anvdko-img-preview');
  if (!img) return;
  const reader = new FileReader();
  reader.onload = function(ev) { img.src = ev.target.result; };
  reader.readAsDataURL(file);
});
</script>
JS;
}
