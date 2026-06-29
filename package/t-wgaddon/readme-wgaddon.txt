TavIR Webgalamb Addon
====================================

Készítő: Cseh Róbert / TavIR
Kapcsolat: https://www.tavir.hu/ | info@tavir.hu
Verzió: 0.2.16
Dátum: 2026-06-29
GitHub: https://github.com/tavir/webgalamb-tavir-addon
Fejlesztői környezet: CODEX - ChatGPT 5.5 magas (Plus előfizetés)
Tesztelt Webgalamb verzió: 8.1.0
Kódolás: UTF-8

Tartalomjegyzék
---------------

1. Fontos felelősségi nyilatkozat
2. Mit nem csinál a csomag?
3. Webgalamb tulajdonos, eredeti licenc és addon licenc
4. Fájlok
5. Egyetlen szükséges bekötés
6. Telepítés lépésenként
7. Funkciók
8. TinyMCE extra gombok és pluginbetöltés
9. CSV export
10. Debug és hibakeresés
11. Biztonsági ellenőrzés
12. Változások
13. Kapcsolat

1. Fontos felelősségi nyilatkozat
---------------------------------

Ez az addon "AS IS", vagyis mindenféle garancia nélkül használható.
Nem hivatalos, független addon Webgalamb 8 rendszerhez, és független az eredeti Webgalamb licenctől, szoftvertulajdonostól és fejlesztőtől.

Nem hivatalos, független addon Webgalamb 8 rendszerhez.
A csomag nem tartalmaz Webgalamb gyári forráskódot, nem tartalmaz Webgalamb gyári állományt, nem dekódol kódolt Webgalamb fájlokat, és nem pótolja vagy módosítja a Webgalamb eredeti licencét. A Webgalamb használatára továbbra is a Webgalamb jogtulajdonosának licencfeltételei vonatkoznak.

Telepítés és használat saját felelősségre történik. Hibás működés, adatvesztés, levélküldési hiba, szoftverhiba, üzemszünet vagy bármilyen kár esetén semmiféle kárigény nem támasztható.
Telepítés előtt erősen javasolt, éles rendszer esetén elvárt: teljes Webgalamb fájlmentés, teljes Webgalamb adatbázismentés, és a visszaállítási lehetőség ellenőrzése.

2. Mit nem csinál a csomag?
---------------------------

- Nem dekódol gyári Webgalamb állományt.
- Nem nyitja ki a kódolt PHP fájlokat.
- Nem módosítja a gyári állományokat és állapotot.
- Nem küld adatot külső szerverre.


3. Webgalamb tulajdonos, eredeti licenc és addon licenc
-------------------------------------------------------

A WEBGALAMB® szoftver nem része ennek az addonnak.

A Webgalamb hivatalos oldala: https://www.webgalamb.hu/

A Webgalamb céginformációs oldala: https://www.webgalamb.hu/ceginformaciok.php

A Webgalamb hivatalos licencdokumentuma: https://www.webgalamb.hu/docs/Webgalamb_8%2B_licencszerzodes.pdf

Ez a csomag nem hivatalos, független addon Webgalamb 8 rendszerhez.
A csomag nem tartalmaz Webgalamb gyári forráskódot, nem tartalmaz Webgalamb gyári állományt, nem dekódol kódolt Webgalamb fájlokat, és nem pótolja vagy módosítja a Webgalamb eredeti licencét.

A Webgalamb használatára továbbra is az eredeti Webgalamb licenc, valamint a Webgalamb jogtulajdonosának feltételei vonatkoznak.

A TavIR Webgalamb Addon saját forráskódjának licence:

GNU General Public License v3.0 only
SPDX azonosító: GPL-3.0-only

A dokumentáció, README és leíró szövegek licence, ha külön nincs jelölve:

Creative Commons Nevezd meg! - Így add tovább! 4.0 Nemzetközi
CC BY-SA 4.0

A fenti licencek kizárólag a TavIR által készített saját addon állományokra vonatkoznak. Nem vonatkoznak a Webgalamb gyári fájljaira, védjegyeire, dokumentációjára vagy bármely harmadik féltől származó elemre.

Kérés: ha módosított vagy továbbadott változat készül, értesítsd a készítőt a Kapcsolat fejezetben megadott elérhetőségen. Ez udvariassági kérés, nem licencfeltétel.

