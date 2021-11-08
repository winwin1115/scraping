<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DOMDocument;
use DOMXPath;

class AutoFunController extends Controller
{
    public function index()
    {
        return view('admin.auto');
    }

    public function createProduct(Request $request)
    {

    }

    public function deleteProduct(Request $request)
    {
        // username and password for API
        $username = "d64689e91e479d726827b3730118355f";
        $password = "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";
        $nextPage = NULL;
        $curl = curl_init();
        // set result limit and Basic auth

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => "https://d64689e91e479d726827b3730118355f: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@japan-upc-wholesale.myshopify.com/admin/api/2021-10/products.json?fields=id,variants&limit=250",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
            )
        );

        // call back function to parse Headers and get Next Page Link
        curl_setopt(
            $curl,
            CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$nextPage) {
                $len = strlen($header);
                $header = explode(':', $header, 2);

                if (count($header) < 2) // ignore invalid headers
                return $len;

                if (trim($header[0]) === "Link" && strpos($header[1], 'next') !== false) {
                    $links = explode(',', $header[1], 2);

                    $link = count($links) === 2 ? $links[1] : $links[0];
                    if (preg_match('/<(.*?)>/', $link, $match) === 1) $nextPage = $match[1];
                }

                return $len;
            }
        );

        // First request

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            print_r($error_msg);
        }
        $parsedResponse = json_decode($response);
        
        $result = $parsedResponse->products;

        // generate new requests till next page is available

        while ($nextPage !== NULL) {
            $nextPage = str_replace('https://', 'https://' . $username . ':' . $password . '@', $nextPage);
            curl_setopt($curl, CURLOPT_URL, $nextPage);

            $parsedResponse->products = [];
            $nextPage = NULL;
            
            $response = curl_exec($curl);
            $parsedResponse = json_decode($response);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
            } else {
                $result = array_merge($result, $parsedResponse->products);
                sleep(2);
            }
        };
        curl_close($curl);
        
        for($i = 0; $i < count($result); $i++)
        {
            $variants = $result[$i]->variants;

            $product = $this->output($variants[0]->sku);

            $pokemon_doc = new DOMDocument;
            libxml_use_internal_errors(true);
            $pokemon_doc->loadHTML($product);
            libxml_clear_errors();

            $pokemon_xpath = new DOMXPath($pokemon_doc);
            $url_temp = $pokemon_xpath->query('//div[@class="l-left"]//ul[@class="ProductDetail__items ProductDetail__items--primary"]//li[@class="ProductDetail__item"]//dl//dd[@class="ProductDetail__description"]/text()');

            if(!is_null($url_temp))
            {
                foreach($url_temp as $item)
                    // $amount = $item->nodeValue;
                    dd($item->nodeValue);
                if(!$amount)
                    $this->removeProduct($result[$i]->id);
            }
            else
            {
                dd('else');
            }
            dd('exit');
        }
    }

    public function output($url)
    {
        $ip = '127.0.0.1';

		$headers = array(
			'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
			'accept-language: en-US,en;q=0.9',
			'cache-control: no-cache',
			'pragma: no-cache',
			'sec-ch-ua: " Not;A Brand";v="99", "Google Chrome";v="94", "Chromium";v="94"',
			'sec-ch-ua-mobile: ?0',
			'sec-fetch-dest: document',
			'sec-fetch-mode: navigate',
			'sec-fetch-site: none',
			'sec-fetch-user: ?1',
			'upgrade-insecure-requests: 1',
			"CLIENT-IP: {$ip}",
			"X-FORWARDED-FOR: {$ip}"
		);

		$agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.81 Safari/537.36';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
        return $output;
    }

    public function removeProduct($id)
    {
        dd('success');
    }
}