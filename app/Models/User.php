<?php

namespace App\Models;


use Api\Maicoldev\Exceptions\HttpException;
use App\Utils\UUID;
use App\Database\Connection;

use PDO;

class User
{

    private ?string $name, $username, $email, $password, $id;
    private ?bool $is_active, $is_admin;
    private array $posts, $followers;
    private static array $users;
    public Connection $conn;

    private const TABLE = "api_users.users";


    /**
     * @return string name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string emial
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return int is_active
     */
    public function getIs_active(): bool
    {
        return $this->is_active;
    }

    /**
     * @param bool is_active
     */
    public function setIs_active(bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    /**
     * @return bool is_admin
     */
    public function getIs_admin(): bool
    {
        return $this->is_admin;
    }

    /**
     * @param bool is_admin
     */
    public function setIs_admin(bool $is_admin): void
    {
        $this->is_admin = $is_admin;
    }

    /**
     * @return string id
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return array posts
     */
    public function getPosts(): array
    {
        return $this->posts;
    }

    /**
     * @return string followers
     */
    public function getFollowers(): array
    {
        return $this->followers;
    }

    /**
     * @param array followers
     */
    public function setFollowers(array $followers): void
    {
        $this->followers = $followers;
    }

    /**
     * @param object Post
     */
    public function setPublish(Post $post): void
    {
        array_push($this->posts, $post);
    }

    /**
     * @return array users
     */
    public static function getUsers(): array
    {
        return self::$users;
    }

    /**
     * @param array users
     */
    public static function setUsers(array $users): void
    {
        self::$users = $users;
    }


    public function __construct(
        ?string $name,
        ?string $username,
        ?string $email,
        ?string $password,
        ?bool $is_active,
        ?bool $is_admin
    ) {
        // properties
        $this->id = UUID::generate();
        $this->username = $username;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->is_active = $is_active;
        $this->is_admin = $is_admin;
        // arrays
        $this->followers = [];
        $this->posts = [];
        self::$users = [];
        // connection
        $this->conn = new Connection();
    }


    /**
     * 
     */
    public function showPosts()
    {
        foreach ($this->posts as $post) {
            var_dump("xxxxxxxxxxxxxxxxxxxxxxx", $post->toString());
        }
    }

    /**
     * 
     */
    private function hastFollower(User $user)
    {
        $found = array_filter(
            $this->followers,
            fn (User $follower) => $follower->id == $user->id,
        );
        return count($found) == 1;
    }

    /**
     * 
     */
    public function addFollower(User $user)
    {
        if (!$this->hastFollower($user)) {
            if ($this->id == $user->id) {
                print_r("No te puedes agregar a ti mismo como follower \n");
            } else {
                array_push($this->followers, $user);
            }
        } else {
            print_r("El usuario $user->username ya es un follower \n");
        }
    }

    /**
     * @return int id 
     */
    public function create(): int
    {
        // echo "create model";

        $password = password_hash($this->password, PASSWORD_DEFAULT);
        $table = self::TABLE;

        $users = self::getAll();

        foreach ($users as $user) {

            if ($this->username === $user['username'] && $this->email === $user['email']) {
                # validate username and email
                throw new HttpException("Username and email already exists", 422);
                exit;
            }

            if ($this->username === $user['username']) {
                # validate username
                throw new HttpException("Username already exists", 422);
                exit;
            }

            if ($this->email === $user['email']) {
                # validate email
                throw new HttpException("Email already exists", 422);
                exit;
            }
        }

        $consulta = "INSERT INTO {$table} (name, username, email, password, is_active, is_admin) VALUES (:name, :username, :email, :password, :is_active, :is_admin)";
        $stmt = $this->conn->prepare($consulta);

        $stmt->execute(array(':name' => $this->name, ':username' => $this->username, ':email' => $this->email, ':password' => $password, ':is_active' => $this->is_active, ':is_admin' => $this->is_admin));

        $this->id = $this->conn->lastInsertId();

        return $this->id;
    }


    /**
     * get all users
     * @return array users
     */
    public static function getAll(): array
    {
        $conn = new Connection;
        $table = self::TABLE;
        $data = [];

        $consulta = "SELECT id, name, username, email, password, is_active, is_admin FROM {$table} ORDER BY name";

        $stmt = $conn->prepare($consulta);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row['is_active'] = (bool) $row['is_active'];
            $row['is_admin'] = (bool) $row['is_admin'];

            array_push($data, $row);
        }

        return $data;
    }


    /**
     * return user
     * @param int id user
     * @return object user
     */
    public static function getUserId(int $id): object
    {

        $table = self::TABLE;
        $conn = new Connection();

        $consulta = "SELECT id, name, username, email, password, is_active, is_admin FROM {$table} WHERE id = :id";

        $stmt = $conn->prepare($consulta);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $user->is_active = (bool) $user->is_active;
        $user->is_admin = (bool) $user->is_admin;

        return $user;
    }

    /**
     * 
     */
    public static function getUserEmail(User $usr): array
    {

        $users = self::getAll();

        foreach ($users as $user) {
            if ($user['email'] === $usr->email) {
                return $user;
            }
        }
    }

    /**
     * @return array User
     */
    public static function showProfile(User $user): array
    {
        $profile = [
            "id" => $user->getId(),
            "uusername" => $user->username,
            "name" => $user->username,
            "followers" => count($user->followers),
            "posts" => count($user->posts)
        ];

        return $profile;
    }
}
