<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Service\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Clock\Clock;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/** Контроллер для работы с записями пользователя */
#[Route('/note')]
final class NoteController extends AbstractController
{
    /**
     * Определение зависимостей
     * @param File $file
     */
    public function __construct(private readonly File $file, private readonly \App\Service\Note $noteService)
    {}

    /**
     * Отображение всех записей
     * @param NoteRepository $noteRepository
     * @return Response
     */
    #[Route(name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        $n = $this->noteService->getNotesFoRender($this->getUser());
        return $this->render('note/index.html.twig', [
            'notes' => $n
        ]);
    }

    /**
     * Создает новую запись
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $data = $this->noteService->saveNote($this->getUser(),$request, $this->getParameter('file_directory'));
        if (empty($data)) {
            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('note/new.html.twig', $data);
    }

    /**
     * Вывод конкретной записи на данный момент не используется
     * @param Note $note
     * @return Response
     */
    #[Route('/{id}', name: 'app_note_show', methods: ['GET'])]
    public function show(Note $note): Response
    {
        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }

    /**
     * Редаетирует запись
     * @param Request $request
     * @param Note $note
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     * @return Response
     */
    #[Route('/{id}/edit', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $data = $this->noteService->saveNote($this->getUser(),$request, $this->getParameter('file_directory'), $note);
        if (empty($data)) {
            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('note/new.html.twig', $data);
    }

    /**
     * Удаление записи
     * @param Request $request
     * @param Note $note
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/{id}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $note->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }
}
