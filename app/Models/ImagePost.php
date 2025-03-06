<?php

namespace App\Models;


class ImagePost extends Post implements IPost
{
    private string $image;
    private string $mensaje;

    public function __construct(
        string $mensaje,
        string $image
    ) {
        print_r("Se creo un nuevo objecto ImagePost \n");
        $this->image = $image;
        parent::__construct($mensaje);
    }

    public function getImagen(): string
    {
        return $this->image;
    }

    public function toString(): string
    {
        $info = "Id: {$this->getId()} \n";
        $info .= "Mensaje: {$this->getMensaje()} \n";
        $info .= "Imagen: {$this->getImagen()} \n\n";
        $info .= "Likes: " . count($this->getLikes()) . "\n\n";

        return $info;
    }
}
