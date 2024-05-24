<?php

namespace App\Entity;

use App\Repository\LogRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $context = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $level = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $level_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $channel = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $extra = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $formatted = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getContext(): ?array
    {
        return $this->context;
    }

    public function setContext(?array $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getLevelName(): ?string
    {
        return $this->level_name;
    }

    public function setLevelName(?string $level_name): static
    {
        $this->level_name = $level_name;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): static
    {
        $this->channel = $channel;

        return $this;
    }

    public function getExtra(): ?array
    {
        return $this->extra;
    }

    public function setExtra(?array $extra): static
    {
        $this->extra = $extra;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getFormatted(): ?string
    {
        return $this->formatted;
    }

    public function setFormatted(?string $formatted): static
    {
        $this->formatted = $formatted;

        return $this;
    }
}
