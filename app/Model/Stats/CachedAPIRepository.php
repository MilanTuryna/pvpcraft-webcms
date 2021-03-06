<?php

namespace App\Model\Stats;

use App\Model\API\CzechCraft;
use App\Model\API\Plugin\Bans;
use App\Model\API\Plugin\Games\Events;
use App\Model\API\Plugin\Games\HideAndSeek;
use App\Model\API\Plugin\Games\SpleefX;
use App\Model\API\Plugin\LuckPerms;
use App\Model\API\Plugin\PlayerTime;
use App\Model\API\Plugin\Senior\Economy as SeniorEconomy;
use App\Model\API\Plugin\Classic\Economy as ClassicEconomy;
use App\Model\DI\API;
use App\Model\Panel\AuthMeRepository;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Nette\Utils\ArrayHash;
use Throwable;

/**
 * Class CachedAPIRepository
 */
class CachedAPIRepository
{
    private AuthMeRepository $authMeRepository;
    private Cache $cache;
    private Bans $bans;
    private HideAndSeek $hideAndSeek;
    private Events $events;
    private LuckPerms $luckPerms;
    private SpleefX $spleefX;
    private SeniorEconomy $seniorEconomy;
    private ClassicEconomy $classicEconomy;
    private CzechCraft $czechCraft;
    private PlayerTime $playerTime;
    private API $api;

    /**
     * CachedAPIRepository constructor.
     * @param AuthMeRepository $authMeRepository
     * @param Storage $storage
     * @param Bans $bans
     * @param HideAndSeek $hideAndSeek
     * @param Events $events
     * @param LuckPerms $luckPerms
     * @param SpleefX $spleefX
     * @param SeniorEconomy $seniorEconomy
     * @param ClassicEconomy $classicEconomy
     * @param CzechCraft $czechCraft
     * @param PlayerTime $playerTime
     * @param API $api
     */
    public function __construct(AuthMeRepository $authMeRepository, Storage $storage,
                                Bans $bans,
                                HideAndSeek $hideAndSeek,
                                Events $events,
                                LuckPerms $luckPerms,
                                SpleefX $spleefX,
                                SeniorEconomy $seniorEconomy,
                                ClassicEconomy $classicEconomy,
                                CzechCraft $czechCraft,
                                PlayerTime $playerTime,
                                API $api)
    {
        $this->authMeRepository = $authMeRepository;
        $this->cache = new Cache($storage);
        $this->bans = $bans;
        $this->hideAndSeek = $hideAndSeek;
        $this->events = $events;
        $this->luckPerms = $luckPerms;
        $this->spleefX = $spleefX;
        $this->seniorEconomy = $seniorEconomy;
        $this->classicEconomy = $classicEconomy;
        $this->czechCraft = $czechCraft;
        $this->playerTime = $playerTime;
        $this->api = $api;
    }

    /**
     * @param $name
     * @return mixed
     * @throws Throwable
     */
    public function getUser($name) {
        $cacheName = 'API_user_' . $name;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->authMeRepository->findByUsername($name);
            $this->cache->save($cacheName,  $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => $this->api->getExpireTime()
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $uuid
     * @return mixed
     * @throws Throwable
     */
    public function getPermGroups($uuid) {
        $cacheName = 'API_permGroups_' . $uuid;
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->luckPerms->getUserGroups($uuid), [
                Cache::EXPIRE => $this->api->getExpireTime()
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    public function getCzechCraftServer() {
        $cacheName = 'API_czechCraftServer';
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->czechCraft->getServer(), [
                Cache::EXPIRE => '1 hour'
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @return mixed
     * @throws Throwable
     */
    public function getTopVoters() {
        $cacheName = 'API_czechCraftTopVoters';
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->czechCraft->getTopPlayers(), [
               Cache::EXPIRE => '24 hour'
            ]);
        }

        return $this->cache->load($cacheName)->data;
    }

    /**
     * @param $player
     * @return mixed
     * @throws Throwable
     */
    public function getCzechCraftPlayer($player) {
        $cacheName = 'API_czechCraft_' . $player;
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->czechCraft->getPlayerInformation($player), [
                Cache::EXPIRE => '24 hour'
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Throwable
     */
    public function isBanned($name) {
        $cacheName = 'API_ban_' . $name;
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName,  (bool)$this->bans->getBanByNick($name)->fetch(), [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @return mixed|int
     * @throws Throwable
     */
    public function getTimesPlayed() {
        $cacheName = 'API_timesPlayed';
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->playerTime->getAllPlayedTime(), [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @return mixed|int
     * @throws Throwable
     */
    public function getRegisterCount() {
        $cacheName = 'API_registerCount';
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->authMeRepository->getRegisterCount(), [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Throwable
     */
    public function getHideAndSeekRow($name) {
        $cacheName = 'API_hideAndSeek_' . $name;
        if(is_null($this->cache->load($cacheName))) {
            $db = $this->hideAndSeek->getRowByName($name)->fetch();
            $this->cache->save($cacheName, $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Throwable
     */
    public function getPlayerEventsRecords($name) {
        $cacheName = 'API_events_' . $name;

        if(is_null($this->cache->load($cacheName))) {
            /**
             * Suppressing error: ->fetch() method triggering USER_NOTICE error (because duplicates)
             */
            $db = @$this->events->getPlayerRecordsByName($name);
            $events = [];
            foreach ($db as $d) {
                $events[$d->event_name] = ArrayHash::from(iterator_to_array($d));
            }
            $this->cache->save($cacheName, $events, [
                Cache::EXPIRE => "24 hours"
            ]);
        }


        return $this->cache->load($cacheName);
    }

    /**
     * @param $uuid
     * @return mixed
     * @throws Throwable
     */
    public function getSpleefStatsByUUID($uuid)
    {
        $cacheName = 'API_playerSpleefStats_' . $uuid;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->spleefX->getRowByUuid($uuid)->fetch();
            $this->cache->save($cacheName, $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Throwable
     */
    public function getClassicEconomy($name) {
        $cacheName = 'API_classicEconomy_' . $name;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->classicEconomy->getRowByName($name)->fetch();
            $this->cache->save($cacheName, $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $name
     * @return mixed
     * @throws Throwable
     */
    public function getSeniorEconomy($name) {
        $cacheName = 'API_seniorEconomy_' . $name;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->seniorEconomy->getRowByName($name)->fetch();
            $this->cache->save($cacheName, $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }
}