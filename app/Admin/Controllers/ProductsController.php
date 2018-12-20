<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ProductsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('商品列表')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('编辑商品')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('添加商品')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->id('Id')->sortable();
        $grid->title('商品名称');
        // $grid->description('商品详情');
        // $grid->image('商品图片');
        $grid->on_sale('已上架')->display(function ($value){
            return $value? '是' : '否';
        });
        $grid->rating('评分');
        $grid->sold_count('销量');
        $grid->review_count('评论数');
        $grid->price('价格');
        $grid->created_at('创建日期');
        // $grid->updated_at('修改日期');
        $grid->actions(function ($actions) {
            $actions->disableView();    // 不显示查看
            // $actions->disableDelete();  // 不显示删除
        });

        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->title('Title');
        $show->description('Description');
        $show->image('Image');
        $show->on_sale('On sale');
        $show->rating('Rating');
        $show->sold_count('Sold count');
        $show->review_count('Review count');
        $show->price('Price');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // 创建一个表单
        $form = new Form(new Product);
        // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
        $form->text('title', '商品名称')->rules('required');
        // $form->textarea('description', 'Description');
        // 创建富文本编辑器
        $form->editor('description', '商品描述')->rules('required');
        $form->image('image', '商品图片')->rules('required|image');

        $form->switch('on_sale', '上架')->default(0);
        
        $form->decimal('rating', '综合评分')->default(5.00);
        $form->number('sold_count', '销量');
        $form->number('review_count', '评论数');
        // $form->decimal('price', '价格');  // 这个在sku里面填写
        //直接添加一对多的关联模型
        $form->hasMany('sku', 'SKU 列表', function (Form\NestedForm $form) {
            $form->text('title', 'SKU 名称')->rules('required');
            $form->text('description', 'SKU 描述')->rules('required');
            $form->text('price', '单价')->rules('required|numeric|min:0.01');
            $form->text('stock', '剩余库存')->rules('required|integer|min:0');
        });
        // 定义事件回调，当模型即将保存时会触发这个回调
        $form->saving(function (Form $form) {
            $form->model()->price = collect($form->input('sku'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
        });
        return $form;
    }
}
