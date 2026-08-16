# Changelog / Valtozasnaplo
## 0.2.22 - 2026-08-16

- Fixed: the frontend injector no longer calls the admin-only `wgaddon_h()` helper function.
- The hook was active, but in debug mode frontend injection could stop with a fatal error.
- The CSV export code restored from 0.2.16 remains unchanged.

## Magyar

- Javítva: a frontend injektáló nem hívja meg az admin felülethez tartozó `wgaddon_h()` segédfüggvényt.
- A wgaddon bekötés aktiválódott, de debug módban a frontend injektálás fatal errorral megszakadhatott.
- A 0.2.16-ból visszahozott CSV export kód változatlan maradt.

## 0.2.21 - 2026-08-16

- Rebuilt from the 0.2.16 package as the baseline; versions 0.2.17-0.2.20 were not used as change references.
- Kept the 0.2.16 CSV export implementation unchanged.
- Renamed the admin log action from "Log megnyitása" to "Log mutatása".
- Enabling PHP error display now automatically enables PHP error file logging.
- Added runtime reassertion for PHP error reporting/logging settings.
- SQL backup and log view/download/delete are available only when `WGADDON_PASSWORD` is configured.
- Added cached GitHub latest-release checking with CSRF protection for manual checks.

## Magyar

- A csomag a 0.2.16 kiadásból épült újra; a 0.2.17-0.2.20 állományok nem voltak változásreferenciák.
- A 0.2.16 CSV export megoldása változatlan maradt.
- A "Log megnyitása" admin gomb szövege "Log mutatása" lett.
- A PHP képernyős hibakiírás bekapcsolása automatikusan bekapcsolja a fájlba naplózást.
- A PHP hibakezelés reassert módban újra beállítja a futási hibanaplózási beállításokat.
- Az SQL mentés és a log mutatása/letöltése/törlése csak beállított `WGADDON_PASSWORD` mellett engedélyezett.
- Gyorsítótárazott GitHub kiadásellenőrzés került be, kézi ellenőrzésnél CSRF védelemmel.
## 0.2.16 - 2026-06-29

- Removed the configuration hint from the login page.
- Changed the login page back button text to "Tovabb a Webgalambhoz".
- The log open button now jumps directly to the log viewer block.
- SQL backup now closes the PHP session before streaming the download, so settings save requests are not blocked by a long-running or cancelled SQL export.
- SQL backup checks for aborted client connections during export loops.

## Magyar

- A belépőoldalról kikerült a konfigurációs jelszómagyarázat.
- A belépőoldali vissza gomb szövege: "Tovább a Webgalambhoz".
- A Log megnyitása gomb közvetlenül a lognéző blokkhoz ugrik.
- Az SQL mentés letöltés előtt lezárja a PHP session írást, így egy hosszú vagy megszakított SQL export nem blokkolja a beállítások mentését.
- Az SQL mentés export közben figyeli a megszakított klienskapcsolatot.

## 0.2.15 - 2026-06-29

- Finalized admin naming as TavIR Webgalamb Addon.
- Changed the Webgalamb injected menu item to TavIR WG Addon.
- Removed the redundant admin status line.
- Reordered TinyMCE selector groups: TinyMCE additions first, Webgalamb factory toolbar items second.
- Updated UI labels: SQL mentes and TavIR Webgalamb Addon mukodesi log.

## Magyar

- Vegleges admin nevhasznalat: TavIR Webgalamb Addon.
- Webgalamb menupont neve: TavIR WG Addon.
- Felesleges admin statuszsor eltavolitva.
- TinyMCE csoportok sorrendje modositva: TinyMCE kiegeszitesek, majd Webgalamb gyari toolbar elemek.
- Feluleti szovegek pontositva: SQL mentes, TavIR Webgalamb Addon mukodesi log.

## 0.2.14 - 2026-06-29

- Moved README, terms and license document links into a separate admin block.
- Removed repeated per-button "Webgalamb factory toolbar" notes from the TinyMCE selector.
- Replaced long per-button TinyMCE notes with a `*` marker and one shared explanation.

## 0.2.13 - 2026-06-29

- Fixed early `WG_PATH` / `WGADDON_PATH` initialization to avoid blank pages when the Webgalamb configuration is loaded before the factory runtime is ready.
- Added `t-wgaddon/index.html` as an informational entry point.
- Organized documentation and license files for GitHub publishing.
- Added full GPL-3.0 license text to `LICENSE`.
- Added SPDX identifier `GPL-3.0-only` to addon PHP files.
- Clarified Webgalamb, TinyMCE and configuration-hook wording.

## Magyar

- Korai `WG_PATH` / `WGADDON_PATH` inicializalas javitva.
- `t-wgaddon/index.html` tajekoztato belepesi pont hozzaadva.
- Dokumentacios es licencallomanyok rendezve GitHub publikaciohoz.
- A `LICENSE` fajlba bekerult a teljes GPL-3.0 licencszoveg.
- Az addon PHP fajlok megkaptak a `GPL-3.0-only` SPDX azonositot.
- Pontosabb Webgalamb, TinyMCE es konfiguracios hook megfogalmazasok.




