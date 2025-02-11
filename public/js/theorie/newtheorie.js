document.addEventListener('DOMContentLoaded', function() {

    const imageInput = document.createElement('input');
    imageInput.type = 'file';
    imageInput.accept = 'image/*';
    imageInput.multiple = true;
    imageInput.style.display = 'none';
    document.body.appendChild(imageInput);

    const mediaInput = document.createElement('input');
    mediaInput.type = 'file';
    mediaInput.accept = 'video/*,audio/*';
    mediaInput.multiple = true;
    mediaInput.style.display = 'none';
    document.body.appendChild(mediaInput);

    const imageIcon = document.querySelector('.editor-btn i.fa-image');
    if (imageIcon) {
        imageIcon.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            imageInput.click();
        });
    }

    const playIcon = document.querySelector('.editor-btn i.fa-play');
    if (playIcon) {
        playIcon.parentElement.addEventListener('click', function(e) {
            e.preventDefault();
            mediaInput.click();
        });
    }

    imageInput.addEventListener('change', function() {
        const files = imageInput.files;
        if (files.length > 0) {
            const textarea = document.getElementById('post-text');
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

    mediaInput.addEventListener('change', function() {
        const files = mediaInput.files;
        if (files.length > 0) {
            const textarea = document.getElementById('post-text');
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
