<?php
namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Database\Explorer;

class SocialPresenter extends Nette\Application\UI\Presenter{

	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}
    protected function createComponentPostForm(): Form
    {
        $form = new Form;
    
        $form->addText('Nazev', 'Nazev:')
        ->setRequired();
        $form->addText('url', 'url:')
            ->setRequired();
       
    
        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];
    
        return $form;
    }
    public function postFormSucceeded(Form $form, array $values): void
{
	 if (!$this->getUser()->isInRole('admin')) {
	
		$this->redirect(':Homepage:');
	
	}
	$postId = $this->getParameter('postId');

	if ($postId) {
		$post = $this->database->table('socialnet')->get($postId);
		$post->update($values);
	} else {
		$post = $this->database->table('socialnet')->insert($values);
	}

	$this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
	$this->redirect(':Homepage:');
}


    public function actionEdit(int $postId): void
{
	if (!$this->getUser()->isInRole('admin')) {
		$this->redirect('Sign:in');
	}
	$post = $this->database->table('socialnet')->get($postId);
	if (!$post) {
		$this->error('Příspěvek nebyl nalezen');
	}
	$this['postForm']->setDefaults($post->toArray());
}

}