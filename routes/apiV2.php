<?php

use Illuminate\Support\Facades\File;

foreach (File::allFiles(__DIR__ . '/v2') as $route_file) {
    require $route_file->getPathname();
}
