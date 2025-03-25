<section id="three" class="spotlight style3 left">
    <span class="image fit main bottom">
        <img id="mainImage" src="./assets/css/images/insta pics/261187398_578241289935103_5528419618173590066_n..jpg" alt="Instagram Subrider" />
    </span>
    <div class="content">
        <header>
            <h2>Conheça nosso Instagram</h2>
            <p>Galeria de Fotos</p>
        </header>
        <p>Confira nosso trabalho através das fotos do Instagram. Cada imagem conta uma história de qualidade e dedicação.</p>
        <ul class="actions">
            <li><a href="https://www.instagram.com/xandov/" target="_blank" class="button">Seguir no Instagram</a></li>
        </ul>
    </div>
    <a href="#subriderfeed" class="goto-next scrolly">Próximo</a>
</section>

<style>
.spotlight .image.main {
    position: relative;
    overflow: hidden;
    background: none !important;
}

.spotlight .image.main img {
    position: relative;
    z-index: 1;
    opacity: 1;
    transition: opacity 1.5s ease-in-out;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.spotlight .image.main img.fade-out {
    opacity: 0;
    pointer-events: none;
}

.spotlight .image.main img.fade-in {
    opacity: 1;
    pointer-events: auto;
}
</style>

<script>
jQuery(document).ready(function($) {
    var $mainImage = $('#mainImage');
    var instagramImages = [
        '261187398_578241289935103_5528419618173590066_n..jpg',
        '261474676_1198066010683425_4546154628925998117_n..jpg',
        '261962781_598096558169662_1290372496156943896_n..jpg',
        '261934196_613442609780737_1191985690364385337_n..jpg',
        '261490504_307548151264999_1988355608404932533_n..jpg'
    ];
    
    var currentIndex = 0;
    var isTransitioning = false;

    function updateMainImage() {
        if (isTransitioning) return;
        
        isTransitioning = true;
        
        // Adiciona classe fade-out
        $mainImage.addClass('fade-out');
        
        // Espera a animação de fade-out terminar
        setTimeout(function() {
            // Atualiza a imagem
            currentIndex = (currentIndex + 1) % instagramImages.length;
            var newImage = new Image();
            
            newImage.onload = function() {
                $mainImage.attr('src', './assets/css/images/insta pics/' + instagramImages[currentIndex])
                    .removeClass('fade-out')
                    .addClass('fade-in');
                isTransitioning = false;
            };
            
            newImage.onerror = function() {
                console.error('Erro ao carregar imagem:', instagramImages[currentIndex]);
                isTransitioning = false;
            };
            
            newImage.src = './assets/css/images/insta pics/' + instagramImages[currentIndex];
        }, 1500);
    }

    // Inicia a primeira transição após 8 segundos
    setTimeout(updateMainImage, 8000);
    
    // Continua alternando a cada 8 segundos
    setInterval(updateMainImage, 8000);
});
</script>