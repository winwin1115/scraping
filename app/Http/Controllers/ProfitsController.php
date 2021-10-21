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
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;

class ProfitsController extends Controller
{
    protected $webDriver;
    private $final_data = [];

    public function index()
    {
        return view('admin.csv');
    }

    public function putCsv(Request $request)
    {
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

        if($request->site_type != '0')
            $urls = Urls::where(['site_type' => $request->site_type])->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request->start_date)))->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request->end_date)))->get();
        else
            $urls = Urls::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request->start_date)))->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request->end_date)))->get();

        $csv_data = [];
        for($k = 0; $k < count($urls); $k++)
        {
            $csv_data = $this->makeCsvData($urls[$k]['site_url'], $currency_rate, $profit_rate);
        }

        // CSV Produce
        $filename = 'scraping.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "mult-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = array('Handle', 'Title', 'Body(HTMl)', 'Vendor', 'Type', 'Tags', 'Published', 'Option1 Name', 'Option1 Value', 'Option2 Name', 'Option2 Value', 'Option3 Name', 'Option3 Vlaue', 'Variant SKU', 'Variant Grams', 'Variant Inventory Tracker', 'Variant Inverntory Qty', 'Variant Inventory Policy', 'Variant Fullfillment Service', 'Variant Price', 'Variant Compare At Price', 'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode', 'Image Src', 'Image Position', 'Image Alt Text', 'Gift Card', 'SEO Title', 'SEO Description', 'Google Shopping/Google Product Category', 'Google Shopping/Gender', 'Google Shopping/Age Group', 'Giigle Shopping/MPN', 'Google Shopping/AdWords Grouping', 'Google Shpping/AdWords Labels', 'Google Shopping/Condition', 'Google Shopping/Custom Product', 'Google Sjopping/Custom Label0', 'Google Shopping/Custom Label1', 'Google Shopping/Custom Label2', 'Google Shopping/Custom Label3', 'Google Shopping/Custom Label4', 'Variant Image', 'Variant Weight Unit', 'Variant Tax Code', 'Cost per item', 'Status', 'Standard Product Type', 'Custom Product Type');

        $callback = function() use($csv_data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($csv_data as $item) {
                $row['Handle'] = $item['handle'];
                $row['Title'] = $item['title'];
                if($item['body'])
                    $row['Body'] = $item['body'];
                $row['vendor'] = $item['vendor'];
                $row['type'] = $item['type'];
                $row['tags'] = $item['tags'];
                $row['published'] = $item['published'];
                $row['option1_name'] = $item['option1_name'];
                $row['option1_value'] = $item['option1_value'];
                $row['option2_name'] = $item['option2_name'];
                $row['option2_value'] = $item['option2_value'];
                $row['option3_name'] = $item['option3_name'];
                $row['option3_value'] = $item['option3_value'];
                $row['variant_sku'] = $item['variant_sku'];
                $row['variant_grams'] = $item['variant_grams'];
                $row['variant_inventory_tracker'] = $item['variant_inventory_tracker'];
                $row['variant_qty'] = $item['variant_qty'];
                $row['variant_inventory_policy'] = $item['variant_inventory_policy'];
                $row['variant_fullfillment_service'] = $item['variant_fullfillment_service'];
                $row['variant_price'] = $item['variant_price'];
                $row['variant_compare_price'] = $item['variant_compare_price'];
                $row['variant_shipping'] = $item['variant_shipping'];
                $row['variant_texable'] = $item['variant_texable'];
                $row['variant_barcode'] = $item['variant_barcode'];
                $row['image_src'] = $item['image_src'];
                $row['image_alt'] = $item['image_alt'];
                $row['image_position'] = $item['image_position'];
                $row['gift_card'] = $item['gift_card'];
                $row['seo_title'] = $item['title'];
                $row['seo_description'] = $item['title'];
                $row['google_product_cateory'] = $item['google_product_cateory'];
                $row['gender'] = $item['gender'];
                $row['age_group'] = $item['age_group'];
                $row['mpn'] = $item['mpn'];
                $row['adwords_group'] = $item['adwords_group'];
                $row['adwords_label'] = $item['adwords_label'];
                $row['condition'] = $item['condition'];
                $row['custom_product'] = $item['custom_product'];
                $row['custom_label0'] = $item['custom_label0'];
                $row['custom_label1'] = $item['custom_label1'];
                $row['custom_label2'] = $item['custom_label2'];
                $row['custom_label3'] = $item['custom_label3'];
                $row['custom_label4'] = $item['custom_label4'];
                $row['variant_image'] = $item['variant_image'];
                $row['variant_weight_unit'] = $item['variant_weight_unit'];
                $row['variant_tax_code'] = $item['variant_tax_code'];
                $row['variant_cost_per_item'] = $item['variant_cost_per_item'];
                $row['status'] = $item['status'];
                $row['standard_product_type'] = $item['standard_product_type'];
                $row['custom_product_type'] = $item['custom_product_type'];

                fputcsv($file, array($row['Handle'], $row['Title'], $row['Body'], $row['vendor'], $row['type'], $row['tags'], $row['published'], $row['option1_name'], $row['option1_value'], $row['option2_name'], $row['option2_value'], $row['option3_name'], $row['option3_value'], $row['variant_sku'], $row['variant_grams'], $row['variant_inventory_tracker'], $row['variant_qty'], $row['variant_inventory_policy'], $row['variant_fullfillment_service'], $row['variant_price'], $row['variant_compare_price'], $row['variant_shipping'], $row['variant_texable'], $row['variant_barcode'], $row['image_src'], $row['image_position'], $row['image_alt'], $row['gift_card'], $row['seo_title'], $row['seo_description'], $row['google_product_cateory'], $row['gender'], $row['age_group'], $row['mpn'], $row['adwords_group'], $row['adwords_label'], $row['condition'], $row['custom_product'], $row['custom_label0'], $row['custom_label1'], $row['custom_label2'], $row['custom_label3'], $row['custom_label4'], $row['variant_image'], $row['variant_weight_unit'], $row['variant_tax_code'], $row['variant_cost_per_item'], $row['status'], $row['standard_product_type'], $row['custom_product_type']));
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

        $title = '';
        $title_temp = $pokemon_xpath->query('//div[@class="ProductTitle__title"]//h1[@class="ProductTitle__text"]');
        if(!is_null($title_temp))
        {
            foreach($title_temp as $item)
                $title = $item->nodeValue;
        }
        else
            $title = '';
        
        $data['title'] = $this->translateTitle($title);

        // $body_temp = $pokemon_xpath->query('//div[@class="ProductExplanation__commentBody js-disabledContextMenu"]//table[tr/td[1]/font/text() = "商品の詳細"]');
        // if(!is_null($body_temp))
        // {
        //     foreach($body_temp as $item)
        //         $data['body'] = $item->C14N();
        // }
        // else
            $data['body'] = '';

        $data['vendor'] = 'Eight kNot Japan Co., Ltd';
        $data['type'] = 'Personal Computers';
        $data['tags'] = 'Personal Computers and Peripherals';
        $data['published'] = 'TRUE';
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
        
        $data['variant_shipping'] = 'TRUE';
        $data['variant_texable'] = 'FALSE';

        $data['variant_barcode'] = '';
        $barcode = '';
        $barcode_temp = $pokemon_xpath->query('//div[@class="ProductExplanation__commentBody js-disabledContextMenu"]//table[last()]//tr[td/font/text() = "型番"]/td[2]/text()');
        if(!is_null($barcode_temp))
        {
            foreach($barcode_temp as $item)
                $barcode = $item->data;
        }
        else
            $barcode = '';
        $data['variant_barcode'] = trim($barcode);

        $image_src = '';
        $image_alt = '';
        $image_position = '';
        $image_src_temp = $pokemon_xpath->query('//div[@class="ProductImage__footer js-imageGallery-footer"]//div[@class="ProductImage__indicator js-imageGallery-indicator"]//ul[@class="ProductImage__thumbnails"]//li//a//img');

        if(!is_null($image_src_temp))
        {
            foreach($image_src_temp as $index => $item)
            {
                if($index == count($image_src_temp)-1)
                {
                    $image_src .= $item->getAttribute('src');
                    $image_alt .= $item->getAttribute('alt');
                    $image_position .= $index + 1;
                }
                else
                {
                    $image_src .= $item->getAttribute('src') . ', ';
                    $image_alt .= $item->getAttribute('alt') . ', ';
                    $image_position .= $index + 1 . ', ';
                }
            }
            $data['image_src'] = $image_src;
            $data['image_alt'] = $image_alt;
            $data['image_position'] = $image_position;
        }
        else
        {
            $data['image_src'] = '';
            $data['image_alt'] = '';   
            $data['image_position'] = '';
        }


        $data['gift_card'] = 'FALSE';
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

    public function makeCsvData($site_url, $currency_rate, $profit_rate)
    {
        $product = $this->output($site_url);

        $url_array = [];

        $pokemon_doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $pokemon_doc->loadHTML($product);
        libxml_clear_errors();

        $pokemon_xpath = new DOMXPath($pokemon_doc);
        $url_temp = $pokemon_xpath->query('//div[@id="mIn"]//div[@id="AS1m3"]//div[@id="list01"]//div[@class="inner cf"]//div[@class="bd cf"]//div[@class="a cf"]//h3//a');
        if(!is_null($url_temp))
        {
            foreach($url_temp as $item)
                array_push($url_array, $item->getAttribute('href'));
        }
        else
            return;
        
        for($i = 0; $i < count($url_array); $i++)
        {
            $output = $this->output($url_array[$i]);
            $result = $this->makeDoc($output, $url_array[$i], $currency_rate, $profit_rate);
            array_push($this->final_data, $result);
        }
        
        return $this->final_data;
    }

    public function translateTitle($title)
    {
        $output = $this->translateOutput($title);
        $eng_title = explode('"', $output)[1];
        return $eng_title;
    }

    public function translateOutput($title)
    {
        $host = "http://translate.google.com/translate_a/single?client=webapp&sl=auto&tl=en&hl=en&dt=at&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=gt&pc=1&otf=1&ssel=0&tsel=0&kc=1&tk=&q=".urlencode( $title );

        $curl = curl_init();
		
        curl_setopt_array($curl, array(
            CURLOPT_URL => $host,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: */*",
                "Accept-Encoding: gzip, deflate",
                "Cache-Control: no-cache",
                "Connection: keep-alive",
                "Cookie: NID=190=NY1ox5yIwHWgl-YC23LlJa8mn9_tWoiLRHJGpd8-RMEJsnh-jrF_cOvMEWqSSsR0J7WSrvhXF-_QqJpJ1s75Ymc76YSqXjS9NxXXnQKSDPmVySE0zNlzrVLQqK3IrmTa-et4Bu-8peiwE9jGnv4QFFjgGuxD5E0Mwbe0bzCvLiU",
                "Host: translate.google.com",
                "Postman-Token: b8b0ae52-b3c2-479e-9c4d-7e73e0540fb8,b70b881c-dcd6-4d23-a9f3-0bd7eeff91e6",
                "User-Agent: PostmanRuntime/7.19.0",
                "cache-control: no-cache"
            ),
        ));

		$output = utf8_decode(curl_exec($curl));
        $err = curl_error($curl);
		curl_close($curl);
        if($err)
            echo 'Curl Error #:' . $err;
        else
            return $output;
    }
}
