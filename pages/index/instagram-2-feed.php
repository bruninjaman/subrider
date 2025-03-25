<section id="subriderfeed" class="wrapper style2 special fade">
    <div class="container">
        <h2>Instagram Subrider</h2>
        <div id="instafeed" class="owl-carousel owl-theme owl-loaded owl-drag">
            <?php
            $instagram_images = [
                '148589275_896595747769012_3753235257108688971_n..jpg',
                '261474676_1198066010683425_4546154628925998117_n..jpg',
                '261962781_598096558169662_1290372496156943896_n..jpg',
                '261934196_613442609780737_1191985690364385337_n..jpg',
                '261490504_307548151264999_1988355608404932533_n..jpg',
                '261548757_2955446608119407_5483122984468588785_n..jpg',
                '271288347_432691818500932_5087543073350839753_n..jpg',
                '262169369_4374650655997870_4884467744191961968_n..jpg',
                '261187398_578241289935103_5528419618173590066_n..jpg',
                '272036274_4666772380110141_7308838789357310763_n..webp',
                '271980133_625941612058713_930109940416519662_n..jpg',
                '272095054_971379680448368_5369506016138860148_n..webp',
                '273169204_1100381577361436_586033089059493075_n..webp',
                '278483865_188964307020922_4111360628985490774_n..jpg',
                '277145677_312188677677933_3101476042461013835_n..jpg',
                '278508451_725874875234738_2590093110711883942_n..jpg',
                '311338747_429316972469695_7967545692968914837_n..jpg',
                '341307480_3056255314508938_5309736330615942229_n..webp',
                '468707359_2626587897528989_6223114869295883325_n..webp',
                '368111809_296293609678174_2860804172513601018_n..webp'
            ];

            foreach ($instagram_images as $image) {
                echo '<div class="item fade-in">';
                echo '<a href="https://www.instagram.com/xandov/" target="_blank">';
                echo '<img src="./assets/css/images/insta pics/' . $image . '" alt="Instagram Subrider" />';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<style>
#instafeed {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

#instafeed .item {
    flex: 0 0 calc(25% - 20px);
    max-width: calc(25% - 20px);
    transition: all 0.5s ease;
    opacity: 1;
}

#instafeed .item.fade-out {
    opacity: 0;
    transform: scale(0.95);
}

#instafeed .item.fade-in {
    opacity: 1;
    transform: scale(1);
}

#instafeed img {
    width: 100%;
    height: 300px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    #instafeed .item {
        flex: 0 0 calc(50% - 20px);
        max-width: calc(50% - 20px);
    }
}

@media (max-width: 480px) {
    #instafeed .item {
        flex: 0 0 calc(100% - 20px);
        max-width: calc(100% - 20px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const items = document.querySelectorAll('#instafeed .item');
    const totalItems = items.length;
    
    function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
    }

    function updateGallery() {
        // Adiciona classe fade-out a todos os itens
        items.forEach(item => {
            item.classList.add('fade-out');
        });

        // Espera a animação de fade-out terminar
        setTimeout(() => {
            // Embaralha os itens
            const itemsArray = Array.from(items);
            shuffleArray(itemsArray);
            
            // Reordena os itens no DOM
            const container = document.getElementById('instafeed');
            itemsArray.forEach(item => {
                container.appendChild(item);
            });

            // Remove a classe fade-out e adiciona fade-in
            items.forEach(item => {
                item.classList.remove('fade-out');
                item.classList.add('fade-in');
            });
        }, 500);
    }

    // Atualiza a galeria a cada 5 segundos
    setInterval(updateGallery, 5000);
});
</script>