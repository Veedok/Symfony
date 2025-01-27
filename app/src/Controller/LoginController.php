<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/** Контроллер авторизации */
class LoginController extends AbstractController
{
    /**
     * Главная страница запрашивает сразу авторизацию, разные роли переводят на разные страницы
     * @param AuthenticationUtils $authenticationUtils
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @return Response
     */
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        if (empty($error)) {
            if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin');
            } elseif ($authorizationChecker->isGranted('ROLE_USER')) {
                return $this->redirectToRoute('app_note_index');
            }
        }
        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Метод для выхода из аккаунта
     * @return void
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
