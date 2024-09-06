document.getElementById('loadMore').addEventListener('click', function () {
    let page = this.getAttribute('data-page');
    let limit = 3;
   // let sort = '{{ sort }}';
   let sort = this.getAttribute('data-sort')

    fetch(`/post_loadMore?page=${page}&limit=${limit}&sort=${sort}`).then(response => response.text()).then(html => {
        if (html.trim() !== '') {
            document.getElementById('posts').innerHTML += html;
            this.setAttribute('data-page', parseInt(page) + 1);
        } else {
            this.style.display = 'none';
        }
    });
});