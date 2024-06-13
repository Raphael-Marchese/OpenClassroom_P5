<?php
declare(strict_types=1);

namespace App\controllers;

use App\model\repository\PostRepository;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostController extends Controller
{

    private PostRepository $repository;
    public function __construct()
    {
        parent::__construct();
        $this->repository = new PostRepository();
    }

    /**
     * Render the post list order by desc on post list page
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getCollection():void
    {
        $posts = $this->repository->findAll();

        echo $this->twig->render('post/list.html.twig', ['posts' => $posts]);
    }

    /**
     * Renter the single post page
     * @param PostRepository $repository
     * @param int $id
     * @return void
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getPost(int $id):void
    {
        $post = $this->repository->findById($id);

        echo $this->twig->render('post/post.html.twig', ['post' => $post]);
    }

    public function createPost():void
    {
        echo $this->twig->render('post/create.html.twig');
    }

    public function submitCreate():void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? null ;
            $chapo = $_POST['chapo'] ?? null ;
            $content = $_POST['content'] ?? null ;
            $createdAt = new \DateTime();
            // @Todo quand la branche user register and login sera mergée :
            //             $user = $_SESSION['LOGGED_USER']['id'] ?? null ;
            $user = 3 ;
        }

        $this->repository->save(title : $title, chapo: $chapo, createdAt: $createdAt, updatedAt: null, content: $content, author: $user);

        echo $this->twig->render('post/success.html.twig');;
    }
}