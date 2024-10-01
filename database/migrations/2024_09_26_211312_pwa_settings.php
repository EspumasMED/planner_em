<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class PWASettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('pwa.pwa_app_name', 'PLANNER EM');
        $this->migrator->add('pwa.pwa_short_name', 'PLANNER');
        $this->migrator->add('pwa.pwa_background_color', '#ffffff');
        $this->migrator->add('pwa.pwa_orientation', 'any');
        $this->migrator->add('pwa.pwa_status_bar', '#fe890b');
        $this->migrator->add('pwa.pwa_theme_color', '#fe890b');
        $this->migrator->add('pwa.pwa_display', 'standalone');
        $this->migrator->add('pwa.pwa_icons_192x192', '');
        $this->migrator->add('pwa.pwa_icons_512x512', '');
        $this->migrator->add('pwa.pwa_start_url', '/');

        
    }
}