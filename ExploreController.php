<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\User;
use App\Libraries\Custom;

class ExploreController extends Controller
{
    protected $custom;

    protected $site_name;
    
    public function __construct()
    {
//        $this->middleware('login')->except('how_to');
    	$this->custom = new Custom();

        $this->site_name = env('SITE_NAME');
    }
    
    public function ask(Request $request)
    {
        $custom = $this->custom;
        $comment = $request->comment;
        $attachment = $request->attachment;
//        $user_id = \Session::get('uid');
        $user_id = 1;
        $time = $custom->time_now();
        
        
        $file_path = '';

        $this->validate($request, [
            'comment' => 'required',
            'attachment' => 'image'
        ]);

        // process file
        if (!empty($attachment))
        {
            $file_response = $this->upload($request);
            if ($file_response->status == 0)
            {
                \Session::flash('flash_message', $file_response->details);
                return redirect()->back();
            }
            $file_path = $file_response->file_path;
        }

        $data_question = [
            'user_id' => $user_id, 'file_url' => $file_path, 'created_at' => $time
        ];
        $question_ins = DB::table('questions')
            ->insert($data_question);

        \Session::flash('flash_message_success', 'Your question has been published');
        return redirect()->back();
    }


    public function upload(Request $request)
    {
        $custom = $this->custom;
        $file_recipients = $request->file('attachment');
        $user_id = \Session::get('uid');
        $time = $custom->time_now();
        $output = 0;
        $file_id = 0;
        $mime_arr = ['image/jpeg','image/png'];

        // var_dump($file_recipients->getSize());exit;

        if ($file_recipients)
        {
            // handle file size
            $size = $file_recipients->getSize();
            if ($size > 5000000)
            {
                $resp = [
                    'status' => 0,
                    'details' => 'File too large. File should not exceed 5MB'
                ];
                return (object) $resp;
            }

            $mime = $file_recipients->getClientMimeType();
            // var_dump($mime);exit;
            if (!in_array($mime, $mime_arr))
            {
                $resp = [
                    'status' => 0,
                    'details' => 'Only images are allowed(png/jpeg)'
                ];
                return (object) $resp;
            }

            $path = '/uploads/';
            // $asset_path = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'blogger-files'.DIRECTORY_SEPARATOR;
            // $destination_path = $this->live_server.DIRECTORY_SEPARATOR.'blogger-files'.DIRECTORY_SEPARATOR;


            $asset_path = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'blogger-files'.DIRECTORY_SEPARATOR;
            $destination_path = $this->live_server.DIRECTORY_SEPARATOR.'blogger-files'.DIRECTORY_SEPARATOR;
            // $destination_path = $asset_path;
            // $destination_path = public_path().$path;
            $filename = $custom->hashh($file_recipients->getClientOriginalName(), $time).'.'.$file_recipients->getClientOriginalExtension();
            $file_path = $destination_path.$filename;
            // getClientOriginalExtension()
            // $file_path = $path.$filename;

            $upload_success = $file_recipients->move($asset_path, $filename);
            // var_dump($upload_success);exit;

            // if file was successfully uploaded
            if ($upload_success)
            {
                $resp = [
                    'status' => 1,
                    'file_path' => $file_path
                ];
                return (object) $resp;
            }
            else
            {
                $resp = [
                    'status' => 0,
                    'details' => 'File not uploaded, try again'
                ];
                return (object) $resp;
            }
        }
        else
        {
            $resp = [
                'status' => 0,
                'details' => 'File upload error'
            ];
            return (object) $resp;
        }
    }

}
