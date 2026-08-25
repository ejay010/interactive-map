<?php

namespace Ekelly\InteractiveMap\Admin;

use Ekelly\InteractiveMap\Repository\PlantRepository;

class ManagePlantsPage
{
    public function register(): void
    {
        add_submenu_page(
            'interactive-map',
            'Manage Plant Links',
            'Manage Plant Links',
            'manage_options',
            'interactive-map-manage-plants',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        $plantRepository = new PlantRepository();
        $message = $this->handleSave($plantRepository);

        $plants = $plantRepository->getAllPlantsWithRegions();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Manage Plant Links</h1>
            <hr class="wp-header-end">

            <p>Below is the list of imported plants. You can add or update the direct URL for each plant. Entering a link here will immediately update the <strong>"View Plant"</strong> button across all island regions the plant belongs to.</p>

            <?php if (!empty($message)): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php echo esc_html($message); ?></p>
                </div>
            <?php endif; ?>

            <?php if (empty($plants)): ?>
                <div class="notice notice-info">
                    <p>No plants found in the catalog yet. Go to <a href="<?php echo admin_url('admin.php?page=interactive-map-import'); ?>">Import Plant Data</a> to upload your CSV spreadsheet.</p>
                </div>
            <?php else: ?>
                <form method="post">
                    <?php wp_nonce_field('interactive_map_save_links'); ?>
                    
                    <p class="submit" style="padding-bottom: 0;">
                        <?php submit_button('Save All Links', 'primary', 'submit_top', false); ?>
                    </p>

                    <table class="wp-list-table widefat fixed striped table-view-list" style="margin-top: 15px;">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 25%;">Species Title</th>
                                <th scope="col" style="width: 20%;">Family</th>
                                <th scope="col" style="width: 15%;">Island Region(s)</th>
                                <th scope="col" style="width: 40%;">Plant Page URL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plants as $plant): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($plant['plant_name']); ?></strong>
                                    </td>
                                    <td>
                                        <em><?php echo esc_html($plant['plant_family']); ?></em>
                                    </td>
                                    <td>
                                        <?php 
                                        $regions = !empty($plant['regions']) ? explode(', ', $plant['regions']) : [];
                                        foreach ($regions as $r) {
                                            echo '<span style="display:inline-block; background:#e8f5e9; color:#2e7d32; padding:2px 6px; border-radius:4px; font-size:11px; margin-right:4px; margin-bottom:2px; font-weight:600;">Region ' . esc_html($r) . '</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <input 
                                            type="url" 
                                            name="plant_urls[<?php echo (int) $plant['id']; ?>]" 
                                            value="<?php echo esc_url($plant['url'] ?? ''); ?>" 
                                            placeholder="https://example.com/plant-page-url" 
                                            style="width: 100%; max-width: 500px;"
                                        />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <p class="submit">
                        <?php submit_button('Save All Links', 'primary', 'submit_bottom', false); ?>
                    </p>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    private function handleSave(PlantRepository $plantRepository): ?string
    {
        if (
            empty($_POST['plant_urls']) || 
            !isset($_POST['_wpnonce']) ||
            !is_array($_POST['plant_urls'])
        ) {
            return null;
        }

        if (!wp_verify_nonce($_POST['_wpnonce'], 'interactive_map_save_links')) {
            wp_die('Invalid security token.');
        }

        $updatedCount = 0;

        foreach ($_POST['plant_urls'] as $plantId => $url) {
            $plantId = (int) $plantId;
            $url = sanitize_text_field($url);

            if ($plantRepository->updatePlantUrl($plantId, $url)) {
                $updatedCount++;
            }
        }

        return "Successfully saved plant links ($updatedCount updated).";
    }
}
