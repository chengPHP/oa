<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DepartmentRequest;
use App\Models\Admin;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth.admin:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->search){
            $search = $request->search;
            $map = [
                ['status', '>=', 0],
                ['name', 'like', '%'.$search.'%']
            ];
        }else{
            $search = '';
            $map = [
                ['status', '>=', 0]
            ];
        }
        $list = Department::where($map)->paginate(config("program.PAGE_SIZE"));
        return view('admin.department.index',compact('list','search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*if(no_permission('createCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        $list = Department::get();
        return view('admin.department.add',compact('list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentRequest $request)
    {
        /*if(no_permission('createCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        $department = new Department();
        $department->name = $request->name;
        $department->pid = $request->pid;
        if($request->pid==0){
            $department->path = '0,';
        }else{
            $path = Department::where("id",$request->pid)->value('path');
            $department->path = $path.','.$request->pid;
        }
        $department->status = $request->status;
        $info = $department->save();
        if($info){
            $message = [
                'code' => 1,
                'message' => '部门添加成功'
            ];
        }else{
            $message = [
                'code' => 0,
                'message' => '部门添加失败'
            ];
        }
        return response()->json($message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(no_permission('showCategory')){
            return view(config('program.no_permission_to_view'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        /*if(no_permission('editCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        $info = Department::find($id);
        return view("admin.department.edit",compact('info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DepartmentRequest $request, $id)
    {
        /*if(no_permission('editCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        $arr = [
            'name' => $request->name,
            'pid' => $request->pid
        ];
        if($request->pid==0){
            $arr['path'] = '0,';
        }else{
            $path = Department::where("id",$request->pid)->value('path');
            $arr['path'] = $path.','.$request->pid;
        }
        $arr['status'] = $request->status;
        $info = Department::where('id',$id)->update($arr);
        if($info){
            $message = [
                'code' => 1,
                'message' => '部门信息修改成功'
            ];
        }else{
            $message = [
                'code' => 0,
                'message' => '部门信息修改失败，请稍后重试'
            ];
        }
        return response()->json($message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*if(no_permission('destroyCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        //把ids字符串拆分成数组
        $idArr = explode(",",$id);
        $message = [];
        foreach ($idArr as $v){
            $info = Department::where('pid',$v)->first();
            if($info){
                $message = [
                    'code' => 0,
                    'message' => '此部门下面还有子类别，不能删除'
                ];
            }else{
                if(Admin::where('department_id',$v)->first()){
                    $message = [
                        'code' => 0,
                        'message' => '此类别下面还有公司员工，不能删除'
                    ];
                }else{
                    $info1 = Department::where('id',$v)->update(['status'=>-1]);
                    if($info1){
                        $message = [
                            'code' => 1,
                            'message' => '部门信息删除成功'
                        ];
                    }
                }
            }
        }

        return response()->json($message);
    }
}
