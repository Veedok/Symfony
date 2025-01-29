<?php

namespace App\Entity;

use App\Repository\NoteRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Clock\Clock;
use Symfony\Component\Form\FormView;

/** Класс записи */
#[ORM\Entity(repositoryClass: NoteRepository::class)]
class Note
{

    /** @var int|null Идентефикатор записи */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** @var string|null Текст записи */
    #[ORM\Column(length: 500)]
    private ?string $body = null;

    /** @var DateTimeInterface|null Дата создания записи */
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $createDt = null;

    /** @var DateTimeInterface|null Дата редактирования записи */
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updateDt = null;

    /** @var User|null Связь с таблицей пользователей */
    #[ORM\ManyToOne(inversedBy: 'notes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    /** @var string|null Название прекрепленного файла */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $filename = null;

    /** @var FormView Форма для изменения записи */
    public FormView $form;
    /**
     * Возвращает идентефикатор записи
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Возвращает текст записи
     * @return string|null
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Установливает текст записи
     * @param string $body Текст
     * @return $this
     */
    public function setBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Возвращает дату создания записи
     * @return DateTimeInterface|null
     */
    public function getCreateDt(): ?DateTimeInterface
    {
        return $this->createDt;
    }

    /**
     * Изменяет дату создания записи
     * @return Note
     */
    public function setCreateDt(): static
    {
        $this->createDt = Clock::get()->now();
        return $this;
    }

    /**
     * Возвращает дату редактирования записи
     * @return DateTimeInterface|null
     */
    public function getUpdateDt(): ?DateTimeInterface
    {
        return $this->updateDt;
    }

    /**
     * Изменяет дату редактировани записи
     * @param DateTimeInterface|null $updateDt
     * @return $this
     */
    public function setUpdateDt(?DateTimeInterface $updateDt): static
    {
        $this->updateDt = $updateDt;

        return $this;
    }

    /**
     * возвращает пользователя кому пренадлежит запись
     * @return User|null
     */
    public function getUserId(): ?User
    {
        return $this->userId;
    }

    /**
     * устанавливает пользователя кому пренадлежит запись
     * @param User|null $userId
     * @return $this
     */
    public function setUserId(?User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Возаращает название файла прикрепленного к записм
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Изменяет название файла прекрепленного к записи
     * @param string|null $filename
     * @return $this
     */
    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }
}
