document.addEventListener('DOMContentLoaded', init);

const REGION_NAMES = {
    '1': 'Turks & Caicos',
    '2': 'Great Inagua',
    '3': 'Little Inagua',
    '4': 'Mayaguana',
    '5': 'Samana Cay',
    '6': 'Acklins & Crooked Island',
    '7': 'Ragged Island Range',
    '8': 'Long Island',
    '9': 'Rum Cay',
    '10': 'Conception Island',
    '11': 'San Salvador',
    '12': 'Cat Island',
    '13': 'Exuma',
    '14': 'Cay Lobos',
    '15': 'Andros',
    '16': 'New Providence (Nassau)',
    '17': 'Eleuthera',
    '18': 'Berry Islands',
    '19': 'Abaco',
    '20': 'Grand Bahama',
    '21': 'Bimini Islands',
    '22': 'Cay Sal Bank'
};

function getRegionName(regionId) {
    return REGION_NAMES[regionId] || `Island Group ${regionId}`;
}

let regionCounts = {};
let currentRegionId = null;

function init() {
    attachRegionEvents();
    setupShowAllButton();
    loadRegionCounts();
    loadPlants();
}

function loadRegionCounts() {
    fetch('/wp-json/interactive-map/v1/plants/counts')
        .then(r => r.json())
        .then(data => {
            if (data && data.regions) {
                regionCounts = data.regions;
                updateRegionTooltips();
            }
        })
        .catch(err => console.error('Error fetching region counts:', err));
}

function updateRegionTooltips() {
    const regionElements = document.querySelectorAll('svg polygon[id], svg path[id], [data-region]');

    regionElements.forEach(region => {
        const regionId = region.dataset.region || region.id;
        if (!regionId || isNaN(regionId)) return;

        const count = regionCounts[regionId] || 0;
        const regionName = getRegionName(regionId);
        const label = `${regionName}: ${count} plant${count === 1 ? '' : 's'}`;

        // Remove existing title element if any
        const existingTitle = region.querySelector('title');
        if (existingTitle) {
            existingTitle.remove();
        }

        // Append SVG <title> element for standard native SVG hover tooltip
        const titleEl = document.createElementNS('http://www.w3.org/2000/svg', 'title');
        titleEl.textContent = label;
        region.appendChild(titleEl);
    });
}

function setupShowAllButton() {
    const showAllBtn = document.getElementById('show-all-plants-btn');
    if (!showAllBtn) return;

    showAllBtn.addEventListener('click', () => {
        currentRegionId = null;

        // Deselect all highlighted map polygons
        document.querySelectorAll('svg .selected').forEach(el => {
            el.classList.remove('selected');
        });

        // Update sidebar header title
        const sidebarHeader = document.querySelector('#plant-sidebar h2');
        if (sidebarHeader) {
            sidebarHeader.textContent = 'All Plants';
        }

        // Fetch all plant species across all islands
        loadPlants(null);
    });
}

function loadPlants(region = null) {
    currentRegionId = region;
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
    const countBadge = document.getElementById('plant-count-badge');
    const summaryContainer = document.getElementById('plant-count-summary');

    const plantCount = plants ? plants.length : 0;

    if (countBadge) {
        countBadge.textContent = `${plantCount} ${plantCount === 1 ? 'Plant' : 'Plants'}`;
    }

    if (summaryContainer) {
        if (currentRegionId) {
            summaryContainer.textContent = `Showing ${plantCount} species for ${getRegionName(currentRegionId)}`;
        } else {
            summaryContainer.textContent = `Showing ${plantCount} species across all regions`;
        }
    }

    if (!plants || !plants.length) {
        container.innerHTML = '<p class="no-plants">No Plants Found.</p>';
        return;
    }

    let html = '';

    plants.forEach(plant => {
        const plantUrl = plant.url || '#';
        html += `
        <div class="plant-card">
            <div class="plant-details">
                <h4 class="plant-title">${plant.title}</h4>
                <span class="plant-family">${plant.family}</span>
            </div>
            <a href="${plantUrl}" class="view-plant-btn" target="_blank" rel="noopener noreferrer">
                View Plant
            </a>
        </div>
        `;
    });

    container.innerHTML = html;
}

const TEXT_REGION_MAP = [
    { keywords: ['inagua', 'matthew town'], regionId: '2' },
    { keywords: ['mayaguana', 'abraham'], regionId: '4' },
    { keywords: ['samana'], regionId: '5' },
    { keywords: ['acklins', 'crooked', 'long cay', 'northwest cay', 'albert town'], regionId: '6' },
    { keywords: ['ragged', 'duncan', 'raccoon', 'nurse', 'jamaica', 'flamingo', 'santo domingo', 'cay verde', 'channel rock'], regionId: '7' },
    { keywords: ['long island', 'clarence town', 'deadman'], regionId: '8' },
    { keywords: ['rum cay', 'port nelson'], regionId: '9' },
    { keywords: ['conception'], regionId: '10' },
    { keywords: ['san salvador', 'cockburn town'], regionId: '11' },
    { keywords: ['cat island', 'littlesan salvador', 'arthurs town'], regionId: '12' },
    { keywords: ['exuma', 'guana cay', 'george town'], regionId: '13' },
    { keywords: ['lobos', 'guinchos', 'sabinal'], regionId: '14' },
    { keywords: ['andros', 'big wood', 'green cay', 'yellow cay', 'water cay', 'williams', 'red shank', 'behrin', 'moxey town'], regionId: '15' },
    { keywords: ['nassau', 'new providence', 'newprovidence', 'providence', 'new', 'rose island'], regionId: '16' },
    { keywords: ['eleuthera', 'currentisland', 'dunmore town', 'spanish wells', 'governors harbour', 'rock sound'], regionId: '17' },
    { keywords: ['berry', 'great harbour', 'little harbour', 'whale cay', 'pine cay', 'bullocks harbour'], regionId: '18' },
    { keywords: ['abaco', 'moore', 'green turtle', 'tilloo', 'marls', 'powell', 'wollendean', 'marsh harbour', 'hope town'], regionId: '19' },
    { keywords: ['grand bahama', 'freeport', 'walker', 'grand cay', 'great sale', 'strangers', 'high rock', 'west end'], regionId: '20' },
    { keywords: ['bimini', 'alice town', 'browns cay', 'cat cays'], regionId: '21' },
    { keywords: ['cay sal', 'cayo largo'], regionId: '22' },
    { keywords: ['caicos', 'turks', 'ambergis', 'seal cays', 'cockburn harbour'], regionId: '1' },
    { keywords: ['providenciales', 'westcaicos'], regionId: '3' }
];

