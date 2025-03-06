<?php

namespace App\Models;

class VideoPost extends Post implements IPost
{
    private string $video;
    private string $mensaje;

    public function __construct(
        string $mensaje,
        string $video
    ) {
        print_r("Se creo un nuevo objecto VideoPost \n");
        $this->video = $video;
        parent::__construct($mensaje);
    }

    public function getVideo(): string
    {
        return $this->video;
    }

    public function toString(): string
    {
        $info = "Id: {$this->getId()} \n";
        $info .= "Mensaje: {$this->getMensaje()} \n";
        $info .= "Video: {$this->getVideo()} \n\n";
        $info .= "Likes: " . count($this->getLikes()) . "\n\n";

        return $info;
    }
}
