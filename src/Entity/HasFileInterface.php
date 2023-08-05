<?php

namespace App\Entity;

interface HasFileInterface
{
    public function getFileUrl(): ?string;

    public function setFileUrl(?string $fileUrl);


}