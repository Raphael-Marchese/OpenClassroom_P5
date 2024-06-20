<?php
declare(strict_types=1);

namespace App\controllers;

use App\entity\BlogPost;
use App\model\repository\PostRepository;
use App\model\validator\FormValidator;
use App\model\validator\ImageValidator;
use App\model\validator\PostValidator;
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
        if($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $sanitizedData = FormValidator::validate($_POST);
        if(isset($_FILES['image']) && !empty($_FILES['image'])) {
            $sanitizedImage = FormValidator::validate($_FILES['image']);
        }


        $title = $sanitizedData['title'] ?? null ;
        $chapo = $sanitizedData['chapo'] ?? null ;
        $content = $sanitizedData['content'] ?? null ;
        $createdAt = new \DateTime();
        $image = $sanitizedImage['size'] !== 0 ? $sanitizedImage : null;
        $user = $_SESSION['LOGGED_USER']['id'] ?? null ;

        $post = new BlogPost(title: $title, chapo: $chapo, content: $content, image: $image, author: $user, status: );

        $validationErrors = array_merge(ImageValidator::validate($image), PostValidator::validate($sanitizedData)) ;




        $this->repository->save(title : $title, chapo: $chapo, createdAt: $createdAt, updatedAt: null, content: $content, author: $user);

        echo $this->twig->render('post/success.html.twig');;
    }
}