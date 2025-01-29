<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** Класс пользователя */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[UniqueEntity(fields: ['login'], message: 'There is already an account with this login')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @var int|null Идентефикатор пользователя */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** @var string|null Логин пользователя */
    #[ORM\Column(length: 180)]
    private ?string $login = null;

    /** @var list<string> Роли пользователя */
    #[ORM\Column]
    private array $roles = [];

    /** @var string Хэш пароля пользователя     */
    #[ORM\Column]
    private string $password;

    /** @var Collection<int, Note> Записи пользователя */
    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: 'userId')]
    private Collection $notes;

    /**
     * Определение зависимостей
     */
    public function __construct()
    {
        $this->notes = new ArrayCollection();
    }

    /**
     * Возвращает идентефикатор пользователя
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Возвращает логин пользователя
     * @return string|null
     */
    public function getLogin(): ?string
    {
        return $this->login;
    }

    /**
     * Изменяет логин пользователя
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login): static
    {
        $this->login = $login;
        return $this;
    }

    /**
     * Возвращает логин пользователя строкой
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * Возвращает роли пользователя
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        return array_unique($roles);
    }

    /**
     * Изменяет роли пользователя
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Возвращает пароль пользователя
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Устанавливает пароль пользователя
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Сброс данных пользователя (не используется но можно чистить сесию или что то в этом духе при выходе)
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Возвращает записи пользователя
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    /**
     * добавляет запись пользователю
     * @param Note $note
     * @return $this
     */
    public function addNote(Note $note): static
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setUserId($this);
        }
        return $this;
    }

    /**
     * Удаляет запись у пользователя
     * @param Note $note
     * @return $this
     */
    public function removeNote(Note $note): static
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getUserId() === $this) {
                $note->setUserId(null);
            }
        }
        return $this;
    }
}
