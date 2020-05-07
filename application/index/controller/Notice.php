<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Notice as NoticeModel;

class Notice extends AdminController{
    // 添加公告
    public function add(){
        $data = processRequest();
        if(!isset($data["notice_title"]) || !isset($data["notice_desc"])){
            return jsonAPI("请求参数为空！", 401);
        }
        $file = null;
        if(isset($data["notice_file"])){
            $file = $data["notice_file"];
        }
        $notice = new NoticeModel([
                "notice_title" => $data["notice_title"],
                "notice_desc" => $data["notice_desc"],
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
            return "server\\uploads\\file\\".$info->getSaveName();
        }else{                              
            //  上传失败获取错误信息                     
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

    // 查询公告
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
            $notice[] = [
                "notice_id" => $value->notice_id,
                "notice_title" => $value->notice_title
            ];
        }
        return jsonAPI("查询成功！", 200, $notice);
    }
}