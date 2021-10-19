<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Urls;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

class ProfitsController extends Controller
{
    protected $webDriver;

    public function index()
    {
        return view('admin.csv');
    }

    public function putCsv(Request $request)
    {
        $output = $this->output('https://page.auctions.yahoo.co.jp/jp/auction/k1012044210');
        dd($output);
        $filename = 'scraping.csv';
        $data = Urls::all();

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
}
