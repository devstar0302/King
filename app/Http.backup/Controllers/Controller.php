<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $breadcrumbs = array();

    protected function getFullDate($date_string) {
        $date_pieces = explode('-', $date_string);
        if(count($date_pieces) != 3) 
            return NULL;
        return date('Y-m-d', strtotime((2000 + (int)$date_pieces[2]).'-'.$date_pieces[1].'-'.$date_pieces[0]));
    }

    public function ajaxSendPdf(Request $request) {
        $from_address = env('MAIL_FROM_ADDRESS', __('a@pampuni.com'));
        $to_address = $request->input('to');
        $subject = $request->input('subject');
        $body = $request->input('body');
 
        $data = [
            'to_email' => $to_address,
            'from_email' => $from_address,
            'body' => $body,
            "locale" => App::getLocale()
        ];
    
        $uploadfiles = array();
        $uploaddir = './uploads/';

        $status = true;
        if(isset($_FILES['pdf'])) {
            $pdf = $_FILES['pdf']['tmp_name'];
            $uploadfile = $uploaddir . 'statistics_chart_'.time();
            $status &= move_uploaded_file($pdf, $uploadfile);
            if($status) {
                $uploadfiles[] = $uploadfile;
            }
        }
        if(isset($_FILES['other_pdf'])) {
            $other_pdf = $_FILES['other_pdf']['tmp_name'];
            $uploadfile = $uploaddir . 'מבדק';
            $status &= move_uploaded_file($other_pdf, $uploadfile);
            if($status) {
                $uploadfiles[] = $uploadfile;
            }
        }
        if(isset($_FILES['scoring_pdf'])) {
            $scoring_pdf = $_FILES['scoring_pdf']['tmp_name'];
            $uploadfile = $uploaddir . 'ציונים';
            $status &= move_uploaded_file($scoring_pdf, $uploadfile);
            if($status) {
                $uploadfiles[] = $uploadfile;
            }
        }
        if(isset($_FILES['guidance_pdf'])) {
            $scoring_pdf = $_FILES['guidance_pdf']['tmp_name'];
            $uploadfile = $uploaddir . 'guidance_'.time();
            $status &= move_uploaded_file($scoring_pdf, $uploadfile);
            if($status) {
                $uploadfiles[] = $uploadfile;
            }
        }

        if ( $status ) {
            Mail::send('emails.sendpdf', $data, function($message) use ($data, $uploadfiles, $subject) {
                $message->from($data['from_email']);
                $message->to($data['to_email']);
                $message->subject($subject);
                foreach($uploadfiles as $file) {
                    $message->attach($file, ['mime' => 'application/pdf']);
                }
            });
        }

        return json_encode(['status' => $status]);
    }
}
