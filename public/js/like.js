document.addEventListener('DOMContentLoaded', function() {
    // Sélectionne tous les formulaires de likes
    document.querySelectorAll('.like-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Création des données à envoyer
            const formData = new FormData();
            formData.append('id', this.dataset.entityId);
            formData.append('type', this.dataset.entityType);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            // Envoi de la requête AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                // Mise à jour de l'icône
                const likeIcon = this.querySelector('.like-icon');
                if (data.isLiked) {
                    likeIcon.innerHTML = '❤️';
                } else {
                    likeIcon.innerHTML = '🤍';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                // Optionnel : afficher un message d'erreur à l'utilisateur
                alert('Une erreur est survenue lors du traitement de votre demande.');
            });
        });
    });
});