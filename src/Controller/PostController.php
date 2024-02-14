<?php
namespace App\Controller;
use App\Model\Post;
use App\Model\PostLike;
use App\Uploader;
class PostController extends AbstractController {
    public function index()
    {
        $filter = isset($_REQUEST['filter']) ? array_filter($_REQUEST['filter']) : null;
        $resultsAmount = Post::count($filter);
        /* 
        $results = Post::all($_REQUEST['limit'] ?? null, $_REQUEST['offset'] ?? null);
        */
        $perPage = $_REQUEST['per_page'] ?? 5;
        $pagesAmount = ceil($resultsAmount / $perPage);
        $currentPage = $_REQUEST['page'] ?? 1;
       
        # enforce min/max currentPage #
        $currentPage = $currentPage > $pagesAmount ? $pagesAmount : ($currentPage < 1 ? 1 : $currentPage);

        $results = Post::all($filter, $perPage, (($currentPage) - 1) * ($perPage));

        $this->setView('index.html', [
            'results' => $results, 
            'pagesAmount' => $pagesAmount,
            'currentPage' => $currentPage,
            'perPage' => $perPage,
            'filter' => $filter
        ]);
    }
    public function one($id = null) {
        $id = $id ?? $this->query['post_id'];
        $uid = $this->getUserId();

        $result = Post::find($id);

        if (!$result) {
            throw new \Exception("This Resource does not exist", 404);
        }
        $result['media'] = $result['media'] ? json_decode($result['media']) : '';   
        if ($uid) {
            $result2 = PostLike::findBy("`post`='$id' AND `user`='$uid'", "id");
            $result['ilike'] = ($result2 !== false);
        }            
        # call view
        $this->setView('post.html', ['result' => $result]);
    }
    public function edit($id = null) {
        $id = $this->query['post_id'] ?? 0;
        if ($id) {
            # retrieve results
            $result = Post::find($id);
            if ($result) {
                if ($result['user'] !== $this->getUserId()) {
                    throw new \Exception('Users are not allowed to edit foreign Posts', 403);
                }
            } else {
                throw new \Exception('This Post does not exist', 404);
            }
        }
        $this->setView('post_update.html', ['result' => $result ?? []]);        
    }    
    public function save($data = null) {
        $data = $data ?? $_REQUEST;

        # TODO: perform mysql query sanitation
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception('Only authenticated Users may create Posts', 401);
        }
        $data['user'] = $user->id;
        if ($data['id']) {
            if (!is_numeric($data['id'])) {
                return;
            }
            $result = Post::update($data);
            
        } else {
            $data['id'] = Post::insert($data);
            $result = $data['id'] > 0;
        }
        $uploader = new Uploader();
        $media = $uploader->handleFileUploads("posts/$data[id]/");

        # Store media array as JSON
        if (count($media) > 0) {
            Post::attachMedia($media, $data['id']);
        }
        return $result;
    }
    public function delete($id = null) {
        $id = $id ?? $this->query['post_id'];
        $uid = $this->getUserId(true);
        $media = $this->db()->query("SELECT `media` FROM `posts` WHERE `id`='$id';")->fetchColumn();
        $media = json_decode($media, true);
        foreach($media as $file) {
            unlink("/var/www/html/public/".$file['path'].$file['name']);
            unlink("/var/www/html/public/".$file['thumb']);
        }
        $result = $this->db()->exec("DELETE FROM `posts` WHERE `id`='$id' AND `user`='$uid' LIMIT 1;");
        return $this->index();
    }
    public function like($id = null) {
        $postId = $id ?? $this->query['post_id'];
        $uid = $this->getUserId(true);

        try {
            $result = $this->db()->query("SELECT `user` FROM `posts` WHERE `id`='$postId'")->fetchColumn();
            if ($uid === $result) {
                throw new \Exception('Users must not like their own posts.', 400);
            }
            $result = PostLike::insert($postId, $uid);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(),304);
        }
        return ["affectedRows" => $result];
    }

    public function unlike($id = null) {
        $postId = $id ?? $this->query['post_id'];
        $uid = $this->getUserId(true);

        try {
            $result = PostLike::delete($postId, $uid);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(),500);
        }
        if (!$result) {
            throw new \Exception("This resource has already been deleted.",304);
        }
        return ["affectedRows" => $result];
    }
}