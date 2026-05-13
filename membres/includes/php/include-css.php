   <!-- ===============================================-->
<!--    Favicons-->
<!-- ===============================================-->
<link rel="apple-touch-icon" sizes="180x180" href="../../assets/img/LOGO.jpg">
<link rel="icon" type="image/png" sizes="32x32" href="../../assets/img/LOGO.jpg">
<link rel="icon" type="image/png" sizes="16x16" href="../../assets/img/LOGO.jpg">
<link rel="shortcut icon" type="image/x-icon" href="../../assets/img/LOGO.jpg">
<link rel="manifest" href="assets/img/favicons/manifest.json">
<meta name="msapplication-TileImage" content="../../assets/img/LOGO.jpg">
<meta name="theme-color" content="#ffffff">
<script src="../anvdko/vendors/simplebar/simplebar.min.js"></script>
<script src="../anvdko/vendors/simplebar/simplebar.min.js"></script>
<script src="../anvdko/assets/js/config.js"></script>


<!-- ===============================================-->
<!--    Stylesheets-->
<!-- ===============================================-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
<link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="../anvdko/vendors/simplebar/simplebar.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
<link href="../anvdko/assets/css/theme-rtl.min.css" type="text/css" rel="stylesheet" id="style-rtl">
<link href="../anvdko/assets/css/theme.min.css" type="text/css" rel="stylesheet" id="style-default">
<link href="../anvdko/assets/css/user-rtl.min.css" type="text/css" rel="stylesheet" id="user-style-rtl">
<link href="../anvdko/assets/css/user.min.css" type="text/css" rel="stylesheet" id="user-style-default">
<script>
  var phoenixIsRTL = window.config.config.phoenixIsRTL;
  if (phoenixIsRTL) {
    var linkDefault = document.getElementById('style-default');
    var userLinkDefault = document.getElementById('user-style-default');
    linkDefault.setAttribute('disabled', true);
    userLinkDefault.setAttribute('disabled', true);
    document.querySelector('html').setAttribute('dir', 'rtl');
  } else {
    var linkRTL = document.getElementById('style-rtl');
    var userLinkRTL = document.getElementById('user-style-rtl');
    linkRTL.setAttribute('disabled', true);
    userLinkRTL.setAttribute('disabled', true);
  }
</script>
<link href="../anvdko/vendors/leaflet/leaflet.css" rel="stylesheet">
<link href="../anvdko/vendors/leaflet.markercluster/MarkerCluster.css" rel="stylesheet">
<link href="../anvdko/vendors/leaflet.markercluster/MarkerCluster.Default.css" rel="stylesheet">

<style type="text/css">
  .table th, table td{padding: 10px 5px !important; vertical-align: middle !important; font-size: 0.8rem;}

  .table th{white-space: nowrap;}

  .table .table-responsive table{white-space: nowrap !important;}

  .btn-xs{padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.2rem;}

  .no-caret::after {display: none !important;}
</style>
    <style type="text/css">
    	.container .card-body{padding: 5px; padding-bottom: 10px;} 
    	.divMenu table td img{width: 80px; height: 80px; border-radius: 8px;}
    	.divMenu table td div{font-weight: bold; position: relative; top: -10px;}
    	.divMenu a.ratio{border-radius: 12px;}

		.divMenu .card-body .ratio-1x1{transition: transform .2s;}
		.divMenu .card-body .ratio-1x1:hover{transform: scale(1.1);}
    </style>