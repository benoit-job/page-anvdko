
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="vendors/popper/popper.min.js"></script>

<script src="vendors/bootstrap/bootstrap.min.js"></script>
<script src="vendors/anchorjs/anchor.min.js"></script>
<script src="vendors/is/is.min.js"></script>
<script src="vendors/fontawesome/all.min.js"></script>
<script src="vendors/lodash/lodash.min.js"></script>
 <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
<script src="vendors/list.js/list.min.js"></script>
<script src="vendors/feather-icons/feather.min.js"></script>
<script src="vendors/dayjs/dayjs.min.js"></script>
<script src="assets/js/phoenix.js"></script>

<script src="vendors/tinymce/tinymce.min.js"></script>  
<script src="vendors/flatpickr/flatpickr.min.js"></script>


<script>
$(document).ready(function() {

    if($(window).width() < 768)
    {
        $('.content').css('padding-left', '2px');
        $('.content').css('padding-right', '2px');
        $('.content').css('overflow-x', 'hidden');
    }
});
</script>


<script>
function afficherToast(text, position, couleur, duree) 
{
    // Définir la couleur en fonction de l'argument "couleur"
    let bgColor = '#007bff'; // Couleur par défaut
    switch (couleur) {
        case 'primary':
            bgColor = '#007bff';
            break;
        case 'success':
            bgColor = '#28a745';
            break;
        case 'danger':
            bgColor = '#dc3545';
            break;
        case 'warning':
            bgColor = '#ffc107';
            break;
        case 'info':
            bgColor = '#17a2b8';
            break;
        default:
            bgColor = '#007bff';
    }

    // Créer le HTML du toast avec les variables dynamiques
    let toastHTML = `
        <div class="toastHenoch ${position} align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; z-index: 1050; top: 20px; right: 20px;">
            <div class="d-flex" style="background-color: ${bgColor}; max-width: 300px; word-wrap: break-word; border-radius: 8px; padding: 8px 15px; font-size: 12px;">
                <div class="toast-body">${text}</div>
            </div>
        </div>
    `;

    // Ajouter le toast au body
    $('body').append(toastHTML);

    // Récupérer l'élément du dernier toast
    let toastElement = $('body').children('.toastHenoch').last();

    // Faire apparaître le toast avec une animation "slide-in"
    setTimeout(function() {
        toastElement.addClass('show'); // Appliquer l'animation d'entrée
    }, 100); // Légère pause avant l'animation

    // Cacher le toast après le délai spécifié avec animation de "slide-up"
    setTimeout(function () {
        toastElement.removeClass('show'); // Animation de sortie
        toastElement.addClass('hide'); // Faire disparaître progressivement
        setTimeout(function() {
            toastElement.remove(); // Supprimer le toast une fois qu'il est complètement caché
        }, 500); // Temps avant suppression du toast (après animation de sortie)
    }, duree);
}


function goBack() {
  window.history.back(); // Cette ligne permet de revenir en arrière dans l'historique du navigateur
}

function spinnerBtn(element, affiche) 
{
    if(affiche){$(element).prepend("<div class='spinner-border spinner-border-sm me-2' style='width: 12px; height: 12px;' role='status'></div>");} 
    else{$(element).find('.spinner-border').remove();}
}
</script>