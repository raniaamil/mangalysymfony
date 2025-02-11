document.addEventListener('DOMContentLoaded', function() {
    // Exemple de données (à remplacer par des données réelles)
    const reviews = [
        { title: "Review 1", manga: "Manga A", note: 8, text: "Très bon manga!" },
        { title: "Review 2", manga: "Manga B", note: 7, text: "Pas mal du tout." }
    ];

    const theories = [
        { title: "Theory 1", manga: "Manga A", text: "Je pense que...", media: "image1.jpg" },
        { title: "Theory 2", manga: "Manga B", text: "Il est possible que...", media: "image2.jpg" }
    ];

    const posts = [
        { title: "Post 1", manga: "Manga A", text: "Voici mon post...", media: "image3.jpg" },
        { title: "Post 2", manga: "Manga B", text: "Un autre post...", media: "image4.jpg" }
    ];

    const comments = [
        { text: "Super commentaire!" },
        { text: "Je ne suis pas d'accord." }
    ];

    // Fonction pour remplir un tableau
    function fillTable(tableId, data, columns) {
        const tableBody = document.querySelector(`#${tableId} tbody`);
        tableBody.innerHTML = '';

        data.forEach(item => {
            const row = document.createElement('tr');
            columns.forEach(col => {
                const cell = document.createElement('td');
                cell.textContent = item[col];
                row.appendChild(cell);
            });

            // Ajout des boutons d'actions
            const actionsCell = document.createElement('td');
            actionsCell.className = 'actions';
            actionsCell.innerHTML = `
                <button onclick="editItem('${tableId}', ${data.indexOf(item)})">Modifier</button>
                <button onclick="deleteItem('${tableId}', ${data.indexOf(item)})">Supprimer</button>
            `;
            row.appendChild(actionsCell);

            tableBody.appendChild(row);
        });
    }

    // Remplir les tableaux
    fillTable('reviews-table', reviews, ['title', 'manga', 'note', 'text']);
    fillTable('theories-table', theories, ['title', 'manga', 'text', 'media']);
    fillTable('posts-table', posts, ['title', 'manga', 'text', 'media']);
    fillTable('comments-table', comments, ['text']);

    // Fonctions pour modifier et supprimer des éléments
    window.editItem = function(tableId, index) {
        alert(`Modifier l'élément ${index} dans ${tableId}`);
        // Ici, vous pouvez ajouter la logique pour modifier l'élément
    };

    window.deleteItem = function(tableId, index) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
            alert(`Supprimer l'élément ${index} dans ${tableId}`);
            // Ici, vous pouvez ajouter la logique pour supprimer l'élément
        }
    };
});