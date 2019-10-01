<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Category;
use App\Models\Paragraph;
use App\Models\FileItem;

use App\Mail\SendMailable;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('auth');
    }


    public function index()
    {
        $user_id = auth()->user()->id;
        $user = DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user_role = '';
        if(count($user) && isset($user[0]->title)) {
            $user_role = strtolower($user[0]->title);
        }

        return view('home', compact('user_role'));
    }

    public function dashboard()
    {
        $breadcrumbs_url = url('/') . '/dashboard';
        return view('dashboard', compact("breadcrumbs_url"));
    }

    public function isImage($name) {
        $name = explode('.', $name);
        $extention = $name[count($name) - 1];
        $extention = strtolower($extention);

        return in_array($extention, ["jpg", "jpeg", "bmp", "gif", "png"]);
    }
    
    public function storeFile(Request $request)
    {
        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        if(isImage($name)) {
            $extension = $file->getClientOriginalExtension();
            $name = time() . rand(100, 999) . '.' . $extension;
            $directory = public_path() . "/images";
            $file->move($directory, $name);
        } else {
            $directory = public_path() . "/uploads";
            $file->move($directory, $name);
        }
        return $name;
    }

    public function simpleStoreFile(Request $request){
        $files = [];
        $uploads = $request->file("uploads");
        if(is_array($uploads)) {
            foreach ($uploads as $file) {
                $name = $file->getClientOriginalName();
                if(isImage($name)) {
                    $extension = $file->getClientOriginalExtension();
                    $name = time() . rand(100, 999) . '.' . $extension;
                }
                $directory = public_path() . "/uploads";
                $file->move($directory, $name);
                $files[] = $name;
            }
        }
        return response()->json($files);
    }

    public function simpleStoreFileSF(Request $request){
        $uploads = $request->input("ids");

        $filenames = [];
        if(is_array($uploads)) {
            $sort = FileItem::getSortArray();
            $files = FileItem::with(['type'])
                         ->leftJoin('file_types', 'files.type_id', '=', 'file_types.id')
                         ->whereIn('files.id', $uploads)
                         ->select('file_types.extension', 'files.*')
                         ->orderBy($sort['column'], $sort['by'])
                         ->get();
 
            $files = FileItem::setImages($files);
    
            foreach ($files as $file) {
                $extension = $file->type->extension;
                if(empty($extension)) {
                    $temp_array = $split('\.', $file->filename);
                    if(count($temp_array) > 0) {
                        $extension = $temp_array[count($temp_array) - 1];
                    }
                }

                $filename = $file->filename;
                if(isImage($filename)) {
                    $filename = time() . rand(100, 999) . '.' . $extension;
                }

                $source_path = public_path()."/uploads/frontend/".$file->filename;
                $destination_path = public_path() . "/uploads/".$filename;
                copy($source_path, $destination_path);

                $filenames[] = $filename;
            }
        }

        return response()->json($filenames);
    }
    
    public function artisan()
    {
        dd(\Artisan::call('migrate:refresh --seed'));
    }

    public function mail($id)
    {
        $user = User::find($id);

        $name = [
            'from_email' => env('MAIL_FROM_ADDRESS', __('a@pampuni.com')),
            'title' => __('Recovery Password Email'),
            'login' => $user->email,
            'login_url' => env('APP_URL'),
            'password' => $user->orig,
            "locale" => App::getLocale()
        ];

        Mail::to($user->email)->send(new SendMailable($name));

//        return redirect()->back()->with('status', 'Email was sent');
        return response()->json(['status' => __('ok')]);
    }

}
