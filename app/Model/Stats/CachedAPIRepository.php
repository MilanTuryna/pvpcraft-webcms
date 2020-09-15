<?php

namespace App\Model\Stats;

use App\Model\API\CzechCraft;
use App\Model\API\Plugin\FastLogin;
use App\Model\API\Plugin\Friends;
use App\Model\API\Plugin\LiteBans;
use App\Model\API\Plugin\LuckPerms;
use App\Model\API\Plugin\PlayerTime;
use App\Model\API\Plugin\TokenManager;
use App\Model\API\Plugin\Verus;
use App\Model\Panel\AuthMeRepository;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\ArrayHash;

/**
 * Class CachedAPIRepository
 */
class CachedAPIRepository
{
    const EXPIRE_TIME = '2 hour';

    private AuthMeRepository $authMeRepository;
    private Cache $cache;
    private FastLogin $fastLogin;
    private Friends $friends;
    private TokenManager $tokenManager;
    private PlayerTime $playerTime;
    private LiteBans $liteBans;
    private Verus $verus;
    private LuckPerms $luckPerms;

    /**
     * CachedAPIRepository constructor.
     * @param AuthMeRepository $authMeRepository
     * @param IStorage $storage
     * @param FastLogin $fastLogin
     * @param Friends $friends
     * @param TokenManager $tokenManager
     * @param PlayerTime $playerTime
     * @param LiteBans $liteBans
     * @param Verus $verus
     * @param LuckPerms $luckPerms
     */
    public function __construct(AuthMeRepository $authMeRepository, IStorage $storage, FastLogin $fastLogin,
                                Friends $friends, TokenManager $tokenManager, PlayerTime $playerTime,
                                LiteBans $liteBans, Verus $verus, LuckPerms $luckPerms)
    {
        $this->authMeRepository = $authMeRepository;
        $this->cache = new Cache($storage);
        $this->fastLogin = $fastLogin;
        $this->friends = $friends;
        $this->tokenManager = $tokenManager;
        $this->playerTime = $playerTime;
        $this->liteBans = $liteBans;
        $this->verus = $verus;
        $this->luckPerms = $luckPerms;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getUser($name) {
        $cacheName = 'API_user_' . $name;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->authMeRepository->findByUsername($name);
            $this->cache->save($cacheName,  $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => self::EXPIRE_TIME
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getFastLogin($name) {
        $cacheName = 'API_fastLogin_' . $name;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->fastLogin->getRow($name);
            $this->cache->save($cacheName,  $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => self::EXPIRE_TIME
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getCountFriends($user) {
        return $this->friends->countOfFriends($user);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getTokenManager($user) {
        $cacheName = 'API_tokenManager_' . $user;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->tokenManager->getRow($user);
            $this->cache->save($cacheName,  $db ? ArrayHash::from($db->toArray()) : null, [
                Cache::EXPIRE => self::EXPIRE_TIME
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function getPermGroups($uuid) {
        $cacheName = 'API_permGroups_' . $uuid;
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->luckPerms->getUserGroups($uuid), [
                Cache::EXPIRE => self::EXPIRE_TIME
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getFriendsList($user) {
        $cacheName = 'API_friendsList_' . $user;
        if (is_null($this->cache->load($cacheName))) {
            $db = $this->friends->getFriends($user);
            $this->cache->save($cacheName, $db ? array_map(function ($n) {
                $n->lastOnline = strtotime(((array)$n->lastOnline)['date'])*1000;
                $n->headImageUrl = "https://minotar.net/avatar/{$n->player_name}.png";
                unset($n->player_id);
                return $n;
            }, $this->friends->getFriends($user)) : null, [
                Cache::EXPIRE => self::EXPIRE_TIME
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @return mixed
     */
    public function getCzechCraftServer() {
        $cacheName = 'API_czechCraftServer';
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, CzechCraft::getServer(), [
                Cache::EXPIRE => '1 hour'
            ]);
        }

        return $this->cache->load($cacheName);
    }

    /**
     * @return mixed
     */
    public function getTopVoters() {
        $cacheName = 'API_czechCraftTopVoters';
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, CzechCraft::getTopPlayers(), [
               Cache::EXPIRE => '24 hour'
            ]);
        }

        return $this->cache->load($cacheName)->data;
    }

    /**
     * @param $name
     * @deprecated
     * @return mixed
     */
    public function getPlayedTime($name) {
        return $this->playerTime->getPlayedTime($name);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function isBanned($name) {
        $cacheName = 'API_ban_' . $name;
        if(is_null($this->cache->load($cacheName))) {
            $this->cache->save($cacheName, $this->verus->isBanned($name) || $this->liteBans->isBanned($name), [
                Cache::EXPIRE => "24 hours"
            ]);
        }

        return $this->cache->load($cacheName);
    }
}