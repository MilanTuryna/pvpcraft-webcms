<?php


namespace App\Forms\Panel\Tickets;


use App\Model\Panel\Tickets\TicketRepository;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 * Class CloseTicketForm
 * @package App\Forms\Panel\Tickets
 */
class CloseTicketForm
{
    private TicketRepository $ticketRepository;
    private Presenter $presenter;
    private $ticketId;

    private string $lockedBy;

    /**
     * CloseTicketForm constructor.
     * @param Presenter $presenter
     * @param TicketRepository $ticketRepository
     * @param $ticketId
     * @param string $lockedBy
     */
    public function __construct(Presenter $presenter, TicketRepository $ticketRepository, $ticketId, string $lockedBy = '')
    {
        $this->presenter = $presenter;
        $this->ticketRepository = $ticketRepository;
        $this->ticketId = $ticketId;
        $this->lockedBy = $lockedBy;
    }

    /**
     * @return Form
     */
    public function create(): Form {
        $form = new Form;
        $form->addSubmit('close')->setRequired();
        $form->onSuccess[] = [$this, 'success'];
        $form->onError[] = function() use ($form) {
            foreach($form->getErrors() as $error) $this->presenter->flashMessage($error, 'error');
        };
        return $form;
    }

    /**
     * @param Form $form
     * @param \stdClass $values
     */
    public function success(Form $form, \stdClass $values) {
        $this->ticketRepository->lockTicket($this->ticketId, $this->lockedBy);
        $this->presenter->flashMessage('Ticket byl úspěšně označen jako vyřešený.', 'dark-green');
    }
}