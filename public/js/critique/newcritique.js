document.addEventListener("DOMContentLoaded", function() {
    console.log("Script chargé !"); // Vérification

    const stars = document.querySelectorAll(".star-rating .star");
    const ratingInput = document.getElementById("review-rating");

    if (stars.length === 0 || !ratingInput) {
        console.error("Erreur : les étoiles ou l'input caché ne sont pas trouvés !");
        return;
    }

    let selectedRating = 0; // Stocke la note sélectionnée

    stars.forEach(star => {
        star.addEventListener("click", function() {
            selectedRating = parseInt(this.dataset.value);
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
            console.log("Note sélectionnée :", selectedRating); // Vérification
        });

        star.addEventListener("mouseover", function() {
            updateStars(parseInt(this.dataset.value));
        });

        star.addEventListener("mouseout", function() {
            updateStars(selectedRating);
        });
    });

    function updateStars(rating) {
        stars.forEach(star => {
            star.classList.toggle("selected", parseInt(star.dataset.value) <= rating);
        });
    }
});
