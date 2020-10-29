<?php


namespace App\Presenters\StatsModule;


use App\Model\API\Plugin\Games\Events;
use App\Model\SettingsRepository;
use App\Presenters\BasePresenter;
use App\Model\API\Status;
use Nette\Application\AbortException;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;

/**
 * Class MainPresenter
 * @package App\Presenters\StatsModule
 */
class MainPresenter extends BasePresenter
{
    private SettingsRepository $settingsRepository;
    private Cache $cache;
    private Events $events;

    public function __construct(SettingsRepository $settingsRepository, IStorage $storage, Events $events)
    {
        parent::__construct();

        $this->settingsRepository = $settingsRepository;
        $this->cache = new Cache($storage);
        $this->events = $events;
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $nastaveni = $this->settingsRepository->getAllRows();
        $this->template->nastaveni = $nastaveni;

        $status = new Status((string)$nastaveni->ip, $this->cache);
        $this->template->status = !$nastaveni->udrzba ? $status->getCachedJson() : null;
    }

    public function renderApp() {
        $events = $this->events->findAllEvents()->fetchAll();
        $this->template->events = $events;
    }

    /**
     * @param $eventName
     * @param int $page
     * @throws AbortException
     */
    public function renderViewEvent($eventName, int $page = 1) {
        $event = $this->events->getEventByName($eventName)->fetch();
        if($event) {
            $this->template->event = $event;
            $eventPlayers = $this->events->getPlayersByEventId($event->event_id);

            $lastPage = 0;
            $paginatorData = $eventPlayers->page($page, 12, $lastPage);
            $this->template->eventPlayers = $paginatorData;

            $this->template->page = $page;
            $this->template->lastPage = $lastPage;

            if($page > $lastPage) {
                $this->redirect("Main:viewEvent", [$eventName,1]);
            }
        } else {
            $this->flashMessage("Event, na který směřuje tvůj odkaz, neexistuje.", "danger");
            $this->redirect("Main:app");
        }
    }
}