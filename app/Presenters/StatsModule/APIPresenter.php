<?php

namespace App\Presenters\StatsModule;

use App\Model\DI\API;
use App\Model\Panel\MojangRepository;
use App\Model\Responses\PrettyJsonResponse;
use App\Model\Stats\CachedAPIRepository;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;

/**
 * Class APIPresenter
 * @package App\Presenters\StatsModule
 */
class APIPresenter extends Presenter {

    private CachedAPIRepository $cachedAPIRepository;
    private MojangRepository $mojangRepository;
    private API $api;

    /**
     * APIPresenter constructor.
     * @param CachedAPIRepository $cachedAPIRepository
     * @param MojangRepository $mojangRepository
     */
    public function __construct(CachedAPIRepository $cachedAPIRepository, MojangRepository $mojangRepository, API $api)
    {
        parent::__construct();

        $this->cachedAPIRepository = $cachedAPIRepository;
        $this->mojangRepository = $mojangRepository;
        $this->api = $api;
    }

    /**
     * @param $json
     * @throws AbortException
     */
    public function sendResponseJson($json) {
        $this->sendResponse(new PrettyJsonResponse($json));
    }

    /**
     * @throws AbortException
     */
    public function actionServerView() {
        $response = [];
        $http = [
            'code' => 200,
            'requestTime' => time()*1000,
            'url' => $this->getHttpRequest()->getUrl(),
            'method' => $this->getHttpRequest()->getMethod(),
            'ip' => $this->getHttpRequest()->getRemoteAddress(),
        ];

        $response = [
            'updateTime' => $this->api->getExpireTime(),
            'http' => $http,
            'czechCraft' => [
                'server' => $this->cachedAPIRepository->getCzechCraftServer(),
                'topVoters' => $this->cachedAPIRepository->getTopVoters()
            ]
        ];

        $this->sendResponseJson($response);
    }

    /**
     * @param $name
     * @throws AbortException
     */
    public function actionView($name) {
        $user = $this->cachedAPIRepository->getUser($name);
        $uuid = $this->mojangRepository->getUUID($name);
        $response = [];
        $http = [
            'code' => 200,
            'requestTime' => time()*1000,
            'url' => $this->getHttpRequest()->getUrl(),
            'method' => $this->getHttpRequest()->getMethod(),
            'ip' => $this->getHttpRequest()->getRemoteAddress(),
        ];

        if($user && $uuid) {
            $response = [
                'updateTime' => $this->api->getExpireTime(),
                'http' => $http,
                'player' => [
                    'exists' => true,
                    'isBanned' => $this->cachedAPIRepository->isBanned($name),
                    'nickname' => $name,
                    'playertime' => null,
                    'uuid' => $uuid,
                    'originalUuid' => $this->mojangRepository->getMojangUUID($name),
                    'czechCraft' => $this->cachedAPIRepository->getCzechCraftPlayer($name),
                    'headImageURL' => "https://minotar.net/avatar/{$name}.png",
                    'perms' => [
                        'groups' => $this->cachedAPIRepository->getPermGroups($uuid)
                    ], 'auth' => [
                        'userID' => $user->id,
                        'regtime' => $user->regdate,
                    ], 'servers' => [
                        'games' => [
                            'events' => $this->cachedAPIRepository->getPlayerEventsRecords($name),
                            'hideAndSeek' => $this->cachedAPIRepository->getHideAndSeekRow($name),
                            'spleef' => $this->cachedAPIRepository->getSpleefStatsByUUID($uuid)
                        ], 'senior' => [
                            'economy' => [
                                "balance" => $this->cachedAPIRepository->getSeniorEconomy($name)
                            ],
                        ], 'classic' => [
                            'economy' => [
                                "balance" => $this->cachedAPIRepository->getClassicEconomy($name)
                            ]
                        ]
                    ]
                ]
            ];
        } else {
            $response = [
                'http' => $http,
                'reason' => 'UUID or user not found',
                'player' => [
                    'exists' => false
                ]
            ];
        }


        $this->sendResponseJson($response);
    }
}