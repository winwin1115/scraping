<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Urls;

class ProfitsController extends Controller
{
    public function index()
    {
        return view('admin.csv');
    }

    public function putCsv(Request $request)
    {
        $filename = 'scraping.csv';
        $data = Urls::all();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "mult-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );
        $columns = array('Title', 'Assign');
        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach($data as $item) {
                $row['Title'] = $item->site_type;
                $row['Assign'] = $item->site_url;

                fputcsv($file, array($row['Title'], $row['Assign']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
