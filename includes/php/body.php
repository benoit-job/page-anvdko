<?php
if (!isset($anvdko_site)) {
    require_once __DIR__ . '/../../include/php/connexion_bdd.php';
    require_once __DIR__ . '/../../include/php/site_public.php';
    $anvdko_site = anvdko_load_site_data($bdd, 1);
}
$slides = $anvdko_site['slides'];
$about = $anvdko_site['about'];
$cartes = $anvdko_site['cartes'];
$bureau = $anvdko_site['bureau'];
$galerie = $anvdko_site['galerie'];
$faq_extra = $anvdko_site['faq_extra'];
$stats = $anvdko_site['stats'];
$img_about = anvdko_public_img_url($about['image_url'] ?? 'assets/img/reunion1.jpg');
$pdf_url = anvdko_public_img_url($about['pdf_url'] ?? 'documents/Imprimer_badges.pdf');
$points = $about['points_liste'] ?? [];
if (!is_array($points)) {
    $points = [];
}
?>
<!-- ======= Hero Section ======= -->
  <section id="hero">
      <div id="heroCarousel" data-bs-interval="5000" class="carousel slide carousel-fade" data-bs-ride="carousel">
      <div class="carousel-inner" role="listbox">
        <?php foreach ($slides as $i => $slide):
          $bg = anvdko_public_img_url($slide['image_url'] ?? '');
          $titre_slide = $slide['titre'] ?? '';
          $texte_slide = $slide['texte'] ?? '';
          $lien = $slide['lien_bouton'] ?? '#about';
          $btn = $slide['texte_bouton'] ?? 'En savoir plus';
        ?>
        <div class="carousel-item<?php echo $i === 0 ? ' active' : ''; ?>" style="background-image: url(<?php echo anvdko_esc_attr($bg); ?>);">
          <div class="carousel-container">
            <div class="carousel-content animate__animated animate__fadeInUp text-center">
              <h2><?php echo $titre_slide; ?></h2>
              <p><?php echo anvdko_esc_html($texte_slide); ?></p>
              <div class="text-center"><a href="<?php echo anvdko_esc_attr($lien); ?>" class="btn-get-started"><?php echo anvdko_esc_html($btn); ?></a></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <a class="carousel-control-prev" href="#heroCarousel" role="button" data-bs-slide="prev">
        <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
      </a>
      <a class="carousel-control-next" href="#heroCarousel" role="button" data-bs-slide="next">
        <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
      </a>
      <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
      </div>
  </section>

  <main id="main">
      <section id="about" class="about-us">
        <div class="container mon-style-doux">
          <div class="container p-4 p-md-5" data-aos="fade-up">
            <div class="row content align-items-center mb-5">
              <div class="col-lg-6 col-12" data-aos="fade-right">
                <h2 style="font-size: 23px; color: #fff;"><?php echo anvdko_esc_html($about['qui_titre'] ?? 'Qui sommes-nous ?'); ?></h2>
                <h3 class="fst-italic"><?php echo anvdko_esc_html($about['qui_sous_titre'] ?? ''); ?></h3>
                <p><?php echo nl2br(anvdko_esc_html($about['qui_texte'] ?? '')); ?></p>
              </div>
              <div class="col-lg-6 col-12 text-center" data-aos="fade-left">
                <img src="<?php echo anvdko_esc_attr($img_about); ?>" class="img-fluid rounded shadow-lg" alt="Association ANVDKO" style="max-width: 100%; height: auto;">
              </div>
            </div>
            <div class="row content">
              <div class="col-12">
                <h4><?php echo anvdko_esc_html($about['mission_titre'] ?? 'Notre mission'); ?></h4>
                <p><?php echo nl2br(anvdko_esc_html($about['mission_texte'] ?? '')); ?></p>
                <h4><?php echo anvdko_esc_html($about['equipe_titre'] ?? 'Notre équipe'); ?></h4>
                <p><?php echo nl2br(anvdko_esc_html($about['equipe_texte'] ?? '')); ?></p>
                <ul>
                  <?php foreach ($points as $pt): if (trim((string)$pt) === '') continue; ?>
                  <li><i class="ri-check-double-line" style="color: orange;"></i> <?php echo anvdko_esc_html($pt); ?></li>
                  <?php endforeach; ?>
                </ul>
                <?php if (!empty($about['citation'])): ?>
                <p class="fst-italic p-3 p-md-4"><?php echo anvdko_esc_html($about['citation']); ?></p>
                <?php endif; ?>
                <div class="text-center mt-4">
                  <a href="<?php echo anvdko_esc_attr($pdf_url); ?>" target="_blank" class="btn btn-warning">Télécharger statuts et règlement intérieur (PDF)</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="Actualites" class="activities-events" style="color: #090A2EFF;">
        <div class="container" data-aos="fade-up">
          <div class="section-title text-center">
            <h2>Actualités & Événements</h2>
            <p>Suivez les temps forts de l'ANVDKO : réunions, projets et événements à venir.</p>
          </div>
          <div class="row justify-content-center">
            <?php foreach ($cartes as $idx => $carte):
              $cid = 'carte-' . ($carte['id'] ?? $idx);
              $img_c = anvdko_public_img_url($carte['image_url'] ?? '');
              $titre_c = $carte['titre'] ?? '';
              $resume_c = $carte['resume'] ?? '';
              $complet_c = $carte['contenu_complet'] ?? $resume_c;
            ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="zoom-in" data-aos-delay="<?php echo min(500, $idx * 100); ?>">
              <div class="card shadow-lg text-center">
                <img src="<?php echo anvdko_esc_attr($img_c); ?>" class="card-img-top img-fixed" alt="<?php echo anvdko_esc_attr($titre_c); ?>">
                <div class="card-body">
                  <h5 class="card-title"><?php echo anvdko_esc_html($titre_c); ?></h5>
                  <p class="card-text"><?php echo anvdko_esc_html($resume_c); ?></p>
                  <button type="button" class="btn d-flex justify-content-end chinois border-0 bg-transparent p-0 ms-auto btn-lire-plus"
                    style="color:blue"
                    data-titre="<?php echo anvdko_esc_attr($titre_c); ?>"
                    data-contenu="<?php echo anvdko_esc_attr($complet_c); ?>"
                    data-image="<?php echo anvdko_esc_attr($img_c); ?>">Lire plus</button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section id="stats" class="stats section-bg">
        <div class="container mon-style-doux2 pt-4" data-aos="fade-up">
          <div class="section-title text-center">
            <h2 class="text-white">Nos Statistiques</h2>
            <p>Chiffres clés de l'ANVDKO illustrant notre engagement et nos résultats.</p>
          </div>
          <div class="row justify-content-center p-3">
            <div class="col-lg-3 col-md-6 col-6 mb-3">
              <div class="stat-box bg-white rounded p-3 mx-1 text-center">
                <i class="bi bi-people-fill" style="font-size: 40px;"></i>
                <h3><span class="counter" data-target="<?php echo max(0, (int)$stats['membres']); ?>">0</span>+</h3>
                <p>Membres engagés</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-3">
              <div class="stat-box bg-white rounded p-3 mx-1 text-center">
                <i class="bi bi-calendar-event-fill" style="font-size: 40px;"></i>
                <h3><span class="counter" data-target="<?php echo max(0, (int)$stats['evenements']); ?>">0</span>+</h3>
                <p>Événements & rencontres</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-3">
              <div class="stat-box bg-white rounded p-3 mx-1 text-center">
                <i class="bi bi-lightbulb-fill" style="font-size: 40px;"></i>
                <h3><span class="counter" data-target="<?php echo max(0, (int)$stats['projets']); ?>">0</span>+</h3>
                <p>Projets en cours</p>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 col-6 mb-3">
              <div class="stat-box bg-white rounded p-3 mx-1 text-center">
                <i class="bi bi-hand-thumbs-up-fill" style="font-size: 40px;"></i>
                <h3><span class="counter" data-target="<?php echo max(0, (int)$stats['prets']); ?>">0</span>+</h3>
                <p>Prêts accordés</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="team" class="team section">
        <div class="container section-title" data-aos="fade-up">
          <h2>Le Bureau Exécutif</h2>
          <p>Découvrez les membres clés de l'équipe dirigeante de l'ANVDKO, engagés pour faire avancer notre mission.</p>
        </div>
        <div class="container">
          <div class="row gy-4">
            <?php foreach ($bureau as $idx => $m):
              $photo = anvdko_public_img_url($m['image_url'] ?? '');
            ?>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($idx + 1) * 100; ?>">
              <div class="member">
                <img src="<?php echo anvdko_esc_attr($photo); ?>" class="img-fluid img-square" alt="<?php echo anvdko_esc_attr($m['nom'] ?? ''); ?>">
                <div class="member-info">
                  <div class="member-info-content">
                    <h4><?php echo anvdko_esc_html($m['nom'] ?? ''); ?></h4>
                    <span><?php echo anvdko_esc_html($m['role_label'] ?? ''); ?></span>
                    <div class="social">
                      <?php if (!empty($m['linkedin'])): ?><a href="<?php echo anvdko_esc_attr($m['linkedin']); ?>" target="_blank" rel="noopener"><i class="bi bi-linkedin"></i></a><?php endif; ?>
                      <?php if (!empty($m['twitter'])): ?><a href="<?php echo anvdko_esc_attr($m['twitter']); ?>" target="_blank" rel="noopener"><i class="bi bi-twitter"></i></a><?php endif; ?>
                      <?php if (!empty($m['facebook'])): ?><a href="<?php echo anvdko_esc_attr($m['facebook']); ?>" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a><?php endif; ?>
                      <?php if (!empty($m['instagram'])): ?><a href="<?php echo anvdko_esc_attr($m['instagram']); ?>" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a><?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section id="gallery" class="gallery">
        <div class="container" data-aos="fade-up">
          <div class="section-title text-center">
            <h2>Notre Galerie</h2>
            <p>Revivez nos meilleurs moments à travers ces images.</p>
          </div>
          <div class="row g-4">
            <?php
            $galerie_affich = $galerie;
            if (count($galerie_affich) === 0) {
                $galerie_affich = [
                    ['titre' => 'Assemblée Générale', 'image_url' => 'assets/img/slide/reunion.png'],
                    ['titre' => 'Réunion des membres', 'image_url' => 'assets/img/slide/reunion2.jpg'],
                    ['titre' => 'Journée de sensibilisation', 'image_url' => 'assets/img/slide/reunion1.jpg'],
                ];
            }
            foreach ($galerie_affich as $idx => $g):
              $gimg = anvdko_public_img_url($g['image_url'] ?? '');
              $gtitre = $g['titre'] ?? 'Photo';
              $gdesc = $g['description'] ?? $gtitre;
            ?>
            <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="<?php echo min(500, $idx * 100); ?>">
              <div class="gallery-item">
                <a href="<?php echo anvdko_esc_attr($gimg); ?>" class="glightbox" data-gallery="gallery" data-title="<?php echo anvdko_esc_attr($gtitre . ' — ' . $gdesc); ?>">
                  <img src="<?php echo anvdko_esc_attr($gimg); ?>" class="img-fluid img-fixed rounded" alt="<?php echo anvdko_esc_attr($gtitre); ?>">
                </a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section id="faq" class="faq">
        <div class="container" data-aos="fade-up">
          <div class="section-title text-center">
            <h2>Questions Fréquemment Posées</h2>
            <p>Vous avez des questions ? Voici les réponses aux questions les plus courantes concernant l'ANVDKO.</p>
          </div>
          <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true">Qu'est-ce que l'ANVDKO et quels sont ses objectifs ?</button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">L'Association de la Nouvelle Vision pour le Développement de Kouakou Oussoukro (ANVDKO) est une organisation communautaire dédiée au développement durable de notre village.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">Comment puis-je devenir membre de l'ANVDKO ?</button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Remplissez le formulaire d'adhésion dans la section « Adhésion » du site.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">Quels types de projets sont menés par l'association ?</button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Infrastructure, éducation, santé, culture et actions sociales — voir la page Projets.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingFour">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">Comment rester informé des actualités et événements ?</button>
              </h2>
              <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Section Actualités du site et réseaux sociaux de l'association.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingFive">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">Puis-je contribuer à la galerie photo ?</button>
              </h2>
              <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Oui, envoyez vos clichés via la section Galerie ou contactez-nous.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingSix">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix">Quel est le coût de l'adhésion ?</button>
              </h2>
              <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Les frais sont fixés chaque année en assemblée générale — section Adhésion.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingSeven">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven">Comment accéder à l'espace membres ?</button>
              </h2>
              <div id="collapseSeven" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Après création de compte et connexion sur l'espace membres.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingEight">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight">Comment puis-je contacter l'association ?</button>
              </h2>
              <div id="collapseEight" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Via la section Contact de ce site.</div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingNine">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine">Puis-je annuler mon adhésion ?</button>
              </h2>
              <div id="collapseNine" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">Oui, via la page Contact. Aucun remboursement pour l'adhésion déjà payée.</div>
              </div>
            </div>
            <?php foreach ($faq_extra as $fi => $fq):
              $hid = 'faqExtra' . ($fq['id'] ?? $fi);
            ?>
            <div class="accordion-item">
              <h2 class="accordion-header" id="head<?php echo $hid; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $hid; ?>"><?php echo anvdko_esc_html($fq['question']); ?></button>
              </h2>
              <div id="<?php echo $hid; ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body"><?php echo nl2br(anvdko_esc_html($fq['reponse'])); ?></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- Contact : inchangé -->
      <section id="contact" class="contact section">
        <div class="container section-title" data-aos="fade-up">
          <h2>Contact</h2>
          <p>Pour toute question ou demande d'information, n'hésitez pas à nous contacter. Nous sommes à votre écoute pour vous accompagner.</p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
          <div class="row gx-3 gy-4 d-flex align-items-stretch">
            <div class="col-lg-4 d-flex flex-column">
              <div class="info-item d-flex align-items-start shadow-sm p-3 mb-3 bg-white rounded" data-aos="fade-up" data-aos-delay="300">
                <i class="bi bi-geo-alt fs-4 me-3"></i>
                <div><h5 class="mb-1">Adresse</h5><p class="mb-0">Kouakou Oussoukro, Côte d'Ivoire</p></div>
              </div>
              <div class="info-item d-flex align-items-start shadow-sm p-3 mb-3 bg-white rounded" data-aos="fade-up" data-aos-delay="400">
                <i class="bi bi-telephone fs-4 me-3"></i>
                <div><h5 class="mb-1">Téléphone</h5><p class="mb-0">+225 07 89 12 34 56</p></div>
              </div>
              <div class="info-item d-flex align-items-start shadow-sm p-3 bg-white rounded" data-aos="fade-up" data-aos-delay="500">
                <i class="bi bi-envelope fs-4 me-3"></i>
                <div><h5 class="mb-1">Email</h5><p class="mb-0">contact@anvdko.org</p></div>
              </div>
            </div>
            <div class="col-lg-8 d-flex">
              <form action="forms/contact.php" method="post" class="php-email-form shadow-sm p-4 bg-white rounded w-100" data-aos="fade-up" data-aos-delay="200">
                <div class="row gy-4">
                  <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Votre nom" required></div>
                  <div class="col-md-6"><input type="email" class="form-control" name="email" placeholder="Votre email" required></div>
                  <div class="col-md-12"><input type="text" class="form-control" name="subject" placeholder="Sujet" required></div>
                  <div class="col-md-12"><textarea class="form-control" name="message" rows="6" placeholder="Votre message" required></textarea></div>
                  <div class="col-md-12 text-center"><button type="submit" class="btn btn-send">Envoyer le message</button></div>
                </div>
              </form>
            </div>
          </div>
          <div class="mt-5" data-aos="fade-up" data-aos-delay="200">
            <iframe class="w-100 rounded shadow-sm" height="300" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d62849.12036991085!2d-5.116092077080115!3d7.531843595038093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfb809c32b5430f9%3A0x617a7a0c58c3f80e!2sDj%C3%A9bonoua!5e0!3m2!1sfr!2sci!4v1748806403746!5m2!1sfr!2sci" loading="lazy" allowfullscreen></iframe>
          </div>
        </div>
      </section>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let counters = document.querySelectorAll('.counter');
      let speed = 50;
      const animateCounters = () => {
        counters.forEach(counter => {
          let updateCount = () => {
            let target = +counter.getAttribute('data-target');
            let count = +counter.innerText;
            let increment = Math.max(1, target / speed);
            if (count < target) {
              counter.innerText = Math.ceil(count + increment);
              setTimeout(updateCount, 50);
            } else {
              counter.innerText = target;
            }
          };
          updateCount();
        });
      };
      let sectionStats = document.querySelector("#stats");
      if (sectionStats) {
        let observer = new IntersectionObserver((entries, observer) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              animateCounters();
              observer.unobserve(sectionStats);
            }
          });
        }, { threshold: 0.5 });
        observer.observe(sectionStats);
      }

      document.querySelectorAll('.btn-lire-plus').forEach(btn => {
        btn.addEventListener('click', function() {
          const titre = this.getAttribute('data-titre') || '';
          const contenu = this.getAttribute('data-contenu') || '';
          const image = this.getAttribute('data-image') || '';
          let html = '<p class="text-start">' + contenu.replace(/\n/g, '<br>') + '</p>';
          if (image) {
            html = '<img src="' + image + '" class="img-fluid rounded mb-3" alt="">' + html;
          }
          if (typeof Swal !== 'undefined') {
            Swal.fire({ title: titre, html: html, width: '700px', confirmButtonText: 'Fermer' });
          } else {
            alert(titre + '\n\n' + contenu);
          }
        });
      });
    });
  </script>
  <script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.querySelector('.php-email-form');
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            const existingMessages = this.querySelectorAll('.alert-message');
            existingMessages.forEach(msg => msg.remove());
            try {
                const response = await fetch('forms/contact.php', { method: 'POST', body: formData });
                const result = await response.json();
                const messageDiv = document.createElement('div');
                messageDiv.className = `alert-message alert ${result.success ? 'alert-success' : 'alert-danger'}`;
                messageDiv.style.cssText = 'margin-top:20px;padding:15px;border-radius:8px;text-align:center';
                messageDiv.innerHTML = result.success ? '<strong>Succès !</strong> ' + result.message : '<strong>Erreur !</strong> ' + result.message;
                contactForm.appendChild(messageDiv);
                if (result.success) contactForm.reset();
            } catch (error) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert-message alert alert-danger';
                errorDiv.textContent = 'Erreur réseau. Réessayez.';
                contactForm.appendChild(errorDiv);
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }
});
  </script>
