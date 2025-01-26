<?php

namespace App\Service;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

/** Сервис для работы админской части */
class AdminService
{

    /**
     * Определение зависимостей
     * @param UserRepository $users
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly UserRepository         $users,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Возвращает всех пользователей кроме админов и запросившего пользователя (Знаю что исключать себя бессмысленно т.к. админ он же админ но запил на будущее)
     * @param $id int ID запросившего пользователя
     * @return mixed
     */
    public function allUsers(int $id): mixed
    {
        $qb = $this->users->createQueryBuilder('u')->where("u.id != :admin AND u.roles NOT LIKE '%ROLE_ADMIN%'")->setParameter('admin', $id);
        $query = $qb->getQuery();
        return $query->execute();

    }

    /**
     * Блокирует или разблокирует пользователя
     * @param int $id ID пользователя роли которого нужно поменять
     * @param string $action Действие
     * @return void
     */
    public function changeUser(int $id, string $action): void
    {
        $user = $this->users->find($id);
        if ($action == 'block'){
            $checked = in_array('ROLE_USER', $user->getRoles());
            $role = [''];
        }else{
            $checked = !in_array('ROLE_USER', $user->getRoles());
            $role = ['ROLE_USER'];
        }
        if ($checked) {
            $user->setRoles($role);
            $this->entityManager->flush();
        }
    }

    /**
     * Удалить все записи пользователя
     * @param int $id ID пользователя записи которого нужно удалить
     * @return void
     */
    public function destroyNotes(int $id): void
    {
        $user = $this->users->find($id);
        $notes = $user->getNotes();
        foreach ($notes as $note) {
            $this->entityManager->remove($note);
        }
        $this->entityManager->flush();

    }
}