<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AdminService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
/** Контроллер Админской части */
#[Route('/admin')]
final class AdminController extends AbstractController
{

    /**
     * Определение зависимостей
     * @param AdminService $adminService
     */
    public function __construct(private readonly AdminService $adminService)
    {
    }


    /**
     * Основной метод отображения
     * @return Response
     */
    #[Route(name: 'app_admin', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'users' => $this->adminService->allUsers($this->getUser()->getId())
        ]);
    }

    /**
     * Заблокировать или разблокировать пользователя
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/{id}/{action}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $this->adminService->changeUser($request->get('id'), $request->get('action'));
        return $this->redirectToRoute('app_admin');
    }

    /**
     * Удаление всех записей пользователя
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/{id}/del', name: 'admin_del_data', methods: ['GET', 'POST'])]
    public function deliteNotes(Request $request, User $user): Response
    {
        $this->adminService->destroyNotes($request->get('id'));
        return $this->redirectToRoute('app_admin');
    }
}
