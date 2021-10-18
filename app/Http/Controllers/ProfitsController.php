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
    public function index()
    {
        return view('admin.csv');
    }

    public function putCsv(Request $request)
    {
        $driverPath = public_path('assets\uploads\chromedriver.exe');
        putenv("webdriver.chrome.driver=" . $driverPath);

        // chrome option
        $options = new ChromeOptions();
        $options->addArguments([
            'disable-infobars',
            '--headless',
            'window-size=1920,1600',
            '--no-sandbox'
        ]);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);
        
        $driver = ChromeDriver::start($capabilities);
        dd('ok');
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
}