function bindTextCaptionsToRegions() {
    const regionPolygons = document.querySelectorAll('svg polygon[id], svg path[id]');
    const regionCentroids = {};

    regionPolygons.forEach(el => {
        const id = el.id || el.dataset.region;
        if (!id || isNaN(id)) return;
        const rid = parseInt(id, 10);
        if (rid < 1 || rid > 22) return;

        let bbox;
        try {
            bbox = el.getBBox();
        } catch (e) {
            return;
        }

        if (bbox && bbox.width > 0) {
            const cx = bbox.x + bbox.width / 2;
            const cy = bbox.y + bbox.height / 2;
            if (!regionCentroids[rid]) {
                regionCentroids[rid] = [];
            }
            regionCentroids[rid].push({ x: cx, y: cy });
        }
    });

    const centroids = {};
    Object.keys(regionCentroids).forEach(rid => {
        const pts = regionCentroids[rid];
        const avgX = pts.reduce((sum, p) => sum + p.x, 0) / pts.length;
        const avgY = pts.reduce((sum, p) => sum + p.y, 0) / pts.length;
        centroids[rid] = { x: avgX, y: avgY };
    });

    const textElements = document.querySelectorAll('svg text');
    textElements.forEach(textEl => {
        const fullTextStr = textEl.textContent.replace(/\s+/g, ' ').trim().toLowerCase();
        if (!fullTextStr) return;

        let matchedRegionId = null;

        // Try text keyword matching first
        for (const entry of TEXT_REGION_MAP) {
            if (entry.keywords.some(kw => fullTextStr.includes(kw))) {
                matchedRegionId = entry.regionId;
                break;
            }
        }

        // Spatial proximity fallback if no keyword match
        if (!matchedRegionId) {
            let bbox;
            try {
                bbox = textEl.getBBox();
            } catch (e) {
                return;
            }

            if (bbox && (bbox.width > 0 || bbox.height > 0)) {
                const tx = bbox.x + bbox.width / 2;
                const ty = bbox.y + bbox.height / 2;

                let minDist = Infinity;
                Object.keys(centroids).forEach(rid => {
                    const c = centroids[rid];
                    const dist = Math.hypot(c.x - tx, c.y - ty);
                    if (dist < minDist) {
                        minDist = dist;
                        matchedRegionId = rid;
                    }
                });

                if (minDist > 150) {
                    matchedRegionId = null;
                }
            }
        }

        if (matchedRegionId) {
            textEl.dataset.region = matchedRegionId;
            textEl.style.pointerEvents = 'auto';
            textEl.style.cursor = 'pointer';
            textEl.querySelectorAll('tspan').forEach(tspan => {
                tspan.dataset.region = matchedRegionId;
                tspan.style.pointerEvents = 'auto';
                tspan.style.cursor = 'pointer';
            });
        }
    });
}


function selectRegion(regionId) {
    if (!regionId || isNaN(regionId)) return;

    // Deselect all previously selected elements
    document.querySelectorAll('svg .selected').forEach(el => {
        el.classList.remove('selected');
    });

    // Select ALL polygons/elements belonging to this island region ID
    const matches = document.querySelectorAll(`svg [id="${regionId}"], svg [data-region="${regionId}"]`);
    matches.forEach(el => {
        el.classList.add('selected');
    });

    // Update sidebar title if header exists
    const sidebarHeader = document.querySelector('#plant-sidebar h2');
    if (sidebarHeader) {
        sidebarHeader.textContent = getRegionName(regionId);
    }

    loadPlants(regionId);
}

function attachRegionEvents() {
    bindTextCaptionsToRegions();

    const svgContainer = document.querySelector('.interactive-map-container svg') || document.querySelector('svg');
    if (svgContainer) {
        svgContainer.addEventListener('click', (e) => {
            let target = e.target;
            while (target && target !== svgContainer) {
                const regionId = target.dataset.region || target.id;
                if (regionId && !isNaN(regionId) && parseInt(regionId, 10) >= 1 && parseInt(regionId, 10) <= 22) {
                    e.preventDefault();
                    e.stopPropagation();
                    selectRegion(regionId);
                    return;
                }
                target = target.parentElement;
            }
        });
    }

    const interactiveElements = document.querySelectorAll('svg polygon[id], svg path[id], svg [data-region], svg text[data-region], svg tspan[data-region]');
    interactiveElements.forEach(el => {
        const regionId = el.dataset.region || el.id;

        if (!regionId || isNaN(regionId)) {
            return;
        }

        el.style.cursor = 'pointer';

        el.addEventListener('click', (e) => {
            e.stopPropagation();
            selectRegion(regionId);
        });
    });
}




