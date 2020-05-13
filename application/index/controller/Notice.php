<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Notice as NoticeModel;
use app\index\model\User;
use app\tools\UserToken;

class Notice extends AdminController{
    // 添加公告
    public function add(){
        $data = processRequest();
        if(!isset($data["notice_title"]) || !isset($data["notice_desc"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $userToken = new UserToken();
        $user_id = $userToken->checkToken();
        $file = null;
        if(isset($data["notice_file"])){
            $file = $data["notice_file"];
        }
        $notice = new NoticeModel([
                "notice_title" => $data["notice_title"],
                "notice_desc" => $data["notice_desc"],
                "user_id" => $user_id,
                "notice_file" => $file
            ]);
        if($notice->save()){
            return jsonAPI("公告添加成功！", 200);
        }
        return jsonAPI("公告添加失败！", 500);
    }

    //上传附件
    public function upload(){
        $file = request()->file("file");
        $info = $file->move(ROOT_PATH."uploads/file");
        if($info){
            return "server/uploads/file/".$info->getSaveName();
        }else{                              
            // 上传失败获取错误信息                     
            return $file->getError();              
        }
    }

    // 编辑公告
    public function modify(){
        $data = processRequest();
        if(!isset($data["notice_title"]) || !isset($data["notice_desc"]) || !isset($data["notice_id"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $file = null;
        if(isset($data["notice_file"])){
            $file = $data["notice_file"];
        }
        $notice = NoticeModel::where("notice_id", $data["notice_id"])->find();
        if(!$notice){
            return jsonAPI("修改公告不存在！", 500);
        }
        $notice->notice_title = $data["notice_title"];
        $notice->notice_desc = $data["notice_desc"];
        $notice->notice_file = $data["notice_file"];
        if($notice->save()){
            return jsonAPI("公告修改成功！", 200);
        }
        return jsonAPI("公告修改失败！", 500);
    }

    // 删除公告
    public function delete(){
        $data = processRequest();
        if(!isset($data["notice_id"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $notice = NoticeModel::where("notice_id", $data["notice_id"])->find();
        if($notice->delete()){
            return jsonAPI("公告删除成功！", 200);
        }
        return jsonAPI("公告删除失败！", 500);
    }

    // 查询公告列表
    public function query(){
        $data = processRequest();
        // 获取当前页码和每页条数
        if(!isset($data["pagenum"]) || !isset($data["pagesize"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $pagenum = $data["pagenum"];
        $pagesize = $data["pagesize"];
        $notice = new NoticeModel();

        if(!isset($data["query"])){
            // 所有公告
            $total  = $notice->count();
            $list = $notice->page($pagenum, $pagesize)->select();
        }else{
            //关键字查询
            $name = $data["query"];
            $total = $notice->where("notice_title", "like", "%".$name."%")->count();
            $list = $notice->where("notice_title", "like", "%".$name."%")->page($pagenum, $pagesize)->select();
        }
        $notice = array();
        foreach ($list as $key => $value) {
            $user = User::where("user_id", $value->user_id)->find();
            $notice[] = [
                "notice_id" => $value->notice_id,
                "notice_title" => $value->notice_title,
                "create_time" => $value->create_time
            ];
        }
        $data = [
            "total" => $total,
            "pagenum" => $pagenum,
            "notice" => $notice
        ];
        return jsonAPI("查询成功！", 200, $data);
    }

    // 公告详情
    public function desc(){
        $data = processRequest();
        // 获取当前页码和每页条数
        if(!isset($data["notice_id"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $notice_id = $data["notice_id"];
        $notice = new NoticeModel();
        $list = $notice->where("notice_id", $notice_id)->find();
        if(!$list){
            return jsonAPI("公告不存在！", 500);
        }
        $user = User::where("user_id", $list->user_id)->find();
        $notice = [
            "notice_id" => $list->notice_id,
            "username" => $user->username,
            "notice_title" => $list->notice_title,
            "notice_desc" => $list->notice_desc,
            "create_time" => $list->create_time,
            "notice_file" => $list->notice_file
        ];
        return jsonAPI("查询成功！", 200, $notice);
    }
}