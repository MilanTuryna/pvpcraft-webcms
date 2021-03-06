<?php


namespace App\Presenters\AdminModule;

use App\Forms\Admin\Category\CreateForm;
use App\Forms\Admin\Category\EditForm;
use App\Model\Admin\Roles\Permissions;
use App\Model\CategoryRepository;
use App\Model\Security\Auth\Authenticator;
use App\Presenters\AdminBasePresenter;

use Nette\Application\AbortException;
use Nette\Application\UI\Multiplier;
use Nette\Application\UI\Form;

/**
 * Class CategoryPresenter
 * @package App\Presenters\AdminModule
 */
class CategoryPresenter extends AdminBasePresenter {
    private CategoryRepository $categoryRepository;

    /**
     * CategoryPresenter constructor.
     * @param Authenticator $authenticator
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(Authenticator $authenticator, CategoryRepository $categoryRepository)
    {
        parent::__construct($authenticator, Permissions::ADMIN_CATEGORIES);

        $this->categoryRepository = $categoryRepository;
    }

    public function renderList() {
        $this->template->categories = $this->categoryRepository->findCategories();
    }

    public function renderEdit($id) {
        $this->template->category = $this->categoryRepository->findCategoryById($id);
    }

    /**
     * @param $id
     * @throws AbortException
     */
    public function actionDelete($id) {
        $deleted = $this->categoryRepository->delete($id);
        if($deleted) {
            $this->flashMessage('Kategorie byla úspěšně odstraněna', 'success');
        } else {
            $this->flashMessage('Kategorie nemohla být odstraněna jelikož neexistuje.', 'danger');
        }
        $this->redirect('Category:list');
    }

    /**
     * @return Form
     */
    public function createComponentCreateForm(): Form {
        return (new CreateForm($this, $this->categoryRepository))->create();
    }

    /**
     * @return Multiplier
     */
    public function createComponentEditForm(): Multiplier {
        return new Multiplier(function(string $categoryId) {
            return (new EditForm($this, $this->categoryRepository,$categoryId))->create();
        });
    }
}