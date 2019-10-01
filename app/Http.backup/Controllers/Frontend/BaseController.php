<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\AttachmentEmail;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use App\Models\FileItem;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
  //delete file by id
  public function deleteFile( Request $request ){
    foreach (explode(',', $request->get('ids')) as $id) {
      FileItem::destroy((int)$id);
    }

    return "Success";
  }

  public function uploadFile( Request $request ){
    if($file = $request->file('Filedata'))
      FileItem::saveFile($file);
    return 1;
  }

  public function newAreaHtml( Request $request ){
    $sort = FileItem::getSortArray();
    $files = FileItem::with(['type'])
      ->leftJoin('file_types', 'files.type_id', '=', 'file_types.id')
      ->select('file_types.extension', 'files.*')
      ->orderBy($sort['column'], $sort['by'])
      ->get();

    $files = FileItem::setImages($files);

    return view('frontend._part._files', compact(['files']))->render();
  }

  public function newsort( Request $request ){
    Session::put('sort', $request->get('sorting'));

    return $this->newAreaHtml($request);
  }

  public function sendmail( Request $request ){
    $from_email = env('MAIL_FROM_ADDRESS', __('a@pampuni.com'));
    $to_email =  $request->get('mail');
    $fileIds = (array)$request->get('fileId');
    $files = FileItem::whereIn('id', $fileIds)->get();

    $data = [
      'from_email'  => $from_email,
      'to_mail'     => $to_email,
      'subject'     => $request->get('subjest'),
      'body'        => $request->get('message'),
      "locale"      => App::getLocale()
    ];

    foreach ($files as $file){
      $data['paths'][] = public_path('uploads/frontend/' .$file->filename);
    }

    Mail::send(new AttachmentEmail($data));

    return json_encode(['status' => __('ok')]);
  }
}
