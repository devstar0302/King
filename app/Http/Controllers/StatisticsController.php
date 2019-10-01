<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Link;
use App\Models\Site;
use App\Models\SubSite;
use App\Models\Malfunction;

class StatisticsController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = auth()->user()->id;
        $user = DB::select('select roles.title from users left join roles on users.role_id=roles.id where users.id='.$user_id);
        $user_role = '';
        if(count($user) && isset($user[0]->title)) {
            $user_role = strtolower($user[0]->title);
        }

        $sites = DB::select('select sites.title, if(sub_sites.site_id is not null, true, false)has_subsites 
                                from sites 
                                    left join (select DISTINCT(site_id) from sub_sites) sub_sites on sites.id=sub_sites.site_id');

        $sites_subsites = DB::select('select sub_sites.title subsite, sites.title site   from  sub_sites left join sites on sub_sites.site_id=sites.id');

        if(!strcmp($user_role, 'contractor') || !strcmp($user_role, 'client')) {
            $links = Link::with('user', 'company', 'site', 'subSite')->where('user_id', '=', $user_id)->get();
            for($i = count($sites) - 1; $i >= 0; $i--) {
                $found = false;
                for($j = 0; $j < count($links); $j++) {
                    if(!strcmp($sites[$i]->title, $links[$j]->site['title'])) {
                        $found = true; break;
                    }
                }
                if(!$found) { unset($sites[$i]); }
            }

            for($i = count($sites_subsites) - 1; $i >= 0; $i--) {
                $found = false;
                for($j = 0; $j < count($links); $j++) {
                    if(!strcmp($sites_subsites[$i]->site, $links[$j]->site['title']) && !strcmp($sites_subsites[$i]->subsite, $links[$j]->subsite['title'])) {
                        $found = true; break;
                    }
                }
                if(!$found) { unset($sites_subsites[$i]); }
            }
        }

        $statistic_types = DB::select('select id, title, value from statistic_types');

        $categories = DB::select('select id, name from categories');
        $category_paragraphs = DB::select('select a.id category_id, a.name category_name, c.id paragraph_id, c.name paragraph_name
                                            from categories a 
                                                left join category_paragraph b on a.id=b.category_id 
                                                left join paragraphs c on b.paragraph_id=c.id where a.id is not null and c.id is not null');


        $default = (object)['title' => __('Statistics type'), 'value'=> __('Statistics type')];                                            
        $statistic_types = array_merge(array($default), $statistic_types);

        for($i = 0; $i < count($statistic_types); $i++) {
            $statistic_types[$i]->title = __($statistic_types[$i]->title);
            if(strpos(strtolower($statistic_types[$i]->value), 'category') !== false) {
                $category_items = array();
                for($j = 0; $j < count($categories); $j++) {
                    $category_id = $categories[$j]->id;
                    $paragraph_items = array();
                    for($k = 0; $k < count($category_paragraphs); $k++) {
                        if($category_id == $category_paragraphs[$k]->category_id) {
                            $paragraph_items[] = array('id' =>$category_paragraphs[$k]->paragraph_id, 'title' => __($category_paragraphs[$k]->paragraph_name), 'value' => $category_paragraphs[$k]->paragraph_name);
                        }
                    }

                    $category_items[] = array('id' => $categories[$j]->id, 'title' => __($categories[$j]->name), 'value' => $categories[$j]->name, 'subs' => $paragraph_items);
                }
                $statistic_types[$i]->id = 'undefined';
                $statistic_types[$i]->subs = $category_items;
            }
            else if( strpos(strtolower($statistic_types[$i]->value), 'malfunction') !== false) {
                $malfunctions = array();
                $malfunctions[] = array('id' => 1, 'title'=> __('STATIC_S'), 'value' => 'S');
                $malfunctions[] = array('id' => 2, 'title'=> __('STATIC_B'), 'value' => 'B');
                $malfunctions[] = array('id' => 3, 'title'=> __('STATIC_All'), 'value' => 'All');
                $malfunctions[] = array('id' => 4, 'title'=> __('STATIC_Repeating'), 'value' => 'Repeating');

                $statistic_types[$i]->id = 'undefined';
                $statistic_types[$i]->subs = $malfunctions;
            }
        }

        $this->breadcrumbs[] = array('url' => action('StatisticsController@index'), 'label'=> __('Statistics'));

        return view('statistics.index', compact('sites', 'sites_subsites', 'statistic_types'))->with(['breadcrumbs' => $this->breadcrumbs]);
    }

    public function getTotalScore(&$statistics, &$sites, &$malfunctions, $start_date, $end_date) {
        if($sites == null)
            return;
        $malfunctionsData = $malfunctions->pluck('data');

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctionsData); $j++) {
                $malfuncData = ["date" => $malfunctionsData[$j]['date'], "site" => $malfunctionsData[$j]['site'],
                    "subsite" => $malfunctionsData[$j]['subsite'], "calculate_total" => $malfunctionsData[$j]['calculate']['total']];

                $should_count = false;
                $date = $this->getFullDate($malfuncData["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfuncData['site'] == $site && $malfuncData['subsite'] == $subsite)
                            $should_count = true;
                    } else {
                        if($malfuncData['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count) {
                    $score = isset($malfuncData['calculate_total']) ? $malfuncData['calculate_total'] : 0;
                    $score = str_replace('%', '', $score);
                    $statistic[] = array('date' => $date, 'score' => $score);
                }
            }

            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function getGastronomyScore(&$statistics, &$sites, &$malfunctions, $start_date, $end_date) {
        if($sites == null)
            return;

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctions); $j++) {
                $malfunc = $malfunctions[$j];

                $should_count = false;
                $date = $this->getFullDate($malfunc->data["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfunc->data['site'] == $site && $malfunc->data['subsite'] == $subsite)
                            $should_count = true;
                    } else {
                        if($malfunc->data['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count) {
                    $score = isset($malfunc->data['gastronomy_score']) ? $malfunc->data['gastronomy_score'] : 0;
                    $score = str_replace('%', '', $score);
                    $statistic[] = array('date' => $date, 'score' => $score);
                }
            }
            
            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function getRiskScore(&$statistics, &$sites, &$malfunctions, $start_date, $end_date) {
        if($sites == null)
            return;

        $risk_values = array('' => 0, '---' => 0, 'LOW' => 1, 'MEDIUM' => 2, 'HIGH' => 3);

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctions); $j++) {
                $malfunc = $malfunctions[$j];

                $should_count = false;
                $date = $this->getFullDate($malfunc->data["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfunc->data['site'] == $site && $malfunc->data['subsite'] == $subsite)
                            $should_count = true;
                    } else {
                        if($malfunc->data['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count) {
                    $score_temp = strtoupper($malfunc->data['risk_level']);
                    $score = isset($score_temp) && $score_temp != -1 ? $risk_values[$score_temp] : 0;
                    if($score != 0) {
                        $statistic[] = array('date' => $date, 'score' => $score);
                    }
                }
            }
            
            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function getServiceScore(&$statistics, &$sites, &$malfunctions, $start_date, $end_date) {
        if($sites == null)
            return;

        $service_values = array('N/A' => 0, 'BAD' => 1, 'GOOD' => 2, 'VERY GOOD' => 3);

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctions); $j++) {
                $malfunc = $malfunctions[$j];

                $should_count = false;
                $date = $this->getFullDate($malfunc->data["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfunc->data['site'] == $site && $malfunc->data['subsite'] == $subsite)
                            $should_count = true;
                    } else {
                        if($malfunc->data['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count) {
                    $score_temp = strtoupper($malfunc->data['service_level']);
                    $score = isset($score_temp) ? $service_values[$score_temp] : 0;
                    $statistic[] = array('date' => $date, 'score' => $score);
                }
            }
            
            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function getCategoryScore(&$statistics, &$sites, &$malfunctions, $category, $start_date, $end_date) {
        if($sites == null)
            return;

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctions); $j++) {
                $malfunc = $malfunctions[$j];

                $should_count = false;
                $date = $this->getFullDate($malfunc->data["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfunc->data['site'] == $site && $malfunc->data['subsite'] == $subsite)
                            $should_count = true;
                    } else {
                        if($malfunc->data['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count) {
                    $score = isset($malfunc->data['calculate'][$category]['value']) ? $malfunc->data['calculate'][$category]['value'] : 0;
                    $score = str_replace('%', '', $score);
                    $statistic[] = array('date' => $date, 'score' => $score);
                }
            }
            
            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function getParagraphScore(&$statistics, &$sites, &$malfunctions, $category, $paragraph, $start_date, $end_date) {
        if($sites == null)
            return;

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctions); $j++) {
                $malfunc = $malfunctions[$j];

                $should_count = false;
                $date = $this->getFullDate($malfunc->data["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfunc->data['site'] == $site && $malfunc->data['subsite'] == $subsite)
                            $should_count = true;
                    } else {
                        if($malfunc->data['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count) {
                    $score = isset($malfunc->data['calculate'][$category][$paragraph]) ? $malfunc->data['calculate'][$category][$paragraph] : 0;
                    $score = str_replace('%', '', $score);
                    $statistic[] = array('date' => $date, 'score' => $score);
                }
            }
            
            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function getMalfunctionScore(&$statistics, &$sites, &$malfunctions, $start_date, $end_date, $type) {
        if($sites == null)
            return;

        $category_paragraphs = DB::select('select a.id category_id, a.name category_name, c.id paragraph_id, c.name paragraph_name
                                                from categories a 
                                                    left join category_paragraph b on a.id=b.category_id 
                                                    left join paragraphs c on b.paragraph_id=c.id where a.id is not null and c.id is not null');

        for($i = 0; $i < count($sites); $i++) {
            $is_subsite = $sites[$i]['subsite'] != '';
            $site = $sites[$i]['site'];
            $subsite = $sites[$i]['subsite'];
            $site_name = $is_subsite ? $site.'-'.$subsite : $site;

            $statistic = array();
            for($j = 0; $j <count($malfunctions); $j++) {
                $malfunc = $malfunctions[$j];

                $should_count = false;
                $date = $this->getFullDate($malfunc->data["date"]);
                if($date != NULL && strtotime($start_date) <= strtotime($date) && strtotime($date) <= strtotime($end_date)) {
                    if($is_subsite ) {
                        if($malfunc->data['site'] == $site && $malfunc->data['subsite'] == $subsite)
                            $should_count = true;
                    } 
                    else {
                        if($malfunc->data['site'] == $site)
                            $should_count = true;
                    }
                }

                if($should_count && isset($malfunc->data['malfunction_type'])) {
                    $score = 0;
                    $malfunc_types = $malfunc->data['malfunction_type'];

                    $malfunc_site = $malfunc->data['site'];
                    $malfunc_subsite = $malfunc->data['subsite'];
            
                    $lastMalfunctionType = [];
                    if( $type == 'Repeating' && (isset($malfunc_site) || isset($malfunc_subsite)) ) {
                        $prev_malfunctions = DB::select('select data from malfunctions where id < '.$malfunc->id.' order by id desc');

                        $malfunc_check_site = DB::select('select sites.title, if(sub_sites.site_id is not null, true, false)has_subsites 
                                                            from sites 
                                                                left join (select DISTINCT(site_id) from sub_sites) sub_sites on sites.id=sub_sites.site_id where sites.title="'.$malfunc_site.'"');
                        $has_subsites = $malfunc_check_site[0]->has_subsites;

                        foreach($prev_malfunctions as $prev_malfunc) {
                            $data = json_decode($prev_malfunc->data);
                            if(isset($malfunc_site) && isset($malfunc_subsite)) {
                                if(isset($data->site) && isset($data->subsite) && $data->site == $malfunc_site && $data->subsite == $malfunc_subsite) {
                                    $lastMalfunctionType = json_decode(json_encode($data->malfunction_type), true); break;
                                }
                            } else if(isset($malfunc_site) && !$has_subsites) {
                                if(isset($data->site) && !strcmp($data->site, $malfunc_site)) {
                                    $lastMalfunctionType = json_decode(json_encode($data->malfunction_type), true); break;
                                }
                            }
                        }
                    }
                    
                    for($k = 0; $k < count($category_paragraphs); $k++) {
                        $category_id = $category_paragraphs[$k]->category_id;
                        $paragraph_id = $category_paragraphs[$k]->paragraph_id;

                        if(isset($malfunc_types[$category_id]) && isset($malfunc_types[$category_id][$paragraph_id])) {
                            $malfunc_type = $malfunc_types[$category_id][$paragraph_id];
                            if($malfunc_type == 'S' || $malfunc_type == 'B') {
                                if( $type == 'S' && $malfunc_type == 'S' || $type == 'B' && $malfunc_type == 'B' || $type == 'All' ) {
                                    $score ++;
                                } else if( $type == 'Repeating' ) {
                                    if( isset($lastMalfunctionType[$category_id]) && isset($lastMalfunctionType[$category_id][$paragraph_id]) ) {
                                        $last_status = $lastMalfunctionType[$category_id][$paragraph_id];
                                        if($last_status == 'S' || $last_status == 'B') {
                                            $score ++;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $statistic[] = array('date' => $date, 'score' => $score);
                }
            }

            $statistics['data'][] = array('site' => $site_name, 'data' => $statistic);
        }
    }

    public function ajaxGetStatistics(Request $request) {
        $sites = $request->input('sites');
        $subsites = $request->input('subsites');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $statistic_type = $request->input('statistic_type');
        $category_id = $request->input('category_id');
        $paragraph_id = $request->input('paragraph_id');

        $malfunctions = Malfunction::all();

        $type = strtolower($statistic_type);

        $statistics = array();
        if($type == 'total score') {
            $this->getTotalScore($statistics, $sites, $malfunctions, $start_date, $end_date);
        }
        else if($type == 'gastronomy score') {
            $this->getGastronomyScore($statistics, $sites, $malfunctions, $start_date, $end_date);
        }
        else if($type == 'risk level') {
            $this->getRiskScore($statistics, $sites, $malfunctions, $start_date, $end_date);
        }
        else if($type == 'service level') {
            $this->getServiceScore($statistics, $sites, $malfunctions, $start_date, $end_date);
        }
        else if($type == 's' || $type == 'b' || $type == 'all' || $type == 'repeating') {
            $this->getMalfunctionScore($statistics, $sites, $malfunctions, $start_date, $end_date, $statistic_type);
        }
        else {
            if($category_id != -1 && $paragraph_id != -1) {
                $this->getParagraphScore($statistics, $sites, $malfunctions, $category_id, $paragraph_id, $start_date, $end_date);
            } else {
                $this->getCategoryScore($statistics, $sites, $malfunctions, $category_id, $start_date, $end_date);
            }
        }

        $statistics['type'] = $type;

        return json_encode($statistics);
    }

    public function ajaxSendPdf(Request $request) {
        return parent::ajaxSendPdf($request);
    }
}
