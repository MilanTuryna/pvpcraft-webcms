Websystem for HeroCraft.cz
=================

Websystem programmed in PHP (Nette) for minecraft server HeroCraft.cz. It's customized for this server. The system can be divided into several parts - web, administration, player's panel, statistics and helpdesk. System working on MySQL database.
All system messages on website are in Czech Language.

## Web (frontpage)
- homepage
  - server status
  - widgets (set in administration, for example: discord, facebook atd.)
- articles
- pages 
- articles archives
- sections
- ticket login

## Panel
The panel does not have many functions now, but we prepare more features.
- Authentication with same login as game
- Player auth information (date of register, date of last login, etc.)
- Ticket system
- Change game password
- Some statistics (with link to player's account on statistics part of websystem)

## Administration

### Content management
For better manipulation with content (pages and articles) is available [TinyMCE](https://www.tiny.cloud/) editor with this plugins:
`table | emoticons | link | image | code | charmap | preview | searchreplace | lists`.

#### Images upload
It's possible to upload images (maximal 10MB) to server but now only independently (without assignment to article) and after file upload add to article classical as image with file URL (copied from Upload section  in Administration). Images will be uploaded to www/upload folder.

### Roles System

Administration it hasn't role system, but it has Permissions system - each user has their own list of permissions

#### Available permissions:
excerpt from `App\Model\Admin\Roles\Permissions`:
```php
const ADMIN_FULL = "*";

const ADMIN_ARTICLES = "admin.articles";
const ADMIN_PAGES = "admin.pages";
const ADMIN_CATEGORIES = "admin.categories";

const ADMIN_MC_HELPERS = "admin.minecraft_helpers";
const ADMIN_MC_CHATLOG = "admin.minecraft_chatlog";
const ADMIN_MC_BANLIST = "admin.minecraft_banlist";
const ADMIN_MC_IPBANLIST = "admin.minecraft_ipbanlist";
const ADMIN_MC_ONLINEPLAYERS = "admin.minecraft_onlineplayers";
const ADMIN_MC_LUCKPERMS = "admin.minecraft_luckperms"

const ADMIN_MC_GAMES = "admin.minecraft_games";
const ADMIN_MC_SENIOR = "admin.minecraft_senior";
const ADMIN_MC_CLASSIC = "admin.minecraft_classic";

const ADMIN_CONTENT_MANAGER = "admin.content_manager";

const ADMIN_GLOBAL_SETTINGS = "admin.global_settings";
const ADMIN_UPLOAD = "admin.upload";
```
Administrator accounts can be managed only by administrator with * (`Permissions::ADMIN_FULL`) permission.

## Helpdesk
Helpdesk is a small administration for helpers, to helpdesk can be logged by game login (if the player is a helper - checking in LuckPerms). 
#### Features (only)
- Tickets management (locking, adding a response...)
  - Ticket subjects: ~~`App\Model\Panel\Core\TicketRepository::SUBJECTS`~~ can be changed in configuration

## Statistics
Statistics isn't working as classic PHP web but data of statistics is obtained from API part with Vue (only as CDN library without CLI). More of player's statistics data is in API documentation (bottom)
### Features
- player game statistics (from minigames, economy etc.)
- global statistics from events (btw. HerniEventy plugin)

## Statistics - API part
This API is used to retrieve cached player statistics data and export it in JSON format. Caching time is 2 hours (excluding useless data - for example: UUID).

`GET "www.example.tld/statistiky/api"`
```json
{
    "updateTime": "2 hours",
    "http": {
        "code": 200,
        "requestTime": 1617691273000,
        "url": "https://herocraft.cz/statistiky/api",
        "method": "GET",
        "ip": "<IP>"
    },
    "stats": {
        "registerCount": 24698,
        "timesPlayed": 1265.35,
        "serverStarted": {
            "timestamp": 1361599200,
            "string": "23.2.2013 7:00"
        }
    },
    "czechCraft": {
        "server": {
            "name": "Herocraft",
            "slug": "hero-craft",
            "address": "play.hero-craft.cz",
            "position": 223,
            "votes": 128
        },
        "topVoters": [
            {
                "username": "xxx",
                "votes": 1
            }
        ]
    }
}
``` 
`GET "www.example.tld/statistiky/api/DarkMilan"` 
```json
{
   "updateTime":"2 hour",
   "http":{
      "code":200,
      "requestTime":1605797807000,
      "url":"https://herocraft.cz/statistiky/api/DarkMilan",
      "method":"GET",
      "ip":"<IP>"
   },
   "player":{
      "exists":true,
      "isBanned":false,
      "nickname":"DarkMilan",
      "playertime":null,
      "uuid":"79d1cceb-8077-387f-86a4-89fc738ce3cb",
      "originalUuid":"e87dd4bed56f4873a6a8f265e59cb4e0",
      "czechCraft":{
         "username":"DarkMilan",
         "data":[
            
         ],
         "vote_count":0,
         "next_vote":"2020-10-16 16:45:03"
      },
      "headImageURL":"https://minotar.net/avatar/DarkMilan.png",
      "perms":{
         "groups":{
            "global":"admini"
         }
      },
      "auth":{
         "userID":23696,
         "regtime":1590256825823
      },
      "servers":{
         "games":{
            "events":{
               "OhnivyZavod":{
                  "id":1,
                  "username":"DarkMilan",
                  "event_id":"1",
                  "best_time":"25.4",
                  "last_played":"06/06/2020 11:49:06",
                  "best_played":"05/31/2020 02:13:59",
                  "event_passed":37,
                  "event_giveup":71,
                  "event_name":"OhnivyZavod",
                  "event_created":"05/27/2020 06:48:51",
                  "event_best":null
               },
               "Akvarko":{
                  "id":15,
                  "username":"DarkMilan",
                  "event_id":"2",
                  "best_time":null,
                  "last_played":"06/06/2020 11:49:00",
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":3,
                  "event_name":"Akvarko",
                  "event_created":"05/30/2020 06:49:11",
                  "event_best":null
               },
               "Bludiste":{
                  "id":17,
                  "username":"DarkMilan",
                  "event_id":"3",
                  "best_time":null,
                  "last_played":"05/31/2020 01:32:05",
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":27,
                  "event_name":"Bludiste",
                  "event_created":"05/30/2020 06:49:58",
                  "event_best":null
               },
               "Skakacka1":{
                  "id":16,
                  "username":"DarkMilan",
                  "event_id":"4",
                  "best_time":null,
                  "last_played":"06/04/2020 10:15:12",
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":5,
                  "event_name":"Skakacka1",
                  "event_created":"05/30/2020 06:51:42",
                  "event_best":null
               },
               "Skakacka2":{
                  "id":18,
                  "username":"DarkMilan",
                  "event_id":"5",
                  "best_time":null,
                  "last_played":null,
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":0,
                  "event_name":"Skakacka2",
                  "event_created":"05/30/2020 06:52:03",
                  "event_best":null
               },
               "SkakackaVez":{
                  "id":39,
                  "username":"DarkMilan",
                  "event_id":"6",
                  "best_time":null,
                  "last_played":"05/31/2020 02:56:48",
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":4,
                  "event_name":"SkakackaVez",
                  "event_created":"05/31/2020 02:32:06",
                  "event_best":null
               },
               "SkakackaLes":{
                  "id":60,
                  "username":"DarkMilan",
                  "event_id":"7",
                  "best_time":null,
                  "last_played":null,
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":0,
                  "event_name":"SkakackaLes",
                  "event_created":"05/31/2020 05:27:44",
                  "event_best":null
               },
               "SkakackaKuchyn":{
                  "id":1993,
                  "username":"DarkMilan",
                  "event_id":"8",
                  "best_time":null,
                  "last_played":null,
                  "best_played":null,
                  "event_passed":0,
                  "event_giveup":0,
                  "event_name":"SkakackaKuchyn",
                  "event_created":"08/30/2020 07:41:02",
                  "event_best":null
               }
            },
            "hideAndSeek":{
               "player_name":"DarkMilan",
               "player_uuid":"79d1cceb-8077-387f-86a4-89fc738ce3cb",
               "hiders_killed":2,
               "seekers_killed":0,
               "wins":2,
               "coins":234,
               "karma":0,
               "games_played":4,
               "hider_chance":60,
               "play_time":"0D-0H-14M-17S",
               "exp":7,
               "modifier":1,
               "special_blocks":"",
               "perks":"",
               "trail":-1,
               "id":4
            },
            "spleef":{
               "PlayerUUID":"79d1cceb-8077-387f-86a4-89fc738ce3cb",
               "Coins":0,
               "SpleggUpgrade":"default",
               "PurchasedSpleggUpgrades":"[]",
               "GlobalStats":"{\"GAMES_PLAYED\":0,\"LOSSES\":0,\"DRAWS\":0,\"SPLEGG_SHOTS\":0,\"BOW_SPLEEF_SHOTS\":0,\"WINS\":0,\"SCORE\":0,\"BLOCKS_MINED\":0}",
               "ExtensionStats":"{}",
               "Boosters":"{}",
               "Perks":"{}"
            }
         },
         "senior":{
            "economy":{
               "balance":null
            }
         },
         "classic":{
            "economy":{
               "balance":null
            }
         }
      }
   }
}
``` 

## Configuration Files
- `app\lang\*` - configurations with messages used in templates (Contributte\Translation)
  - default: 
    - front.cs_CZ.neon
    - helpdesk.cs_CZ.neon
    - panel.cs_cz.neon
    - stats.cs_CZ.neon
- `app\config\*` - web system configurations file
  - application.neon - nette (forms, session, tracy) settings
  - database.neon - MySQL configuration
  - extensions.neon - extensions from third party
  - loader.neon - webloader (loader for CSS & JS files) settings
  - parameters.neon - parameters used in DI configuration
  - services.neon - DI configuration

## Connection with Minecraft plugins and other

Each plugin has its own connection to the database in configuration and custom class in `App\Model\API\Plugin`
excluding Iconomy, which is abstract class with childs for Senior server and Classic Server (because of Dependency Injection - database connection).

#### Support plugins (panel, statistics) from MySQL:
- AuthMe
  - for authme is here AuthmeRepository (`App\Model\API\Panel\AuthmeRepository`)
  - authenticator for helpdesk and player's panel section
- iConomy
  - Classic\Economy
  - Senior\Economy
- Games\HerniEventy (custom plugin)
- Games\HideAndSeek
- Games\SpleefX ([link](https://www.spigotmc.org/resources/%E2%99%9B-spleefx-%E2%99%9B-spleef-splegg-bow-spleef-perks-upgrades-shops-and-much-much-more.73093/))
- Bans
- ChatLog (custom plugin)
- LuckPerms ([link](https://luckperms.net/))
- OnlinePlayers (custom plugin)
- PlayerTime

#### Unused plugins but with implementation class `App\Model\API\Plugin\Deprecated`
- EpicLevels ([link](https://songoda.com/marketplace/product/epiclevels-gain-levels-by-fighting.44))
- FastLogin ([link](https://www.spigotmc.org/resources/fastlogin.14153/))
- Friends ([link](https://www.spigotmc.org/resources/friends-mc1-8-x-1-16-x-party-system.12063/))
- LiteBans ([link](https://www.spigotmc.org/resources/litebans.3715/))
- Lottery 
- RoyaleEconomy ([link](https://www.spigotmc.org/resources/royaleeconomy.81135/))
- SkinRestorer ([link](https://www.spigotmc.org/resources/skinsrestorer.2124/))
- TokenManager ([link](https://www.spigotmc.org/resources/tokenmanager.8610/))
- Verus

#### CzechCraft
This websystem supporting CzechCraft API (https://czech-craft.eu/api//). Server slug can be changed from configuration.

#### Status API
The status of minecraft server provides https://api.mcsrvstat.us/2/ API service. IP address of Minecraft Server can be changed in the settings (in administration).

## TODO features
`??` = maybe

- better image uploading
- generating favicon.ico from logo
- better design in statistics and panel
- articles filter by category
- add only url pages & secret pages
- code review (use best practices, code design etc...)
- added language settings to administration `??`


## Provedené změny (CZECH) (neaktualizováno)
- 16.11 - 23.11
    - opraveno ořezávání obsahu článků na hlavní straně
    - změněno kódování editoru na RAW
    - dokončený výpis helperů
    - přidány permisse k sekci "Minecraft > Helpers"
    - opraven odehraný čas helpera (nyní se vyhledává bezohledu postavení písmen (lower,upper))
    - odstraněn nefunkční graf z "Minecraft > Helpers" (bude dodělán příště)
    - přidán odkaz na statistiku hráče při otevření jeho ticketu v helpdesku
    - přidán odkaz na github do patičky v administraci
    - přidána speciální mc permisse "web.implement" (tohoto hráče to ukáže jako helpera na webu)
    - opraven CLI script na ukládaní odehraného času helperů
    - možnost nastavení předmětů ticketu v konfiguraci
    - přidána možnost nastavit ke každému předmětu jinou placeholder zprávu
    - možnost nastavit server-slug na czechcraft v konfiguraci
- 23.11 - 30.11
    - možnost změny footeru (patičky) v administraci
    - html editor v administraci (zvýraznění, odsazení)
    - přidán favicon.ico (16px logo) které se zobrazuje u karty
    - přidány herní sekce do konfigurace
    - bezpečností aktualizace Nette (3.0 -> 3.0.7)
    - upraven počet článku na jednu stránku v archivu
    - malé opravy v designu po redesignu (paddingy, marginy)
    - přidány seo elementy pro lepší orientaci pro vyhledávče
    - možnost změny meta tagu description na hlavní stránce
    - možnost změny času expirace cache herních dat (statistik)
    - nastavení google analytics v konfiguraci
    - homepage redesign
- 30.11 - 7.12
    - propojení webhooku discord webhooku
    - nastavení discord webhooku v konfiguraci
    - změněna výška textarea při přidávání ticketu v panelu
    - nyní se helper v helpdesku detekuje též podle práv na serveru "lobbyspawn"
    - přidán přehled aktuální konfigurace v administraci
    - přidána možnost aktualizovat provedené změny v konfiguraci (vymazat mezipamět konfigurace)
    - přidán breadcrumb u vytvořených stránek (tzv. drobečková navigace)
    - možnost zvolit routu přesměrování po úspěšném přihlašování v panelu, např:
      - /panel/login?returnRoute=:Panel:Ticket:list - po přihlášení přesměruje na seznam ticketů
    - luckperms přehled v administraci s možností vyhledat hráče dle nicku
- 7.12 - 14.12
    - oprava parametru ?returnRoute (vyskytla se chyba na produkci)
    - implementace knihovny [Contributte\Translation](https://github.com/contributte/translation) pro multijazyčné použití
    - přidána první konfigurace zpráv v `app\lang`, zatím zprávy v hráčském panelu (11.12)
    - aktualizace použitých knihoven composer.json
    - přidány konfigurační soubory v `app\lang` pro moduly: Panel, Front, Helpdesk, Stats, tedy kromě Administrace
    - implementace tichto konfiguračních souborů v šablonách webového systému
    - autorizace při volání akcí (cli skriptů např: `example.tld/cli/savingPlaytime/<pass>`) pod CronPresenter (autorizační kód v konfiguraci)
    - oprava skriptu pro uložení odehraného času helperů (metoda: `CronPresenter::actionSavingPlaytime`)
    - přidáno tlačítko pro odstranění dané permisse v LuckPerms přehledu (administrace)
    - opraven a změněn výpis týdenního odehraného času při rozkliknutí karty helpera (administrace)
- 14.12 - 21.12
    - přidána reference (odkaz) v kartě helpera na přehled práv daného helpera (administrace)
    - typewriter efekt na IP adresu serveru na hlavní stránce
- 21.12 - 28.12
    - odstraněn momentální design
    - práce na novém designu + nových modulech v administraci (rozvržení, přidávání sekcí a tak dále)
    - tlačítko TICKETY přidáno do widgetu - JEN NA PRODUKCI!
- 28.12 - 4.1.2021
    - opraveno rolování (scrollbar) na stránce s přehledem daného eventu ve statistikách
    - opraveno zobrazování hráčských statistik (změněna adresa API, překlik)
    - přidán náhled ticketů s přihlášením, viz. routa `Login:ticketLogin` `/panel/tickety/nahled`
    - přidána podpora nových odpovědí do Captchy
    - opraven výpis eventů - seřazení dle času - podpora 16ti ciferného čísla s 2 desetinnými čísly
    - přidán sloupec poslední odpovědi v ticketech
    - přidána informace o tom, kdo ticket uzavřel v helpdesku
    - přidán výpis nejlepšího času eventu rovnou na úvodní stránce herních statistik
    - přidána nová otázka/odpověď do Captchy využité v panelu
    - změněn design helpdesku na nový, inspirovaný administrací pro lepší orientaci
- 4.1 - 11.1.2021
    - přidána možnost skrytého článku
    - přidány nové data do API `/api` které se tykají serveru - zaokrouhlený počet odehraných hodin celkově, počet registrovaných hráču 
- 11.1 - 2.3.2021
    - update z 'better-design' branch (větve) - sekce, nový design, lepší manipulace s obsahem, mnohem více nastavení v administraci