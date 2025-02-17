const slides = document.querySelectorAll('.slider img');
const slider = document.querySelector('.slider');
const dots = document.querySelectorAll('.slider-nav a');
let currentIndex = 0;

// Fonction pour montrer une diapositive spécifique
function showSlide(index) {
    // Assure-toi que l'index est dans les limites
    if (index < 0) {
        index = slides.length - 1;
    } else if (index >= slides.length) {
        index = 0;
    }
    const slideWidth = slides[0].clientWidth;
    slider.scrollLeft = index * slideWidth;

    // Mettre à jour les indicateurs
    dots.forEach(dot => dot.style.opacity = '0.75');
    dots[index].style.opacity = '1';

    currentIndex = index;
}

// Fonction pour passer à la diapositive suivante
function nextSlide() {
    showSlide(currentIndex + 1);
}

// Fonction pour passer à la diapositive précédente
function previousSlide() {
    showSlide(currentIndex - 1);
}

// Ajoute les événements aux boutons de navigation gauche/droite
document.querySelector('.nav-button.left').addEventListener('click', previousSlide);
document.querySelector('.nav-button.right').addEventListener('click', nextSlide);

// Ajoute les événements de clic aux points de navigation
dots.forEach((dot, index) => {
    dot.addEventListener('click', (e) => {
        e.preventDefault(); // Empêche le comportement par défaut du lien
        showSlide(index);
    });
});

// Défilement automatique toutes les 3 secondes
let interval = setInterval(nextSlide, 3000);

// Arrêter le défilement automatique lors du survol
slider.addEventListener('mouseenter', () => clearInterval(interval));
slider.addEventListener('mouseleave', () => interval = setInterval(nextSlide, 3000));

// Initialisation
showSlide(currentIndex);

// Sélectionne le bouton
const backToTopButton = document.getElementById('back-to-top');

// Affiche le bouton lorsque l'utilisateur défile vers le bas
window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTopButton.classList.add('show');
    } else {
        backToTopButton.classList.remove('show');
    }
});

// Remonte en haut de la page lorsqu'on clique sur le bouton
backToTopButton.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // Défilement fluide
    });
});

