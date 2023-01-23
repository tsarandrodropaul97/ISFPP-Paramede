<?php

namespace App\Entity;


class RechercheParAnnee
{

    private $an;


    public function getAn(): ?int
    {
        return $this->an;
    }

    public function setAn(int $an): self
    {
        $this->an = $an;

        return $this;
    }
}
