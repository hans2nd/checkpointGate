<?php

/**
 * Fallback: redirect all requests to public/index.php
 * This file ensures Laravel works when deployed in a subfolder.
 */

// Load the public index.php
require __DIR__.'/public/index.php';
