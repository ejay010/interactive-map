document.addEventListener('DOMContentLoaded', init);

function init() {
    attachRegionEvents();
    loadPlants();
}

function loadPlants(region = null) {
    let endpoint = '/wp-json/interactive-map/v1/plants';

    if (region) {
        endpoint += '/' + region;
    }

    fetch(endpoint)
        .then(r => r.json())
        .then(renderPlants)
        .catch(error => console.error(error));
}

function renderPlants(plants) {

    const container = document.getElementById('plant-results');

    if (!plants.length) {
        container.innerHTML = '<p>No Plants Found.</p>';
        return;
    }

    let html = '';

    plants.forEach(plant => {
        html += `
        <div class="plant">
            <a href="${plant.url}">
                ${plant.title}
            </a>
            <br/>
            <small>${plant.family}</small>
        </div>
        `;
    })

    container.innerHTML = html;
}

function attachRegionEvents() {
    document.querySelectorAll('[data-region], svg path').forEach(path => {
        path.classList.remove('selected');
    });

    document.querySelectorAll('[data-region], svg path').forEach(region => {
        region.style.cursor = 'pointer';

        region.addEventListener('click', () => {

            region.classList.add('selected');

            const regionId = region.dataset.region || region.id;

            loadPlants(regionId);
        })
    })
}
