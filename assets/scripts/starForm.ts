let stars = document.querySelectorAll('.stars-form');
let noteField: HTMLInputElement = document.querySelector('#comment_note')

stars.forEach(star => {
    star.addEventListener('click', () => {
        noteField.value=star.getAttribute('data-value')
       
        if(star.classList.contains('orange')){
            star.classList.remove('orange')
            star.classList.add('checked')
        };

        stars.forEach((elem) => {
            if(star.getAttribute('data-value') !== elem.getAttribute('data-value')){
                elem.classList.remove('checked')
                elem.classList.add('orange')
            }
        })
    });
});
