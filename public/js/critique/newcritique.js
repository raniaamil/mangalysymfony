document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".review-form").addEventListener("submit", function (event) {
        event.preventDefault();
        
        const title = document.getElementById("review-title").value.trim();
        const reviewText = document.getElementById("review-text").value.trim();
        const rating = document.getElementById("review-rating").value.trim();
        
        if (!title || !reviewText || !rating) {
            alert("Veuillez remplir tous les champs avant de publier votre critique.");
            return;
        }
        
        const ratingValue = parseFloat(rating);
        if (isNaN(ratingValue) || ratingValue < 1 || ratingValue > 5) {
            alert("Veuillez entrer une note valide entre 1 et 5.");
            return;
        }
        
        console.log("Critique soumise :", {
            titre: title,
            texte: reviewText,
            note: ratingValue
        });
        
        alert("Votre critique a bien été publiée !");
        window.location.href = "page_du_manga.html";
    });
});
