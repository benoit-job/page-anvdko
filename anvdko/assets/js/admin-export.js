/**
 * Export Excel / Word / PowerPoint depuis la zone [data-export-region] ou les tableaux principaux.
 */
(function () {
  'use strict';

  function getExportRegion() {
    return document.querySelector('[data-export-region]') || document.querySelector('.content') || document.body;
  }

  function getExportTitle() {
    var bar = document.querySelector('.anvdko-export-bar');
    if (bar && bar.getAttribute('data-export-title')) {
      return bar.getAttribute('data-export-title');
    }
    var h = document.querySelector('.content h2, .content h3');
    return h ? h.textContent.trim() : 'Export ANVDKO';
  }

  function collectTables(region) {
    var tables = region.querySelectorAll('table');
    if (tables.length) {
      return Array.prototype.slice.call(tables);
    }
    var clone = region.cloneNode(true);
    clone.querySelectorAll('.no-print, .anvdko-export-bar, script, style, .spinner-border').forEach(function (el) {
      el.remove();
    });
    var wrap = document.createElement('div');
    wrap.innerHTML = clone.innerHTML.trim() || '<p>Aucune donnée à exporter.</p>';
    var t = document.createElement('table');
    t.innerHTML = '<tr><td>' + wrap.innerHTML.replace(/<\/td>\s*<td/g, '</td><td') + '</td></tr>';
    return [t];
  }

  function tableToMatrix(table) {
    var rows = [];
    table.querySelectorAll('tr').forEach(function (tr) {
      var cells = [];
      tr.querySelectorAll('th, td').forEach(function (cell) {
        var input = cell.querySelector('input, select, textarea');
        cells.push((input ? (input.value || input.textContent) : cell.innerText).trim().replace(/\s+/g, ' '));
      });
      if (cells.length) {
        rows.push(cells);
      }
    });
    return rows;
  }

  function downloadBlob(blob, filename) {
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(function () {
      URL.revokeObjectURL(a.href);
      a.remove();
    }, 200);
  }

  function slugify(s) {
    return (s || 'export').replace(/[^\w\-]+/g, '_').substring(0, 80);
  }

  function exportExcel(title) {
    var region = getExportRegion();
    var tables = collectTables(region);
    var lines = [];
    lines.push(title);
    lines.push('');
    tables.forEach(function (table, idx) {
      if (tables.length > 1) {
        lines.push('Tableau ' + (idx + 1));
      }
      tableToMatrix(table).forEach(function (row) {
        lines.push(row.map(function (c) {
          return '"' + String(c).replace(/"/g, '""') + '"';
        }).join(';'));
      });
      lines.push('');
    });
    var bom = '\uFEFF';
    var blob = new Blob([bom + lines.join('\r\n')], { type: 'application/vnd.ms-excel;charset=utf-8' });
    downloadBlob(blob, slugify(title) + '.xls');
  }

  function exportWord(title) {
    var region = getExportRegion();
    var tables = collectTables(region);
    var body = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"><title>' + title + '</title></head><body>';
    body += '<h1>' + title + '</h1><p>Exporté le ' + new Date().toLocaleString('fr-FR') + '</p>';
    tables.forEach(function (table) {
      body += '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;width:100%;">';
      table.querySelectorAll('tr').forEach(function (tr) {
        body += '<tr>';
        tr.querySelectorAll('th, td').forEach(function (cell) {
          var tag = cell.tagName.toLowerCase() === 'th' ? 'th' : 'td';
          body += '<' + tag + '>' + cell.innerText + '</' + tag + '>';
        });
        body += '</tr>';
      });
      body += '</table><br/>';
    });
    body += '</body></html>';
    var blob = new Blob(['\ufeff', body], { type: 'application/msword' });
    downloadBlob(blob, slugify(title) + '.doc');
  }

  function exportPpt(title) {
    var region = getExportRegion();
    var tables = collectTables(region);
    var slides = '';
    slides += '<?xml version="1.0" encoding="UTF-8"?>';
    slides += '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Slide</x:Name></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    slides += '<html><head><meta charset="utf-8"><title>' + title + '</title>';
    slides += '<style>body{font-family:Calibri,Arial,sans-serif;} .slide{page-break-after:always;padding:24px;} table{border-collapse:collapse;width:100%;font-size:12px;} th,td{border:1px solid #333;padding:6px;}</style></head><body>';
    slides += '<div class="slide"><h1 style="color:#4a148c;">' + title + '</h1><p>' + new Date().toLocaleString('fr-FR') + '</p></div>';
    tables.forEach(function (table, idx) {
      slides += '<div class="slide"><h2>Tableau ' + (idx + 1) + '</h2>' + table.outerHTML + '</div>';
    });
    slides += '</body></html>';
    var blob = new Blob(['\ufeff', slides], { type: 'application/vnd.ms-powerpoint' });
    downloadBlob(blob, slugify(title) + '.ppt');
  }

  function handleExport(format) {
    var title = getExportTitle();
    try {
      if (format === 'excel') {
        exportExcel(title);
      } else if (format === 'word') {
        exportWord(title);
      } else if (format === 'ppt') {
        exportPpt(title);
      }
      if (typeof afficherToast === 'function') {
        afficherToast('Export ' + format.toUpperCase() + ' généré.', 'top', 'success', 2500);
      }
    } catch (e) {
      console.error(e);
      if (typeof afficherToast === 'function') {
        afficherToast('Erreur export : ' + e.message, 'top', 'danger', 4000);
      } else {
        alert('Erreur export : ' + e.message);
      }
    }
  }

  document.addEventListener('click', function (ev) {
    var btn = ev.target.closest('.anvdko-export-btn');
    if (!btn) {
      return;
    }
    ev.preventDefault();
    handleExport(btn.getAttribute('data-format'));
  });

  document.addEventListener('DOMContentLoaded', function () {
    var region = document.querySelector('[data-export-region]');
    if (!region || document.querySelector('.anvdko-export-bar')) {
      return;
    }
    var title = getExportTitle();
    var bar = document.createElement('div');
    bar.className = 'anvdko-export-bar no-print d-flex flex-wrap align-items-center gap-2 mb-3';
    bar.setAttribute('data-export-title', title);
    bar.innerHTML =
      '<span class="text-body-secondary small me-1"><i class="fas fa-download me-1"></i>Exporter :</span>' +
      '<button type="button" class="btn btn-sm btn-success anvdko-export-btn" data-format="excel"><i class="fas fa-file-excel me-1"></i> Excel</button>' +
      '<button type="button" class="btn btn-sm btn-primary anvdko-export-btn" data-format="word"><i class="fas fa-file-word me-1"></i> Word</button>' +
      '<button type="button" class="btn btn-sm btn-danger anvdko-export-btn" data-format="ppt"><i class="fas fa-file-powerpoint me-1"></i> PowerPoint</button>';
    var anchor = region.querySelector('.content > .pb-5, .content > .pb-6, .pb-5, .pb-6');
    if (anchor) {
      anchor.insertBefore(bar, anchor.firstChild);
    } else {
      region.insertBefore(bar, region.firstChild);
    }
  });
})();
