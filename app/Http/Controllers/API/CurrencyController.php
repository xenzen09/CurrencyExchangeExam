<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::select('name','code')->get();
        return response([
            'data' => CurrencyResource::collection($currencies)
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        return response($currency->rate, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        //
        $api_key = '2362d2985cb49cfef300d470353af95d';
        $url = 'http://data.fixer.io/api/latest?access_key='.$api_key;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL,$url);
        $result=curl_exec($curl);
        curl_close($curl);
        $data = json_decode($result, true);

        $date = $data['date'];

        $currency  = array();

        foreach ($data['rates'] as $code => $value) {
            $currency[$code] = [
                'code' => $code,
                'rate' => $value,
                'date' => $date,
            ];
        }

        $url2 = 'http://data.fixer.io/api/symbols?access_key='.$api_key;
        $curl2 = curl_init();
        curl_setopt($curl2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl2, CURLOPT_URL,$url2);
        $result2=curl_exec($curl2);
        curl_close($curl2);
        $data2 = json_decode($result2, true);

        foreach ($data2['symbols'] as $code => $name) {
            $currency[$code]['name'] = $name;
        }

        foreach ($currency as $code => $curr) {
            $c =  Currency::firstOrCreate($curr);
            // extract($curr);
            // $c->code = $code;
            // $c->name = $name;
            // $c->rate = $rate;
            // $c->date = $date;
            // $c->save();
        }

        print_r("Done!");
    }
}
