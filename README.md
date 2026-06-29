# TavIR Webgalamb Addon

**Nem hivatalos, független admin- és felületbővítő addon Webgalamb 8 rendszerhez.**

A **TavIR Webgalamb Addon** olyan saját PHP és JavaScript alapú eszközcsomag, amely a Webgalamb 8 adminfelület használatát kényelmesebbé, átláthatóbbá és gyorsabban ellenőrizhetővé teszi. A csomag TinyMCE segédfunkciókat, táblázatos listákhoz CSV exportot, debug- és naplókezelést, PHP hibakijelzést, SQL mentést és mobilon is használható addon adminfelületet ad.

> **Fontos:** ez nem hivatalos Webgalamb modul, nem Webgalamb által kiadott kiegészítő, és nem tartalmaz Webgalamb gyári forráskódot vagy gyári állományt. A Webgalamb használatára továbbra is a Webgalamb jogtulajdonosának licencfeltételei vonatkoznak.

---

## Mire jó?

A TavIR Webgalamb Addon azoknak készült, akik Webgalamb 8 rendszert használnak, és szeretnének néhány gyakorlati adminisztrációs, szerkesztési és hibakeresési funkciót kényelmesebben elérni.

A csomag célja:

- gyorsabb munka a Webgalamb adminfelületen,
- látható táblázatok gyors CSV exportja,
- TinyMCE szerkesztőeszközök bővítése,
- Webgalamb gyári TinyMCE toolbar elemek kényelmesebb elérése,
- működési és PHP hibák naplózása,
- hibakeresési információk áttekinthetőbb megjelenítése,
- SQL adatbázismentés indítása az addon adminfelületéről,
- reszponzív, mobilon is használható addon adminoldal.

A projekt célja nem a Webgalamb gyári működésének kiváltása, hanem a meglévő adminfelület melletti, saját felelősségre használható segédréteg biztosítása.

---

## Fő funkciók

### CSV export Webgalamb listákhoz

Az addon böngészőoldali CSV export gombot ad a Webgalamb felületén látható táblázatos listákhoz.

Ez hasznos lehet például gyors ellenőrzéshez, kimutatáshoz, áttekintéshez vagy ideiglenes munkafájl készítéséhez.

A CSV export:

- böngészőben fut,
- a látható táblázatos listákból dolgozik,
- nem küld adatot külső szerverre,
- nem helyettesíti a Webgalamb gyári Import / Export funkcióját.

### TinyMCE extra eszköztár

A Webgalamb szerkesztőfelületén használt TinyMCE editorhoz az addon új, böngészőoldalon létrehozott eszköztársort tud hozzáadni.

A TinyMCE funkciók külön kapcsolhatók:

- TinyMCE kiegészítő gombok,
- Webgalamb gyári toolbar elemek külön csoportban,
- összes kijelölése,
- összes törlése,
- csak nem Webgalamb gyári elemek kijelölése.

Az addon a kiválasztott gombokhoz szükséges TinyMCE plugineket a Webgalamb saját TinyMCE könyvtárából próbálja betölteni. Ha egy plugin nincs meg vagy utólag nem aktiválható, az addon ezt debug módban naplózza.

### Képbeszúró ablak igazítása

A csomag képes a Webgalamb / TinyMCE képbeszúró ablakának megjelenési problémáit CSS / böngészőoldali igazítással javítani.

Ez különösen hasznos lehet régebbi adminfelületeken, ahol a tallózómező vagy a dialog elrendezése nem ideális.

### Debug és működési log

Az addon saját működési naplót vezethet:

```text
files/wgaddon.log
```

A log JSON-szerű, soronkénti bejegyzéseket tartalmaz. Az érzékeny mezők, például jelszó, token, CSRF vagy hasonló adatok rejtett jelölést kapnak.

Az addon adminfelületen elérhető:

- log megnyitása,
- log letöltése,
- log törlése,
- debug marker ellenőrzése,
- PHP hibanaplózási szint beállítása.

### PHP hibakeresés

Hibakereséshez az addon képes PHP hibákat naplózni és szükség esetén képernyőn is megjeleníteni.

Beállítható, hogy:

- minden PHP hiba, notice, warning és deprecated üzenet naplózódjon,
- vagy csak warning / error jellegű hibák kerüljenek a logba.

A képernyős PHP hibakiírás éles használatnál nem javasolt, csak hibakeresési időszakra.

### SQL adatbázismentés

Az addon adminfelületén külön SQL mentési funkció is található.

