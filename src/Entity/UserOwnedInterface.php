<?php

namespace App\Entity;

interface UserOwnedInterface
{
    public function getAuthor(): ?User;

    public function setAuthor(?User $author): static;
}