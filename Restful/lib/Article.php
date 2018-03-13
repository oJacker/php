<?php

require_once __DIR__.'/ErrorCode.php';

class Article{

    /**
     * 数据库句柄
     * @var
     */
    private $_db;

    /**
     * 构造方法
     * Article constructor.
     * @param PDO $_db 数据库连接句柄
     *
     */
    public function __construct($_db)  {
        $this->_db=$_db;
    }
    /**
     * 创建文章
     * @param $title
     * @param $content
     * @param $userId
     */
    public function create($title,$content,$userId){
        if(empty($tite)){
            throw new Exception('文章标题不能为空',ErrorCode::ARTICLE_TILE_CANNOT_EMPTY);
        }
        if(empty($content)){
            throw new Exception('文章内容不能为空',ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY);
        }
        $sql = 'INSERT INTO `article`(`title`,`content`,`userId`,`createdAt`)VALUES (:title,:content,:userId,:createdAt)';
        $createdAt = time();
        $stmt =$this->_db->prepare($sql);
        $stmt->bindParam(':title',$title);
        $stmt->bindParam(':content',$content);
        $stmt->bindParam(':userId',$userId);
        $stmt->bindParam(':createdAt',$createdAt);
        if(!$stmt->execute()){
            throw new Exception('发表文章失败',ErrorCode::ARTICLE_CREATE_FAIL);
        }
        return[
            'articleId' =>$this->_db->lastInsertId(),
            'title'=>$title,
            'content'=>$content,
            'createdAt'=>$createdAt
        ];
    }

    /**
     * 查看一篇文章
     * @param $articleId
     * @return mixed
     * @throws Exception
     */
    public function view($articleId){
        if(empty($articleId)){
            throw new Exception('文章ID不能为空',ErrorCode::ARTICLE_ID_CANNOT_EMPTY);
        }
        $sql = 'select * from `article` where `articleId`=:Id';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':id',$articleId);
        $stmt->execute();
        $article =  $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($article)){
            throw new Exception('文章不存在',ErrorCode::ARITCLE_NOT_FOUND);
        }
        return $article;
    }

    /**
     * 编辑文章
     * @param $articleId
     * @param $title
     * @param $content
     * @param $userId
     */
    public function edit($articleId,$title,$content,$userId){
        $article=$this->view($articleId);
        if($article['userId'] !== $userId){
            throw new Exception('无权编辑改文章',ErrorCode::PERMISSION_DENIED);
        }

        $title = empty($title) ? $article['title'] : $title;
        $content = empty($content) ? $article['content'] : $content;

        if ($title === $article['title'] && $content === $article['content']){
            return $article;
        }
        $sql = 'update `article` set `title`=:title,`content`=:content where `articleId`=:id';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':id',$articleId);
        $stmt->bindParam(':title',$title);
        $stmt->bindParam(':content',$content);
        if(!$stmt->execute()){
            throw new Exception('文件编辑失败',ErrorCode::ARITCLE_EDIT_FAIL);
        }
        return [
            'articleId' =>$articleId,
            'title' => $title,
            'content'=>$content,
            'createdAt'=>$article['createdAt']
        ];


    }

    /**
     * 删除文章
     * @param $articleId
     * @param $userId
     * @return bool
     */
    public function delete($articleId,$userId){
        $article = $this->view($articleId);
        if($article['userId'] !== $userId){
            throw new Exception('无权操作',ErrorCode::PERMISSION_DENIED);
        }
        $sql = 'DELETE FROM `article` where `articleId`=:articleId and `userId`=:userId';
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':articleId',$articleId);
        $stmt->bindParam(':userId',$userId);
        if(false === $stmt->execute()){
            throw  new Exception('删除失败',ErrorCode::ARTICLE_DELETE_FAIL);
        }
        return true;
    }

    /**
     * 读取文章列表
     * @param $userId
     * @param int $page
     * @param int $size
     */
    public function getList($userId,$page=1,$size=10){

        if($size > 100){
            throw new Exception('分页大小最大为100',ErrorCode::PAGE_SIZE_TO_BIG);
        }
        $sql = 'select * from `article` where `userId`=:userId limit :limit,:offset';
        $limit = ($page-1 ) * $size;
        $limit = $limit<0 ? 0:$limit;
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':userId',$userId);
        $stmt->bindParam(':limit',$limit);
        $stmt->bindParam(':offset',$size);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;


    }
}