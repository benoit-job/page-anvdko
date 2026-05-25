(function () {
  'use strict';

  function loadCotisationsStats(annee) {
    var zones = ['adhesion', 'mensuelle', 'exceptionnelle'];
    zones.forEach(function (z) {
      var el = document.getElementById('stats-' + z);
      if (el) {
        el.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
      }
    });

    fetch('ajax/cotisations_stats_ajax.php?annee=' + encodeURIComponent(annee))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (!data.success) {
          throw new Error(data.message || 'Erreur chargement');
        }
        zones.forEach(function (z) {
          var el = document.getElementById('stats-' + z);
          if (el && data.html && data.html[z]) {
            el.innerHTML = data.html[z];
          }
        });
        var label = document.getElementById('cotisations-annee-label');
        if (label) {
          label.textContent = 'Année ' + data.annee;
        }
      })
      .catch(function (err) {
        zones.forEach(function (z) {
          var el = document.getElementById('stats-' + z);
          if (el) {
            el.innerHTML = '<p class="text-danger small mb-0">' + err.message + '</p>';
          }
        });
      });
  }

  function initAnneeSelect() {
    var sel = document.getElementById('cotisations-annee');
    if (!sel) {
      return;
    }
    sel.addEventListener('change', function () {
      loadCotisationsStats(sel.value);
    });
    loadCotisationsStats(sel.value);
  }

  document.addEventListener('DOMContentLoaded', initAnneeSelect);
  window.anvdkoLoadCotisationsStats = loadCotisationsStats;
})();
