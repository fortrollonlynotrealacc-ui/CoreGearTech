document.getElementById('search-input').addEventListener('input', function () {
    const query = this.value.trim();

    if (query.length > 0) {
        fetch(`search_suggestions.php?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                const suggestionsContainer = document.getElementById('suggestions');
                suggestionsContainer.innerHTML = '';

                if (data.length > 0) {
                    data.forEach(item => {
                        const suggestionItem = document.createElement('div');
                        suggestionItem.classList.add('suggestion-item');
                        suggestionItem.innerHTML = `
                            <img src="${item.image}" alt="${item.name}">
                            <span>${item.name}</span>
                        `;
                        suggestionItem.addEventListener('click', () => {
                            document.getElementById('search-input').value = item.name;
                            suggestionsContainer.innerHTML = '';
                        });
                        suggestionsContainer.appendChild(suggestionItem);
                    });
                }
            })
            .catch(error => console.error('Error fetching suggestions:', error));
    } else {
        document.getElementById('suggestions').innerHTML = '';
    }
});
