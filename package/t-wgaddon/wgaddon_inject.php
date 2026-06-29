<?php
/*
 * TavIR Webgalamb Addon
 * Frontend injector
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
 * Note: Optional frontend additions loaded according to the addon admin settings.
 */
if (!defined('WG_PATH')) {
    define('WG_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}
if (isset($_SERVER['SCRIPT_FILENAME']) && realpath($_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)) {
    header('Content-Type: text/plain; charset=utf-8');
    exit('TavIR Webgalamb Addon injektáló komponens. Közvetlenül nem futtatható. Az admin belépési pont: ../wgaddon.php');
}

if (!defined('WGADDON_PATH')) {
    define('WGADDON_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}
require_once WGADDON_PATH . 'wgaddon_core.php';
WGAddon::install();
WGAddon::configureRuntime();

$wgaddonCsv = WGAddon::enabled('datatable_csv_export');
$wgaddonTinyToolbar = WGAddon::enabled('tinymce_subsup');
$wgaddonTinyButtons = WGAddon::enabledTinyMceButtons();
$wgaddonImageDialogFix = WGAddon::enabled('tinymce_image_dialog_fix');
$wgaddonAdminMenu = WGAddon::enabled('admin_menu_link');
$wgaddonDebug = WGAddon::enabled('debug_mode');
$wgaddonBaseUrl = isset($_SERVER['SCRIPT_NAME']) ? rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') : '';
if ($wgaddonBaseUrl == '/') {
    $wgaddonBaseUrl = '';
}
$wgaddonAdminUrl = $wgaddonBaseUrl . '/wgaddon.php';
WGAddon::log('frontend inject loaded: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'unknown'));
?>
<!-- WGADDON-INJECT-LOADED 0.2.16 -->
<?php if ($wgaddonDebug) { ?>
<!-- WGADDON-DEBUG active=1 version=0.2.16 csv=<?php echo $wgaddonCsv ? '1' : '0'; ?> tinymce_toolbar=<?php echo $wgaddonTinyToolbar ? '1' : '0'; ?> tiny_buttons=<?php echo count($wgaddonTinyButtons); ?> image_fix=<?php echo $wgaddonImageDialogFix ? '1' : '0'; ?> admin_menu=<?php echo $wgaddonAdminMenu ? '1' : '0'; ?> log=files/wgaddon.log -->
<?php } ?>
<?php if ($wgaddonImageDialogFix || $wgaddonTinyToolbar || $wgaddonCsv || $wgaddonDebug) { ?>
<style>
#wgaddon-debug-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 2147483000;
    padding: 8px 44px 8px 12px;
    background: #fff3cd;
    color: #5f4500;
    border-bottom: 1px solid #ffe08a;
    border-top: 1px solid #ffe08a;
    text-align: center;
    font: 14px/1.3 Arial, sans-serif;
}
#wgaddon-debug-banner button {
    position: absolute;
    top: 4px;
    right: 10px;
    width: 26px;
    height: 26px;
    border: 1px solid #d8aa2e;
    border-radius: 3px;
    background: #fff8df;
    color: #5f4500;
    font: bold 16px/20px Arial, sans-serif;
    cursor: pointer;
}
.wgaddon-csv-export {
    margin: 0 0 10px 0;
}
.wgaddon-tinymce-toolbar {
    border-top: 1px solid #ddd !important;
    background: #f7f7f7 !important;
}
.wgaddon-tinymce-toolbar .mce-flow-layout {
    white-space: normal !important;
}
.wgaddon-tinymce-toolbar-body {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: center !important;
    gap: 0 !important;
    max-width: 100% !important;
}
.wgaddon-tinymce-toolbar .wgaddon-tinymce-btn {
    float: none !important;
    display: inline-block !important;
    flex: 0 0 auto !important;
    margin: 2px 0 2px -1px !important;
}
.wgaddon-tinymce-toolbar .mce-container-body {
    min-height: 34px !important;
}
.wgaddon-tinymce-toolbar .wgaddon-tinymce-btn button,
.wgaddon-tinymce-toolbar .mce-btn button {
    min-width: 30px !important;
}
.mce-window .mce-browsebutton {
    width: 220px !important;
    height: 34px !important;
    min-height: 34px !important;
    overflow: hidden !important;
}
.mce-window .mce-browsebutton button {
    width: 100% !important;
    height: 100% !important;
    line-height: 30px !important;
    overflow: hidden !important;
    text-align: center !important;
}
.mce-window .mce-browsebutton input[type="file"] {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    height: 100% !important;
    opacity: 0 !important;
    cursor: pointer !important;
}
.mce-window .mce-dropzone {
    box-sizing: border-box !important;
}
</style>
<?php } ?>

<?php if ($wgaddonCsv || $wgaddonTinyToolbar || $wgaddonAdminMenu || $wgaddonDebug || $wgaddonImageDialogFix) { ?>
<script>
(function($) {
    if (!$) {
        return;
    }

    var wgaddonLastDomDebug = '';
    var wgaddonLastReadyLog = '';
    var wgaddonTinyButtons = <?php echo json_encode($wgaddonTinyButtons); ?> || [];
    var wgaddonPluginLoadState = {};
    var wgaddonTinyToolbarBuilding = false;
    var wgaddonTinyPluginsChecked = false;

    function csvCell(text) {
        text = $.trim(String(text || '')).replace(/\s+/g, ' ');
        return '"' + text.replace(/"/g, '""') + '"';
    }

    function tableLooksExportable(table) {
        return table.find('tr').length > 1 && table.find('td,th').length > 1;
    }

    function exportTable(tableElement) {
        var table = $(tableElement);
        var api = $.fn.dataTable && $.fn.dataTable.isDataTable(tableElement) ? table.DataTable() : null;
        var rows = [];

        if (api) {
            var headers = [];
            $(api.columns().header()).each(function() {
                headers.push(csvCell($(this).text()));
            });
            rows.push(headers.join(';'));

            api.rows({ search: 'applied' }).every(function() {
                var row = [];
                $(this.node()).find('td').each(function() {
                    row.push(csvCell($(this).text()));
                });
                rows.push(row.join(';'));
            });
        } else {
            table.find('tr:visible').each(function() {
                var row = [];
                $(this).find('th,td').each(function() {
                    row.push(csvCell($(this).text()));
                });
                if (row.length) {
                    rows.push(row.join(';'));
                }
            });
        }

        var blob = new Blob(["\ufeff" + rows.join("\r\n")], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var stamp = new Date().toISOString().slice(0, 19).replace(/[:T]/g, '-');
        link.href = URL.createObjectURL(blob);
        link.download = 'webgalamb-tabla-' + stamp + '.csv';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(link.href);
    }

    function addCsvButtons() {
        var added = 0;
        $('div.dataTables_wrapper table, table.orderable-table, table.dataTable, .table-responsive table, .table-responsive-md table').each(function() {
            var table = $(this);
            var tableElement = table.get(0);
            var wrapper = table.closest('.dataTables_wrapper');
            var host = wrapper.length ? wrapper.first() : table.closest('.table-responsive, .table-responsive-md');
            var insertTarget = host.length ? host.first() : table;

            if (!tableLooksExportable(table) || table.data('wgaddonCsv') || insertTarget.prev('.wgaddon-csv-export').length) {
                return;
            }

            table.data('wgaddonCsv', 1);
            $('<button type="button" class="btn btn-sm btn-wg wgaddon-csv-export">CSV export</button>')
                .on('click', function() {
                    sendDebugLog('csv export click', {
                        rows: table.find('tbody tr:visible').length,
                        columns: table.find('thead th').length || table.find('tr:first th, tr:first td').length,
                        id: table.attr('id') || '',
                        classes: table.attr('class') || ''
                    });
                    exportTable(tableElement);
                })
                .insertBefore(insertTarget);
            added++;
        });
        return added > 0;
    }

    function getTinyMceEditor() {
        if (!window.tinymce) {
            return null;
        }
        if (tinymce.get('myTextArea')) {
            return tinymce.get('myTextArea');
        }
        if (tinymce.activeEditor && tinymce.activeEditor.getContainer) {
            return tinymce.activeEditor;
        }
        if (tinymce.editors && tinymce.editors.length) {
            for (var i = 0; i < tinymce.editors.length; i++) {
                if (tinymce.editors[i] && tinymce.editors[i].getContainer) {
                    return tinymce.editors[i];
                }
            }
        }
        return null;
    }

    function clickNativeTinyMceButton(button) {
        var target = $();
        var nativeLabels = [];
        if (button.native_label) {
            nativeLabels.push(button.native_label);
        }
        if (button.native_labels && button.native_labels.length) {
            for (var labelIndex = 0; labelIndex < button.native_labels.length; labelIndex++) {
                nativeLabels.push(button.native_labels[labelIndex]);
            }
        }
        if (button.native_icon) {
            target = $('.mce-toolbar:not(.wgaddon-tinymce-toolbar) .mce-i-' + button.native_icon)
                .closest('.mce-btn')
                .find('button')
                .first();
        }
        if (!target.length && nativeLabels.length) {
            target = $('.mce-toolbar:not(.wgaddon-tinymce-toolbar) .mce-btn, .mce-toolbar:not(.wgaddon-tinymce-toolbar) .mce-menubtn, .mce-toolbar:not(.wgaddon-tinymce-toolbar) .mce-splitbtn')
                .filter(function() {
                    var label = $(this).attr('aria-label') || '';
                    for (var i = 0; i < nativeLabels.length; i++) {
                        if (label === nativeLabels[i] || label.indexOf(nativeLabels[i]) !== -1) {
                            return true;
                        }
                    }
                    return false;
                })
                .find('button')
                .first();
        }
        if (!target.length && nativeLabels.length) {
            target = $('.mce-toolbar:not(.wgaddon-tinymce-toolbar) .mce-btn, .mce-toolbar:not(.wgaddon-tinymce-toolbar) .mce-menubtn').filter(function() {
                var text = $(this).text().replace(/\s+/g, ' ');
                for (var i = 0; i < nativeLabels.length; i++) {
                    if (text.indexOf(nativeLabels[i]) !== -1) {
                        return true;
                    }
                }
                return false;
            }).find('button').first();
        }
        if (target.length) {
            if (button.native_probe) {
                return true;
            }
            if (button.native_open) {
                var wrapper = target.closest('.mce-btn, .mce-menubtn, .mce-splitbtn');
                var opener = wrapper.find('button.mce-open, button[id$="-open"], .mce-open').last();
                if (opener.length) {
                    target = opener;
                }
            }
            triggerTinyMceNativeClick(target);
            return true;
        }
        return false;
    }

    function triggerTinyMceNativeClick(target) {
        var element = target && target.length ? target.get(0) : null;
        if (!element) {
            return;
        }
        var events = ['mousedown', 'mouseup', 'click'];
        for (var i = 0; i < events.length; i++) {
            try {
                var event = document.createEvent('MouseEvents');
                event.initMouseEvent(events[i], true, true, window, 1, 0, 0, 0, 0, false, false, false, false, 0, null);
                element.dispatchEvent(event);
            } catch (ignore) {
                target.trigger(events[i]);
            }
        }
        if (typeof element.click === 'function') {
            try {
                element.click();
            } catch (ignoreClick) {}
        }
    }

    function editorHasPlugin(editor, pluginName) {
        if (!pluginName) {
            return true;
        }
        if (!editor || !editor.plugins) {
            return false;
        }
        return !!editor.plugins[pluginName];
    }

    function tinyMcePluginBaseUrl() {
        if (window.tinymce && tinymce.baseURL) {
            return tinymce.baseURL.replace(/\/$/, '') + '/plugins';
        }
        var base = '';
        $('script[src*="tinymce"]').each(function() {
            var src = $(this).attr('src') || '';
            var match = src.match(/^(.*?\/tinymce4?)\/tinymce(?:\.min)?\.js/i);
            if (match && match[1]) {
                base = match[1] + '/plugins';
                return false;
            }
            match = src.match(/^(.*?)\/tinymce(?:\.min)?\.js/i);
            if (match && match[1]) {
                base = match[1] + '/plugins';
                return false;
            }
        });
        if (base) {
            return base;
        }
        return '<?php echo htmlspecialchars($wgaddonBaseUrl, ENT_QUOTES, 'UTF-8'); ?>/static/plugins/tinymce4/plugins';
    }

    function loadScriptWithFallback(urls, callback) {
        var index = 0;
        function next() {
            if (index >= urls.length) {
                callback(false, '');
                return;
            }
            var url = urls[index++];
            var script = document.createElement('script');
            script.src = url;
            script.async = true;
            script.onload = function() {
                callback(true, url);
            };
            script.onerror = function() {
                if (script.parentNode) {
                    script.parentNode.removeChild(script);
                }
                next();
            };
            document.head.appendChild(script);
        }
        next();
    }

    function activateLoadedPlugin(editor, pluginName) {
        if (!editor || !pluginName || editorHasPlugin(editor, pluginName) || !window.tinymce || !tinymce.PluginManager) {
            return editorHasPlugin(editor, pluginName);
        }
        try {
            var pluginFactory = tinymce.PluginManager.get(pluginName);
            if (typeof pluginFactory === 'function') {
                editor.plugins[pluginName] = pluginFactory(editor, tinyMcePluginBaseUrl() + '/' + pluginName) || {};
                return true;
            }
        } catch (error) {
            sendDebugLog('tinymce plugin activation failed', {
                plugin: pluginName,
                error: String(error && error.message ? error.message : error)
            });
        }
        return editorHasPlugin(editor, pluginName);
    }

    function ensureTinyMcePlugins(editor, done) {
        var required = [];
        var seen = {};
        for (var i = 0; i < wgaddonTinyButtons.length; i++) {
            var pluginName = wgaddonTinyButtons[i].requires_plugin || '';
            if (pluginName && !seen[pluginName] && !editorHasPlugin(editor, pluginName)) {
                seen[pluginName] = true;
                required.push(pluginName);
            }
        }
        if (!required.length) {
            done();
            return;
        }

        var base = tinyMcePluginBaseUrl();
        var index = 0;
        function loadNext() {
            if (index >= required.length) {
                done();
                return;
            }
            var pluginName = required[index++];
            if (editorHasPlugin(editor, pluginName) || wgaddonPluginLoadState[pluginName] === 'loaded') {
                activateLoadedPlugin(editor, pluginName);
                loadNext();
                return;
            }
            if (wgaddonPluginLoadState[pluginName] === 'failed') {
                loadNext();
                return;
            }
            wgaddonPluginLoadState[pluginName] = 'loading';
            loadScriptWithFallback([
                base + '/' + pluginName + '/plugin.min.js',
                base + '/' + pluginName + '/plugin.js'
            ], function(success, url) {
                if (success && activateLoadedPlugin(editor, pluginName)) {
                    wgaddonPluginLoadState[pluginName] = 'loaded';
                    sendDebugLog('tinymce plugin loaded', {
                        plugin: pluginName,
                        url: url
                    });
                } else {
                    wgaddonPluginLoadState[pluginName] = 'failed';
                    sendDebugLog('tinymce plugin load failed', {
                        plugin: pluginName,
                        base: base
                    });
                }
                loadNext();
            });
        }
        loadNext();
    }

    function buttonIsAvailable(button, editor) {
        if (button.requires_plugin && !editorHasPlugin(editor, button.requires_plugin)) {
            return false;
        }
        if (button.native_icon || button.native_label) {
            return clickNativeTinyMceButton($.extend({}, button, { native_probe: true })) || !!button.command;
        }
        return !!button.command;
    }

    function runTinyMceCommand(button) {
        var editor = getTinyMceEditor();
        sendDebugLog('tinymce command', {
            key: button.key || '',
            command: button.command || '',
            nativeIcon: button.native_icon || '',
            nativeLabel: button.native_label || '',
            editorId: editor && editor.id ? editor.id : '',
            editorFound: !!editor
        });

        if (button.requires_plugin && !editorHasPlugin(editor, button.requires_plugin)) {
            sendDebugLog('tinymce command skipped: missing plugin', {
                key: button.key || '',
                requiredPlugin: button.requires_plugin || ''
            });
            return;
        }

        if (clickNativeTinyMceButton(button)) {
            return;
        }

        if (!editor) {
            return;
        }
        editor.focus();
        if (button.command) {
            editor.execCommand(button.command, false, null);
        }
    }

    function tinyButtonHtml(button, index, total) {
        var classes = 'mce-widget mce-btn wgaddon-tinymce-btn';
        if (index === 0) {
            classes += ' mce-first';
        }
        if (index === total - 1) {
            classes += ' mce-last';
        }
        var iconName = button.icon || button.native_icon || '';
        var icon = iconName ? '<i class="mce-ico mce-i-' + iconName + '"></i>' : '<span class="mce-txt">' + $('<div>').text(button.text || button.label).html() + '</span>';
        return '<div class="' + classes + '" tabindex="-1" role="button" aria-label="' + $('<div>').text(button.label).html() + '">' +
            '<button type="button" tabindex="-1" data-wgaddon-key="' + $('<div>').text(button.key).html() + '" title="' + $('<div>').text(button.label).html() + '">' +
            icon +
            '</button></div>';
    }

    function addTinyMceToolbar() {
        if (!wgaddonTinyButtons.length) {
            return true;
        }
        if ($('#wgaddon-tinymce-toolbar').length) {
            return true;
        }
        if (wgaddonTinyToolbarBuilding) {
            return false;
        }

        var toolbarGroupBody = $('.mce-tinymce .mce-toolbar-grp > .mce-container-body.mce-stack-layout').first();
        if (!toolbarGroupBody.length) {
            var readyState = {
                tinyMceAvailable: !!window.tinymce,
                editorCount: window.tinymce && tinymce.editors ? tinymce.editors.length : 0,
                toolbarGroups: $('.mce-toolbar-grp').length,
                tinyContainers: $('.mce-tinymce').length
            };
            var readyText = JSON.stringify(readyState);
            if (readyText !== wgaddonLastReadyLog) {
                wgaddonLastReadyLog = readyText;
                sendDebugLog('tinymce toolbar not ready', readyState);
            }
            return false;
        }

        var editor = getTinyMceEditor();
        if (!editor) {
            return false;
        }
        if (!wgaddonTinyPluginsChecked) {
            wgaddonTinyToolbarBuilding = true;
            ensureTinyMcePlugins(editor, function() {
                wgaddonTinyPluginsChecked = true;
                wgaddonTinyToolbarBuilding = false;
                buildTinyMceToolbar();
            });
            return false;
        }

        return buildTinyMceToolbar();
    }

    function buildTinyMceToolbar() {
        if ($('#wgaddon-tinymce-toolbar').length) {
            return true;
        }

        var toolbarGroupBody = $('.mce-tinymce .mce-toolbar-grp > .mce-container-body.mce-stack-layout').first();
        var buttons = '';
        var editor = getTinyMceEditor();
        var activeButtons = [];
        var skippedButtons = [];
        for (var i = 0; i < wgaddonTinyButtons.length; i++) {
            if (buttonIsAvailable(wgaddonTinyButtons[i], editor)) {
                activeButtons.push(wgaddonTinyButtons[i]);
            } else {
                skippedButtons.push({
                    key: wgaddonTinyButtons[i].key || '',
                    requiresPlugin: wgaddonTinyButtons[i].requires_plugin || '',
                    nativeIcon: wgaddonTinyButtons[i].native_icon || '',
                    nativeLabel: wgaddonTinyButtons[i].native_label || '',
                    command: wgaddonTinyButtons[i].command || ''
                });
            }
        }
        for (var j = 0; j < activeButtons.length; j++) {
            buttons += tinyButtonHtml(activeButtons[j], j, activeButtons.length);
        }

        if (!activeButtons.length) {
            sendDebugLog('tinymce toolbar skipped: no available buttons', {
                configuredButtons: wgaddonTinyButtons.length,
                skippedButtons: skippedButtons
            });
            return true;
        }

        var toolbar = $(
            '<div id="wgaddon-tinymce-toolbar" class="mce-container mce-toolbar mce-stack-layout-item wgaddon-tinymce-toolbar" role="toolbar">' +
                '<div class="mce-container-body mce-flow-layout">' +
                    '<div class="mce-container mce-flow-layout-item mce-first mce-last mce-btn-group" role="group">' +
                        '<div class="wgaddon-tinymce-toolbar-body">' + buttons + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        toolbar.on('click', 'button', function(event) {
            event.preventDefault();
            var key = $(this).data('wgaddon-key');
            for (var i = 0; i < activeButtons.length; i++) {
                if (activeButtons[i].key === key) {
                    runTinyMceCommand(activeButtons[i]);
                    return;
                }
            }
        });

        toolbarGroupBody.append(toolbar);
        sendDebugLog('tinymce toolbar inserted', {
            configuredButtons: wgaddonTinyButtons.length,
            activeButtons: activeButtons.length,
            skippedButtons: skippedButtons,
            toolbarGroups: $('.mce-toolbar-grp').length,
            tinyMceAvailable: !!window.tinymce,
            editorPlugins: editor && editor.plugins ? Object.keys(editor.plugins) : []
        });
        return true;
    }

    function addAdminMenuLink() {
        if ($('.wgaddon-admin-link').length) {
            return true;
        }

        var link = '<a class="dropdown-item wgaddon-admin-link" href="<?php echo htmlspecialchars($wgaddonAdminUrl, ENT_QUOTES, 'UTF-8'); ?>">TavIR WG Addon</a>';
        var tudastar = $('#head-navbar .dropdown-menu a[href*="webgalamb.hu/tudastar"], .navbar .dropdown-menu a[href*="webgalamb.hu/tudastar"]').last();
        if (tudastar.length) {
            tudastar.after(link);
            return true;
        }

        var egyebMenu = $('#head-navbar .dropdown-menu, .navbar .dropdown-menu').filter(function() {
            return $(this).closest('.dropdown').text().indexOf('Egy') !== -1;
        }).last();
        if (egyebMenu.length) {
            egyebMenu.append('<div class="dropdown-divider wgaddon-admin-link"></div>' + link);
            return true;
        }

        var sideMenu = $('#sidebar ul.components, nav#sidebar ul.components').first();
        if (sideMenu.length) {
            sideMenu.append('<hr><li class="wgaddon-admin-link"><a href="<?php echo htmlspecialchars($wgaddonAdminUrl, ENT_QUOTES, 'UTF-8'); ?>">TavIR WG Addon</a></li>');
            return true;
        }

        return false;
    }

    function addDebugBanner() {
        if ($('#wgaddon-debug-banner').length) {
            return true;
        }
        try {
            if (window.sessionStorage && sessionStorage.getItem('wgaddonDebugBannerClosed') === '1') {
                return true;
            }
        } catch (ignore) {}

        $('body').prepend(
            '<div id="wgaddon-debug-banner">' +
            'WG Addon debug aktív - működési log: files/wgaddon.log' +
            '<button type="button" aria-label="Debug sáv bezárása" title="Debug sáv bezárása">×</button>' +
            '</div>'
        );
        $('#wgaddon-debug-banner button').on('click', function() {
            $('#wgaddon-debug-banner').remove();
            try {
                if (window.sessionStorage) {
                    sessionStorage.setItem('wgaddonDebugBannerClosed', '1');
                }
            } catch (ignore) {}
            sendDebugLog('debug banner closed', {});
        });
        return true;
    }

    function fixImageDialog() {
        var fixed = 0;
        $('.mce-window .mce-browsebutton').each(function() {
            var browse = $(this);
            browse.css({
                width: '220px',
                height: '34px',
                minHeight: '34px',
                overflow: 'hidden'
            });
            browse.find('input[type="file"]').css({
                position: 'absolute',
                left: 0,
                top: 0,
                width: '100%',
                height: '100%',
                opacity: 0,
                cursor: 'pointer'
            });
            fixed++;
        });
        return fixed > 0;
    }

    function sendDebugLog(eventName, details) {
        <?php if ($wgaddonDebug) { ?>
        var payload = {
            event: eventName,
            details: JSON.stringify(details || {})
        };

        if (window.navigator && window.navigator.sendBeacon && window.FormData) {
            var formData = new FormData();
            formData.append('event', payload.event);
            formData.append('details', payload.details);
            window.navigator.sendBeacon('<?php echo htmlspecialchars($wgaddonAdminUrl, ENT_QUOTES, 'UTF-8'); ?>?wgaddon_client_log=1', formData);
        } else {
            $.post('<?php echo htmlspecialchars($wgaddonAdminUrl, ENT_QUOTES, 'UTF-8'); ?>?wgaddon_client_log=1', payload);
        }

        if (window.console && window.console.info) {
            window.console.info('WGADDON DEBUG', eventName, details || {});
        }
        <?php } ?>
    }

    function debugDomPatterns() {
        <?php if ($wgaddonDebug) { ?>
        var state = {
            url: window.location.href,
            tables: $('div.dataTables_wrapper table, table.orderable-table, table.dataTable, .table-responsive table, .table-responsive-md table').length,
            csvButtons: $('.wgaddon-csv-export').length,
            tinyMceAvailable: !!window.tinymce,
            tinyMceEditor: !!getTinyMceEditor(),
            tinyToolbarGroups: $('.mce-toolbar-grp').length,
            tinyAddonToolbar: $('#wgaddon-tinymce-toolbar').length,
            adminMenuLinks: $('.wgaddon-admin-link').length,
            imageDialogs: $('.mce-window').length,
            imageBrowseButtons: $('.mce-window .mce-browsebutton').length
        };
        var stateText = JSON.stringify(state);
        if (stateText !== wgaddonLastDomDebug) {
            wgaddonLastDomDebug = stateText;
            sendDebugLog('dom patterns', state);
        }
        <?php } ?>
    }

    function runAddons() {
        <?php if ($wgaddonDebug) { ?>
        addDebugBanner();
        <?php } ?>

        <?php if ($wgaddonCsv) { ?>
        addCsvButtons();
        <?php } ?>

        <?php if ($wgaddonTinyToolbar) { ?>
        addTinyMceToolbar();
        <?php } ?>

        <?php if ($wgaddonAdminMenu) { ?>
        addAdminMenuLink();
        <?php } ?>

        <?php if ($wgaddonImageDialogFix) { ?>
        fixImageDialog();
        <?php } ?>

        <?php if ($wgaddonDebug) { ?>
        debugDomPatterns();
        <?php } ?>
    }

    $(function() {
        runAddons();
        var attempts = 0;
        var waiter = window.setInterval(function() {
            attempts++;
            runAddons();
            if (attempts >= 120) {
                window.clearInterval(waiter);
            }
        }, 500);
    });
})(window.jQuery);
</script>
<?php } ?>


