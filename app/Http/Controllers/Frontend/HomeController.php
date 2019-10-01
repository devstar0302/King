<?php

/**

 * Created by PhpStorm.

 * User: Nikolay

 * Date: 15.10.2018

 * Time: 5:51

 */

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\DB;

use App\Models\FileItem;
use App\Models\FileType;

class HomeController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $title = 'Shared folder';
        $sort = FileItem::getSortArray();
        $files = FileItem::with(['type'])
                     ->leftJoin('file_types', 'files.type_id', '=', 'file_types.id')
                     ->select('file_types.extension', 'files.*')
                     ->orderBy($sort['column'], $sort['by'])
                     ->get();

        $files = FileItem::setImages($files);

        $user_id = auth()->user()->id;
        $user = DB::select('select roles.title from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user_role = '';
        if(count($user) && isset($user[0]->title)) {
            $user_role = strtolower($user[0]->title);
        }

        $this->breadcrumbs[] = array('url' => action('Frontend\HomeController@index'), 'label' => __('Signs&Forms'));

        return view('frontend.home', compact(['title', 'files', 'user_role']))->with(['breadcrumbs' => $this->breadcrumbs]);
    }

    public function printFile( $id )
    {
        $title = 'Print file';
        $file  = FileItem::with(['type'])->whereId($id)->first();

        return view('frontend.print', compact(['title', 'file']));
    }
}