A mentés célja gyors rendszergazdai ellenőrzés vagy biztonsági munkamentés lehet. A teljes Webgalamb rendszer mentését nem helyettesíti.

Éles rendszerben továbbra is javasolt:

- teljes Webgalamb fájlmentés,
- teljes Webgalamb adatbázismentés,
- visszaállítási lehetőség ellenőrzése.

### Mobilbarát addon admin

A `wgaddon.php` adminfelület reszponzív:

- a gombok mobilon teljes szélességre tördelnek,
- a TinyMCE checkbox lista kis képernyőn egy oszlopos,
- a lognéző mező görgethető,
- a felület telefonon is használhatóbb.

---

## Mit nem csinál az addon?

A csomag szándékosan nem nyúl olyan területekhez, amelyek Webgalamb licenc-, biztonsági vagy stabilitási szempontból érzékenyek.

Az addon:

- nem tartalmaz Webgalamb gyári forráskódot,
- nem tartalmaz Webgalamb gyári állományt,
- nem dekódol kódolt Webgalamb fájlokat,
- nem nyitja ki a kódolt PHP állományokat,
- nem módosítja a `wg8.php` fájlt,
- nem módosítja a `tinymce_config.php` fájlt,
- nem küld adatot külső szerverre,
- nem használ Webgalamb gyári belépési logikát saját adminbelépésként,
- nem pótolja vagy módosítja a Webgalamb eredeti licencét.

---

## Telepítéshez melyik ZIP-et használd?

Telepítéshez **ne** a GitHub **Code → Download ZIP** gombját használd. Az a teljes forrás-repositoryt tölti le GitHub-fájlokkal, fejlesztői állományokkal és olyan elemekkel együtt, amelyek nem kellenek a Webgalamb könyvtárba.

Telepítéshez a **Releases** oldalon található kész csomagot használd:

```text
webgalamb-tavir-addon-x.y.z.zip
```

A release ZIP telepítésre előkészített csomag. A tartalma közvetlenül a Webgalamb főkönyvtárába bontható ki.

---

## Telepítés röviden

A release ZIP fő tartalma:

```text
wgaddon.php
t-wgaddon/
```

Ezeket a Webgalamb telepítési könyvtárába kell bemásolni vagy kibontani.

A részletes telepítési és használati dokumentáció a csomagban található:

```text
t-wgaddon/readme-wgaddon.txt
```

A jelenlegi csomag egy kis kézi bekötési blokkot igényel a felhasználó saját, licencelt Webgalamb telepítésében, a nyitott konfigurációs fájl végén. A pontos blokk és a lépések a `readme-wgaddon.txt` állományban szerepelnek.

Telepítés előtt erősen javasolt:

1. teljes Webgalamb fájlmentés,
2. teljes Webgalamb adatbázismentés,
3. a visszaállítási lehetőség ellenőrzése.

---

## Fájlszerkezet a telepítőcsomagban

```text
wgaddon.php
t-wgaddon/
  index.html
  readme-wgaddon.txt
  README.md
  LICENSE
  LICENSE-DOCS.md
  NOTICE.md
  THIRD_PARTY_NOTICES.md
  DISCLAIMER.md
  CHANGELOG.md
  SECURITY.md
  wgaddon_core.php
  wgaddon_inject.php
  wgaddon_shutdown.php
```

A Webgalamb főkönyvtárában csak a `wgaddon.php` marad közvetlenül. Az addon saját fájljai, dokumentációi és licencállományai a `t-wgaddon/` könyvtár alatt vannak.

---

## Tesztelt környezet

A csomag dokumentációja szerint a tesztelt Webgalamb verzió:

```text
Webgalamb 8+ v8.1.0
```

Más Webgalamb 8 / 8+ verziókon a működés külön ellenőrzést igényel.

A GitHubon és a dokumentációban szereplő kompatibilitási állítás mindig a ténylegesen tesztelt verzióra vonatkozik. A későbbi Webgalamb-verziókhoz külön tesztelés javasolt.

---

## Kinek hasznos?

A TavIR Webgalamb Addon hasznos lehet:

- Webgalambot használó kisvállalkozásoknak,
- hírlevélküldő rendszert üzemeltető rendszergazdáknak,
- Webgalamb adminfelületet karbantartó technikai felhasználóknak,
- TinyMCE szerkesztőfelületet gyakran használó adminoknak,
- CSV exportot és gyors listakimentést igénylő felhasználóknak,
- hibakeresési és logolási eszközöket kereső üzemeltetőknek,
- Webgalamb 8 rendszert saját tárhelyen futtató felhasználóknak.

---

