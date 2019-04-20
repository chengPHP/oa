<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CategoryRequest;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*if(no_permission('Category')){
            return view(config('program.no_permission_to_view'));
        }*/
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
        $list = Category::where($map)->paginate(config("program.PAGE_SIZE"));
        return view('admin.category.index',compact('list','search'));
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
        $list = Category::get();
        return view('admin.category.add',compact('list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        /*if(no_permission('createCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        $category = new Category();
        $category->category_code = $request->category_code;
        $category->name = $request->name;
        $category->pid = $request->pid;
        if($request->pid==0){
            $category->path = '0,';
        }else{
            $path = Category::where("id",$request->pid)->value('path');
            $category->path = $path.','.$request->pid;
        }
        $category->status = $request->status;
        $info = $category->save();
        if($info){
            $message = [
                'code' => 1,
                'message' => '类别添加成功'
            ];
        }else{
            $message = [
                'code' => 0,
                'message' => '类别添加失败'
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
        $info = Category::find($id);
        return view("admin.category.edit",compact('info'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        /*if(no_permission('editCategory')){
            return view(config('program.no_permission_to_view'));
        }*/
        $arr = [
            'category_code' => $request->category_code,
            'name' => $request->name,
            'pid' => $request->pid
        ];
        if($request->pid==0){
            $arr['path'] = '0,';
        }else{
            $path = Category::where("id",$request->pid)->value('path');
            $arr['path'] = $path.','.$request->pid;
        }
        $arr['status'] = $request->status;
        $info = Category::where('id',$id)->update($arr);
        if($info){
            $message = [
                'code' => 1,
                'message' => '类别信息修改成功'
            ];
        }else{
            $message = [
                'code' => 0,
                'message' => '类别信息修改失败，请稍后重试'
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
            $info = Category::where('pid',$v)->first();
            if($info){
                $message = [
                    'code' => 0,
                    'message' => '此类别下面还有子类别，不能删除'
                ];
            }else{
                if(Asset::where('category_id',$v)->first()){
                    $message = [
                        'code' => 0,
                        'message' => '此类别下面还有资产，不能删除'
                    ];
                }else{
                    $info1 = Category::where('id',$v)->update(['status'=>-1]);
                    if($info1){
                        $message = [
                            'code' => 1,
                            'message' => '类别信息删除成功'
                        ];
                    }
                }
            }
        }

        return response()->json($message);
    }
}
