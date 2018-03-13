<?php
/**
 * Created by PhpStorm.
 * User: SINCE
 * Date: 2018/3/13
 * Time: 11:40
 */

require_once __DIR__.'/../lib/User.php';
require_once __DIR__.'/../lib/Article.php';
$pdo = require __DIR__.'/../lib/db.php';
class Restful{
    /**
     * @var User
     */
    private $_user;
    /**
     * @var Article
     */
    private $_article;

    /**
     * 请求方法
     * @var string
     */
    private $_requestMethod;

    /**
     * 请求资源名称
     * @var string
     */
    private  $_resourName;

    /**
     * 请求资源ID
     * @var string
     */
    private  $_id;

    /**
     * 允许请求的资源列表
     * @var array
     */
    private $_allowResource =['users','articles'];

    /**
     * 允许请求HTTP方法
     * @var array
     */
    private $_allowRequestMethods = ['GET','POST','PUT','DELETE','OPTIONS'];

    /**
     * 常用状态码
     * @var array
     */
    private $_statusCode=[
        200=>'ok',
        204=>'No Content',
        400=>'Bad Request',
        401=>'Unauthorized',
        403=>'Forbidden',
        404=>'Not Found',
        405=>'Method Not Allowed',
        500=>'Server Internal Error'
    ];
    /**
     * Restful constructor.
     * @param User $_user
     * @param Article $_article
     */

    public function __construct(User $_user, Article $_article)
    {
        $this->_user=$_user;
        $this->_article=$_article;
    }

    public function run(){
        try{
            $this->_setupRequestMethod();
            $this->_setupResource();
           // $this->_setupId();
            if($this->_resourName == 'users'){
                $this->_json($this->_handleUser());
            }else{
                $this->_json($this->_handleArticle());
            }
        }catch (Exception $e){
            $this->_json(['error'=>$e->getMessage()],$e->getCode());
        }

    }
    private function _setupResource(){
        $path=$_SERVER['PATH_INFO'];
        $params = explode('/',$path);
        $this->_resourName = $params[1];
        if(!in_array($this->_resourName,$this->_allowResource)){
            throw new Exception('请求资源不被允许',400);
        }
        if(!empty($params[2])){
            $this->_id = $params[2];
        }
    }
    /**
     * 初始化请求方法
     * @throws Exception
     */
    private function _setupRequestMethod(){
        $this->_requestMethod = $_SERVER['REQUEST_METHOD'];
        if(!in_array($this->_requestMethod,$this->_allowRequestMethods)){
            throw new   Exception('请求方法不允许',405);
        }
    }

