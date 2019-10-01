<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;

use Carbon\Carbon;
use function MongoDB\BSON\toJSON;

use App\Models\Company;
use App\Models\FRR;
use App\Models\Link;
use App\Models\Risk;
use App\Models\Site;
use App\Models\SubSite;
use App\Models\Category;
use App\Models\Paragraph;
use App\Models\Malfunction;
use App\Models\Guidance;
use App\Models\User;
use App\Models\FileItem;

use App\Mail\ReportMailable;

class MalfunctionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $employee =  $request->input('employee');
        $site =  $request->input('site');
        $subsite =  $request->input('subsite');
        $from = $request->input('from');
        $to = $request->input('to');

        $malfunctions = Malfunction::all();
        $guidances = Guidance::all();

        if ($request->input('from') && $request->input('to')) {
            $from = $request->input('from');
            $to = $request->input('to');
            $date_range = $from.' - '.$to;

            $malfunctions = $malfunctions->reject(function($malfunction) use ($from, $to) {
                $date = isset($malfunction->data['date']) ? $this->getFullDate($malfunction->data['date']) : '';
                return empty($date) || strtotime($from) -strtotime($date) > 0 || strtotime($date) - strtotime($to) > 0;
            });

            $guidances = $guidances->reject(function($guidance) use ($from, $to) {
                $date = isset($guidance->data['date']) ? $this->getFullDate($guidance->data['date']) : '';
                return empty($date) || strtotime($from) -strtotime($date) > 0 || strtotime($date) - strtotime($to) > 0;
            });
        }

        $user_id = auth()->user()->id;
        $user = DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user_role = '';
        if(count($user) && isset($user[0]->title)) {
            $user_role = strtolower($user[0]->title);
        }

        $links = Link::with('user', 'company', 'site', 'subSite')->where('user_id', '=', $user_id)->get();

        $malfunctions = $malfunctions->reject(function($malfunction_model) use($links, $user_role, $employee, $site, $subsite) {
            $malfunc = $malfunction_model->data;
            $should_remove = false;
            if($user_role == 'contractor' || $user_role == 'client') {
                $found = true;
                foreach($links as $key => $link) {
                    if(isset($link->site['title']) && isset($link->subsite['title'])) {
                        $found = isset($malfunc['site']) && isset($malfunc['subsite']) && $malfunc['site'] == $link->site['title'] && $malfunc['subsite'] == $link->subsite['title'];
                    }
                    else if(isset($link->site['title']) && !isset($link->subsite['title'])) {
                        $found = isset($malfunc['site']) && $malfunc['site'] == $link->site['title'];
                    }
                    if($found) break;
                }
                $should_remove = !$found;
            }
            if(!$should_remove && $employee != '') {
                $should_remove = !isset($malfunc['employee_name']) || strpos(strtolower($malfunc['employee_name']), strtolower($employee)) === false;
            }
            if(!$should_remove && $site != '') {
                $should_remove = !isset($malfunc['site']) || strpos(strtolower($malfunc['site']), strtolower($site)) === false;
            }
            if(!$should_remove && $subsite != '') {
                $should_remove = !isset($malfunc['subsite']) || strpos(strtolower($malfunc['subsite']), strtolower($subsite)) === false;
            }

            return $should_remove;
        });

        $guidances = $guidances->reject(function($guidance_model) use($links, $user_role, $employee, $site, $subsite) {
            $guidance = $guidance_model->data;

            $should_remove = false;
            if($user_role == 'contractor' || $user_role == 'client') {
                $found = true;
                foreach($links as $key => $link) {
                    if(isset($link->site['title']) && isset($link->subsite['title'])) {
                        $found = isset($guidance['site']) && isset($guidance['subsite']) && $guidance['site'] == $link->site['title'] && $guidance['subsite'] == $link->subsite['title'];
                    }
                    else if(isset($link->site['title']) && !isset($link->subsite['title'])) {
                        $found = isset($guidance['site']) && $guidance['site'] == $link->site['title'];
                    }
                    if($found) break;
                }
                $should_remove = !$found;
            }
            if(!$should_remove && $employee != '') {
                $should_remove = !isset($guidance['employee_name']) || strpos(strtolower($guidance['employee_name']), strtolower($employee)) === false;
            }
            if(!$should_remove && $site != '') {
                $should_remove = !isset($guidance['site']) || strpos(strtolower($guidance['site']), strtolower($site)) === false;
            }
            if(!$should_remove && $subsite != '') {
                $should_remove = !isset($guidance['subsite']) || strpos(strtolower($guidance['subsite']), strtolower($subsite)) === false;
            }

            return $should_remove;
        });

        $this->breadcrumbs[] = array('url' => action('MalfunctionController@index'), 'label' => __('Form list'));

        $view_data = compact('malfunctions', 'guidances', 'employee', 'site', 'subsite', 'from', 'to');
        $view_with_data = ['breadcrumbs' => $this->breadcrumbs, 'user' => json_encode($user[0])];

        return view('malfunctions.index', $view_data)->with($view_with_data);
    }

    public function create()
    {
        $malfunction = Malfunction::create([]);
        return redirect(action('MalfunctionController@edit', $malfunction->id));
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $malfunction_id = $id;
        $malfunction = Malfunction::findOrFail($id);
        $date = $this->getFullDate($malfunction->data["date"]);
        $paragraphs_total = [];
        $categories_total = 0.00;
        $fork = Category::where('fork', '=', 1)->first();
        $fork = isset($fork) ? $fork->id : 0;

        if(isset($malfunction->data['categories'])) {
            foreach ($malfunction->data['categories'] as $category) {
                $paragraphs_total[$category['id']] = 0.00;
                if (isset($malfunction->data['calculate'][$category['id']]['value'])) {
                    $categories_total += (float)$malfunction->data['calculate'][$category['id']]['value'];
                }
                if(isset($malfunction->data['paragraphs']) && isset($malfunction->data['paragraphs'][$category['id']])) {
                    foreach ($malfunction->data['paragraphs'][$category['id']] as $paragraph) {
                        if (isset($paragraphs_total[$category['id']]) && isset($malfunction->data['calculate'][$category['id']][$paragraph['id']])) {
                            $paragraphs_total[$category['id']] += (float)$malfunction->data['calculate'][$category['id']][$paragraph['id']];
                        }
                    }
                }
            }
        }

        $user_id = auth()->user()->id;
        $user = DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user = json_encode($user[0]);

        $data = $malfunction->data;
        $data['status']['users'][$user_id]['last_visit_date'] = date("Y-m-d H:i:s");

        $total = (float)$data['calculate']['total'];

        if ($fork != 0 && in_array($fork, array_keys($data['calculate']))) {
            $total = $total - (float)$data['calculate'][$fork]['value'];
            $data['gastronomy_score'] = $data['calculate'][$fork]['value'];

            $data['calculate']['total'] = ($total + (float)$data['gastronomy_score']) ."%";
        }

        $malfunction->save();
        $data['calculate']['total'] = $total .'%';
        $malfunction->data = json_encode($data);

        $nameCode = $malfunction->nameCode;

        $this->breadcrumbs[] = array('url' => action('MalfunctionController@index'), 'label' => __('Form list'));
        $this->breadcrumbs[] = array('url' => action('MalfunctionController@show', $id), 'label' => '#'.$nameCode.__('-view'));

        $view_data = compact('malfunction_id', 'malfunction', 'paragraphs_total', 'categories_total', 'user', 'date');
        $view_with_data = ['breadcrumbs' => $this->breadcrumbs];

        return view('malfunctions.show', $view_data)->with($view_with_data);
    }

    public function get($id)
    {
        $malfunction_model = Malfunction::findOrFail($id);
        if ($malfunction_model) {
            $malfunction = $malfunction_model->data;
        } else {
            $malfunction = [];
        }

        dd($malfunction);
    }

    public function edit($id)
    {
        $categories = Category::with('paragraphs')->get();
        $companies = Company::getAllCompanies();
        $fork = Category::where('fork', '=', 1)->first();
        $fork = isset($fork) ? $fork->id : 0;
        $sites = Site::getAllSites();
        $subsites = SubSite::getAllSubSites();
        $malfunction_model = Malfunction::findOrFail($id);

        $nameCode = $malfunction_model->nameCode;
        $malfunction = $malfunction_model->data;
        $malfunction_id = $id;
        $date = $this->getFullDate($malfunction["date"]);
        $risk = Risk::query()->where('malfunction_id', '=', $id)->first();

        $user_id = auth()->user()->id;
        $user = DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user = json_encode($user[0]);

        $site = $malfunction['site'];
        $subsite = $malfunction['subsite'];

        $lastMalfunctionType = []; $lastMalfunctionFinding = [];
        if(isset($site) || isset($subsite)) {
            $malfunctions = DB::select('select data from malfunctions where id < '.$id.' order by id desc');
            $check_site = DB::select('select sites.title, if(sub_sites.site_id is not null, true, false)has_subsites 
                                    from sites 
                                        left join (select DISTINCT(site_id) from sub_sites) sub_sites on sites.id=sub_sites.site_id where sites.title="'.$site.'"');
            $has_subsites = true;
            if(!empty($check_site)) {
                $has_subsites = $check_site[0]->has_subsites;
            }

            foreach($malfunctions as $malfunc) {
                $data = json_decode($malfunc->data);
                if(isset($site) && isset($subsite)) {
                    if(isset($data->site) && isset($data->subsite) && $data->site == $site && $data->subsite == $subsite) {
                        $lastMalfunctionType = isset($data->malfunction_type) ? $data->malfunction_type : [];
                        $lastMalfunctionFinding = isset($data->malfunction_finding) ? $data->malfunction_finding : [];
                        break;
                    }
                } else if(isset($site) && !$has_subsites) {
                    if(isset($data->site) && $data->site == $site) {
                        $lastMalfunctionType = isset($data->malfunction_type) ? $data->malfunction_type : [];
                        $lastMalfunctionFinding = isset($data->malfunction_finding) ? $data->malfunction_finding : [];
                        break;
                    }
                }                
            }
        }

        $total = (float)$malfunction['calculate']['total'];

        if ($fork != 0 && in_array($fork, array_keys($malfunction['calculate'] ?? []))) {
            $malfunction['calculate']['total'] = ($total - (float)$malfunction['calculate'][$fork]['value']).'%';
            $malfunction['gastronomy_score'] = $malfunction['calculate'][$fork]['value'];
        }

        $sort = FileItem::getSortArray();
        $files = FileItem::with(['type'])
                ->leftJoin('file_types', 'files.type_id', '=', 'file_types.id')
                ->select('file_types.extension', 'files.*')
                ->orderBy($sort['column'], $sort['by'])
                ->get();

        $files = FileItem::setImages($files);

        $this->breadcrumbs[] = array('url' => action('MalfunctionController@index'), 'label' => __('Form list'));
        $this->breadcrumbs[] = array('url' => action('MalfunctionController@edit', $id), 'label' => '#'.$nameCode.__('-edit'));

        $view_data = compact('categories', 'malfunction', 'malfunction_id', 'nameCode', 'companies', 'sites', 'subsites', 'fork', 'risk', 'date', 'user', 'files');
        $view_with_data = [
            'breadcrumbs'               => $this->breadcrumbs,
            'lastMalfunctionType'       => json_encode($lastMalfunctionType),
            'lastMalfunctionFinding'    => json_encode($lastMalfunctionFinding)
        ];

        return view('malfunctions.form', $view_data)->with($view_with_data);
    }

    public function filterCompany(Request $request)
    {
        $sites = Site::query()->where('company_id', '=', $request->id)->get();
        return response()->json([
            'sites' => $sites,
        ]);

    }

    public function filterSite(Request $request)
    {
        $subsites = SubSite::query()->where('site_id', '=', $request->id)->get();
        $repres = Site::query()->where('id', '=', $request->id)->get();
        $link_id = Link::where('site_id', '=', $request->id)->get();
        $link_id = $link_id ? $link_id->toArray() : [];
        $user_id = count($link_id) ? $link_id[0]['user_id'] : "";
        $employe = DB::select("SELECT `id`, `name` FROM `users` WHERE id='$user_id'");
        $check_site = DB::select('select sites.title, if(sub_sites.site_id is not null, true, false)has_subsites 
                            from sites 
                                left join (select DISTINCT(site_id) from sub_sites) sub_sites on sites.id=sub_sites.site_id where sites.title="'.$repres[0]->title.'"');

        $lastMalfunctionType = []; $lastMalfunctionFinding = [];
        if(isset($request->malfunction_id) && $check_site[0]->has_subsites) {
            $malfunctions = DB::select('select data from malfunctions where id < '.$request->malfunction_id.' order by id desc');
            foreach($malfunctions as $malfunc) {
                $data = json_decode($malfunc->data);
                if(isset($data->site) && $data->site == $repres[0]->title) {
                    $lastMalfunctionType = isset($data->malfunction_type) ? $data->malfunction_type : [];
                    $lastMalfunctionFinding = isset($data->malfunction_finding) ? $data->malfunction_finding : [];
                    break;
                }
            }
        }

        $result = [
            'subsites' => $subsites, 'employe' => $employe, 'repres' => $repres,
            'lastMalfunctionType' => json_encode($lastMalfunctionType),
            'lastMalfunctionFinding' => json_encode($lastMalfunctionFinding)
        ];
        return response()->json($result);
    }

    public function filterSubsite(Request $request)
    {
        $subsite = SubSite::query()->find($request->id);
        
        $lastMalfunctionType = []; $lastMalfunctionFinding = [];
        if(isset($request->malfunction_id)) {
            $site = Site::query()->find($request->site_id);
            $malfunctions = DB::select('select data from malfunctions where id < '.$request->malfunction_id.' order by id desc');
            foreach($malfunctions as $malfunc) {
                $data = json_decode($malfunc->data);
                if(isset($data->malfunction_type) && isset($data->site) && $data->site == $site->title && isset($data->subsite) && $data->subsite == $subsite->title) {
                    $lastMalfunctionType = isset($data->malfunction_type) ? $data->malfunction_type : [];
                    $lastMalfunctionFinding = isset($data->malfunction_finding) ? $data->malfunction_finding : [];
                }
            }
        }

        $result = [
            'repres' => $subsite->representative,
            'lastMalfunctionType' => json_encode($lastMalfunctionType),
            'lastMalfunctionFinding' => json_encode($lastMalfunctionFinding)
        ];
        return response()->json($result);
    }

    public function find(Request $request)
    {
        $frr = FRR::query()->where('paragraph_id', '=', $request->paragraph_id)->first();
        $paragraph = Paragraph::find($request->paragraph_id);
        $success = false;
        $risk = '';
        $repair = '';
        if ($paragraph->type == 'severe') {
            $success = true;
        }
        if ($request->find == $frr->finding) {
                $risk = $frr->risk;
                $repair = $frr->repair;
        }
        $id = $frr->id;

        $result = [
            'success' => $success,
            'id' => $id,
            'risk' => $risk,
            'repair' => $repair
        ];

        return response()->json($result);
    }

    public function level(Request $request)
    {
        $risk = Risk::query()->where('malfunction_id', '=', $request->id)->first();

        if ($risk) {
            $risk->malfunction_id = $request->id;
            $risk->level = $request->level;
        } else {
            Risk::query()->create([
                'malfunction_id' => $request->id,
                'level' => $request->level
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $fork = Category::where('fork', '=', 1)->first();
        $fork = isset($fork) ? $fork->id : 0;
        $request_data = $request->data;

        $stage = $request_data['status']['stage'];
        $admin_changed_date = $request_data['status']['admin_changed_date'];
        unset($request_data['status']);
        unset($request_data['status']['admin_changed_date']);

        $malfunction = Malfunction::find($id);
        $data = $malfunction->data;

        if (isset($request_data['calculate'][$fork]['value'])) {
            $request_data['calculate']['total'] = ((float)$request_data['calculate']['total'] + (float)$request_data['calculate'][$fork]['value']) .'%';
        }

        $data = isset($data) && $data > 0 ? array_merge($data, $request_data) : $request_data;

        $data['status']['stage'] = $stage;
        if($admin_changed_date == 'changed') {
            $data['status']['admin_changed_date'] = date("Y-m-d H:i:s");
        }

        if(isset($data['photo'])) {
            $photo = $data['photo'];
            foreach($photo as $category_id => $category) {
                foreach($category as $paragraph_id => $paragraph) {
                    foreach($paragraph as $key => $file) {
                        logger('Nested foreach loop');
                        if(empty($file)) unset($photo[$category_id][$paragraph_id][$key]);
                    }
                }
            }
            $data['photo'] = $photo;
        }

        if(isset($data['malfunction-uploads'])) {
            $uploads = $data['malfunction-uploads'];
            foreach($uploads as $key => $file) {
                if(empty($file)) unset($uploads[$key]);
            }
            $data['malfunction-uploads'] = $uploads;
            logger('Uploads');
        }

        $malfunction->data = json_encode($data);
        $malfunction->save();

        $risk_level = isset($request_data['risk_level']) ? $request_data['risk_level'] : '';
        $risk = Risk::firstOrCreate(array('malfunction_id' => $id, 'level' => $risk_level));
        $risk->save();
    }

    public function destroy($id)
    {
        $malfunction = Malfunction::find($id);
        $malfunction->delete();
        return redirect(action('MalfunctionController@index'))->with('status', __('Successfully deleted!'));
    }

    public function ajaxDuplicateMalfunction(Request $request) {
        $malfunction_id = $request->input('malfunction_id');

        $malfunction = Malfunction::find($malfunction_id);
        $newMalfunction = $malfunction->replicate();
        $data = $newMalfunction->data;
        $data['employee_name'] = auth()->user()->name;
        $data['admin_name'] = '';
        $newMalfunction->data = json_encode($data);
        $newMalfunction->save();

        $risk = Risk::where('malfunction_id', '=', $malfunction_id)->first();
        if(isset($risk)) {
            $newRisk = $risk->replicate();
            $newRisk->malfunction_id = $newMalfunction->id;
            $newRisk->save();
        }

        $result = [
            'status' => __('ok'),
            'malfunction_id' => $newMalfunction->id
        ];

        return json_encode($result);
    }

    public function ajaxSendPdf(Request $request) {
        return parent::ajaxSendPdf($request);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createGuidance()
    {
        $guidance = Guidance::create([]);
        return redirect(action('MalfunctionController@editGuidance', $guidance->id));
    }

    public function showGuidance($id)
    {
        $user_id = auth()->user()->id;
        $user = DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user = json_encode($user[0]);

        $guidance_id = $id;
        $guidance = Guidance::findOrFail($id);
        
        $data = $guidance->data;
        $data['status']['users'][$user_id]['last_visit_date'] = date("Y-m-d H:i:s");
        $guidance->data = json_encode($data);
        $guidance->save();

        $nameCode = $guidance->nameCode;
        
        $this->breadcrumbs[] = array('url' => action('MalfunctionController@index'), 'label' => __('Form list'));
        $this->breadcrumbs[] = array('url' => action('MalfunctionController@showGuidance', $id), 'label' => '#'.$nameCode.__('-Guidance-view'));

        $view_data = compact('guidance_id', 'guidance', 'user');
        $view_with_data = [
            'breadcrumbs' => $this->breadcrumbs
        ];

        return view('malfunctions.show-guidance', $view_data)->with($view_with_data);
    }

    public function editGuidance($id)
    {
        $sites = Site::getAllSites();
        $subsites = SubSite::getAllSubSites();

        $guidance = [];
        $nameCode = '';
        $guidance_id = $id;

        $guidance_model = Guidance::findOrFail($id);
        if ($guidance_model) {
            $guidance = $guidance_model->data;
            $nameCode = $guidance_model->nameCode;
        }
        
        $user_id = auth()->user()->id;
        $user = DB::select('select users.id, roles.title, `name` from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user = json_encode($user[0]);

        $sort = FileItem::getSortArray();
        $files = FileItem::with(['type'])
                ->leftJoin('file_types', 'files.type_id', '=', 'file_types.id')
                ->select('file_types.extension', 'files.*')
                ->orderBy($sort['column'], $sort['by'])
                ->get();

        $files = FileItem::setImages($files);

        $this->breadcrumbs[] = array('url' => action('MalfunctionController@index'), 'label' => __('Form list'));
        $this->breadcrumbs[] = array('url' => action('MalfunctionController@editGuidance', $id), 'label' => '#'.$nameCode.__('-Guidance-edit'));

        $view_data = compact('guidance', 'guidance_id', 'nameCode', 'sites', 'subsites', 'user', 'files');
        $view_with_data = ['breadcrumbs' => $this->breadcrumbs];

        return view('malfunctions.form-guidance', $view_data)->with($view_with_data);
    }

    public function updateGuidance($id, Request $request)
    {
        $request_data = $request->data;
        $stage = $request_data['status']['stage'];
        $admin_changed_date = $request_data['status']['admin_changed_date'];
        unset($request_data['status']);
        unset($request_data['status']['admin_changed_date']);

        $guidance = Guidance::find($id);
        $data = $guidance->data;
        $data = isset($data) ? array_merge($data, $request_data) : $request_data;
        $data['status']['stage'] = $stage;
        if($admin_changed_date == 'changed') {
            $data['status']['admin_changed_date'] = date("Y-m-d H:i:s");
        }

        if(isset($data['photo'])) {
            $photo = $data['photo'];
            foreach($photo as $paragraph_id => $paragraph) {
                foreach($paragraph as $key => $file) {
                    if(empty($file)) unset($photo[$paragraph_id][$key]);
                }
            }
            $data['photo'] = $photo;
        }

        if(isset($data['guidance-uploads'])) {
            $uploads = $data['guidance-uploads'];
            foreach($uploads as $key => $file) {
                if(empty($file)) unset($uploads[$key]);
            }
            $data['guidance-uploads'] = $uploads;
        }

        $guidance->data = json_encode($data);
        $guidance->save();
    }

    public function destroyGuidance($id)
    {
        $guidance = Guidance::find($id);
        $guidance->delete();
        return redirect(action('MalfunctionController@index'))->with('status', __('Successfully deleted!'));
    }
    
    public function ajaxDuplicateGuidance(Request $request) {
        $guidance_id = $request->input('guidance_id');

        $guidance = Guidance::find($guidance_id);
        $newGuidance = $guidance->replicate();
        $data = $newGuidance->data;
        $data['employee_name'] = auth()->user()->name;
        $data['admin_name'] = '';
        $newGuidance->data = json_encode($data);
        $newGuidance->save();

        $result = [
            'status' => __('ok'),
            'guidance_id' => $newGuidance->id
        ];

        return json_encode($result);
    }

    public function ajaxMalfunctionSaveComments($id, Request $request) {
        $comments = (array)$request->data['comments'];
        $key = key($comments);

        $user_id = auth()->user()->id;
        $date = date("Y-m-d H:i:s");

        $malfunction = Malfunction::find($id);
        $data = $malfunction->data;
        $data['comments'][$key] = $comments[$key];
        $data['status']['last_comment_date'] = $date;
        $data['status']['users'][$user_id]['last_visit_date'] = $date;
        $malfunction->data = json_encode($data);
        $malfunction->save();

        return json_encode(['status' => __('ok')]);
    }

    public function ajaxGuidanceSaveComments($id, Request $request) {
        $comments = (array)$request->data['comments'];
        $key = key($comments);

        $user_id = auth()->user()->id;
        $date = date("Y-m-d H:i:s");

        $guidance = Guidance::find($id);
        $data = $guidance->data;
        $data['comments'][$key] = $comments[$key];
        $data['status']['last_comment_date'] = $date;
        $data['status']['users'][$user_id]['last_visit_date'] = $date;
        $guidance->data = json_encode($data);
        $guidance->save();

        return json_encode(['status' => __('ok')]);
    }

    public function ajaxChangeStatus(Request $request) {
        $id = $request->input('id');
        $type =  $request->input('type');
        $status =  $request->input('status');
        $admin_name = $request->input('admin_name');

        if ($type == 'malfunction') {
            $malfunction = Malfunction::find($id);
            $data = $malfunction->data;
            $data = array_merge($data, ['admin_name' => $admin_name]);
            $data['status']['stage'] = $status;
            $malfunction->data = json_encode($data);
            $malfunction->save();
        } else {
            $guidance = Guidance::find($id);
            $data = $guidance->data;
            $data = array_merge($data, ['admin_name' => $admin_name]);
            $data['status']['stage'] = $status;
            $guidance->data = json_encode($data);
            $guidance->save();
        }

        $site = Site::where('title', '=', $data['site'])->first();
        $subSite = SubSite::where('title', '=', $data['subsite'])->first();

        if ($status == 'publish' && !empty($site) && !empty($subSite)) {
            $from_address = env('MAIL_FROM_ADDRESS', __('a@pampuni.com'));
            $subject = __('New report been published');
            $login_url = 'https://lac2.net/public/login';

            $userIds = Link::where('site_id', '=', $site->id)
                ->where('sub_site_id', '=', $subSite->id)
                ->pluck('user_id');

            $roleUsers = User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.title', 'Contractor')
                ->orWhere('roles.title', 'Client')
                ->selectRaw('users.id as user_id, users.name as user_name, users.email as email')
                ->get();

            $matches = $roleUsers->filter(function (User $user) use ($userIds) {
                return $userIds->contains($user->user_id);
            });

            foreach($matches as $key => $user) {
                $data = [
                    'subject'       => $subject,
                    'from_address'  => $from_address,
                    'to_address'    => $user->email,
                    'login_url'     => $login_url,
                    "locale"        => App::getLocale()
                ];

                Mail::to($user->email)->send(new ReportMailable($data));
            }
        }

        return json_encode(['status' => __('ok')]);
    }
}
