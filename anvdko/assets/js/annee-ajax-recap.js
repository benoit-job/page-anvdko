/**
 * Changement d'année (ou motif) en AJAX sans bouton Valider.
 * Utiliser : select.annee-ajax-recap[data-ajax-url]
 */
(function () {
  'use strict';

  function postFormData(url, formData) {
    return fetch(url, { method: 'POST', body: formData }).then(function (r) { return r.json(); });
  }

  function getFormDataFromPage(cfg) {
    var fd = new FormData();
    var anneeEl = document.querySelector(cfg.anneeSelector || '.annee-ajax-recap');
    if (anneeEl) {
      fd.append(anneeEl.name || 'annee', anneeEl.value);
    }
    var motifEl = document.querySelector(cfg.motifSelector || '#id_motif');
    if (motifEl && motifEl.value) {
      fd.append(motifEl.name || 'id_motif', motifEl.value);
    }
    return fd;
  }

  function bindRecap(cfg) {
    var url = cfg.url;
    if (!url) {
      return;
    }

    function reload() {
      var target = document.querySelector(cfg.targetSelector || '#recap-ajax-content');
      if (target) {
        target.style.opacity = '0.5';
      }
      postFormData(url, getFormDataFromPage(cfg))
        .then(function (data) {
          if (!data.success) {
            throw new Error(data.message || 'Erreur');
          }
          if (cfg.onSuccess) {
            cfg.onSuccess(data);
          } else if (target) {
            if (data.stats_html) {
              var stats = document.querySelector(cfg.statsTarget || '#recap-stats-zone');
              if (stats) {
                stats.innerHTML = data.stats_html;
              }
            }
            if (data.content_html) {
              target.innerHTML = data.content_html;
            }
            if (data.table_html) {
              var tbody = document.querySelector(cfg.tableBodySelector || '#recap-table-body');
              if (tbody) {
                tbody.innerHTML = data.table_html;
              }
            }
          }
          if (target) {
            target.style.opacity = '1';
          }
        })
        .catch(function (err) {
          if (target) {
            target.style.opacity = '1';
          }
          if (typeof afficherToast === 'function') {
            afficherToast(err.message, 'top', 'danger', 3500);
          }
        });
    }

    document.querySelectorAll(cfg.anneeSelector || '.annee-ajax-recap').forEach(function (el) {
      el.addEventListener('change', reload);
    });
    if (cfg.motifSelector) {
      var motif = document.querySelector(cfg.motifSelector);
      if (motif) {
        motif.addEventListener('change', reload);
      }
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.anvdkoRecapAjaxConfig) {
      bindRecap(window.anvdkoRecapAjaxConfig);
    }
  });

  window.anvdkoBindRecapAjax = bindRecap;
})();
