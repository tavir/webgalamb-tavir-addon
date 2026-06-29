<?php
/*
 * TavIR Webgalamb Addon
 * Admin interface
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
 * Note: Standalone addon administration file. It uses its own addon password and does not decode factory files.
 */
header('Content-Type: text/html; charset=utf-8');

if (!defined('WG_PATH')) {
    define('WG_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
}
if (!defined('WGADDON_PATH')) {
    define('WGADDON_PATH', WG_PATH . 't-wgaddon' . DIRECTORY_SEPARATOR);
}
if (!defined('WGADDON_ADMIN_BOOT')) {
    define('WGADDON_ADMIN_BOOT', true);
}
if (is_file(WG_PATH . 'files/wg8conf.php')) {
    include_once WG_PATH . 'files/wg8conf.php';
}
if (function_exists('session_status')) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
} elseif (!session_id()) {
    session_start();
}
require_once WGADDON_PATH . 'wgaddon_core.php';

WGAddon::install();
WGAddon::configureRuntime();
WGAddon::log('admin page loaded: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'cli'));

if (!isset($_SESSION['wgaddon_csrf'])) {
    $_SESSION['wgaddon_csrf'] = bin2hex(random_bytes(16));
}

function wgaddon_password_required()
{
    return defined('WGADDON_PASSWORD') && WGADDON_PASSWORD !== '';
}

function wgaddon_password_valid($password)
{
    if (!wgaddon_password_required()) {
        return true;
    }
    return hash_equals((string)WGADDON_PASSWORD, (string)$password);
}

function wgaddon_base_url()
{
    $script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '/wgaddon.php';
    $base = str_replace('\\', '/', dirname($script));
    return $base == '/' ? '' : rtrim($base, '/');
}

function wgaddon_url($file, $query = '')
{
    $url = wgaddon_base_url() . '/' . ltrim($file, '/');
    if ($query !== '') {
        $url .= '?' . ltrim($query, '?');
    }
    return $url;
}

function wgaddon_h($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function wgaddon_document_files()
{
    return array(
        'readme' => array('label' => 'README / telepítés', 'file' => 'readme-wgaddon.txt', 'type' => 'text/plain; charset=utf-8'),
        'repo_readme' => array('label' => 'README.md', 'file' => 'README.md', 'type' => 'text/markdown; charset=utf-8'),
        'license' => array('label' => 'LICENSE', 'file' => 'LICENSE', 'type' => 'text/plain; charset=utf-8'),
        'license_docs' => array('label' => 'LICENSE-DOCS.md', 'file' => 'LICENSE-DOCS.md', 'type' => 'text/markdown; charset=utf-8'),
        'notice' => array('label' => 'NOTICE.md', 'file' => 'NOTICE.md', 'type' => 'text/markdown; charset=utf-8'),
        'third_party' => array('label' => 'THIRD_PARTY_NOTICES.md', 'file' => 'THIRD_PARTY_NOTICES.md', 'type' => 'text/markdown; charset=utf-8'),
        'disclaimer' => array('label' => 'DISCLAIMER.md', 'file' => 'DISCLAIMER.md', 'type' => 'text/markdown; charset=utf-8'),
        'changelog' => array('label' => 'CHANGELOG.md', 'file' => 'CHANGELOG.md', 'type' => 'text/markdown; charset=utf-8'),
        'security' => array('label' => 'SECURITY.md', 'file' => 'SECURITY.md', 'type' => 'text/markdown; charset=utf-8')
    );
}

if (isset($_GET['readme'])) {
    $_GET['doc'] = 'readme';
}

if (isset($_GET['doc'])) {
    $docs = wgaddon_document_files();
    $docKey = preg_replace('/[^a-z_]/', '', (string)$_GET['doc']);
    if (!isset($docs[$docKey])) {
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Ismeretlen dokumentum.';
        exit;
    }
    $docPath = WGADDON_PATH . $docs[$docKey]['file'];
    if (!is_file($docPath)) {
        header('HTTP/1.1 404 Not Found');
        header('Content-Type: text/plain; charset=utf-8');
        echo 'A dokumentum nem található.';
        exit;
    }
    header('Content-Type: text/plain; charset=utf-8');
    if (!empty($docs[$docKey]['type'])) {
        header('Content-Type: ' . $docs[$docKey]['type']);
    }
    header('X-Content-Type-Options: nosniff');
    readfile($docPath);
    exit;
}

if (isset($_GET['logout'])) {
    unset($_SESSION['wgaddon_logged_in']);
    header('Location: ' . wgaddon_url('wgaddon.php'));
    exit;
}

if (isset($_POST['loginpassword'])) {
    if (wgaddon_password_valid($_POST['loginpassword'])) {
        $_SESSION['wgaddon_logged_in'] = '1';
    } else {
        $loginError = 'Hibás jelszó.';
        WGAddon::log('addon login failed');
    }
}

$loggedIn = !wgaddon_password_required() || (isset($_SESSION['wgaddon_logged_in']) && $_SESSION['wgaddon_logged_in'] === '1');

if ($loggedIn && WGAddon::enabled('debug_mode') && isset($_GET['wgaddon_client_log'])) {
    $event = isset($_POST['event']) ? $_POST['event'] : 'client debug';
    $details = isset($_POST['details']) ? $_POST['details'] : '';
    WGAddon::log('client: ' . $event, array('details' => $details));
    header('HTTP/1.1 204 No Content');
    exit;
}

if ($loggedIn && isset($_GET['download_log'])) {
    WGAddon::log('addon log download requested');
    if (function_exists('session_write_close')) {
        session_write_close();
    }
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="wgaddon-log-' . date('Ymd-His') . '.txt"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store, no-cache, max-age=0');
    header('Pragma: no-cache');
    echo WGAddon::readLog(5000000);
    exit;
}

function wgaddon_csrf_token()
{
    return isset($_SESSION['wgaddon_csrf']) ? $_SESSION['wgaddon_csrf'] : '';
}

function wgaddon_valid_csrf()
{
    return isset($_POST['wgaddon_csrf']) && hash_equals(wgaddon_csrf_token(), $_POST['wgaddon_csrf']);
}

function wgaddon_sql_value($value)
{
    if ($value === null) {
        return 'NULL';
    }

    return "'" . WGAddon::escape($value) . "'";
}

function wgaddon_stop_if_connection_aborted()
{
    if (function_exists('connection_aborted') && connection_aborted()) {
        exit;
    }
}

function wgaddon_download_sql_backup()
{
    ignore_user_abort(false);
    $stamp = date('Ymd-His');
    header('Content-Type: text/plain; charset=utf-8');
    header('Content-Disposition: attachment; filename="webgalamb-db-backup-' . $stamp . '.sql"');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: no-store, no-cache, max-age=0');
    header('Pragma: no-cache');

    echo "-- Webgalamb raw SQL backup\n";
    echo "-- Created by TavIR Webgalamb Add-on\n";
    echo "-- Date: " . date('Y-m-d H:i:s') . "\n";
    echo "-- Important: restore only after testing on a copy.\n\n";
    echo "SET NAMES utf8;\n";
    echo "SET FOREIGN_KEY_CHECKS=0;\n\n";
    wgaddon_stop_if_connection_aborted();

    $tablesResult = WGAddon::query('SHOW TABLES');
    while ($tablesResult && ($tableRow = WGAddon::fetchRow($tablesResult))) {
        wgaddon_stop_if_connection_aborted();
        $table = $tableRow[0];
        $tableEscaped = str_replace('`', '``', $table);

        echo "-- --------------------------------------------------------\n";
        echo "-- Table: `" . $tableEscaped . "`\n\n";
        $createResult = WGAddon::query('SHOW CREATE TABLE `' . $tableEscaped . '`');
        if ($createResult && ($createRow = WGAddon::fetchAssoc($createResult))) {
            $createSql = preg_replace('/^CREATE TABLE /', 'CREATE TABLE IF NOT EXISTS ', $createRow['Create Table']);
            echo $createSql . ";\n\n";
        }

        $dataResult = WGAddon::query('SELECT * FROM `' . $tableEscaped . '`');
        if ($dataResult) {
            while ($dataRow = WGAddon::fetchAssoc($dataResult)) {
                wgaddon_stop_if_connection_aborted();
                $fields = array();
                foreach (array_keys($dataRow) as $fieldName) {
                    $fields[] = '`' . str_replace('`', '``', $fieldName) . '`';
                }

                $values = array();
                foreach ($dataRow as $value) {
                    $values[] = wgaddon_sql_value($value);
                }
                echo 'INSERT INTO `' . $tableEscaped . '` (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ");\n";
            }
            echo "\n";
            wgaddon_stop_if_connection_aborted();
        }
    }

    echo "SET FOREIGN_KEY_CHECKS=1;\n";
    exit;
}

if ($loggedIn && isset($_POST['backup_sql']) && wgaddon_valid_csrf()) {
    WGAddon::log('sql backup requested');
    if (function_exists('session_write_close')) {
        session_write_close();
    }
    wgaddon_download_sql_backup();
}

if ($loggedIn && isset($_POST['delete_log'])) {
    if (!wgaddon_valid_csrf()) {
        $logError = 'Érvénytelen biztonsági token. Töltsd újra az oldalt.';
        WGAddon::log('log delete blocked: invalid csrf');
    } else {
        WGAddon::log('addon log delete requested');
        if (WGAddon::deleteLog()) {
            WGAddon::forceLog('addon log deleted');
            $logMessage = 'Addon log törölve. Az új log első sora a törlés bejegyzése.';
        } else {
            $logError = 'Az addon log törlése sikertelen. Ellenőrizd a files mappa jogosultságát.';
            WGAddon::log('addon log delete failed');
        }
    }
}

if ($loggedIn && isset($_POST['save_settings'])) {
    if (!wgaddon_valid_csrf()) {
        $saveError = 'Érvénytelen biztonsági token. Töltsd újra az oldalt.';
        WGAddon::log('settings save blocked: invalid csrf');
    } else {
        $keys = array('datatable_csv_export', 'tinymce_subsup', 'tinymce_image_dialog_fix', 'admin_menu_link', 'debug_mode', 'php_error_log', 'php_display_errors');
        foreach (WGAddon::tinymceButtons() as $button) {
            $keys[] = 'tinymce_btn_' . $button['key'];
        }
        foreach ($keys as $key) {
            WGAddon::set($key, isset($_POST[$key]) ? '1' : '0');
        }
        WGAddon::set('php_error_level', isset($_POST['php_error_level']) && $_POST['php_error_level'] === 'warnings' ? 'warnings' : 'all');
        WGAddon::configureRuntime();
        WGAddon::log('settings saved');
        $saved = true;
    }
}

function wgaddon_checked($key)
{
    echo WGAddon::enabled($key) ? ' checked' : '';
}

function wgaddon_selected($key, $value)
{
    echo WGAddon::get($key, '') === $value ? ' selected' : '';
}

$wgaddonAdminUrl = wgaddon_url('wgaddon.php');
?><!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>TavIR Webgalamb Addon</title>
    <link rel="stylesheet" href="<?php echo wgaddon_h(wgaddon_url('static/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo wgaddon_h(wgaddon_url('static/css/style.css')); ?>">
    <style>
        body { background: #f5f5f5; }
        .wgaddon-box { max-width: 980px; margin: 40px auto; background: #fff; padding: 24px; border: 1px solid #ddd; }
        .wgaddon-footer-links { display: flex; flex-wrap: wrap; justify-content: center; gap: 4px 8px; }
        .wgaddon-footer-links span { white-space: nowrap; }
        .wgaddon-title { color: #ff6600; }
        .wgaddon-actions .btn, .wgaddon-actions a { margin-bottom: 8px; }
        .wgaddon-log-text { width: 100%; min-height: 360px; font-family: Consolas, Monaco, monospace; font-size: 12px; white-space: pre; overflow: auto; }
        .wgaddon-top-actions { border: 1px solid #ddd; background: #fafafa; padding: 12px; margin: 18px 0; }
        .wgaddon-inline-actions { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .wgaddon-inline-actions form { margin: 0; }
        .wgaddon-tinymce-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 8px 16px; }
        @media (max-width: 640px) {
            body { background: #fff; }
            .wgaddon-box { width: 100%; max-width: none; margin: 0; padding: 14px; border: 0; }
            .wgaddon-title { font-size: 1.55rem; line-height: 1.2; }
            .wgaddon-top-actions, .wgaddon-actions, .wgaddon-inline-actions { display: flex; flex-direction: column; gap: 8px; }
            .wgaddon-top-actions .btn, .wgaddon-actions .btn, .wgaddon-actions a, .wgaddon-inline-actions .btn { width: 100%; margin-bottom: 0; white-space: normal; }
            .wgaddon-tinymce-grid { grid-template-columns: 1fr; gap: 10px; }
            .card-body { padding: 14px; }
            .alert { overflow-wrap: anywhere; }
            .wgaddon-log-text { min-height: 260px; font-size: 11px; }
            .wgaddon-footer-links { justify-content: flex-start; }
            .wgaddon-footer-links span { white-space: normal; }
        }
    </style>
</head>
<body>
<?php if (WGAddon::enabled('debug_mode')) { ?>
<!-- WGADDON-ADMIN-DEBUG active=1 version=0.2.16 log=files/wgaddon.log error_log=<?php echo WGAddon::enabled('php_error_log') ? '1' : '0'; ?> display_errors=<?php echo WGAddon::enabled('php_display_errors') ? '1' : '0'; ?> error_level=<?php echo wgaddon_h(WGAddon::get('php_error_level', 'all')); ?> -->
<?php } ?>
    <div class="wgaddon-box">
        <h1 class="wgaddon-title text-center">TavIR Webgalamb Addon <small>0.2.16</small></h1>
        <p class="text-center text-muted">Nem hivatalos, független addon Webgalamb 8 rendszerhez.</p>

        <?php if (!$loggedIn) { ?>
            <?php if (!empty($loginError)) { ?>
                <div class="alert alert-danger mt-3"><?php echo wgaddon_h($loginError); ?></div>
            <?php } ?>
            <form method="post" action="<?php echo wgaddon_h($wgaddonAdminUrl); ?>" class="mt-4">
                <div class="form-group">
                    <label for="loginpassword">Jelszó</label>
                    <input type="password" class="form-control" id="loginpassword" name="loginpassword" autofocus>
                </div>
                <button type="submit" class="btn btn-wg">Belépés</button>
                <a href="<?php echo wgaddon_h(wgaddon_url('wg8.php')); ?>" class="btn btn-secondary">Tovább a Webgalambhoz</a>
            </form>
        <?php } else { ?>
            <?php if (!WGAddon::dbAvailable()) { ?>
                <div class="alert alert-danger mt-3">
                    Addon adatbázis hiba: <?php echo wgaddon_h(WGAddon::dbError()); ?>
                </div>
            <?php } ?>
            <?php if (!empty($saved)) { ?>
                <div class="alert alert-success mt-3">Beállítások elmentve.</div>
            <?php } ?>
            <?php if (!empty($saveError)) { ?>
                <div class="alert alert-danger mt-3"><?php echo wgaddon_h($saveError); ?></div>
            <?php } ?>
            <?php if (!empty($logMessage)) { ?>
                <div class="alert alert-success mt-3"><?php echo wgaddon_h($logMessage); ?></div>
            <?php } ?>
            <?php if (!empty($logError)) { ?>
                <div class="alert alert-danger mt-3"><?php echo wgaddon_h($logError); ?></div>
            <?php } ?>
            <?php if (WGAddon::enabled('debug_mode')) { ?>
                <div class="alert alert-warning mt-3">
                    Figyelem: az addon debug aktív. A működés követése és a PHP hibák a files/wgaddon.log fájlba kerülhetnek.
                    <?php if (WGAddon::enabled('php_display_errors')) { ?>
                        A PHP hibák képernyőre írása is aktív, ezt éles használat után kapcsold ki.
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="wgaddon-top-actions">
                <a href="<?php echo wgaddon_h(wgaddon_url('wg8.php')); ?>" class="btn btn-wg">Vissza a Webgalambba</a>
                <a href="<?php echo wgaddon_h(wgaddon_url('wgaddon.php', 'logout=1')); ?>" class="btn btn-secondary">Kilépés</a>
            </div>

            <div class="card mb-3">
                <div class="card-header">README / feltételek / licenc állományok</div>
                <div class="card-body">
                    <div class="wgaddon-inline-actions">
                        <?php foreach (wgaddon_document_files() as $docKey => $docInfo) { ?>
                            <a href="<?php echo wgaddon_h(wgaddon_url('wgaddon.php', 'doc=' . $docKey)); ?>" target="_blank" class="btn btn-secondary"><?php echo wgaddon_h($docInfo['label']); ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <form method="post" action="<?php echo wgaddon_h($wgaddonAdminUrl); ?>" class="mt-4">
                <input type="hidden" name="save_settings" value="1">
                <input type="hidden" name="wgaddon_csrf" value="<?php echo wgaddon_h(wgaddon_csrf_token()); ?>">

                <div class="alert alert-info small">
                    Fontos: a wgaddon saját belépést használ, nem a Webgalamb gyári beléptetési logikáját. A Webgalamb gyári levélküldése külön natív wg8.php munkamenetet ellenőrizhet, ezért levélküldés előtt a wg8.php felületén is jelentkezz be.
                </div>

                <div class="card mb-3">
                    <div class="card-header">Felhasználói kiegészítések</div>
                    <div class="card-body">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="datatable_csv_export" name="datatable_csv_export"<?php wgaddon_checked('datatable_csv_export'); ?>>
                            <label class="custom-control-label" for="datatable_csv_export">Általános CSV export gomb a táblázatos listák fölé</label>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="tinymce_subsup" name="tinymce_subsup"<?php wgaddon_checked('tinymce_subsup'); ?>>
                            <label class="custom-control-label" for="tinymce_subsup">TinyMCE extra eszköztár a levélszerkesztőben</label>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="tinymce_image_dialog_fix" name="tinymce_image_dialog_fix"<?php wgaddon_checked('tinymce_image_dialog_fix'); ?>>
                            <label class="custom-control-label" for="tinymce_image_dialog_fix">Képbeszúró ablak tallózó mező igazítása</label>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="admin_menu_link" name="admin_menu_link"<?php wgaddon_checked('admin_menu_link'); ?>>
                            <label class="custom-control-label" for="admin_menu_link">Kiegészítő hivatkozás megjelenítése az Egyéb menü / Tudástár alatt</label>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">TinyMCE extra gombok</div>
                    <div class="card-body">
                        <p class="small text-muted">
                            Ezek a gombok külön addon eszköztár sorban jelennek meg a TinyMCE szerkesztő tetején.
                        </p>
                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-secondary" id="wgaddon-select-all-tinymce">Összes kijelölése</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="wgaddon-clear-all-tinymce">Összes törlése</button>
                            <button type="button" class="btn btn-sm btn-secondary" id="wgaddon-select-nonfactory-tinymce">Csak nem Webgalamb gyáriak</button>
                        </div>
                        <?php $tinyGroups = WGAddon::groupedTinyMceButtons(); ?>
                        <h6 class="mt-3">TinyMCE kiegészítések</h6>
                        <div class="wgaddon-tinymce-grid mb-3">
                            <?php foreach ($tinyGroups['addon'] as $button) { ?>
                                <?php $field = 'tinymce_btn_' . $button['key']; ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input wgaddon-tinymce-option" data-wgaddon-factory="0" id="<?php echo wgaddon_h($field); ?>" name="<?php echo wgaddon_h($field); ?>"<?php wgaddon_checked($field); ?>>
                                    <?php $mark = !empty($button['addon_note']) ? ' *' : ''; ?>
                                    <label class="custom-control-label" for="<?php echo wgaddon_h($field); ?>"><?php echo wgaddon_h($button['label'] . $mark); ?></label>
                                </div>
                            <?php } ?>
                        </div>
                        <h6 class="mt-3">Webgalamb gyári toolbar elemek</h6>
                        <div class="wgaddon-tinymce-grid">
                            <?php foreach ($tinyGroups['factory'] as $button) { ?>
                                <?php $field = 'tinymce_btn_' . $button['key']; ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input wgaddon-tinymce-option" data-wgaddon-factory="1" id="<?php echo wgaddon_h($field); ?>" name="<?php echo wgaddon_h($field); ?>"<?php wgaddon_checked($field); ?>>
                                    <?php $mark = !empty($button['addon_note']) ? ' *' : ''; ?>
                                    <label class="custom-control-label" for="<?php echo wgaddon_h($field); ?>"><?php echo wgaddon_h($button['label'] . $mark); ?></label>
                                </div>
                            <?php } ?>
                        </div>
                        <p class="small text-muted mt-3 mb-0">* Külön TinyMCE plugin vagy speciális szerkesztőállapot kellhet; ha nem aktiválható, az addon debug logban jelzi.</p>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">Hibakeresés és naplózás</div>
                    <div class="card-body">
                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="debug_mode" name="debug_mode"<?php wgaddon_checked('debug_mode'); ?>>
                            <label class="custom-control-label" for="debug_mode">Debug marker, lapon látható figyelmeztetés és működési napló bekapcsolása</label>
                        </div>

                        <div class="custom-control custom-checkbox mb-3">
                            <input type="checkbox" class="custom-control-input" id="php_error_log" name="php_error_log"<?php wgaddon_checked('php_error_log'); ?>>
                            <label class="custom-control-label" for="php_error_log">PHP error log engedélyezése a files/wgaddon.log fájlba</label>
                        </div>

                        <div class="form-group">
                            <label for="php_error_level">PHP hibanaplózási szint</label>
                            <select class="form-control" id="php_error_level" name="php_error_level">
                                <option value="all"<?php wgaddon_selected('php_error_level', 'all'); ?>>Minden PHP hiba, notice, warning és deprecated üzenet</option>
                                <option value="warnings"<?php wgaddon_selected('php_error_level', 'warnings'); ?>>Csak warning/error jellegű hibák</option>
                            </select>
                        </div>

                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="php_display_errors" name="php_display_errors"<?php wgaddon_checked('php_display_errors'); ?>>
                            <label class="custom-control-label" for="php_display_errors">PHP hibák képernyőre írása is legyen aktív debug idejére</label>
                        </div>
                    </div>
                </div>

                <div class="wgaddon-actions mt-3">
                    <div><button type="submit" class="btn btn-wg">Mentés</button></div>
                </div>
            </form>

            <div class="card mb-3">
                <div class="card-header">SQL mentés</div>
                <div class="card-body">
                    <p class="small text-muted">Különálló adatbázis mentési funkció. Telepítés vagy nagyobb módosítás előtt teljes mentéshez használható.</p>
                    <form method="post" action="<?php echo wgaddon_h($wgaddonAdminUrl); ?>">
                        <input type="hidden" name="wgaddon_csrf" value="<?php echo wgaddon_h(wgaddon_csrf_token()); ?>">
                        <button type="submit" name="backup_sql" value="1" class="btn btn-secondary">SQL mentés</button>
                    </form>
                </div>
            </div>

            <?php $logInfo = WGAddon::logInfo(); ?>
            <div class="card mt-4 mb-3">
                <div class="card-header">TavIR Webgalamb Addon működési log</div>
                <div class="card-body">
                    <p class="mb-2">Fájl: <strong>files/wgaddon.log</strong></p>
                    <p class="mb-3">
                        Állapot:
                        <?php echo $logInfo['exists'] ? 'létezik, méret: ' . (int)$logInfo['size'] . ' byte' : 'még nem jött létre'; ?>
                        | files mappa írható:
                        <?php echo $logInfo['writable_dir'] ? 'igen' : 'nem'; ?>
                    </p>
                    <div class="wgaddon-inline-actions">
                        <a href="<?php echo wgaddon_h(wgaddon_url('wgaddon.php', 'view_log=1#wgaddon-log-view')); ?>" class="btn btn-secondary">Log megnyitása</a>
                        <a href="<?php echo wgaddon_h(wgaddon_url('wgaddon.php', 'download_log=1')); ?>" class="btn btn-secondary">Log letöltése</a>
                        <form method="post" action="<?php echo wgaddon_h($wgaddonAdminUrl); ?>">
                            <input type="hidden" name="wgaddon_csrf" value="<?php echo wgaddon_h(wgaddon_csrf_token()); ?>">
                            <button type="submit" name="delete_log" value="1" class="btn btn-danger" onclick="return confirm('Biztosan törlöd a files/wgaddon.log fájlt?');">Log törlése</button>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['view_log'])) { ?>
                <div class="card mb-3" id="wgaddon-log-view">
                    <div class="card-header">wgaddon.log tartalma</div>
                    <div class="card-body">
                        <?php $logText = WGAddon::readLog(); ?>
                        <?php if ($logText === '') { ?>
                            <div class="alert alert-info mb-0">A log fájl üres vagy még nem létezik.</div>
                        <?php } else { ?>
                            <textarea class="form-control wgaddon-log-text" readonly wrap="off"><?php echo wgaddon_h($logText); ?></textarea>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <hr>
        <p class="text-center small mb-0">
            Kapcsolat és támogatás: Cseh Róbert / TavIR |
            <a href="https://www.tavir.hu/" target="_blank" rel="noopener">https://www.tavir.hu/</a> |
            <a href="mailto:info@tavir.hu">info@tavir.hu</a> |
            <a href="https://github.com/tavir/webgalamb-tavir-addon" target="_blank" rel="noopener">GitHub repo</a> |
            <a href="<?php echo wgaddon_h(wgaddon_url('wgaddon.php', 'doc=readme')); ?>" target="_blank" rel="noopener">Használati feltételek</a>
        </p>
    </div>
    <script>
    (function() {
        var selectAll = document.getElementById('wgaddon-select-all-tinymce');
        var clearAll = document.getElementById('wgaddon-clear-all-tinymce');
        var selectNonFactory = document.getElementById('wgaddon-select-nonfactory-tinymce');
        function setTinyMceOptions(value) {
            var boxes = document.querySelectorAll('.wgaddon-tinymce-option');
            for (var i = 0; i < boxes.length; i++) {
                boxes[i].checked = value;
            }
        }
        if (selectAll) {
            selectAll.onclick = function() { setTinyMceOptions(true); };
        }
        if (clearAll) {
            clearAll.onclick = function() { setTinyMceOptions(false); };
        }
        if (selectNonFactory) {
            selectNonFactory.onclick = function() {
                var boxes = document.querySelectorAll('.wgaddon-tinymce-option');
                for (var i = 0; i < boxes.length; i++) {
                    boxes[i].checked = boxes[i].getAttribute('data-wgaddon-factory') !== '1';
                }
            };
        }
    })();
    </script>
</body>
</html>

