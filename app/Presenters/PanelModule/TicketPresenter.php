<?php


namespace App\Presenters\PanelModule;


use App\Forms\Panel\Tickets\AddResponseForm;
use App\Forms\Panel\Tickets\AddTicketForm;
use App\Forms\Panel\Tickets\CloseTicketForm;
use App\Model\Panel\Core\TicketRepository;
use App\Model\Security\Form\Captcha;
use App\Model\Security\Auth\PluginAuthenticator;
use App\Model\SettingsRepository;
use App\Model\Stats\CachedAPIRepository;
use App\Presenters\PanelBasePresenter;
use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Database\Table\ActiveRow;

/**
 * Class TicketPresenter
 * @package App\Presenters\PanelModule
 */
class TicketPresenter extends PanelBasePresenter
{
    private TicketRepository $ticketRepository;
    private PluginAuthenticator $pluginAuthenticator;

    private ActiveRow $user;

    /**
     * TicketPresenter constructor.
     * @param SettingsRepository $settingsRepository
     * @param PluginAuthenticator $pluginAuthenticator
     * @param TicketRepository $ticketRepository
     */
    public function __construct(SettingsRepository $settingsRepository, PluginAuthenticator $pluginAuthenticator, TicketRepository $ticketRepository)
    {
        parent::__construct($settingsRepository);

        $this->pluginAuthenticator = $pluginAuthenticator;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @throws AbortException
     */
    public function startup()
    {
        parent::startup(); // TODO: Change the autogenerated stub
        $user = $this->pluginAuthenticator->getUser();
        if(!(bool)$user) {
            $this->flashMessage('Pro manipulaci s hráčským panelem, proveďte autorizaci.', 'error');
            $this->redirect('Login:main');
        } else {
            $this->user = $user;
            $this->template->user = $user;
        }
    }

    /**
     * @param int $page
     * @throws AbortException
     */
    public function renderList(int $page = 1) {
        $tickets = $this->ticketRepository->getTicketsByAuthor($this->user->realname);

        $lastPage = 0;
        $ticketsPaginator = $tickets->page($page, 8, $lastPage);
        $this->template->tickets = $ticketsPaginator;
        $this->template->ticketsCount = $this->ticketRepository->getTicketsCount($this->user->realname);

        $this->template->page = $page;
        $this->template->lastPage = $lastPage;

        if($page > $lastPage) {
            $this->redirect("Ticket:list");
        }
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function renderView($id) {
        $ticket = $this->ticketRepository->getTicketById($id);

        try {
            if($ticket) {
                if($ticket->author === $this->user->realname) {
                    $this->template->ticket = $ticket;
                    $captcha = Captcha::getRandomMethod();
                    $this->template->captcha = $captcha;
                    $this->template->captchaOrder = Captcha::getMethodOrder($captcha);
                    $this->template->ticketResponses = $this->ticketRepository->getTicketResponses($ticket->id);
                    $this->template->responseTypes = TicketRepository::TYPES;
                } else {
                    throw new \Exception('Tento ticket nepatří tobě!');
                }
            } else {
                throw new \Exception('Tento ticket neexistuje!');
            }
        } catch (\Exception $exception) {
            $this->flashMessage($exception->getMessage(), 'error');
            $this->redirect('Ticket:list');
        }
    }

    /**
     * @return Multiplier
     */
    public function createComponentAddResponseForm(): Multiplier {
        return new Multiplier(function ($methodOrder) { // captcha
            return new Multiplier(function ($ticketId) use ($methodOrder) { // ticket
                return (new AddResponseForm($this, new Captcha(array_keys(Captcha::methods)[$methodOrder]), $this->ticketRepository, $this->user, $ticketId))->create();
            });
        });
    }

    /**
     * @return Multiplier
     */
    public function createComponentCloseTicketForm(): Multiplier {
        return new Multiplier(function ($ticketId) {
            return (new CloseTicketForm($this, $this->ticketRepository, $ticketId))->create();
        });
    }

    /**
     * @return Form
     */
    public function createComponentAddTicketForm(): Form {
        return (new AddTicketForm($this, $this->ticketRepository, $this->user))->create();
    }
}