<?php

/**
 * Forward root requests to the public folder.
 * This is used for Shared Hosting where Document Root cannot be changed.
 */
require_once __DIR__ . '/public/index.php';
