<?php

namespace Ekelly\InteractiveMap\Admin;

use Ekelly\InteractiveMap\Import\CsvImporter;

class ImportPage 
{
    public function register(): void
    {
        add_submenu_page(
            'interactive-map',
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
            <p>Your CSV must contain the following columns (or their aliases):</p>
            
            <ul>
                <li><strong>Species</strong> (or <code>plant_name</code>)</li>
                <li><strong>Family</strong> (or <code>plant_family</code>)</li>
                <li><strong>Island Grouping</strong> (or <code>region_id</code>)</li>
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
                    <li>Missing Pages: <?php echo esc_html(count($summary['missing']))?></li>
                </ul>
                <?php
                if (count($summary['missing']) > 0) {
                    echo '<ul>';
                    
                    foreach ($summary['missing'] as $plant) {
                    echo '<li>' . esc_html($plant) .'</li>';
                    }
                    
                    echo '</ul>';
                } 
                
                ?>
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
