


window.addEventListener('load', () => {

    const searchInput: HTMLInputElement = document.querySelector('#search-input');    
    const searchButton: HTMLButtonElement = document.querySelector('#search-button');

    if (searchInput && searchButton) {        
        searchButton.addEventListener('click', (e) => {
            e.preventDefault();
            console.log(searchInput.value);

            fetch('/jeux/ajax/search_engine/' + searchInput.value, {
                method: 'GET'
            }).then(response => {

            });

            // GO TO AJAX FETE DES FLEURS            
        });
    }

});
