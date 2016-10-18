<?php
/**
 * A WordPress fő konfigurációs állománya
 *
 * Ebben a fájlban a következő beállításokat lehet megtenni: MySQL beállítások
 * tábla előtagok, titkos kulcsok, a WordPress nyelve, és ABSPATH.
 * További információ a fájl lehetséges opcióiról angolul itt található:
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 *  A MySQL beállításokat a szolgáltatónktól kell kérni.
 *
 * Ebből a fájlból készül el a telepítési folyamat közben a wp-config.php
 * állomány. Nem kötelező a webes telepítés használata, elegendő átnevezni
 * "wp-config.php" névre, és kitölteni az értékeket.
 *
 * @package WordPress
 */

// ** MySQL beállítások - Ezeket a szolgálatótól lehet beszerezni ** //
/** Adatbázis neve */
define('DB_NAME', 'mridevco_globalhungary');

/** MySQL felhasználónév */
define('DB_USER', 'mridevco_admin');

/** MySQL jelszó. */
define('DB_PASSWORD', 'MoIst1991');

/** MySQL  kiszolgáló neve */
define('DB_HOST', 'localhost');

/** Az adatbázis karakter kódolása */
define('DB_CHARSET', 'utf8mb4');

/** Az adatbázis egybevetése */
define('DB_COLLATE', '');

/**#@+
 * Bejelentkezést tikosító kulcsok
 *
 * Változtassuk meg a lenti konstansok értékét egy-egy tetszóleges mondatra.
 * Generálhatunk is ilyen kulcsokat a {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org titkos kulcs szolgáltatásával}
 * Ezeknek a kulcsoknak a módosításával bármikor kiléptethető az összes bejelentkezett felhasználó az oldalról.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '=gCU[`O|*<KE+owKNVxem?<aE1C;x_672g***;8l=B$r7 q%JVZLuQ~O)s]#!`eb');
define('SECURE_AUTH_KEY', '1aKOYp34zv$.M.pSzHWlw>2g#x1Q7fvU=>&/u!#t(vAb^-~5a-0;$n(-*X<~!L0#');
define('LOGGED_IN_KEY', 'kmV/ZFG)%Xv`MR3~Id^Dk>tQlU-Q2V=RztYV]XRpQS>g|xH[Ry7zR#f@n,,s+N!o');
define('NONCE_KEY', 'o#Ss{qo=uge$b-~B+Nb]>qLa~nM6_sldlwFq]+%IOh@0+A</${85/eVQ@o~z7] 1');
define('AUTH_SALT',        'iNd#9;*zj`kRS8dilu{#>o(jZHcL;cl5}[okG-F54Tp%`=9A_DuU&U+FlH0xg,hx');
define('SECURE_AUTH_SALT', 'LfC(]<]!NcX*1ltl@jR2y*51yC*UfRXT.ptc>V0lEFWaG.{,+FJ}:Q<cziUjRxC]');
define('LOGGED_IN_SALT',   'U4Ya8FGJ1w&rJ}fc0?GwPo?W `T[}G+W$s.!.X<yf*fZ{or-h.y~TtlnHWR$V5A!');
define('NONCE_SALT',       '9qvO6MclIJs:6ERR<~+VG._jcmoCBj!IS AvZf2PB%fhxjC<)^?Oy~PzcE6M~M4:');

/**#@-*/

/**
 * WordPress-adatbázis tábla előtag.
 *
 * Több blogot is telepíthetünk egy adatbázisba, ha valamennyinek egyedi
 * előtagot adunk. Csak számokat, betűket és alulvonásokat adhatunk meg.
 */
$table_prefix  = 'wp_gh_';

/**
 * Fejlesztőknek: WordPress hibakereső mód.
 *
 * Engedélyezzük ezt a megjegyzések megjelenítéséhez a fejlesztés során.
 * Erősen ajánlott, hogy a bővítmény- és sablonfejlesztők használják a WP_DEBUG
 * konstansot.
 */
define('WP_DEBUG', false);
/* Multisite */


/* Ennyi volt, kellemes blogolást! */

/** A WordPress könyvtár abszolút elérési útja. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Betöltjük a WordPress változókat és szükséges fájlokat. */
require_once(ABSPATH . 'wp-settings.php');
