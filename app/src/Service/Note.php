<?php

namespace App\Service;

use App\Entity\User;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class Note
{

    /**
     * Определение зависимостей
     * @param NoteRepository $noteRepository
     * @param FormFactoryInterface $form
     * @param RouterInterface $router
     * @param File $file
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly NoteRepository         $noteRepository,
        private readonly FormFactoryInterface   $form,
        private readonly RouterInterface        $router,
        private readonly File                   $file,
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface       $slugger
    )
    {

    }

    /**
     * Генерация записей пользователя
     * @param UserInterface $user
     * @return array
     */
    public function getNotesFoRender(UserInterface $user): array
    {
        $notes = $this->noteRepository->findBy(['userId' => $user]);
        array_walk($notes, function (\App\Entity\Note $note) {
            $note->form = $this->form->create(NoteType::class, $note,
                ['action' => $this->router->generate('app_note_edit', ['id' => $note->getId()])])->createView();
        });
        return $notes;
    }

    /**
     * Метод для создания новой записи и редактирования существующей
     * @param UserInterface|User $user Класс пользователя
     * @param Request $request Запрос
     * @param $path string Путь для сохранения файла
     * @param \App\Entity\Note|null $editNote Если передана то будет редактироватся иначе создаватся
     * @return false|array
     */
    public function saveNote(UserInterface|User $user, Request $request, string $path, \App\Entity\Note $editNote = null): false|array
    {
        $note = $editNote;
        !isset($editNote) && $note = new \App\Entity\Note();
        $form = $this->form->create(NoteType::class, $note);
        $form->handleRequest($request);
        if (!isset($editNote)){
            $note->setCreateDt();
            $note->setUserId($user);
        } else {
            $note->setUpdateDt(Clock::get()->now());
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $note->setFilename($this->file->save($file, $path, $this->slugger));
            }
            !isset($editNote) && $this->entityManager->persist($note);
            $this->entityManager->flush();
            return false;
        }
        return ['note' => $note, 'form' => $form];
    }


}