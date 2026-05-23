<div class="badge-container" id="badge-<?php echo $membre_data['id']; ?>">
    <!-- Recto -->
    <div class="badge">
        <div class="background-logo"></div>
        <div class="header">
            <div class="title-text">
                <h2>Association de la Nouvelle Vision pour le <br> Développement de Kouakou Oussoukro</h2>
                <small>« Une jeunesse, une vision, un avenir pour Kouakou Oussoukro ! »</small>
            </div>
            <div class="logo"></div>
        </div>

        <div class="milieu">
            <h3>CARTE DE MEMBRE ANVDKO</h3>
        </div>

        <div class="photo" style="background-image: url('<?php echo $membre_data['imagePath']; ?>')"></div>

        <div class="info">
            <div><span>Nom :</span> <?php echo safe_safe_ucfirst($membre_data['nom']); ?></div>
            <div><span>Prénoms :</span> <?php echo safe_safe_ucfirst($membre_data['prenom']); ?></div>
            <div><span>Né(e) le :</span> <?php echo $membre_data['date_naissance']; ?></div>
            <div><span style="width: 120px;">Lieu résidence :</span> <?php echo safe_safe_ucfirst($membre_data['ville_commune']); ?></div>
            <div><span style="width: 120px;">N° d'adhésion :</span> <?php echo $membre_data['num_adhesion']; ?></div>
        </div>

        <div class="qr-section">
            <canvas class="qr-code" data-url="<?php echo $membre_data['qr_url']; ?>"></canvas>
            <div class="badge-type"><?php echo safe_safe_ucfirst($membre_data['poste_occupe']); ?></div>
        </div>

        <hr>

        <div class="footer">
            <div class="footer-left">
                <div><span>Contact :</span> <?php echo $membre_data['num_telephone']; ?></div>
                <div style="visibility: hidden;"><span>Email :</span> <?php echo $membre_data['email'] ?? ''; ?></div>
            </div>
            <div class="footer-right">
                <?php if (!empty($membre_data['signaturePath'])): ?>
                    <img src="<?php echo $membre_data['signaturePath']; ?>" class="signature-img" alt="Signature"/>
                <?php endif; ?>
            </div>
        </div>

        <button class="flip-btn" onclick="flipCard(<?php echo $membre_data['id']; ?>)">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <!-- Verso -->
    <div class="card-back">
        <div class="background-logo1"></div>
        
        <div class="notice">
            <h3>Engagement du Membre</h3>
            <p>
                En tant que membre de l'ANVDKO, je m'engage à contribuer activement au développement de Kouakou Oussoukro et de toute la région de Djébonoua. 
                Avec esprit d'unité, de travail et de solidarité, je participe aux projets de l'association pour valoriser notre village, 
                soutenir l'initiative des jeunes, et bâtir ensemble une nouvelle vision porteuse d'avenir pour notre communauté.
            </p>
        </div>

        <div class="footer1">
            <div class="contacts1">
                <div><span>Tél :</span> +225 0 171 166 820/ 0 709 503 220</div>
                <div><span>Email :</span> anvdkocontact@gmail.com</div>
                <div><span>Site web :</span> anvdko.site</div>
            </div>
            <div class="qr1"></div>
        </div>

        <button class="flip-btn" onclick="flipCard(<?php echo $membre_data['id']; ?>)">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>