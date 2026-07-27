document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#features path').forEach(region => {
        region.style.cursor = 'pointer';

        region.addEventListener('mouseenter', () => {
            region.style.fill = '#4CAF50';
        });

        region.addEventListener('mouseleave', () => {
            region.style.fill = '';
        });

        region.addEventListener('click', () => {
            fetch(`/wp-json/interactive-map/v1/plants/${region.id}`)
                .then(response => response.json())
                .then(plants => {
                    const results = document.getElementById('plant-results');

                    results.innerHTML = '';

                    if (plants.length === 0) {
                        results.innerHTML = '<p>No plants found.</p>';

                        return;
                    }

                    plants.forEach(plant => {
                        results.innerHTML += `
                        <p>
                            <a href="${plant.url}">
                                ${plant.title}
                            </a></br>
                            <small>${plant.family}</small>
                        </p>
                        `;
                    })
                });
        });
    });
});