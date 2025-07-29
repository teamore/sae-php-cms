<?php
namespace App\Controller;
use App\Config;
use App\Model\Post;
use App\Model\PostComment;
use App\Model\PostLike;
use App\Traits\Paginatable;
use App\Uploader;
class PostController extends AbstractController {
    use Paginatable;
    public function index()
    {
        $filter = isset($_REQUEST['filter']) ? array_filter($_REQUEST['filter']) : null;
        $this->setPagination(Post::count($filter));
        $this->setResultsPerPage($_REQUEST['per_page'] ?? Config::getConfig('paginator')['per_page']);

        $results = Post::all($filter, $this->resultsPerPage, (($this->currentPage) - 1) * ($this->resultsPerPage));

        $this->setView('index.html', [
            'results' => $results,
            'filter' => $filter,
            'paginatorConfig' => Config::getConfig('paginator')
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
        $comments = $this->db()->query("SELECT * FROM `post_comments` WHERE `post`='$id';")->fetchAll();
        $result['comments'] = $comments;
        # call view
        $this->setView('post.html', ['result' => $result]);
    }
    public function comment($id = null) {
        $id = $this->query['post_id'] ?? 0;
        $uid = $this->getUserId(true);
        $result = PostComment::insert($id, $uid, $_POST['content'], $_POST['title']);
        return $this->one($id);
    }
    public function uncomment($commentId = null) {
        $commentId = $this->query['comment_id'] ?? 0;
        $uid = $this->getUserId(true);
        if ($uid) {
            $comment = PostComment::find($commentId);
            if ($uid != $comment['user']) {
                $authorId = Post::find($comment['post'], 'p.`user`');
                if ($authorId != $uid) {
                    throw new \Exception('Users are not allowed to delete foreign Comments', 403);
                }
            }
        }            
        try {
            $result = PostComment::delete($commentId);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(),500);
        }
        if (!$result) {
            throw new \Exception("This resource has already been deleted.",304);
        }
        return ["affectedRows" => $result];
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
        if ($data['id'] ?? null) {
            if (!is_numeric($data['id'])) {
                return;
            }
            $result = Post::update($data);
            
        } else {
            $data['id'] = Post::insert($data);
            $result = $data['id'];
        }
        
        $uploader = new Uploader();
        Uploader::delete($data['id'], 'posts');
        $media = $uploader->handleFileUploads("posts/$data[id]/");

        # Store media array as JSON
        if (count($media) > 0) {
            Post::attachMedia($media['media'], $data['id']);
        }
        return $result;
    }
    public function delete($id = null) {
        $id = $id ?? $this->query['post_id'] ?? null;
        if (!$id) {
            throw new \Exception('You may only delete existing posts', 404);
        }
        $uid = $this->getUserId(true);
        $media = $this->db()->query("SELECT `media` FROM `posts` WHERE `id`='$id';")->fetchColumn();
        if ($media) {
            $media = json_decode($media, true);
            foreach($media as $file) {
                unlink("/var/www/html/public/".$file['path'].$file['name']);
                unlink("/var/www/html/public/".$file['thumb']);
            }
        }
        $result = $this->db()->exec("DELETE FROM `posts` WHERE `id`='$id' AND `user`='$uid' LIMIT 1;");
        return $result;
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
            $result = PostLike::unlike($postId, $uid);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(),500);
        }
        if (!$result) {
            throw new \Exception("This resource has already been deleted.",304);
        }
        return ["affectedRows" => $result];
    }
}