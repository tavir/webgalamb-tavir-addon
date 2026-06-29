<?php
/*
 * TavIR Webgalamb Addon
 * Core settings and helper functions
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
 * Note: Own settings component. It creates and uses only the addon settings table.
 */
if (!defined('WG_PATH')) {
    define('WG_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
if (!defined('WGADDON_PATH')) {
    define('WGADDON_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    header('Content-Type: text/plain; charset=utf-8');
    exit('TavIR Webgalamb Addon komponens. Közvetlenül nem futtatható. Az admin belépési pont: ../wgaddon.php');
}

class WGAddon
{
    protected static $dbAvailable = null;
    protected static $dbError = '';
    protected static $dbMode = '';
    protected static $dbLink = null;
    protected static $phpShutdownRegistered = false;

    public static function table()
    {
        return DB_PREFIX . 'wgaddon_settings';
    }

    protected static function ensureDbConnection()
    {
        if (self::$dbAvailable === true) {
            if (self::$dbMode === 'mysqli' && class_exists('mysqli') && self::$dbLink instanceof mysqli && @self::$dbLink->ping()) {
                return;
            }
        }

        if (!class_exists('mysqli')) {
            self::$dbAvailable = false;
            self::$dbError = 'Nem érhető el használható adatbázis-kezelő. Az addon a files/wg8conf.php DB_* adataival mysqli kapcsolatot próbál létrehozni.';
            return;
        }

        if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASSWORD') || !defined('DB_DATABASE')) {
            self::$dbAvailable = false;
            self::$dbError = 'Hiányos adatbázis konfiguráció a files/wg8conf.php fájlban.';
            return;
        }

        $mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
        if ($mysqli->connect_errno) {
            self::$dbAvailable = false;
            self::$dbError = 'Nem sikerült csatlakozni az adatbázishoz: ' . $mysqli->connect_error;
            return;
        }
        @$mysqli->set_charset('utf8');
        self::$dbMode = 'mysqli';
        self::$dbLink = $mysqli;
        self::$dbAvailable = true;
    }

    public static function dbAvailable()
    {
        self::ensureDbConnection();
        return self::$dbAvailable !== false;
    }

    public static function dbError()
    {
        return self::$dbError;
    }

    public static function query($sql)
    {
        self::ensureDbConnection();
        if (!self::dbAvailable()) {
            return false;
        }
        if (self::$dbMode === 'mysqli') {
            return self::$dbLink->query($sql);
        }
        return false;
    }

    public static function escape($value)
    {
        self::ensureDbConnection();
        if (self::$dbMode === 'mysqli' && class_exists('mysqli') && self::$dbLink instanceof mysqli) {
            return self::$dbLink->real_escape_string($value);
        }
        return addslashes($value);
    }

    public static function fetchAssoc($result)
    {
        if (!$result) {
            return false;
        }
        if (self::$dbMode === 'mysqli') {
            return $result->fetch_assoc();
        }
        return false;
    }

    public static function fetchRow($result)
    {
        if (!$result) {
            return false;
        }
        if (self::$dbMode === 'mysqli') {
            return $result->fetch_row();
        }
        return false;
    }

    public static function numRows($result)
    {
        if (!$result) {
            return 0;
        }
        if (self::$dbMode === 'mysqli') {
            return $result->num_rows;
        }
        return 0;
    }

    public static function install()
    {
        self::ensureDbConnection();
        if (!self::dbAvailable()) {
            return;
        }
        $table = self::table();
        self::query("CREATE TABLE IF NOT EXISTS `{$table}` (
            `setting_key` varchar(80) NOT NULL,
            `setting_value` text NOT NULL,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        self::defaultValue('datatable_csv_export', '1');
        self::defaultValue('tinymce_subsup', '1');
        self::defaultValue('tinymce_image_dialog_fix', '1');
        self::defaultValue('admin_menu_link', '1');
        self::defaultValue('debug_mode', '0');
        self::defaultValue('php_error_log', '0');
        self::defaultValue('php_display_errors', '0');
        self::defaultValue('php_error_level', 'all');

        foreach (self::tinymceButtons() as $button) {
            self::defaultValue('tinymce_btn_' . $button['key'], !empty($button['default']) ? '1' : '0');
        }
    }

    public static function defaultValue($key, $value)
    {
        self::ensureDbConnection();
        if (!self::dbAvailable()) {
            return;
        }
        $table = self::table();
        $key = self::escape($key);
        $exists = self::query("SELECT `setting_key` FROM `{$table}` WHERE `setting_key`='{$key}' LIMIT 1");
        if ($exists && self::fetchAssoc($exists)) {
            return;
        }

        $value = self::escape($value);
        self::query("INSERT INTO `{$table}` (`setting_key`, `setting_value`) VALUES ('{$key}', '{$value}')");
    }

    public static function get($key, $default = '')
    {
        self::ensureDbConnection();
        if (!self::dbAvailable()) {
            return $default;
        }
        self::install();
        $table = self::table();
        $key = self::escape($key);
        $result = self::query("SELECT `setting_value` FROM `{$table}` WHERE `setting_key`='{$key}' LIMIT 1");
        if ($result && ($row = self::fetchAssoc($result))) {
            return $row['setting_value'];
        }

        return $default;
    }

    public static function enabled($key)
    {
        return self::get($key, '0') == '1';
    }

    public static function set($key, $value)
    {
        self::ensureDbConnection();
        if (!self::dbAvailable()) {
            return;
        }
        self::install();
        $table = self::table();
        $key = self::escape($key);
        $value = self::escape($value);
        self::query("INSERT INTO `{$table}` (`setting_key`, `setting_value`) VALUES ('{$key}', '{$value}')
            ON DUPLICATE KEY UPDATE `setting_value`='{$value}'");
    }

    public static function logFile()
    {
        return WG_PATH . 'files/wgaddon.log';
    }

    public static function configureRuntime()
    {
        if (self::enabled('php_error_log') || self::enabled('debug_mode') || self::enabled('php_display_errors')) {
            error_reporting(self::errorReportingLevel());
            ini_set('log_errors', '1');
            ini_set('display_errors', self::enabled('php_display_errors') ? '1' : '0');
            ini_set('error_log', self::logFile());
            set_error_handler(array('WGAddon', 'handlePhpError'), self::errorReportingLevel());
            self::registerPhpErrorShutdown();
        }
    }

    public static function errorReportingLevel()
    {
        if (self::get('php_error_level', 'all') === 'warnings') {
            return E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR;
        }
        return E_ALL;
    }

    public static function handlePhpError($severity, $message, $file, $line)
    {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $extra = array(
            'severity' => $severity,
            'message' => $message,
            'source_file' => self::shortPath($file),
            'source_line' => $line
        );

        if (self::enabled('php_error_log') || self::enabled('debug_mode') || self::enabled('php_display_errors')) {
            self::forceLog('php error', $extra);
        }

        if (self::enabled('php_display_errors')) {
            $type = self::phpErrorName($severity);
            echo '<div style="position:relative;z-index:2147483001;margin:0;padding:8px 12px;background:#fff3cd;border:1px solid #ffe08a;color:#5f4500;font:13px/1.4 Arial,sans-serif;">';
            echo 'WG Addon PHP hiba: ' . htmlspecialchars($type . ': ' . $message . ' [' . self::shortPath($file) . ':' . $line . ']', ENT_QUOTES, 'UTF-8');
            echo '</div>';
        }

        return false;
    }

    protected static function registerPhpErrorShutdown()
    {
        if (self::$phpShutdownRegistered) {
            return;
        }
        self::$phpShutdownRegistered = true;
        register_shutdown_function(array('WGAddon', 'handlePhpShutdownError'));
    }

    public static function handlePhpShutdownError()
    {
        $error = error_get_last();
        if (!$error) {
            return;
        }

        $fatalMask = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
        if (!(isset($error['type']) && ($error['type'] & $fatalMask))) {
            return;
        }

        $extra = array(
            'severity' => $error['type'],
            'message' => isset($error['message']) ? $error['message'] : '',
            'source_file' => isset($error['file']) ? self::shortPath($error['file']) : '',
            'source_line' => isset($error['line']) ? $error['line'] : 0
        );

        if (self::enabled('php_error_log') || self::enabled('debug_mode') || self::enabled('php_display_errors')) {
            self::forceLog('php shutdown error', $extra);
        }

        if (self::enabled('php_display_errors')) {
            $message = isset($error['message']) ? $error['message'] : '';
            $file = isset($error['file']) ? self::shortPath($error['file']) : '';
            $line = isset($error['line']) ? $error['line'] : 0;
            echo '<div style="position:relative;z-index:2147483001;margin:0;padding:8px 12px;background:#fff3cd;border:1px solid #ffe08a;color:#5f4500;font:13px/1.4 Arial,sans-serif;">';
            echo 'WG Addon PHP leállási hiba: ' . htmlspecialchars($message . ' [' . $file . ':' . $line . ']', ENT_QUOTES, 'UTF-8');
            echo '</div>';
        }
    }

    protected static function phpErrorName($severity)
    {
        $map = array(
            E_ERROR => 'E_ERROR',
            E_WARNING => 'E_WARNING',
            E_PARSE => 'E_PARSE',
            E_NOTICE => 'E_NOTICE',
            E_CORE_ERROR => 'E_CORE_ERROR',
            E_CORE_WARNING => 'E_CORE_WARNING',
            E_COMPILE_ERROR => 'E_COMPILE_ERROR',
            E_COMPILE_WARNING => 'E_COMPILE_WARNING',
            E_USER_ERROR => 'E_USER_ERROR',
            E_USER_WARNING => 'E_USER_WARNING',
            E_USER_NOTICE => 'E_USER_NOTICE',
            E_STRICT => 'E_STRICT',
            E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
            E_DEPRECATED => 'E_DEPRECATED',
            E_USER_DEPRECATED => 'E_USER_DEPRECATED'
        );
        return isset($map[$severity]) ? $map[$severity] : 'E_' . $severity;
    }

    public static function tinymceButtons()
    {
        return array(
            array('key' => 'subscript', 'label' => 'Alsó index', 'command' => 'Subscript', 'icon' => 'subscript', 'text' => 'x2', 'default' => true),
            array('key' => 'superscript', 'label' => 'Felső index', 'command' => 'Superscript', 'icon' => 'superscript', 'text' => 'x2', 'default' => true),
            array('key' => 'selectall', 'label' => 'Összes kijelölése', 'command' => 'SelectAll', 'icon' => 'selectall', 'text' => 'All'),
            array('factory' => true, 'key' => 'bold', 'label' => 'Félkövér', 'command' => 'Bold', 'icon' => 'bold', 'text' => 'B'),
            array('factory' => true, 'key' => 'italic', 'label' => 'Dőlt', 'command' => 'Italic', 'icon' => 'italic', 'text' => 'I'),
            array('factory' => true, 'key' => 'underline', 'label' => 'Aláhúzott', 'command' => 'Underline', 'icon' => 'underline', 'text' => 'U'),
            array('factory' => true, 'key' => 'strikethrough', 'label' => 'Áthúzott', 'command' => 'Strikethrough', 'icon' => 'strikethrough', 'text' => 'S'),
            array('key' => 'styleselect', 'label' => 'Stílus választó', 'native_icon' => '', 'native_label' => 'Formats', 'native_labels' => array('Formats', 'Stílus', 'Stílusok'), 'native_open' => true, 'text' => 'Style'),
            array('factory' => true, 'key' => 'formatselect', 'label' => 'Bekezdés/formátum választó', 'native_icon' => '', 'native_label' => 'Bekezdés', 'native_labels' => array('Bekezdés', 'Paragraph', 'Format', 'Formats'), 'native_open' => true, 'text' => 'P'),
            array('factory' => true, 'key' => 'fontselect', 'label' => 'Betűtípus választó', 'native_icon' => '', 'native_label' => 'Font Family', 'native_labels' => array('Font Family', 'Betűtípus', 'Betűcsalád', 'Arial', 'Verdana'), 'native_open' => true, 'text' => 'Font'),
            array('factory' => true, 'key' => 'fontsizeselect', 'label' => 'Betűméret választó', 'native_icon' => '', 'native_label' => 'Font Sizes', 'native_labels' => array('Font Sizes', 'Betűméret', 'Méret', '14px', '16px'), 'native_open' => true, 'text' => 'Size'),
            array('factory' => true, 'key' => 'forecolor', 'label' => 'Szövegszín', 'native_icon' => 'forecolor', 'native_label' => 'Text color', 'native_labels' => array('Text color', 'Szövegszín', 'Szín'), 'native_open' => true, 'requires_plugin' => 'textcolor', 'text' => 'A'),
            array('factory' => true, 'key' => 'backcolor', 'label' => 'Háttérszín', 'native_icon' => 'backcolor', 'native_label' => 'Background color', 'native_labels' => array('Background color', 'Háttérszín', 'Háttér szín'), 'native_open' => true, 'requires_plugin' => 'textcolor', 'text' => 'Bg'),
            array('factory' => true, 'key' => 'wg6links', 'label' => 'Feliratkozási mezők', 'native_label' => 'Feliratkozási mezők', 'native_labels' => array('Feliratkozási mezők', 'Mező', 'Fields'), 'native_open' => true, 'text' => 'Mező'),
            array('factory' => true, 'key' => 'fullscreen', 'label' => 'Teljes képernyő', 'native_icon' => 'fullscreen', 'native_label' => 'Fullscreen', 'command' => 'mceFullScreen', 'requires_plugin' => 'fullscreen', 'text' => 'Full'),
            array('factory' => true, 'key' => 'code', 'label' => 'HTML forrás', 'native_icon' => 'code', 'native_label' => 'Source code', 'command' => 'mceCodeEditor', 'requires_plugin' => 'code', 'text' => 'Code'),
            array('factory' => true, 'key' => 'template', 'label' => 'Sablon beszúrása', 'native_icon' => 'template', 'native_label' => 'Insert template', 'requires_plugin' => 'template', 'text' => 'Tpl'),
            array('key' => 'newdocument', 'label' => 'Új dokumentum', 'command' => 'mceNewDocument', 'icon' => 'newdocument', 'requires_plugin' => 'newdocument', 'text' => 'New', 'addon_note' => 'külön plugin kell'),
            array('key' => 'print', 'label' => 'Nyomtatás', 'command' => 'mcePrint', 'icon' => 'print', 'requires_plugin' => 'print', 'text' => 'Print'),
            array('key' => 'spellchecker', 'label' => 'Helyesírás-ellenőrzés', 'native_icon' => 'spellchecker', 'requires_plugin' => 'spellchecker', 'text' => 'ABC', 'addon_note' => 'külön plugin kell'),
            array('factory' => true, 'key' => 'cut', 'label' => 'Kivágás', 'command' => 'Cut', 'icon' => 'cut', 'text' => 'Cut'),
            array('factory' => true, 'key' => 'copy', 'label' => 'Másolás', 'command' => 'Copy', 'icon' => 'copy', 'text' => 'Copy'),
            array('factory' => true, 'key' => 'paste', 'label' => 'Beillesztés', 'command' => 'Paste', 'icon' => 'paste', 'text' => 'Paste'),
            array('factory' => true, 'key' => 'pastetext', 'label' => 'Beillesztés szövegként', 'native_icon' => 'pastetext', 'text' => 'Txt'),
            array('factory' => true, 'key' => 'alignleft', 'label' => 'Balra zárás', 'command' => 'JustifyLeft', 'icon' => 'alignleft', 'text' => 'L'),
            array('factory' => true, 'key' => 'aligncenter', 'label' => 'Középre zárás', 'command' => 'JustifyCenter', 'icon' => 'aligncenter', 'text' => 'C'),
            array('factory' => true, 'key' => 'alignright', 'label' => 'Jobbra zárás', 'command' => 'JustifyRight', 'icon' => 'alignright', 'text' => 'R'),
            array('factory' => true, 'key' => 'alignjustify', 'label' => 'Sorkizárt', 'command' => 'JustifyFull', 'icon' => 'alignjustify', 'text' => 'J'),
            array('factory' => true, 'key' => 'bullist', 'label' => 'Felsorolás', 'command' => 'InsertUnorderedList', 'icon' => 'bullist', 'text' => 'UL'),
            array('factory' => true, 'key' => 'numlist', 'label' => 'Számozott lista', 'command' => 'InsertOrderedList', 'icon' => 'numlist', 'text' => 'OL'),
            array('factory' => true, 'key' => 'outdent', 'label' => 'Behúzás csökkentése', 'command' => 'Outdent', 'icon' => 'outdent', 'text' => '<'),
            array('factory' => true, 'key' => 'indent', 'label' => 'Behúzás növelése', 'command' => 'Indent', 'icon' => 'indent', 'text' => '>'),
            array('factory' => true, 'key' => 'blockquote', 'label' => 'Idézet blokk', 'command' => 'mceBlockQuote', 'icon' => 'blockquote', 'text' => 'Q'),
            array('factory' => true, 'key' => 'undo', 'label' => 'Visszavonás', 'command' => 'Undo', 'icon' => 'undo', 'text' => 'Undo'),
            array('factory' => true, 'key' => 'redo', 'label' => 'Újra', 'command' => 'Redo', 'icon' => 'redo', 'text' => 'Redo'),
            array('factory' => true, 'key' => 'link', 'label' => 'Link beszúrása', 'native_icon' => 'link', 'native_label' => 'Insert/edit link', 'command' => 'mceLink', 'requires_plugin' => 'link', 'text' => 'Link'),
            array('factory' => true, 'key' => 'unlink', 'label' => 'Link törlése', 'native_icon' => 'unlink', 'native_label' => 'Remove link', 'command' => 'Unlink', 'requires_plugin' => 'link', 'text' => 'Unlink'),
            array('factory' => true, 'key' => 'anchor', 'label' => 'Horgony', 'native_icon' => 'anchor', 'native_label' => 'Anchor', 'command' => 'mceAnchor', 'requires_plugin' => 'anchor', 'text' => 'A'),
            array('factory' => true, 'key' => 'image', 'label' => 'Kép', 'native_icon' => 'image', 'native_label' => 'Insert/edit image', 'command' => 'mceImage', 'requires_plugin' => 'image', 'text' => 'Img'),
            array('key' => 'media', 'label' => 'Média beszúrása', 'command' => 'mceMedia', 'icon' => 'media', 'requires_plugin' => 'media', 'text' => 'Media', 'addon_note' => 'külön plugin kell'),
            array('key' => 'pagebreak', 'label' => 'Oldaltörés', 'command' => 'mcePageBreak', 'icon' => 'pagebreak', 'requires_plugin' => 'pagebreak', 'text' => 'Page', 'addon_note' => 'külön plugin kell'),
            array('key' => 'charmap', 'label' => 'Speciális karakter', 'command' => 'mceShowCharmap', 'icon' => 'charmap', 'requires_plugin' => 'charmap', 'text' => 'Char'),
            array('key' => 'insertdate', 'label' => 'Dátum beszúrása', 'native_icon' => 'insertdate', 'requires_plugin' => 'insertdatetime', 'text' => 'Date'),
            array('key' => 'inserttime', 'label' => 'Idő beszúrása', 'native_icon' => 'inserttime', 'requires_plugin' => 'insertdatetime', 'text' => 'Time'),
            array('key' => 'insertdatetime', 'label' => 'Dátum/idő beszúrása', 'native_icon' => 'insertdatetime', 'requires_plugin' => 'insertdatetime', 'text' => 'DateTime'),
            array('factory' => true, 'key' => 'table', 'label' => 'Táblázat menü', 'native_icon' => 'table', 'native_label' => 'Table', 'native_open' => true, 'requires_plugin' => 'table', 'text' => 'Tbl'),
            array('factory' => true, 'key' => 'visualaid', 'label' => 'Vizuális segédvonalak', 'native_icon' => 'visualaid', 'native_label' => 'Visual aids', 'text' => 'Aid'),
            array('factory' => true, 'key' => 'hr', 'label' => 'Vízszintes vonal', 'native_icon' => 'hr', 'native_label' => 'Horizontal line', 'command' => 'InsertHorizontalRule', 'requires_plugin' => 'hr', 'text' => 'HR'),
            array('factory' => true, 'key' => 'removeformat', 'label' => 'Formázás törlése', 'native_icon' => 'removeformat', 'native_label' => 'Clear formatting', 'command' => 'RemoveFormat', 'text' => 'Clear'),
            array('factory' => true, 'key' => 'visualblocks', 'label' => 'Blokkok mutatása', 'native_icon' => 'visualblocks', 'native_label' => 'Show blocks', 'command' => 'mceVisualBlocks', 'requires_plugin' => 'visualblocks', 'text' => 'VB'),
            array('key' => 'visualchars', 'label' => 'Láthatatlan karakterek mutatása', 'command' => 'mceVisualChars', 'icon' => 'visualchars', 'requires_plugin' => 'visualchars', 'text' => 'VC', 'addon_note' => 'nem működik minden TinyMCE 4 telepítésben utólag'),
            array('factory' => true, 'key' => 'searchreplace', 'label' => 'Keresés és csere', 'native_icon' => 'searchreplace', 'native_label' => 'Find and replace', 'command' => 'SearchReplace', 'requires_plugin' => 'searchreplace', 'text' => 'Search'),
            array('key' => 'ltr', 'label' => 'Balról jobbra írásirány', 'command' => 'mceDirectionLTR', 'icon' => 'ltr', 'requires_plugin' => 'directionality', 'text' => 'LTR', 'addon_note' => 'külön plugin kell'),
            array('key' => 'rtl', 'label' => 'Jobbról balra írásirány', 'command' => 'mceDirectionRTL', 'icon' => 'rtl', 'requires_plugin' => 'directionality', 'text' => 'RTL', 'addon_note' => 'külön plugin kell'),
            array('factory' => true, 'key' => 'imagetools', 'label' => 'Képeszközök', 'native_icon' => 'imagetools', 'requires_plugin' => 'imagetools', 'text' => 'IT', 'addon_note' => 'csak képkijelölésnél aktív'),
            array('factory' => true, 'key' => 'textpattern', 'label' => 'Szövegminták', 'native_icon' => 'textpattern', 'requires_plugin' => 'textpattern', 'text' => 'TP', 'addon_note' => 'nem külön gombként működik'),
            array('factory' => true, 'key' => 'colorpicker', 'label' => 'Színválasztó', 'native_icon' => 'colorpicker', 'requires_plugin' => 'colorpicker', 'text' => 'Color', 'addon_note' => 'a szöveg/háttérszín gomb használja'),
            array('factory' => true, 'key' => 'lists', 'label' => 'Lista eszközök', 'native_icon' => 'lists', 'requires_plugin' => 'lists', 'text' => 'List', 'addon_note' => 'a lista gombok használják'),
            array('factory' => true, 'key' => 'preview', 'label' => 'Előnézet', 'command' => 'mcePreview', 'icon' => 'preview', 'requires_plugin' => 'preview', 'text' => 'Prev'),
            array('factory' => true, 'key' => 'emoticons', 'label' => 'Emotikonok', 'native_icon' => 'emoticons', 'native_label' => 'Emoticons', 'native_labels' => array('Emoticons', 'Emotikonok', 'Hangulatjelek'), 'native_open' => true, 'command' => 'mceEmoticons', 'requires_plugin' => 'emoticons', 'text' => ':)'),
            array('key' => 'youtube', 'label' => 'YouTube videó', 'native_label' => 'YouTube videó', 'requires_plugin' => 'youtube', 'text' => 'YT'),
            array('factory' => true, 'key' => 'nonbreaking', 'label' => 'Nem törő szóköz', 'native_icon' => 'nonbreaking', 'native_label' => 'Nonbreaking space', 'command' => 'mceNonBreaking', 'requires_plugin' => 'nonbreaking', 'text' => 'NB')
        );
    }

    public static function enabledTinyMceButtons()
    {
        $enabled = array();
        foreach (self::groupedTinyMceButtons() as $group) {
            foreach ($group as $button) {
                if (self::enabled('tinymce_btn_' . $button['key'])) {
                    $enabled[] = $button;
                }
            }
        }
        return $enabled;
    }

    public static function groupedTinyMceButtons()
    {
        $groups = array('factory' => array(), 'addon' => array());
        foreach (self::tinymceButtons() as $button) {
            if (!empty($button['factory'])) {
                $groups['factory'][] = $button;
            } else {
                $groups['addon'][] = $button;
            }
        }
        return $groups;
    }

    public static function log($message, $extra = array())
    {
        if (!self::enabled('debug_mode')) {
            return;
        }

        self::writeLogLine($message, $extra);
    }

    public static function forceLog($message, $extra = array())
    {
        self::writeLogLine($message, $extra);
    }

    protected static function writeLogLine($message, $extra = array())
    {
        $file = self::logFile();
        $dir = dirname($file);
        if (!is_dir($dir) || !is_writable($dir)) {
            return;
        }

        $trace = debug_backtrace();
        $callerFile = isset($trace[1]['file']) ? $trace[1]['file'] : (isset($trace[0]['file']) ? $trace[0]['file'] : '');
        $lineData = array(
            'ts' => date('Y-m-d H:i:s'),
            'event' => $message,
            'file' => self::shortPath($callerFile),
            'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'CLI',
            'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
            'get' => self::sanitizeParams(isset($_GET) ? $_GET : array()),
            'post' => self::sanitizeParams(isset($_POST) ? $_POST : array()),
            'extra' => self::sanitizeParams($extra)
        );

        $json = json_encode($lineData);
        if ($json === false) {
            $json = '{"event":"log encode failed"}';
        }

        $line = str_replace(array("\r", "\n"), ' ', $json) . "\n";
        error_log($line, 3, $file);
    }

    protected static function shortPath($file)
    {
        if (!$file) {
            return '';
        }

        if (defined('WG_PATH') && strpos($file, WG_PATH) === 0) {
            return str_replace('\\', '/', substr($file, strlen(WG_PATH)));
        }

        return str_replace('\\', '/', $file);
    }

    protected static function sanitizeParams($value)
    {
        if (is_array($value)) {
            $clean = array();
            foreach ($value as $key => $item) {
                $keyText = strtolower((string)$key);
                if (strpos($keyText, 'pass') !== false
                    || strpos($keyText, 'psw') !== false
                    || strpos($keyText, 'pwd') !== false
                    || strpos($keyText, 'jelszo') !== false
                    || strpos($keyText, 'csrf') !== false
                    || strpos($keyText, 'token') !== false) {
                    $clean[$key] = '[hidden]';
                } else {
                    $clean[$key] = self::sanitizeParams($item);
                }
            }
            return $clean;
        }

        if (is_object($value)) {
            return '[object]';
        }

        $text = (string)$value;
        $text = str_replace(array("\r", "\n"), ' ', $text);
        if (strlen($text) > 300) {
            $text = substr($text, 0, 300) . '...';
        }

        return $text;
    }

    public static function logInfo()
    {
        $file = self::logFile();
        return array(
            'file' => $file,
            'exists' => is_file($file),
            'size' => is_file($file) ? filesize($file) : 0,
            'writable_dir' => is_dir(dirname($file)) && is_writable(dirname($file))
        );
    }

    public static function readLog($maxBytes = 200000)
    {
        $file = self::logFile();
        if (!is_file($file)) {
            return '';
        }

        $size = filesize($file);
        $offset = max(0, $size - $maxBytes);
        return file_get_contents($file, false, null, $offset);
    }

    public static function deleteLog()
    {
        $file = self::logFile();
        if (!is_file($file)) {
            return true;
        }

        return unlink($file);
    }
}


