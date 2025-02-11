document.addEventListener('DOMContentLoaded', function() {
    // Création d'un input file caché pour les images
    const imageInput = document.createElement('input');
    imageInput.type = 'file';
    imageInput.accept = 'image/*';
    imageInput.multiple = true;
    imageInput.style.display = 'none';
    document.body.appendChild(imageInput);

    // Création d'un input file caché pour les vidéos et fichiers audio
    const mediaInput = document.createElement('input');
    mediaInput.type = 'file';
    mediaInput.accept = 'video/*,audio/*';
    mediaInput.multiple = true;
    mediaInput.style.display = 'none';
    document.body.appendChild(mediaInput);

    // Sélection des boutons dans la barre d'outils
    const imageButton = document.querySelector('.editor-btn i.fa-image');
    const playButton = document.querySelector('.editor-btn i.fa-play');

    // Lorsque l'utilisateur clique sur l'icône image, ouvrir le sélecteur d'images
    if (imageButton) {
        imageButton.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            imageInput.click();
        });
    }

    // Lorsque l'utilisateur clique sur l'icône play, ouvrir le sélecteur pour vidéo/audio
    if (playButton) {
        playButton.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            mediaInput.click();
        });
    }

    // Récupérer le textarea où insérer le code HTML
    const textarea = document.getElementById('post-text');

    // Traitement pour les images
    imageInput.addEventListener('change', function() {
        const files = imageInput.files;
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgHTML = `<img src="${e.target.result}" alt="Image ajoutée" style="max-width:100%;"/>\n`;
                    textarea.value += imgHTML;
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Traitement pour les vidéos et fichiers audio
    mediaInput.addEventListener('change', function() {
        const files = mediaInput.files;
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                reader.onload = function(e) {
                    let mediaHTML = '';
                    if (file.type.startsWith('video/')) {
                        mediaHTML = `<video controls style="max-width:100%;"><source src="${e.target.result}" type="${file.type}">Votre navigateur ne supporte pas la lecture vidéo.</video>\n`;
                    } else if (file.type.startsWith('audio/')) {
                        mediaHTML = `<audio controls style="max-width:100%;"><source src="${e.target.result}" type="${file.type}">Votre navigateur ne supporte pas la lecture audio.</audio>\n`;
                    }
                    textarea.value += mediaHTML;
                };
                reader.readAsDataURL(file);
            }
        }
    });
});
