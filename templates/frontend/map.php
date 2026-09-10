<div class="interactive-map-container">
    
    <div class="map-wrapper">
        <div class="map-mobile-helper">
            <label for="island-select-dropdown" class="map-dropdown-label">Quick Island Selector:</label>
            <div class="map-select-wrap">
                <select id="island-select-dropdown" class="island-select-dropdown" aria-label="Select an island group">
                    <option value="">All Islands (Overview)</option>
                </select>
            </div>
        </div>

        <div class="map">
            <?= file_get_contents(IM_PLUGIN_PATH . 'assets/svg/bs.svg'); ?>
        </div>
    </div>

    <aside id="plant-sidebar">
        <div class="sidebar-header-row">
            <div class="header-title-wrap">
                <span id="plant-count-badge" class="plant-count-badge"></span>
            </div>
            <button id="show-all-plants-btn" class="show-all-btn" type="button">Show All Plants</button>
        </div>
                        <h2>Select an Island</h2>

        <div id="plant-count-summary" class="plant-count-summary"></div>
        <div id="plant-results"></div>
    </aside>

</div>
