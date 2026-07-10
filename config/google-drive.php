<?php

return [
    'backup_enabled' => (bool) env('GOOGLE_DRIVE_BACKUP_ENABLED', true),
    'root_folder_name' => env('GOOGLE_DRIVE_ROOT_FOLDER', 'Faktury'),
    'backup_filename' => 'backup.json',
    'schema_version' => 1,
];
