<?php

/**
 * Fallback front controller for hosts that ignore the root .htaccess
 * (e.g. some shared hosts). It defers to Laravel's real entry point in /public.
 */
require __DIR__.'/public/index.php';
