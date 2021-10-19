<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Urls;
use App\Currencys;
use App\Profits;

use DOMDocument;
use DOMXPath;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;

class ProfitsController extends Controller
{
    protected $webDriver;

    public function index()
    {
        return view('admin.csv');
    }

    public function putCsv(Request $request)
    {
        // 
        $currencys = Currencys::first();
        $profits = Profits::first();

        if($currencys)
            $currency_rate = $currencys['currency_rate'];
        else
            $currency_rate = '';

        if($profits)
            $profit_rate = $profits['profit_rate'];
        else
            $profit_rate = '';

        //HTTP Client Url
        $httpClient = new Client();
        $client = new Client(HttpClient::create(array(
            'headers' => array(
                'user-agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0', // will be forced using 'Symfony BrowserKit' in executing
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.5',
                'Referer' => 'http://yourtarget.url/',
                'Upgrade-Insecure-Requests' => '1',
                'Save-Data' => 'on',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache',
            ),
        )));
        $client->setServerParameter('HTTP_USER_AGENT', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:73.0) Gecko/20100101 Firefox/73.0');
        $product_response = $client->request('GET', 'https://auctions.yahoo.co.jp/seller/rainbowjp1449?sid=rainbowjp1449&b=1&n=50&s1=cbids&o1=a&mode=1&p=%E3%83%8E%E3%83%BC%E3%83%88%E3%83%91%E3%82%BD%E3%82%B3%E3%83%B3&auccat=&aq=-1&oq=&anchor=1&slider=');

        $result = '';
        $data = [];
        $product_response->filter('#mIn #AS1m3 .inner.cf .bd.cf .a.cf h3 a')->each(function ($node) use ($currency_rate, $profit_rate, $httpClient, $result, $data) {
            $output = $this->output($node->attr('href'));
            $result = $this->makeDoc($output, $node->attr('href'), $currency_rate, $profit_rate);
            array_push($data, $result);
            dd($data);
        });

        // CSV Produce
        $filename = 'scraping.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "mult-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = array('Handle', 'Title', 'Body(HTMl)', 'Vendor', 'Tags', 'Published', 'Option1 Name', 'Option1 Value', 'Option2 Name', 'Option2 Value', 'Option3 Name', 'Option3 Vlaue', 'Variant SKU', 'Variant Vrams', 'Variant Inventory Tracker', 'Variant Inverntory Qty', 'Variant Inventory Policy', 'Variant Fullfillment Service', 'Variant Price', 'Variant Compare At Price', 'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode', 'Image Src', 'Image POosition', 'Image Alt Text', 'Gift Card', 'SEO Title', 'SEO Description', 'Google Shopping/Google Product Category', 'Google Shopping/Gender', 'Google Shopping/Age Group', 'Giigle Shopping/MPN', 'Google Shopping/AdWords Grouping', 'Google Shpping/AdWords Labels', 'Google Shopping/Condition', 'Google Shopping/Custom Product', 'Google Sjopping/Custom Label0', 'Google Shopping/Custom Label1', 'Google Shopping/Custom Label2', 'Google Shopping/Custom Label3', 'Google Shopping/Custom Label4', 'Variant Image', 'Variant Weight Unit', 'Variant Tax Code', 'Cost per item', 'Status', 'Standard Product Type', 'Custom Product Type');

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                $row['Handle'] = $item->site_type;
                $row['Title'] = $item->site_url;

                fputcsv($file, array($row['Handle'], $row['Title']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function output($url)
    {
        $ip = '127.0.0.1';

		$headers = array(
			'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
			'accept-language: en-US,en;q=0.9',
			'cache-control: no-cache',
			'pragma: no-cache',
			'sec-ch-ua: " Not;A Brand";v="99", "Google Chrome";v="91", "Chromium";v="91"',
			'sec-ch-ua-mobile: ?0',
			'sec-fetch-dest: document',
			'sec-fetch-mode: navigate',
			'sec-fetch-site: none',
			'sec-fetch-user: ?1',
			'upgrade-insecure-requests: 1',
			"CLIENT-IP: {$ip}",
			"X-FORWARDED-FOR: {$ip}"
		);

		$agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36';

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

    public function makeDoc($output, $href, $currency_rate, $profit_rate)
    {
        $data = [];

        $pokemon_doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $pokemon_doc->loadHTML($output);
        libxml_clear_errors();

        // $product_id = [];
        $product_name = [];
        $pokemon_xpath = new DOMXPath($pokemon_doc);
        $product_name_temp = $pokemon_xpath->query('//div[@id="yjBreadcrumbs"]//p//b');
        if(!is_null($product_name_temp))
        {
            foreach($product_name_temp as $item)
            {
                // array_push($product_id, $item->getAttribute('data-ylk'));
                array_push($product_name, $item->nodeValue);
            }
            $handle = '';
            for($i = 0; $i < 3; $i++)
            {
                if($i == 2)
                    $handle .= $product_name[$i];
                else
                    $handle .= $product_name[$i] . ' > ';
            }
            $data['handle'] = $handle;
        }
        else
            $data['handle'] = '';

        $title_temp = $pokemon_xpath->query('//div[@class="ProductTitle__title"]//h1[@class="ProductTitle__text"]');
        if(!is_null($title_temp))
        {
            foreach($title_temp as $item)
                $data['title'] = $item->nodeValue;
        }
        else
            $data['title'] = '';

        // $body_temp = $pokemon_xpath->query('//div[@class="ProductExplanation__commentBody"]//');

        $data['vendor'] = 'Eight kNot Japan Co., Ltd';
        $data['type'] = 'Personal Computers';
        $data['tags'] = 'Personal Computers and Peripherals';
        $data['published'] = true;
        $data['option1_name'] = 'Title';
        $data['option1_value'] = 'Default Title';
        $data['option2_name'] = '';
        $data['option2_value'] = '';
        $data['option3_name'] = '';
        $data['option3_value'] = '';
        $data['variant_sku'] = $href;
        $data['variant_grams'] = '2500';
        $data['variant_inventory_tracker'] = 'Eight kNot Japan Co., Ltd';
        $data['variant_qty'] = '1';
        $data['variant_inventory_policy'] = 'deny';
        $data['variant_fullfillment_service'] = 'manual';

        $price = '';
        $real_price = '';
        $price_array = [];
        $price_temp = $pokemon_xpath->query('//div[@class="Price Price--buynow"]//div[@class="Price__borderBox"]//dl[@class="Price__body "]//dd[@class="Price__value"]');
        if(!is_null($price_temp))
        {
            foreach($price_temp as $item)
                $price = $item->nodeValue;

            $price = explode('円', trim($price))[0];
            $price_array = explode(',', $price);
            for($j = 0; $j < count($price_array); $j++)
                $real_price .= $price_array[$j];
            $real_price = (float)$real_price;

            $data['variant_price'] = $real_price * $currency_rate * $profit_rate;
            $data['variant_compare_price'] = $real_price * $currency_rate;
        }
        else
        {
            $data['variant_price'] = '';
            $data['variant_compare_price'] = '';
        }
        
        $data['variant_shooping'] = true;
        $data['variant_texable'] = false;

        // $barcode_temp = $pokemon_xpath->query('//div[]');
        // $data['variant_barcode'] = $item->nodeValue;
        $data['variant_barcode'] = '';

        $image_src = '';
        $image_alt = '';
        $image_src_temp = $pokemon_xpath->query('//div[@class="ProductImage__footer"]//div[@class="ProductImage__indicator"]//ul[@class="ProductImage__thumbnails"]//li[@class="ProductImage__thumbnail"]//a//img');
        if(!is_null($image_src_temp))
        {
            foreach($image_src_temp as $index => $item)
            {
                if($index == count($image_src_temp)-1)
                {
                    $image_src .= $item->attr('src');
                    $image_alt .= $item->attr('alt');
                }
                else
                {
                    $image_src .= $item->attr('src') . ', ';
                    $image_alt .= $item->attr('alt') . ', ';
                }
            }
            $data['image_src'] = $image_src;
            $data['image_alt'] = $image_alt;
        }
        else
        {
            $data['image_src'] = '';
            $data['image_alt'] = '';   
        }

        $image_position = '';
        $image_position_temp = $pokemon_xpath->query('//div[@class="ProductImage__footer"]//div[@class="ProductImage__indicator"]//ul[@class="ProductImage__thumbnails"]//li[@class="ProductImage__thumbnail"]//a');
        if(!is_null($image_position_temp))
        {
            foreach($image_position_temp as $index => $item)
            {
                if($index == count($image_position_temp)-1)
                    $image_position .= $item->attr('data-rapid_p');
                else
                    $image_position .= $item->attr('data-rapid_p') . ', ';
            }
            $data['image_position'] = $image_position;
        }
        else
            $data['image_position'] = '';

        $data['gift_card'] = false;
        $data['google_product_cateory'] = '';
        $data['gender'] = '';
        $data['age_group'] = '';
        $data['mpn'] = '';
        $data['adwords_group'] = '';
        $data['adwords_label'] = '';
        $data['condition'] = '';
        $data['custom_product'] = '';
        $data['custom_label0'] = '';
        $data['custom_label1'] = '';
        $data['custom_label2'] = '';
        $data['custom_label3'] = '';
        $data['custom_label4'] = '';
        $data['variant_image'] = '';
        $data['variant_weight_unit'] = 'g';
        $data['variant_tax_code'] = '';
        $data['variant_cost_per_item'] = '';
        $data['status'] = 'active';
        $data['standard_product_type'] = '';
        $data['custom_product_type'] = 'Computer';

        return $data;
    }
}
