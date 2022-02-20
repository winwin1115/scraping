<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Currencys;
use App\Profits;
use App\Asin;

use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Log;

class AsinController extends Controller
{
    private $final_data = [];
    private $custom_title = '';
    private $tran_count = 0;

    public function index()
    {
        $asin_info = Asin::all();
        return view('admin.asins')->with(['asin_info' => $asin_info]);
    }

    public function addAsins(Request $request)
    {
        $asin_info = $request->asin_info;
        for ($i = 0; $i < count($asin_info); $i++)
        {
            if($asin_info[$i])
            {
                $import_name = 'インポート' . floor($i / 50);
                $asins = new Asin;
                $asins->asin = $asin_info[$i];
                $asins->import_name = $import_name;
                $asins->save();
            }
        }
        return response()->json(['status' => '200']);
    }

    public function deleteAsin(Request $request)
    {
        $asin = Asin::findOrFail($request->asin_id);
        $asin->delete();
        return response()->json(['status' => '200']);
    }

    public function viewCsvData()
    {
        $date = Carbon::now();
        $import_name = Asin::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($date)))
            ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($date)))
            ->selectRaw('MAX(import_name) as import_name')
            ->groupBy('import_name')
            ->get();
        return view('admin.amazon-csv')->with(['import_name' => $import_name]);
    }

    public function getimportName(Request $request)
    {
        
        $import_name = Asin::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request->date)))
            ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request->date)))
            ->selectRaw('MAX(import_name) as import_name')
            ->groupBy('import_name')
            ->get();
        return response()->json(['import_name' => $import_name]);
    }

    public function putAsinCsv(Request $request)
    {
        $currencys = Currencys::first();
        $profits = Profits::first();

        if($currencys)
            $currency_rate =  1 / $currencys['currency_rate'];
        else
            $currency_rate = '';

        if($profits)
            $profit_rate = $profits['profit_rate'];
        else
            $profit_rate = '';

        $insert_date = $request->insert_date;
        $import_name = $request->import_name;
        $asin_info = Asin::where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($request->insert_date)))
            ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($request->insert_date)))
            ->where(['import_name' => $request->import_name])
            ->get();
        for($i = 0; $i < count($asin_info); $i++)
            $csv_data = $this->makeCsvData($asin_info[$i]['asin'], $currency_rate, $profit_rate);

        // CSV Produce
        $filename = 'scraping.csv';

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "mult-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = array('Handle', 'Title', 'Body (HTML)', 'Vendor', 'Standard Product Type', 'Custom Product Type', 'Tags', 'Published', 'Option1 Name', 'Option1 Value', 'Option2 Name', 'Option2 Value', 'Option3 Name', 'Option3 Value', 'Variant SKU', 'Variant Grams', 'Variant Inventory Tracker', 'Variant Inventory Qty', 'Variant Inventory Policy', 'Variant Fulfillment Service', 'Variant Price', 'Variant Compare At Price', 'Variant Requires Shipping', 'Variant Taxable', 'Variant Barcode', 'Image Src', 'Image Position', 'Image Alt Text', 'Gift Card', 'SEO Title', 'SEO Description', 'Google Shopping / Google Product Category', 'Google Shopping / Gender', 'Google Shopping / Age Group', 'Google Shopping / MPN', 'Google Shopping / AdWords Grouping', 'Google Shopping / AdWords Labels', 'Google Shopping / Condition', 'Google Shopping / Custom Product', 'Google Shopping / Custom Label 0', 'Google Shopping / Custom Label 1', 'Google Shopping / Custom Label 2', 'Google Shopping / Custom Label 3', 'Google Shopping / Custom Label 4', 'Variant Image', 'Variant Weight Unit', 'Variant Tax Code', 'Cost per item', 'Status');

        $callback = function() use($csv_data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($csv_data as $item) {
                for($k = 0; $k < count($item['image_src']); $k++)
                {
                    if($k == 0)
                    {
                        $row['Handle'] = $item['handle'];
                        $row['Title'] = $item['title'];
                        $row['Body'] = $item['body'];
                        $row['vendor'] = $item['vendor'];
                        $row['type'] = $item['type'][0];
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
                        $row['image_src'] = $item['image_src'][0];
                        $row['image_position'] = $item['image_position'][0];
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

                        fputcsv($file, array($row['Handle'], $row['Title'], $row['Body'], $row['vendor'], $row['standard_product_type'], $row['custom_product_type'], $row['tags'], $row['published'], $row['option1_name'], $row['option1_value'], $row['option2_name'], $row['option2_value'], $row['option3_name'], $row['option3_value'], $row['variant_sku'], $row['variant_grams'], $row['variant_inventory_tracker'], $row['variant_qty'], $row['variant_inventory_policy'], $row['variant_fullfillment_service'], $row['variant_price'], $row['variant_compare_price'], $row['variant_shipping'], $row['variant_texable'], $row['variant_barcode'], $row['image_src'], $row['image_position'], '', $row['gift_card'], $row['seo_title'], $row['seo_description'], $row['google_product_cateory'], $row['gender'], $row['age_group'], $row['mpn'], $row['adwords_group'], $row['adwords_label'], $row['condition'], $row['custom_product'], $row['custom_label0'], $row['custom_label1'], $row['custom_label2'], $row['custom_label3'], $row['custom_label4'], $row['variant_image'], $row['variant_weight_unit'], $row['variant_tax_code'], $row['variant_cost_per_item'], $row['status']));
                    }
                    else
                    {
                        $row['Handle'] = $item['handle'];
                        $row['Title'] = '';
                        $row['Body'] = '';
                        $row['vendor'] = '';
                        $row['type'] = '';
                        $row['tags'] = '';
                        $row['published'] = '';
                        $row['option1_name'] = '';
                        $row['option1_value'] = '';
                        $row['option2_name'] = '';
                        $row['option2_value'] = '';
                        $row['option3_name'] = '';
                        $row['option3_value'] = '';
                        $row['variant_sku'] = '';
                        $row['variant_grams'] = '';
                        $row['variant_inventory_tracker'] = '';
                        $row['variant_qty'] = '';
                        $row['variant_inventory_policy'] = '';
                        $row['variant_fullfillment_service'] = '';
                        $row['variant_price'] = '';
                        $row['variant_compare_price'] = '';
                        $row['variant_shipping'] = '';
                        $row['variant_texable'] = '';
                        $row['variant_barcode'] = '';
                        $row['image_src'] = $item['image_src'][$k];
                        $row['image_position'] = $item['image_position'][$k];
                        $row['gift_card'] = '';
                        $row['seo_title'] = '';
                        $row['seo_description'] = '';
                        $row['google_product_cateory'] = '';
                        $row['gender'] = '';
                        $row['age_group'] = '';
                        $row['mpn'] = '';
                        $row['adwords_group'] = '';
                        $row['adwords_label'] = '';
                        $row['condition'] = '';
                        $row['custom_product'] = '';
                        $row['custom_label0'] = '';
                        $row['custom_label1'] = '';
                        $row['custom_label2'] = '';
                        $row['custom_label3'] = '';
                        $row['custom_label4'] = '';
                        $row['variant_image'] = '';
                        $row['variant_weight_unit'] = '';
                        $row['variant_tax_code'] = '';
                        $row['variant_cost_per_item'] = '';
                        $row['status'] = '';
                        $row['standard_product_type'] = '';
                        $row['custom_product_type'] = '';

                        fputcsv($file, array($row['Handle'], $row['Title'], $row['Body'], $row['vendor'], $row['standard_product_type'], $row['custom_product_type'], $row['tags'], $row['published'], $row['option1_name'], $row['option1_value'], $row['option2_name'], $row['option2_value'], $row['option3_name'], $row['option3_value'], $row['variant_sku'], $row['variant_grams'], $row['variant_inventory_tracker'], $row['variant_qty'], $row['variant_inventory_policy'], $row['variant_fullfillment_service'], $row['variant_price'], $row['variant_compare_price'], $row['variant_shipping'], $row['variant_texable'], $row['variant_barcode'], $row['image_src'], $row['image_position'], '', $row['gift_card'], $row['seo_title'], $row['seo_description'], $row['google_product_cateory'], $row['gender'], $row['age_group'], $row['mpn'], $row['adwords_group'], $row['adwords_label'], $row['condition'], $row['custom_product'], $row['custom_label0'], $row['custom_label1'], $row['custom_label2'], $row['custom_label3'], $row['custom_label4'], $row['variant_image'], $row['variant_weight_unit'], $row['variant_tax_code'], $row['variant_cost_per_item'], $row['status']));
                    }
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function makeCsvData($asin, $currency_rate, $profit_rate)
    {
        $url = 'https://www.amazon.co.jp/dp/' . $asin;
        $output = $this->output($url);
        $result = $this->makeDoc($output, $asin, $url, $currency_rate, $profit_rate);
        array_push($this->final_data, $result);
        
        return $this->final_data;
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

    public function makeDoc($output, $asin, $href, $currency_rate, $profit_rate)
    {
        $data = [];

        $pokemon_doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $pokemon_doc->loadHTML($output);
        libxml_clear_errors();

        $pokemon_xpath = new DOMXPath($pokemon_doc);

        $title = '';
        $title_temp = $pokemon_xpath->query('//div[@id="titleSection"]//span[@id="productTitle"]/text()');
        if(!is_null($title_temp))
        {
            foreach($title_temp as $item)
                $title = trim($item->nodeValue);
        }
        else
            $title = '';
        if($this->tran_count == 100)
        {
            sleep(60);
            $this->tran_count = 0;
        }
        $data['title'] = $this->translateTitle($title);
        $this->tran_count++;
        if($this->custom_title == $data['title'])
        {
            $data['title'] .= '1';
        }

        $data['handle'] = strtolower(str_replace(' ', '-', $data['title']));

        $body_array = [];
        $body_text = '';
        $tran_word = '';
        $body_temp = $pokemon_xpath->query('//div[@id="productDetails_feature_div"]//table[@id="productDetails_techSpec_section_1"]');
        if(count($body_temp))
        {
            foreach($body_temp as $item)
                $data['body'] = $item->C14N();
        }
        else
            $data['body'] = '';

        $data['vendor'] = 'Eight kNot Japan Co., Ltd';

        $data['type'] = '';
        $data['tags'] = '';
        $type_array = [];
        $type_temp = $pokemon_xpath->query('//div[@id="nav-subnav"]//a//span[@class="nav-a-content"]/text()');
        if(!is_null($type_temp))
        {
            foreach($type_temp as $item)
                array_push($type_array, $item->nodeValue);
            $data['type'] = trim(preg_replace("/\r|\n/", "", $type_array[0]));
            $data['tags'] = trim(preg_replace("/\r|\n/", "", $type_array[0]));
        }
        $data['published'] = 'TRUE';
        $data['option1_name'] = 'Title';
        $data['option1_value'] = 'Default Title';
        $data['option2_name'] = '';
        $data['option2_value'] = '';
        $data['option3_name'] = '';
        $data['option3_value'] = '';
        $data['variant_sku'] = $href;

        $grams_temp = [];
        $grams_temp = $pokemon_xpath->query('//div[@class="a-expander-content a-expander-section-content a-section-expander-inner"]//table//tr[th[@class="a-color-secondary a-size-base prodDetSectionEntry"]/text() = " Product Dimensions "]/td/text()');
        if(count($grams_temp))
        {
            foreach($grams_temp as $item)
                $grams_array = explode(';', $item->nodeValue)[1];
            if($grams_array)
                $data['variant_grams'] = trim(explode('g', $grams_array)[0]);
        }
        else
            $data['variant_grams'] = '0';

        $data['variant_inventory_tracker'] = 'shopify';
        $data['variant_qty'] = '3';
        $data['variant_inventory_policy'] = 'deny';
        $data['variant_fullfillment_service'] = 'manual';

        $price = 0;
        $price_temp = [];
        $price_va_array = [];
        $data['variant_price'] = 0;
        $data['variant_compare_price'] = 0;
        $price1_temp = $pokemon_xpath->query('//div[@id="corePrice_feature_div"]//div[@class="a-section a-spacing-micro"]//span[@class="a-offscreen"]/text()');
        $price2_temp = $pokemon_xpath->query('//span[@class="a-size-mini olpWrapper"]');
        $price3_temp = $pokemon_xpath->query('//div[@id="olp_feature_div"]//span[@class="a-size-base a-color-price"]/text()');
        $no_price_temp = $pokemon_xpath->query('//div[@class="a-section a-spacing-small a-text-center"]//span[@class="a-color-price a-text-bold"]/text()');
        if(count($price1_temp))
        {
            $price_value = '';
            foreach($price1_temp as $item)
                $price_value = $item->nodeValue;
            if($price_value)
            {
                $price = explode("¥", $price_value)[1];
                $price = trim(str_replace(',', '', $price));
                $data['variant_price'] = (float)$price * $currency_rate * $profit_rate;
                $data['variant_compare_price'] = (float)$price * $currency_rate * 1.1;
            }
        }
        else if(count($price2_temp))
        {
            foreach($price2_temp as $item)
                array_push($price_va_array, $item->nodeValue);
            if(count($price_va_array))
            {
                $price = explode("¥", $price_va_array[0])[1];
                $price = trim(str_replace(',', '', $price));
                $data['variant_price'] = (float)$price * $currency_rate * $profit_rate;
                $data['variant_compare_price'] = (float)$price * $currency_rate * 1.1;
            }
        }
        else if(count($price3_temp))
        {
            $price_value = '';
            foreach($price3_temp as $item)
                $price_value = $item->nodeValue;
            if($price_value)
            {
                var_dump($price_value);
                $price = explode("¥", $price_value)[1];
                $price = trim(str_replace(',', '', $price));
                $data['variant_price'] = (float)$price * $currency_rate * $profit_rate;
                $data['variant_compare_price'] = (float)$price * $currency_rate * 1.1;
            }
        }
        else if(count($no_price_temp))
        {
            $price_value = '';
            foreach($no_price_temp as $item)
            {
                if($item->nodeValue == 'Currently unavailable.')
                {
                    $data['variant_price'] = 0;
                    $data['variant_compare_price'] = 0;
                }
            }
            
        }
        else
        {
            Log::error($output);
            $data['variant_price'] = 0;
            $data['variant_compare_price'] = 0;
        }
        
        $data['variant_shipping'] = 'TRUE';
        $data['variant_texable'] = 'FALSE';
        $data['variant_barcode'] = '';
        $data['image_src'] = [];
        $data['image_position'] = [];
        // $image_src_temp = $pokemon_xpath->query('//div[@id="altImages"]//ul//li[@class="a-spacing-small item"]//img');
        $image_src_temp = $pokemon_xpath->query('//div[@class="imgTagWrapper"]//img');
        if(!is_null($image_src_temp))
        {
            foreach($image_src_temp as $index => $item)
            {
                array_push($data['image_src'], $item->getAttribute('src'));
                array_push($data['image_position'], $index + 1);
            }
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
        $data['variant_cost_per_item'] = (float)$price * $currency_rate;
        $data['status'] = 'active';
        $data['standard_product_type'] = '';
        $data['custom_product_type'] = 'Computer';

        $this->custom_title = $data['title'];

        return $data;
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
