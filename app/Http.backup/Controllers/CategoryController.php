<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Paragraph;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
    }

    public function saveTable(Request $request)
    {
        $result = [
            'new_paragraphs' => 0,
            'new_categories' => 0,
            'categories_edited' => 0,
            'categories_deleted' => 0,
            'paragraphs_edited' => 0,
            'paragraphs_deleted' => 0
        ];
        $input_categories = $request->input('category');

        $fork = $request->fork;

        $created_categories = [];

        if (isset($fork)) {
            $categories = Category::query()->get();

            foreach ($categories as $category) {
                $category->fork = 0;
                $category->save();
            }
            $cat = Category::where('id', '=', $fork)->first();
            $cat->fork = 1;
            $cat->save();

        }

        if (isset($input_categories['new'])) {
            foreach ($input_categories['new'] as $key => $category) {
                $created_categories[$key] = Category::create([
                    'name' => $category['name'] ? $category['name'] : ' ',
                    'score' => $category['score'] ? $category['score'] : 0
                ]);
                $result['new_categories']++;
            }
        }
        if (isset($input_categories['delete'])) {
            foreach ($input_categories['delete'] as $category_id => $value) {
                Category::find($category_id)->delete();
                $result['categories_deleted']++;
            }
        }

        if (isset($input_categories['edit'])) {
            foreach ($input_categories['edit'] as $category_id => $data) {
                $category = Category::find($category_id);
                $category->name = $data['name'] ? $data['name'] : ' ';
                $category->score = $data['score'] ? $data['score'] : 0;
                $category->save();

                $result['categories_edited']++;
            }
        }


        $input_paragraphs = $request->input('paragraph');
        if (isset($input_paragraphs['new'])) {
            foreach ($input_paragraphs['new'] as $category_id => $paragraphs) {
                foreach ($paragraphs as $paragraph) {
                    if (stristr($category_id, 'new-') !== FALSE) {
                        $new_cat_id = (int)str_replace('new-', '', $category_id);
                        $category_id = $created_categories[$new_cat_id];
                    }

                    Paragraph::create([
                        'type' => $paragraph['type'] ? $paragraph['type'] : 'normal',
                        'name' => $paragraph['name'] ? $paragraph['name'] : ' ',
                        'score' => $paragraph['score'] ? $paragraph['score'] : 0
                    ])->categories()->attach($category_id);
                    $result['new_paragraphs']++;
                }
            }
        }
        if (isset($input_paragraphs['delete'])) {
            foreach ($input_paragraphs['delete'] as $paragraph_id => $value) {
                Paragraph::find($paragraph_id)->delete();
                $result['paragraphs_deleted']++;
            }
        }
        if (isset($input_paragraphs['edit'])) {
            foreach ($input_paragraphs['edit'] as $paragraph_id => $data) {
                $paragraph = Paragraph::find($paragraph_id);
                $paragraph->type = $data['type'] ? $data['type'] : 'normal';
                $paragraph->name = $data['name'] ? $data['name'] : ' ';
                $paragraph->score = $data['score'] ? $data['score'] : 0;
                $paragraph->save();
                $result['paragraphs_edited']++;
            }
        }

        return redirect(action('CategoryController@index'))->with('status', __('Data saved'));
    }

    public function index()
    {
        $categories = Category::with('paragraphs')->get();
//        $categoriesTotalScore = Category::select('score')->sum('score');
        $categoriesTotalScore = Paragraph::select('score')->sum('score');
        $paragraphs = Paragraph::get();

        $this->breadcrumbs[] = array('url' => action('CategoryController@index'), 'label' => __('Category management'));

        return view('categories.index', compact('categories', 'categoriesTotalScore', 'paragraphs'))->with(['breadcrumbs' => $this->breadcrumbs]);
    }


    public function create()
    {
        $paragraphs = Paragraph::get();
        return view('categories.create', compact('paragraphs'));
    }


    public function store(Request $request)
    {
        $newData = $request->all();
        $categoriesTotalScore = Category::select('score')->sum('score');
        $categoriesTotalScore = $categoriesTotalScore + $newData['score'];
        if ($categoriesTotalScore > 100) {
            flash('Categories score sum can not be more than 100%')->error();
            return back();
        }
        $category = Category::create($newData);
        if (isset($newData['paragraph'])) {
            foreach ($newData['paragraph'] as $key => $paragraph) {
                $category->paragraphs()->attach($key);
            }
        }
        flash('Category stored!')->success();
        return back();
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $paragraphs = Paragraph::all();
        return view('categories.edit', compact('paragraphs', 'category'));
    }

    public function update(Request $request, Category $category)
    {
        $categoriesTotalScore = Category::select('score')->sum('score');
        if ($categoriesTotalScore > 100) {
            flash('Categories score sum can not be more than 100%')->error();
            return back();
        }
        $newData = $request->all();
        $category->update($newData);
        $category->paragraphs()->detach();
        if (isset($newData['paragraph'])) {
            foreach ($newData['paragraph'] as $key => $paragraph) {
                $category->paragraphs()->attach($key);
            }
        }
        flash('Category updated!')->success();
        return back();
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        flash('Category deleted!')->error();
        return back();
    }

    public function addParagraphToCategory($id)
    {
        $newData = request()->all();
        $category = Category::find($id);
        $score = 0;
        if (isset($category->paragraphs)) {
            foreach ($category->paragraphs as $paragraph) {
                $score += $paragraph->score;
            }
        }
        $score = $newData['score'] + $score;
        if ($score > 100) {
            flash('Paragraph score for this category can not be more than 100%! Enter some other value')->error();
            return back();
        }
        $paragraph = Paragraph::create($newData);
        $paragraph->categories()->attach($id);
        flash('Paragraph added to category!')->success();
        return back();
    }
}
