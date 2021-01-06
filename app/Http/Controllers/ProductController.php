<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
//    public function __construct()
//    {
//        $this->product = new Product();
//        $this->customer = new Customer();
//    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $customer = $this->CustomerSave($request);

            $findCustomer  = Customer::find($customer->id);

            $this->ProductSave($request->product, $request, $findCustomer);
            DB::commit();
            return ['status'=>1,'message'=>'Success'];

        } catch (Exception $ex) {
            DB::rollBack();
            echo $ex->getMessage();
        }
    }

    /**
     * @param Request $request
     * @return Customer
     */
    protected function CustomerSave(Request $request): Customer
    {
//        dd($request->customer);
        $customer = new Customer();
        $customer['customer'] = $request->customer;
        $customer->save();
        return $customer;
    }

    /**
     * @param $products
     * @param Request $request
     * @param $findCustomer
     */
    protected function ProductSave($products, Request $request, $findCustomer): void
    {

        foreach ($products as $key => $value) {
            $product_info = new Product();
            $product_info['product'] = $value;
            $product_info['price'] = $request->price[$key];
            $product_info->customerTest()->associate($findCustomer);
            $product_info->save();
        }
    }

}
