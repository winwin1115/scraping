<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Currencys;
use App\Profits;
use Symfony\Component\HttpKernel\Profiler\Profile;

class CurrencysController extends Controller
{
    public function index()
    {
        $currencys = Currencys::first();
        $profits = Profits::first();

        if($currencys)
            $currency_rate = $currencys['currency_rate'];
        else
            $currency_rate = '';

        if($profits)
            $profits_rate = $profits['profit_rate'];
        else
            $profits_rate = '';
        return view('admin.currencys')->with(
            [
                'currency_rate' => $currency_rate,
                'profit_rate' => $profits_rate
            ]
        );
    }

    public function updateCurrency(Request $request)
    {
        $currency_count = Currencys::count();
        if($currency_count)
        {
            $currency = Currencys::where('id', '1')->update(['currency_rate' => $request->currency_rate]);
            if($currency)
                flash('換率更新が成功しました。')->success();
            else
                flash('換率更新が失敗しました。')->error();
        }
        else
        {
            $currency = new Currencys;
            $currency->currency_rate = $request->currency_rate;
            if($currency->save())
                flash('換率更新が成功しました。')->success();
            else
                flash('換率更新が失敗しました。')->error();
        }
        return redirect()->back();
    }

    public function updateProfit(Request $request)
    {
        $profit_count = Profits::count();
        if($profit_count)
        {
            $profit = Profits::where('id', '1')->update(['profit_rate' => $request->profit_rate]);
            if($profit)
                flash('利益率更新が成功しました。')->success();
            else
                flash('利益率更新が失敗しました。')->error();
        }
        else
        {
            $profit = new Profits;
            $profit->profit_rate = $request->profit_rate;
            if($profit->save())
                flash('利益率更新が成功しました。')->success();
            else
                flash('利益率更新が失敗しました。')->error();
        }

        return redirect()->back();
    }
}
