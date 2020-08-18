<?php


namespace App\Presenters\AdminModule;

use App\Forms\Admin\Minigame\CreateForm;
use App\Forms\Admin\Minigame\EditForm;
use App\Model\Security\Authenticator;
use App\Presenters\AdminBasePresenter;
use App\Model\DynamicRepository;

use Nette\Application\AbortException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Database\Context;

/**
 * Class MinigamePresenter
 * @package App\Presenters\AdminModule
 */
class MinigamePresenter extends AdminBasePresenter
{
    private Context $context;
    private DynamicRepository $gameRepository;

    /**
     * MinigamePresenter constructor.
     * @param Authenticator $authenticator
     * @param Context $context
     */
    public function __construct(Authenticator $authenticator, Context $context)
    {
        parent::__construct($authenticator);

        $this->gameRepository = new DynamicRepository($context, 'minigames');
        $this->context = $context;
    }

    public function startup()
    {
        parent::startup(); // TODO: Change the autogenerated stub
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function renderEdit($id) {
        $minigame = $this->gameRepository->findById($id);
        if($minigame) {
            $this->template->minigame = $minigame;
        } else {
            $this->flashMessage('Tato minihra nelze editovat, pravděpodobně neexistuje!', 'danger');
            $this->redirect('Minigame:list');
        }
    }

    public function actionDelete($id) {
        $deleted = $this->gameRepository->delete('id = ?', $id);
        if($deleted) {
            $this->flashMessage('Tato minihra byla úspěšně odstraněna!', 'success');
        } else {
            $this->flashMessage('Tato minihra neexistovala, tudíž nemohla být odstraněna', 'danger');
        }

        $this->redirect('Minigame:list');
    }

    public function renderList() {
        $this->template->minigames = $this->gameRepository->findAll();
    }

    /**
     * @return Form
     */
    public function createComponentCreateForm(): Form {
        return (new CreateForm($this, $this->gameRepository))->create();
    }

    public function createComponentEditForm(): Multiplier {
        return new Multiplier(function(string $minigameId) {
            return (new EditForm($this, $this->gameRepository, $minigameId))->create();
        });
    }
}