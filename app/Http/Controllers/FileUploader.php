<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUploaderModel;
use App\Jobs\ProcessMailchimpSync;

class FileUploader extends Controller
{
    public function showUploadForm()
    {
        return view('upload');
    }

    public function handleFileUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->storeAs('uploads', 'uploaded_file.' . $file->getClientOriginalExtension());

        $file = storage_path('app/' . $path);

        $customerArr = $this->csvToArray($file);

        for ($i = 0; $i < count($customerArr); $i ++)
        {
            FileUploaderModel::firstOrCreate($customerArr[$i]);
        }

         return view('sendtoqueue');
       // return back()->with('success', 'File uploaded successfully.')->with('file', $path);
    }

    public function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename)){
            return false;
        }
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function startSyncView()
    {
         return view('sendtoqueue');
    }

    public function startSync()
    {
            ProcessMailchimpSync::dispatch();

            return back()->with('success', 'Sync started in the background!');
    }
}
