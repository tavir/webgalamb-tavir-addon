<?php
/*
 * TavIR Webgalamb Addon
 * Shutdown loader
 *
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * Copyright (C) 2026 Cseh Róbert / TavIR
 *
 * This file is part of the TavIR Webgalamb Addon.
 *
 * The TavIR Webgalamb Addon is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 only as
 * published by the Free Software Foundation.
 *
 * This addon is an independent, unofficial Webgalamb extension. WEBGALAMB® and
 * the original Webgalamb software are not part of this addon and remain subject
 * to their own license terms.
 *
 * Author: Cseh Róbert / TavIR
 * Contact: https://www.tavir.hu/ | info@tavir.hu
 * Version: 0.2.16
 * Date: 2026-06-29
 * GitHub: https://github.com/tavir/webgalamb-tavir-addon
 *
 * Note: Addon loader registered from the open configuration file to avoid editing encoded factory files internally.
 */

if (defined('WGADDON_SHUTDOWN_EMITTED')) {
    return;
}
define('WGADDON_SHUTDOWN_EMITTED', true);

$wgaddonScript = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', $_SERVER['SCRIPT_NAME']) : '';
$wgaddonUriPath = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
$wgaddonUriPath = $wgaddonUriPath ? str_replace('\\', '/', $wgaddonUriPath) : '';

if (basename($wgaddonScript) !== 'wg8.php' && basename($wgaddonUriPath) !== 'wg8.php') {
    return;
}

if (!defined('WG_PATH')) {
    define('WG_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
if (!defined('WGADDON_PATH')) {
    define('WGADDON_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

if (!is_file(WGADDON_PATH . 'wgaddon_inject.php')) {
    return;
}

if (is_file(WGADDON_PATH . 'wgaddon_core.php')) {
    include_once WGADDON_PATH . 'wgaddon_core.php';
    if (class_exists('WGAddon')) {
        WGAddon::dbAvailable();
    }
}

include WGADDON_PATH . 'wgaddon_inject.php';


