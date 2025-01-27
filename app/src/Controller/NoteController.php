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
    public function __construct(private readonly File $file)
    {}

    /**
     * Отображение всех записей
     * @param NoteRepository $noteRepository
     * @return Response
     */
    #[Route(name: 'app_note_index', methods: ['GET'])]
    public function index(NoteRepository $noteRepository): Response
    {
        $notes = $noteRepository->findBy(['userId' => $this->getUser()]);
        array_walk($notes, function (Note &$note) {
            $note->form = $this->createForm(NoteType::class, $note,
                ['action' => $this->generateUrl('app_note_edit', ['id' => $note->getId()])])->createView();
        });
        return $this->render('note/index.html.twig', [
            'notes' => $notes,
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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        $note->setCreateDt(Clock::get()->now());
        $note->setUserId($this->getUser());
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $note->setFilename($this->file->save($file, $this->getParameter('file_directory'), $slugger));
            }
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('note/new.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
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
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        $note->setUpdateDt(Clock::get()->now());
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();
            if ($file) {
                $note->setFilename($this->file->save($file, $this->getParameter('file_directory'), $slugger));
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
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
