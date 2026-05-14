<footer class="footer border-top border-translucent bg-body-emphasis py-2" style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;">
  <div class="container-fluid px-3">
    <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-between gap-2">
      <div class="d-flex align-items-center gap-2">
        <img src="../assets/img/LOGO.jpg" alt="ANVDKO" width="22" height="22" class="rounded" style="object-fit:cover;">
        <span class="text-body-secondary small">
          © <?php echo date('Y'); ?> <strong class="text-body">ANVDKO</strong>
          <span class="d-none d-md-inline">— Nouvelle Vision pour le Développement de Kouakou Oussoukro</span>
        </span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <?php if (!empty($_SESSION['configuration']['contact1'])): ?>
          <span class="text-body-tertiary small"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($_SESSION['configuration']['contact1']); ?></span>
        <?php endif; ?>
        <a href="mailto:anvdkocontact@gmail.com" class="text-body-tertiary small text-decoration-none">
          <i class="fas fa-envelope me-1"></i><span class="d-none d-lg-inline">anvdkocontact@gmail.com</span>
        </a>
        <?php if (!empty($_SESSION['utilisateur']['pseudo'])): ?>
          <span class="badge bg-primary bg-opacity-10 text-primary small"><i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($_SESSION['utilisateur']['pseudo']); ?></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
</footer>