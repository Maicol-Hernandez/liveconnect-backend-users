<?php

namespace App\Models;

use App\Utils\UUID;


class Post
{
    private string $id;
    private string $mensaje;
    private array $likes;
    public function __construct(string $mensaje)
    {
        print_r("Se creo un nuevo objecto Post \n");
        $this->id = UUID::generate();
        $this->mensaje = $mensaje;
        $this->likes = [];
    }
    # -> solo tiene alcance dentro de la clase
    private function saludo1(): string
    {
        return "Hola desde este post[saludo1], con el id $this->id";
    }
    # -> tiene alce desde la clase padre y tambien solo desde la hija
    protected function saludo()
    {
        return "Hola desde este post[saludo], con el id $this->id";
    }
    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }

    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    public function getLikes(): array
    {
        return $this->likes;
    }

    protected function checkIfUserLiked(User $user): bool
    {

        $found = array_filter(
            $this->likes,
            function (Like $like) use ($user) {
                return $like->getUser()->getId() == $user->getId();
            }
        );
        return count($found) == 1;
    }

    public function addLike(User $user)
    {
        if ($this->checkIfUserLiked($user)) {
            $this->removeLike($user);
        } else {
            $like = new Like($user);
            array_push($this->likes, $like);
        }
    }

    public function removeLike(User $user)
    {
        $this->likes = array_filter(
            $this->likes,
            fn (Like $like) => $like->getUser()->getId() == $user->getId()
        );
    }
}
