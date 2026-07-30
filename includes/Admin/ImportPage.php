<?php

namespace Ekelly\InteractiveMap\Admin;

use Ekelly\InteractiveMap\Import\CsvImporter;

class ImportPage 
{
    public function register(): void
    {
        add_submenu_page(
            'tools.php',
            'Import Plant Data',
            'Import Plant Data',
            'manage_options',
            'interactive-map-import',
            [$this, 'render']
        );
    }

    public function render(): void
    {
        ?>
        <div class="wrap">
            <h1>Import Plant Data</h1>
            <p>Your CSV must contain the following columns:</p>
            
            <ul>
                <li>plant_name</li>
                <li>plant_family</li>
                <li>region_id</li>
            </ul>
            
            <?php $this->handleImport(); ?>
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('interactive_map_import'); ?>
                <input type="file" name="csv" accept=".csv" required>
                <?php submit_button('Import CSV'); ?>
            </form>
        </div>
        <?php
    }

    private function handleImport(): void
    {
        if (
            empty($_FILES['csv']) || 
            !isset($_POST['_wpnonce'])
        ) {
            return;
        }

        if (
            !wp_verify_nonce(
                $_POST['_wpnonce'],
                'interactive_map_import'
            )
        ) {
            wp_die('Invalid request.');
        } 

        try {

            $importer = new CsvImporter();

            $result = $importer->import(
                $_FILES['csv']['tmp_name']
            );

            $summary = $result->toArray();

            ?>
            <div class="notice notice-success">
                <p><strong>Import completed successfully.</strong></p>
                <ul>
                    <li>Processed: <?php echo esc_html($summary['processed']); ?></li>
                    <li>Imported: <?php echo esc_html($summary['imported']); ?></li>
                    <li>Skipped: <?php echo esc_html($summary['skipped']); ?></li>
                </ul>
            </div>
            <?php

        } catch (\Exception $e) {
            ?>
            <div class="notice notice-error">
                <p><?php echo esc_html($e->getMessage()); ?></p>
            </div>
            <?php
        }
    }
}
