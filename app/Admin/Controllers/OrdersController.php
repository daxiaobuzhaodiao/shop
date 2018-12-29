<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Exceptions\InvalidRequestException;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Exceptions\InternalException;

class OrdersController extends Controller
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
            ->header('订单列表')
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
    public function show(Order $order, Content $content)
    {
        // return $content
        //     ->header('Detail')
        //     ->description('description')
        //     ->body($this->detail($id));

        return $content
        ->header('查看订单')
        ->description('description')
        ->body(view('admin.orders.show', compact('order')));//面顶部和左侧都还是 Laravel-Admin 原本的菜单，而页面主要内容就变成了我们这个模板视图渲染的内容了。
    }
    
    // 发货
    public function ship(Order $order, Request $request)
    {
        // 判断订单是否已付款
        if(!$order->paid_at){
            throw new InvalidRequestException('订单未付款不能发货');
        }
        // 判断订单状态是否是未发货
        if($order->ship_status !== Order::SHIP_STATUS_PENDING){
            throw new InvalidRequestException('该订单已发货');       
        }
        // 表单验证  在laravel 5.5 之后 表单验证成功后会直接返回校验过的值
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no' => ['required']
        ], [], [
            // 显示中文信息
            'express_company' => '物流公司',
            'express_no' => '物流单号'
        ]);
        // 更改订单状态为已发货
        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            // 在 order 模型的 $casts 中定义了 ship_data = json  所以可直接将数组复制给 ship_data 字段
            'ship_data' => $data
        ]);
        // 返回上一页 订单状态也将会更新
        return back();
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
            ->header('Edit')
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
            ->header('Create')
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
        $grid = new Grid(new Order);
        // 只显示已支付的订单 并按支付时间倒叙排序（最新订单靠前）
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');
        $grid->id('Id');
        $grid->no('订单流水号');
        $grid->column('user.name', '买家');// 关联关系的字段，使用column方法
        $grid->total_amount('总金额');
        $grid->payment_method('支付方法');
        $grid->paid_at('支付时间');
        $grid->ship_status('物流状态')->display(function ($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('退款状态')->display(function ($value) {
            return Order::$refundStatusMap[$value];
        });
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
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
        $show = new Show(Order::findOrFail($id));

        $show->id('Id');
        $show->no('No');
        $show->user_id('User id');
        $show->address('Address');
        $show->total_amount('Total amount');
        $show->remark('Remark');
        $show->paid_at('Paid at');
        $show->payment_method('Payment method');
        $show->payment_no('Payment no');
        $show->refund_status('Refund status');
        $show->refund_no('Refund no');
        $show->closed('Closed');
        $show->reviewed('Reviewed');
        $show->ship_status('Ship status');
        $show->ship_data('Ship data');
        $show->extra('Extra');
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
        $form = new Form(new Order);

        $form->text('no', 'No');
        $form->number('user_id', 'User id');
        $form->textarea('address', 'Address');
        $form->decimal('total_amount', 'Total amount');
        $form->textarea('remark', 'Remark');
        $form->datetime('paid_at', 'Paid at')->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', 'Payment method');
        $form->text('payment_no', 'Payment no');
        $form->text('refund_status', 'Refund status')->default('pending');
        $form->text('refund_no', 'Refund no');
        $form->switch('closed', 'Closed');
        $form->switch('reviewed', 'Reviewed');
        $form->text('ship_status', 'Ship status')->default('pending');
        $form->textarea('ship_data', 'Ship data');
        $form->textarea('extra', 'Extra');

        return $form;
    }

    // 处理退款申请
    public function handleRefund(HandleRefundRequest $request, Order $order)
    {
        // 1 判断订单状态是否是 以申请退款
        if($order->refund_status !== Order::REFUND_STATUS_APPLIED){
            throw new InvalidRequestException('订单状态不正确');
        }
        // 2 判断是否同意退款
        if($request->agree){
            // dd(11);
            // 清空拒绝退款理由
            $extra = $order->extra ?: [];
            unset($extra['refund_disagree_reason']);
            // 更新extra字段
            $order->update([
                'extra' => $extra,
            ]);
            // 调用退款逻辑
            $this->_refundOrder($order);
        }else{
            // 2.2 将拒绝退款理由存入orders表的 extra 字段中
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->reason;
            // 2.3 将订单退款状态改为 不同意退款（未退款）
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra' => $extra
            ]);
        }

        return $order;
    }

    // 执行退款操作
    public function _refundOrder($order)
    {
        // dd($order->payment_method);
        // 判断订单的支付方式
        switch ($order->payment_method){
            case 'wechat':
                // 留空，暂时做不了
                break;
            case 'alipay':
                // 1 生成退款单号
                $refundNo = Order::getAvailableRefundNo();
                // dd($refundNo);  // 82b55aa36b234863a401b774ac80ea0a      32位
                // 2 发起退款请求
                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no,   // 订单号
                    'refund_amount' => $order->total_amount, // 退款金额
                    'out_request_no' => $refundNo   // 退款单号
                ]);
                // 3 判断退款失败或成功     （根据支付宝文档，如果返回值里面有sub_code 字段说明退款失败）
                if($ret->sub_code){
                    return 11;
                    // 退款失败
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    // 将订单退款状态标记为退款失败
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra
                    ]);
                }else{
                    // 退款成功
                    $order->update([
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                        'refund_no' => $refundNo
                    ]);
                }
                break;
            default:
                // 原则上不可能出现，这个只是为了代码健壮性
                throw new InternalException('未知订单支付方法:'.$order->payment_method);
                break;
        }    
    }
}
