
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="../anvdko/vendors/popper/popper.min.js"></script>
<script src="../anvdko/vendors/bootstrap/bootstrap.min.js"></script>
<script src="../anvdko/vendors/anchorjs/anchor.min.js"></script>
<script src="../anvdko/vendors/is/is.min.js"></script>
<script src="../anvdko/vendors/fontawesome/all.min.js"></script>
<script src="../anvdko/vendors/lodash/lodash.min.js"></script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
<script src="../anvdko/vendors/list.js/list.min.js"></script>
<script src="../anvdko/vendors/feather-icons/feather.min.js"></script>
<script src="../anvdko/vendors/dayjs/dayjs.min.js"></script>
<script src="../anvdko/assets/js/phoenix.js"></script>
<script src="../anvdko/vendors/echarts/echarts.min.js"></script>
<script src="../anvdko/vendors/leaflet/leaflet.js"></script>
<script src="../anvdko/vendors/leaflet.markercluster/leaflet.markercluster.js"></script>
<script src="../anvdko/vendors/leaflet.tilelayer.colorfilter/leaflet-tilelayer-colorfilter.min.js"></script>
<script src="../anvdko/assets/js/ecommerce-dashboard.js"></script>
<script>
(function(){
  if (document.getElementById('anvdko-page-loader')) return;
  var css = '#anvdko-page-loader{position:fixed;inset:0;background:linear-gradient(135deg,#f8f6ff,#fff);z-index:20000;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .35s ease;}' +
            '#anvdko-page-loader.anvdko-hide{opacity:0;pointer-events:none;}';
  var s = document.createElement('style'); s.textContent = css; document.head.appendChild(s);
  var d = document.createElement('div');
  d.id = 'anvdko-page-loader';
  d.innerHTML = '<img src="../assets/img/LOGO.jpg" width="80" height="80" class="rounded-circle mb-3 shadow" style="object-fit:cover" alt="ANVDKO">' +
                '<div class="spinner-border" style="color:#4a148c;width:2.5rem;height:2.5rem;" role="status"><span class="visually-hidden">Chargement…</span></div>';
  document.body.appendChild(d);
  window.addEventListener('load', function(){
    d.classList.add('anvdko-hide');
    setTimeout(function(){ d.style.display = 'none'; }, 400);
  });
})();
</script>