## Gyakori kérdések

### Ez hivatalos Webgalamb modul?

Nem. Ez egy nem hivatalos, független TavIR addon Webgalamb 8 rendszerhez.

### Tartalmaz Webgalamb gyári forráskódot?

Nem. A csomag saját addon fájlokat tartalmaz, Webgalamb gyári forráskódot vagy gyári állományt nem.

### Módosítja a Webgalamb gyári fájlokat?

A csomag nem módosítja a `wg8.php` vagy `tinymce_config.php` fájlt, és nem dekódol kódolt állományokat. A jelenlegi működéshez egy nyitott konfigurációs fájl végére kell kézi bekötési blokkot illeszteni a felhasználó saját, licencelt Webgalamb telepítésében. A részletek a `t-wgaddon/readme-wgaddon.txt` fájlban találhatók.

### Használható Webgalamb 8.5.0-val?

A csomag dokumentáltan Webgalamb 8+ v8.1.0 alatt lett tesztelve. Más verziókon, így 8.5.0 esetén is külön kompatibilitási ellenőrzés szükséges.

### Ez WordPress plugin vagy webshop-integráció?

Nem. Ez nem WordPress plugin, nem WooCommerce plugin és nem webshop-integráció. Ez egy Webgalamb admin- és felületbővítő addon.

### Küld adatot külső szerverre?

Nem. A dokumentáció szerint az addon nem küld adatot külső szerverre. A CSV export és a TinyMCE / DOM bővítés böngészőoldali működésre épül.

### Hol van a teljes kézikönyv?

A teljes telepítési, használati, hibakeresési és licencleírás itt található:

```text
t-wgaddon/readme-wgaddon.txt
```

---

## Biztonsági megjegyzések

Az addon saját fájljain végzett ellenőrzés szerint a csomagban nincs:

- `DROP`,
- `DELETE SQL`,
- `TRUNCATE`,
- `ALTER`,
- `eval`,
- `base64_decode`,
- gyári Webgalamb kód dekódolás,
- külső adatküldés.

Egyetlen szándékos fájltörlési lehetőség az addon működési log törlése:

```text
files/wgaddon.log
```

Ez az addon adminfelületéről, bejelentkezve, CSRF védelemmel történik.

Biztonsági hiba vagy sérülékenység bejelentéséhez lásd:

```text
SECURITY.md
```

---

## Licenc

A TavIR Webgalamb Addon saját PHP forráskódjának licence:

```text
GNU General Public License v3.0 only
SPDX-License-Identifier: GPL-3.0-only
```

A dokumentáció, README és leíró szövegek licence, ha külön nincs jelölve:

```text
Creative Commons Nevezd meg! - Így add tovább! 4.0 Nemzetközi
CC BY-SA 4.0
```

A fenti licencek kizárólag a TavIR által készített saját addon állományokra vonatkoznak. Nem vonatkoznak a Webgalamb gyári fájljaira, védjegyeire, dokumentációjára vagy bármely harmadik féltől származó elemre.

Részletek:

```text
LICENSE
LICENSE-DOCS.md
NOTICE.md
THIRD_PARTY_NOTICES.md
DISCLAIMER.md
```

---

## Nem hivatalos jogi és működési megjegyzés

A WEBGALAMB® szoftver nem része ennek az addonnak.

A TavIR Webgalamb Addon nem hivatalos, független addon Webgalamb 8 rendszerhez. A csomag nem tartalmaz Webgalamb gyári forráskódot, nem tartalmaz Webgalamb gyári állományt, nem dekódol kódolt Webgalamb fájlokat, és nem pótolja vagy módosítja a Webgalamb eredeti licencét.

A Webgalamb használatára továbbra is az eredeti Webgalamb licenc, valamint a Webgalamb jogtulajdonosának feltételei vonatkoznak.

---

## Kapcsolat

Készítő: **Cseh Róbert / TavIR**

Weboldal:

```text
https://www.tavir.hu/
```

E-mail:

```text
info@tavir.hu
```

GitHub repository:

```text
https://github.com/tavir/webgalamb-tavir-addon
```

---

## Keresési kulcsszavak

Webgalamb addon, Webgalamb 8 addon, Webgalamb 8 kiegészítés, TavIR Webgalamb Addon, Webgalamb TinyMCE, Webgalamb CSV export, Webgalamb debug, Webgalamb log, Webgalamb SQL mentés, Webgalamb admin eszköz, Webgalamb hírlevélküldő, Webgalamb felületbővítés, Webgalamb 8.1.0, nem hivatalos Webgalamb addon.