    /**
     * 输出JSON
     * @param $array
     */
    private function _json($array,$code = 0){

        if($array === null && $code ===0){
            $code = 204;
        }
        if($code===0 && $array !==null){
            $code = 200;
        }
        header("HTTP/1.1".$code." ".$this->_statusCode[$code]);
        header('Content-Type:application/json;charset=utf-8');
        if($array !==null){
            echo json_encode($array,JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    /**
     * 请求用户
     * @return array
     * @throws Exception
     */
    private function _handleUser(){
        if($this->_requestMethod != 'POST'){
            throw new Exception('请求方法不允许',405);
        }
        $body = $this->_getBodyParams();
        if(empty($body['username'])){
            throw new Exception('用户名不能为空',400);
        }
        if(empty($body['password'])){
            throw new Exception('密码不能为空',400);
        }
        return $this->_user->register($body['username'],$body['password']);
    }

    /**
     * 请求文章资源
     */
    private function _handleArticle(){
        switch($this->_requestMethod){
            case 'POST':
                return $this->_handleArticleCreate();
            case 'PUT':
                return $this->_handleArticleEdit();
            case 'DELETE':
                return $this->_handleArticleDelete();
            case 'GET':
                if(empty($this->_id)){
                    return $this->_handleArticleList();
                }else{
                    return $this->_handleArticleView();
                }
            default:
                throw new Exception('请求方法不被允许',405);
        }
    }

    /**
     * 创建文章
     * @ return array
     * @throws Exception
     */
    private  function  _handleArticleCreate(){
        $body = $this->$this->_getBodyParams();
        if(empty($body['title'])){
            throw new Exception('文章标题不能为空',400);
        }
        if(empty($body['content'])){
            throw new Exception('文章内容不能为空',400);
        }

        $user = $this->_userLgoin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        try{
            $article = $this->_article->create($body['title'],$body['content'],$user['userId']);
        }catch (Exception $e){
            if(!in_array($e->getCode(),[
                ErrorCode::ARTICLE_TILE_CANNOT_EMPTY,
                ErrorCode::ARTICLE_CONTENT_CANNOT_EMPTY,
                ])
            ){
                throw new Exception($e->getMessage(),400);
            }
            throw new Exception($e->getMessage(),500);
        }
    }

    /**
     * 用户登录
     * @param $PHP_AUTH_USER
     * @param $PHP_AUTH_PW
     * @return mixed
     * @throws Exception
     */
    private function _userLgoin($PHP_AUTH_USER,$PHP_AUTH_PW){
        try{
            return $this->_user->login($PHP_AUTH_USER,$PHP_AUTH_PW);
        }catch (Exception $e){
            if(in_array($e->getCode(),[
                ErrorCode::USERNAME_CANNOT_EMPTY,
                ErrorCode::PASSWORD_CANNOT_EMPTY],
                ErrorCode::USERNAME_OR_PASSWORD_INVALID
            )){
                throw new Exception($e->getMessage(),400);
            }
            throw new Exception($e->getMessage(),500);
        }
    }

    /**
     * 文章编辑
     * @return array|mixed
     * @throws Exception
     */
    private  function _handleArticleEdit(){
        $user = $this->_userLgoin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        try{
            $article = $this->_article->view($this->_id);
            if($article['userId'] !=$user['userId']){
                throw new Exception('无权编辑',403);
            }
            $body = $this->_getBodyParams();
            $title= empty($body['title']) ? $article['title'] :$body['title'];
            $content= empty($body['content']) ? $article['content'] :$body['content'];
            if($title == $article['title'] && $content == $article['content']){
                return $article;
            }else{
                $this->_article->edit($article['articleId'],$title,$content,$user['userId']);
            }
        }catch (Exception $e){
            if($e->getCode() < 100){
                if($e->getCode() == ErrorCode::ARITCLE_NOT_FOUND){
                    throw new Exception($e->getMessage(),404);
                }else{
                    throw new Exception($e->getMessage(),500);
                }
            }else{
                throw $e;
            }

        }
    }

    /**
     * 删除文章
     * @return null
     * @throws Exception
     */
    private  function  _handleArticleDelete(){
        //用户登录
        $user = $this->_userLgoin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        try{
            $article= $this->_article->view($this->_id);
            if($article['userId'] !=$user['userId']){
                throw new Exception('无权删除',403);
            }
            $this->_article->delete($article['articleId'],$user['userId']);
            return null;
        }catch (Exception $e){
            if($e->getCode() < 100){
                if($e->getCode() == ErrorCode::ARITCLE_NOT_FOUND){
                    throw new Exception($e->getMessage(),404);
                }else{
                    throw new Exception($e->getMessage(),500);
                }
            }else{
                throw $e;
            }
        }
    }

    /**
     *文章分页
     * @return array
     * @throws Exception
     */
    private function _handleArticleList(){

        $user = $this->_userLgoin($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW']);
        $page = isset($_GET['page']) ? $_GET['page']:1;
        $size = isset($_GET['size']) ? $_GET['size']:10;
        if($size>100){
            throw new Exception('分页大小最大100',400);
        }
        return $this->_article->getList($user['userId'],$page,$size);
    }

    private function  _handleArticleView(){
        try{
            return $this->_article->view($this->_id);
        }catch (Exception $e){
            if($e->getCode() == ErrorCode::ARITCLE_NOT_FOUND){
                throw new Exception($e->getMessage(),404);
            }else{
                throw new Exception($e->getMessage(),500);
            }
        }
    }
    /*
     * 获取请求体参数
     * @return mixed
     * @throws Exception
     */
    private function _getBodyParams(){
        $raw = file_get_contents('pp://input');
        if(empty($raw)){
            throw new Exception('请求参数错误',400);

        }
        return json_decode($raw,true);
    }
}
$article = new Article($pdo);
$user = new User($pdo);
$restful = new Restful($user,$article);
$restful->run();