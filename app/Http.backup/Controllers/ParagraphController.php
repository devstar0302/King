<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\FRR;
use App\Models\Paragraph;
use App\Models\Category;

class ParagraphController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function saveTable($id, Request $request)
    {
        $new = $request->input('new');
        if ($new) {
            foreach ($new as $frr) {
                FRR::create([
                    'paragraph_id' => $id,
//                    'type' => $frr['type'],
                    'repair' => $frr['repair'],
                    'risk' => $frr['risk'],
                    'finding' => $frr['finding']
                ]);
            }
        }

        $delete = $request->input('delete');
        if ($delete) {
            foreach ($delete as $frr_id => $value) {
                FRR::find($frr_id)->delete();
            }
        }

        $edit = $request->input('edit');
        if ($edit) {
            foreach ($edit as $frr_id => $frr_data) {
                $frr = FRR::find($frr_id);
                $frr->finding = $frr_data['finding'];
                $frr->risk = $frr_data['risk'];
                $frr->repair = $frr_data['repair'];
//                $frr->type = $frr_data['type'];
                $frr->save();
            }
        }

        return redirect(action('ParagraphController@edit', $id))->with('status', __('Data saved'));
    }


    public function index()
    {
        $categories = Category::with('paragraphs')->get();
        $categoriesTotalScore = Category::select('score')->sum('score');
        $paragraphs = Paragraph::get();

        $this->breadcrumbs[] = array('url' => action('CategoryController@index'), 'label'=> __('Category management'));

        return view('paragraphs.index', compact('categories', 'categoriesTotalScore', 'paragraphs'))->with(['breadcrumbs' => $this->breadcrumbs]);

        // return redirect('/');
//        $paragraphs = Paragraph::get();
//        return view('paragraphs.index', compact('paragraphs'));
    }


    public function create()
    {
        return view('paragraphs.create');
    }

    public function store(Request $request)
    {
        $newData = $request->all();
        if (isset($newData['category_id']) && $newData['category_id'] != null) {
            $category_id = $newData['category_id'];
            unset($newData['category_id']);
        }
        $paragraph = Paragraph::create($newData);
        if (isset($newData['category_id']) && $newData['category_id'] != null) {
            $paragraph->categories()->attach($category_id);
        }
        flash('Paragraph created!')->success();
        return back();
    }

    public function edit(Paragraph $paragraph)
    {
        $breadcrumbs_url = url('/') . '?paragraph_id=' . $paragraph->id;

        $this->breadcrumbs[] = array('url' => action('CategoryController@index'), 'label'=> __('Category management'));
        $this->breadcrumbs[] = array('url' => action('ParagraphController@edit', $paragraph), 'label'=> __('FRR'));
        $paragraphNumber = \request()->number;

        return view('paragraphs.edit', compact('paragraph', 'breadcrumbs_url', 'paragraphNumber'))->with(['breadcrumbs' => $this->breadcrumbs]);
    }


    public function update(Request $request, Paragraph $paragraph)
    {
        $newData = $request->all();
        $paragraph->update($newData);
        flash('Paragraph updated!')->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Paragraph $paragraph
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paragraph = Paragraph::find($id);
        $paragraph->delete();
        flash('Paragraph deleted!')->error();
        return back();
    }
}