Licenc és jogi állományok:
- LICENSE: teljes GPL-3.0 licencszöveg a saját addon forráskódra.
- LICENSE-DOCS.md: CC BY-SA 4.0 jelölés a dokumentációra.
- NOTICE.md: általános projektmegjegyzések.
- THIRD_PARTY_NOTICES.md: Webgalamb és TinyMCE hivatkozások.
- DISCLAIMER.md: felelősségi nyilatkozat.

4. Fájlok
---------

Másold a Webgalamb telepítési mappájába (a továbbiakban: <WEBGALAMB>):
1. wgaddon.php
2. t-wgaddon/readme-wgaddon.txt
3. t-wgaddon/README.md
4. t-wgaddon/LICENSE
5. t-wgaddon/LICENSE-DOCS.md
6. t-wgaddon/NOTICE.md
7. t-wgaddon/THIRD_PARTY_NOTICES.md
8. t-wgaddon/DISCLAIMER.md
9. t-wgaddon/CHANGELOG.md
10. t-wgaddon/SECURITY.md
11. t-wgaddon/wgaddon_core.php
12. t-wgaddon/wgaddon_inject.php
13. t-wgaddon/wgaddon_shutdown.php
14. t-wgaddon/index.html

A Webgalamb főkönyvtárában csak a wgaddon.php marad. Az addon saját fájljai, licencei és dokumentációi a t-wgaddon könyvtár alatt vannak.

5. Egyetlen szükséges bekötés
-----------------------------
Ezt csak a teljes mentés után végezdd el!
A <WEBGALAMB>/files/wg8conf.php állományról külön biztonsági mentés is javasolt - ebben vannak az adatbázis hozzáférési adatok. Ezen adatok módosítása a rendszer működésképtelenségét okozza!
A WebGalamb telepítési könyvtárában a nem kódolt <WEBGALAMB>/files/wg8conf.php fáljban add meg az addon saját jelszavát. Ha üres, az addon nem kér belépést.
A nyitott wg8conf.php fájl VÉGÉRE kell bemásolni folytatólagosan a --cut--/--cut-- közti rész:

--- cut ---

define("WGADDON_PASSWORD", '');

