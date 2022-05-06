
export interface HTMLData {
    html: string;
}



window.addEventListener('load', () => {

    const searchInput: HTMLInputElement = document.querySelector('#search-input');

    if (searchInput) {        
        searchInput.addEventListener('keyup', (e) => {
            e.preventDefault();
            console.log(searchInput.value);

            if (searchInput.value.length != 0) {
                fetch('/jeux/ajax/search_engine/' + searchInput.value, {
                    method: 'GET'
                }).then(response => {
                    return response.json() as Promise<HTMLData>;                
                })            
                .then(data => {
                    let results = document.querySelector('#livesearch');
                    results.innerHTML = data.html;
                    // searchInput.innerHTML += data.html;
                    
                });
            }
        });
    }

});
