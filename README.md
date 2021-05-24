# kittenCMS
Simple and easy CMS for LAMP server

1. A rendszer Bemutatkozás

A rendszer célja, hogy egy rendkívül egyszerű CMS rendszert biztositson, ahol
a felhasználók könnyen tudjanak egy egyszerű weboldalt szerkeszteni.

2. rendszer követelmények

2.1 szerver oldali követelmények
A rendszer a népszerű LAMP technologiát használja szerver oldalon.
Ehhez a következő rendszer verziók elvártak:

- Webszerver: Apache 2
- PHP:        PHP 7.x vagy újabb.
- Adatbázis:  Mysql 5.5 vagy újabb.
              MariaDb 5.5 vagy újabb.

Apache-on be állitva kell lennie a php.ini fájlban:
file_uploads = On

2.2. Kliens oldali követelmények
A rendszer kilens oldalon javascript alapon dolgozza fel a szerver oldalról
érkező kéréseket, ezért a rendszer használatához nélkülözhetetlen a bekapcsolt
javascript támogatás.

A rendszer a következő js lib-ekkel van függőségben:
publikus rész:
- jquery 1.12.1

admin felület
- jquery 3.5.1
- jqueryUi 1.12.1
- underscore 1.12.1
- charts.js 2.9.4 (vagy 3.0.2)
- SunEditor.js 2.38.1  (https://github.com/JiHong88/SunEditor/tree/master/dist)

Ezek figyelembe vételével a böngésző támogatás a következőképpen alakul:
Microsoft Internet Explorer: 6+ Admin felület: 11
Firefox: utolsó 2 kiadás
Google Chrome: utolsó 2 kiadás (de elvben 45+)
Microsoft Edge: utolsó 2 kiadás
Safari: utolsó 2 kiadás
Opera: utolsó 2 kiadás

4. Telepítés
4.1. Adatbázis
Szükséges létrehozni a rendszer számára egy mysql adatbázis sémát. Ebben le kell
futtatni a telepitő csomag sql/alter_01.sql fájl állományát.

Ezt követően javasolt létrehozni egy technikai user-t akin keresztül a rendszer
eléri a saját tábláit.
Ennek a technikai user-nek a következő jogosultságot javasolt rendelni a
a rendszernek létrehozott sémára.
GRANT SELECT, INSERT, DELETE

4.2. Fájlok felmásolása
A telepítő csomagban található következő könyvtárak és teljes tartalmúk
másolása tárhelyre:
- admin
- config
- dal
- includes
- log
- rest
- templates
- uploads

A fenti könyvtárak közül az apache server technikai userének írási/olvasási jogot
kell adni a következőre:
- log
- uploads

Az összes többi könyvtár esetében read only jogosultság elégséges az apache
server technikai user számára

4.3. Config beállitása
A rendszer alap konfigurációs állományát a config könyvtárban lévő config.php
szerkesztésével lehet módositani.

A telepítéshez a következő configok átírása az alapértékekről nélkülözhetetlen a
rendszer működéséhez:

$dbServer           => Az adatbázis szerver elérése (ip cim, vagy alias)
$dbName             => Az adatbázis séma megnevezése
$dbUser             => A rendszer által az adatbázis kapcsolathoz használt user
$dbPwd              => A rendszer által az adatbázis kapcsolathoz használt user
                       jelszava (clear text módban)
$baseUrl            => A rendszert milyen url-en lehet elérni
                       (pl. https://peldaoldal.hu)
$passwordSalt       => Egyedileg generált string (pl. uuid).
                       Biztonsági okokból javasolt megváltoztatni.
                       Figyelem! Amikor ez megváltoztatásra kerül, a felhasználók
                       addig használt jelszavai érvényüket vesztik (az admin
                       felhasználóké is) és újat kell generálni!
                       A generálás leírását lásd az 5-ik fejezetben.

A configban még a következő értékeket változtathatunk:
$loginTiral         => Meghatározza, hogy kitiltás előtt mennyi alkalommal
                       próbálkozhat a felhasználó az admin oldalra történő
                       bejelentkezéssel.
                       Itt csak egész számot lehetséges megadni.
                       Ha 0 akkor nem történik soha kitiltás
                       de ez nem javasolt, mert így a rendszer kitett a
                       rosszindulatú brute force támadásnak.
$forrbidenTime      => Meghatározza, hogy mennyi időre kerüljön kitiltásra
                       az admin oldalról a felhasználó ha $loginTrial
                       paraméterben megadott alkalomnál többször próbálkozott
                       hibás jelszóval. Az érték percben számolt. Csak egész számot
                       lehet megadni.
                       Az értékét nem javasolt 30 alá állítani mert így a rendszer
                       kitett a rosszindulatú brute force támadásnak.
$logFileName        => A rendszer milyen fájlnévvel készítsen log állományt.
$logFilePath        => Meghatározza, hogy a log fájlt melyik könyvtárba készítse
                       a rendszer.
                       Fontos hogy a log könyvtárra az apache-t futtató
                       technikai felhasználónak írás/olvasási joga legyen.
$forrbiddenPasswords=> Az itt ; felsorolt jelszavakat nem választhatják a
                       felhasználók.
$uploadFileByteLimit=> Az itt megadott byte érte a maximum amekkora fájlt a
                       felhasználók feltölthetnek.

5. Első bejelentkezés az admin felületre.

Ha a fentiekben a javasoltnak megfelelően módosítottuk a $passwordSalt
értékét akkor az admin user-nek szükséges azt frissiteni az adatbázisban.

Ehhez a következő sql-t kell lefuttatni:
update sys_user set user_pwd = 'új password hash' where user_name = 'admin';

ahol:
admin = a módosítandó felhasználó (első telepítésnél még csak admin
felahsználó keletkezik.)
új password hash előállításához a következő módon kell a hashelni kivánt string-et
előllítáni:
újjelszó + "#" + $passwordSalt config.
pl. (ha $passwordSalt config értéke: 8bb95c8b-632a-41d5-9f84-4e1318659b2d)
'titkosjelszavam#8bb95c8b-632a-41d5-9f84-4e1318659b2d'

Ennek az sha256-os hash-ét kell előállítani. Ez a fenti példa alapján:
b480bcc9cfae94e8cae293f9a4616664a06d9035a2ce89e8a3a4c2b7826b83f2 lesz.
(sha256 előállítás sok féleképpen lehetséges, talán a legegyszerűbb online
convertert használni pl: https://coding.tools/sha256,
https://emn178.github.io/online-tools/sha256.html)

Így a következő sql-t futtatnák le:
update sys_user set
user_pwd = 'b480bcc9cfae94e8cae293f9a4616664a06d9035a2ce89e8a3a4c2b7826b83f2'
where user_name = 'admin';

Ezt követően a példa alapján az admin oldalon admin felhasználóval és
'titkosjelszavam' jelszó párossal be lehet lépni.

Ha nem változtattuk meg a telepítéskor a config-ban $passwordSalt config értékét
az első telepítés után akkor az admin felhasználó admin123 jelszóval tud belépni
(De ez igen gyenge védelem. Ha így tennénk mindenféleképpen változtassuk meg
az admin felhasználó jelszavát!!!)

Az admin felület elérhetősége /admin/index.php útvonalon található.
(pl. https://peldaoldal.hu/admin/index.php )