/* WGADDON-SHUTDOWN-HOOK 0.2.16 */
if (!defined('WGADDON_ADMIN_BOOT')) {
    if (!defined('WG_PATH')) {
        define('WG_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    }
    if (!defined('WGADDON_PATH')) {
        define('WGADDON_PATH', WG_PATH . 't-wgaddon' . DIRECTORY_SEPARATOR);
    }
    $wgaddonCore = WGADDON_PATH . 'wgaddon_core.php';
    if (is_file($wgaddonCore)) {
        include_once $wgaddonCore;
        if (class_exists('WGAddon')) {
            WGAddon::configureRuntime();
        }
    }
    if (!defined('WGADDON_SHUTDOWN_ACTIVE')) {
        define('WGADDON_SHUTDOWN_ACTIVE', true);
        register_shutdown_function(function () {
            $wgaddonShutdown = WGADDON_PATH . 'wgaddon_shutdown.php';
            if (is_file($wgaddonShutdown)) { include $wgaddonShutdown; }
        });
    }
}
--- cut ---

Ha a <WEBGALAMB>/files/wg8conf.php végén nincs lezáró ?>, akkor a hozzáfűzés után ne is legyen!

6. Telepítés lépésenként
------------------------

1. Készíts teljes fájlmentést.
2. Készíts teljes adatbázismentést.
3. Másold a wgaddon.php fájlt a Webgalamb főkönyvtárába (<WEBGALAMB>).
4. Másold a t-wgaddon könyvtárat a Webgalamb főkönyvtárába.
5. A <WEBGALAMB>/files/wg8conf.php végére másold be a WGADDON-SHUTDOWN-HOOK blokkot.
6. Nyisd meg: <WEBGALAMB>/wgaddon.php
7. Ha a WGADDON_PASSWORD nem üres, lépj be az addon saját jelszavával.
8. Kapcsold be a kívánt funkciókat.
9. Nyomj Mentés gombot.
10. Nyisd meg: WebGalamb admin felületét a szokásos módon a <WEBGALAMB>/wg8.php elindításával.
11. Debug esetben ellenőrizd a HTML forrásban: "WGADDON-INJECT-LOADED" szöveg meglétét.

7. Funkciók
-----------

Kapcsolható funkciók: 
- listák CSV export,
- TinyMCE extra eszköztár: TinyMCE gombok egyenkénti engedélyezése, 
- TinyMCE képbeszúró ablak igazítása, 
- PHP Error () engedélyezés/tiltás marker,
- files/wgaddon.log napló, megnyitás/letöltés/törlés.
- PHP error log, 
- SQL adatbázis export.
A wgaddon admin mobilon is reszponzív: a gombok teljes szélességre tördelnek, a TinyMCE checkbox lista egy oszlopos lesz, a log textbox kisebb képernyőn is görgethető.

8. TinyMCE extra gombok és pluginbetöltés
-----------------------------------------

A TinyMCE-ben a szerkesztőfelület betöltése után, böngészőoldalon hoz létre egy új eszköztár sort.
A TinyMCE admin lista két csoportra van bontva:
- TinyMCE kiegészítések, majd
- Webgalamb gyári toolbar elemek.
A gyári Webgalamb toolbar listákban szereplő ikonok az beállítófelületen külön csoportban szerepelnek. 
A TinyMCE választónál három gyorsgomb van: Összes kijelölése, Összes törlése, Csak nem Webgalamb gyáriak.

Webgalamb eredeti elsődleges toolbar: bold italic underline strikethrough | formatselect fontselect fontsizeselect | forecolor backcolor | wg6links
Webgalamb eredeti másodlagos toolbar: cut copy paste pastetext pasteword codemagic | search replace | bullist numlist | outdent indent blockquote | alignleft aligncenter alignright alignjustify | undo redo | link unlink image cleanup | table visualaid | hr removeformat | searchreplace imagetools textpattern colorpicker lists | code preview emoticons

Ha a kiválasztott gombhoz TinyMCE plugin tartozik, az addon megpróbálja betölteni a plugint a Webgalamb saját TinyMCE könyvtárából. Ha nincs meg vagy nem aktiválható utólag, az addon debug logban jelzi.
A WebGalamb gyárilag bekapcsolt toolbar ikonjai a kiegészítőként bekapcsolva nem minden esetben működnek!

9. CSV export
-------------

A CSV export addon oldali, böngészőben fut. A látható táblázatos listákhoz ad gyors CSV export gombot. Nem helyettesíti a Webgalamb gyári Import - Export funkcióját.

10. Debug és hibakeresés
-----------------------

Debug log: <WEBGALAMB>/files/wgaddon.log. Minden addon debug bejegyzés egy soros JSON. A jelszó, psw/pwd, token és CSRF mezők [hidden] jelölést kapnak.
A PHP hibanaplózás szintje választható: minden PHP hiba/notice/warning/deprecated, vagy csak warning/error jellegű hibák.
A PHP hibák képernyőre írását csak hibakeresés idejére javasolt bekapcsolni. Ilyenkor az addon saját hiba blokkot is kiír, és a hibát a files/wgaddon.log fájlba naplózza. Éles használatnál kapcsold ki.
A debug alsó sáv alapértelmezetten nem látszik; csak bekapcsolt debug mellett jelenik meg, és a bezárás után az adott böngésző munkamenetben zárva marad.

11. Biztonsági ellenőrzés
-------------------------

A saját addon fájlokban ellenőrizve: nincs DROP, DELETE SQL, TRUNCATE, ALTER, eval, base64_decode, gyári kód dekódolás, külső adatküldés. Egyetlen szándékos fájltörlési lehetőség: <WEBGALAMB>/files/wgaddon.log, csak addon adminból, bejelentkezve, CSRF tokennel lehetséges.

12. Változások
--------------

0.2.16 - 2026-06-29
- A belépőoldalról kikerült a konfigurációs jelszómagyarázat.
- A belépőoldali vissza gomb szövege: "Tovább a Webgalambhoz".
- A Log megnyitása gomb közvetlenül a lognéző blokkhoz ugrik.
- Az SQL mentés letöltés előtt lezárja a PHP session írást, így egy hosszú vagy megszakított SQL export nem blokkolja a beállítások mentését.
- Az SQL mentés export közben figyeli a megszakított klienskapcsolatot.

0.2.15 - 2026-06-29
- Az admin felület végleges névhasználata: TavIR Webgalamb Addon.
- A Webgalamb menüpont neve: TavIR WG Addon.
- A felesleges admin státuszsor kikerült.
- A TinyMCE csoportok sorrendje módosult: először a TinyMCE kiegészítések, utána a Webgalamb gyári toolbar elemek.
- A felületen az SQL mentés megnevezése egységesítve lett.
- Az addon log blokk címe: TavIR Webgalamb Addon működési log.

0.2.14 - 2026-06-29
- Az adminban a README, feltételek és licenc állományok külön blokkba kerültek.
- A TinyMCE gombválasztóban a gombnevek mellől kikerültek az ismétlődő gyári toolbar megjegyzések.
- A TinyMCE plugin/megjegyzés jelölés gombonként rövid csillag lett, közös magyarázó sorral.

0.2.13 - 2026-06-29
- Fehér képernyőt okozó korai WG_PATH függés javítva.
- A wg8conf.php hook és az addon core önállóan beállítja a WG_PATH/WGADDON_PATH útvonalakat, ha a gyári wg8.php indulásakor ezek még nem léteznek.
- A t-wgaddon könyvtár kapott index.html tájékoztató oldalt, hogy közvetlen megnyitáskor ne üres oldal jelenjen meg.

0.2.12 - 2026-06-29
- Az addon saját állományai a t-wgaddon könyvtár alá kerültek; a Webgalamb főkönyvtárában csak a wgaddon.php marad.
- A files/wg8conf.php bekötési blokkja a t-wgaddon/wgaddon_core.php és t-wgaddon/wgaddon_shutdown.php fájlokra mutat.
- A TinyMCE admin lista gyári Webgalamb toolbar és addon kiegészítő csoportokra vált szét.
- A PHP hibanaplózás szintje állítható: minden hiba vagy csak warning/error jellegű hibák.
- A PHP hibák képernyős visszajelzése saját, nem eltűnő hiba blokkban jelenik meg, és a hiba a files/wgaddon.log fájlba is bekerül.
- A debug alsó sáv csak bekapcsolt debug mellett jelenik meg.

0.2.11 - 2026-06-29
- Saját addon adatbázisréteg került be mysqli elsődleges használattal.
- Az addon a DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE és DB_PREFIX konstansokat a files/wg8conf.php fájlból olvassa.
- A wgaddon.php és a SQL mentés közvetlen mysql_* hívásai kiváltva WGAddon adatbázis wrapperrel.
- A shutdown loader is az addon saját adatbázis wrapperét használja.
- A wgaddon admin továbbra sem használja a Webgalamb gyári class.main.php / belephet belépési logikáját.

0.2.10 - 2026-06-29
- A wgaddon admin boot alatt a files/wg8conf.php hook nem indít frontend/runtime injektálást.
- A session indítás kompatibilisebb lett régebbi PHP környezetekkel.
- Adatbázis vagy mysql_* hiány esetén az addon látható hibaüzenetet ad fehér képernyő helyett.

0.2.9 - 2026-06-29
- A wgaddon belépés saját WGADDON_PASSWORD alapú belépésre váltott.
- Üres WGADDON_PASSWORD esetén az addon nem kér belépést.
- A Webgalamb gyári class.main.php / belephet belépési logika kikerült a wgaddon.php-ból.
- README.md hozzáadva GitHub használathoz, kiemelt nem hivatalos jogi megjegyzéssel.
- NOTICE.md, LICENSE-DOCS.md, THIRD_PARTY_NOTICES.md és DISCLAIMER.md kétnyelvű lett.

0.2.8 - 2026-06-29
- A SQL mentés önálló admin blokkba került.
- A TavIR Webgalamb Addon működési log gombjai egy soros akciósávba kerültek.
- A Webgalamb gyári TinyMCE jelölés bővült: teljes képernyő, sablon, horgony, blokkok, nem törő szóköz.
- A PHP képernyős hibakiíráshoz a wg8conf hook korábban tölti be az addon runtime beállítást.
- A wgaddon admin figyelmeztet, hogy levélküldéshez natív wg8.php belépés is szükséges lehet.

0.2.7 - 2026-06-29
- Mobilon használható, reszponzív wgaddon.php adminfelület.
- Webgalamb gyári TinyMCE toolbar ikonok zárójeles jelölése.
- Új TinyMCE gyorsgomb: Csak nem Webgalamb gyáriak.
- GPL-3.0 forráskód licenc és CC BY-SA 4.0 dokumentációs licenc pontosítása.
- LICENSE, LICENSE-DOCS.md, NOTICE.md, THIRD_PARTY_NOTICES.md és DISCLAIMER.md hozzáadva.

0.2.6 - 2026-06-28
- TinyMCE legördülő/split gombok működése javítva.
- Használati feltételek hivatkozás bekerült.
- Webgalamb tulajdonosi/licenc hivatkozások bekerültek.

13. Kapcsolat
-------------

Név: Cseh Róbert / TavIR
Weboldal: https://www.tavir.hu/
E-mail: info@tavir.hu
GitHub repo: https://github.com/tavir/webgalamb-tavir-addon


