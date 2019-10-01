<?php

use App\Models\Category;
use App\Models\FRR;
use App\Models\Paragraph;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SeedCategoriesTable extends Seeder
{
  private $lastCategory;
  private $lastParagraph;

  public function run()
  {
    foreach (Excel::load(storage_path('catgories-paragraphs-finding-risk-repair.xlsx'))->get() as $index => $fileRow) {
      if (!is_null($fileRow['category_name'])) {
        $this->lastCategory = Category::firstOrCreate([
          'name' => $fileRow['category_name']
        ]);
      }

      if (!is_null($fileRow['paragraph_name'])) {
        $this->lastParagraph = Paragraph::firstOrCreate([
          'name' => $fileRow['paragraph_name'],
          'score' => $fileRow['paragraph_score'],
          'type' => is_null($fileRow['critical_malfunction']) ? 'normal' : 'severe'
        ]);

        DB::table('category_paragraph')->where('category_id', $this->lastCategory->id)
          ->where('paragraph_id', $this->lastParagraph->id)->delete();

        DB::table('category_paragraph')->insert([
          'category_id' => $this->lastCategory->id,
          'paragraph_id' => $this->lastParagraph->id
        ]);

        FRR::create([
          'paragraph_id' => $this->lastParagraph->id,
          'type' => $this->lastParagraph->type,
          'finding' => $fileRow['finding'],
          'risk' => $fileRow['risk'],
          'repair' => $fileRow['repair'],
        ]);
      }

      if (is_null($fileRow['category_name']) && is_null($fileRow['paragraph_name']) &&
        (!is_null($fileRow['finding']) || !is_null($fileRow['risk']) || !is_null($fileRow['repair']))) {
        FRR::create([
          'paragraph_id' => $this->lastParagraph->id,
          'type' => $this->lastParagraph->type,
          'finding' => $fileRow['finding'],
          'risk' => $fileRow['risk'],
          'repair' => $fileRow['repair'],
        ]);
      }
    }

    foreach (Category::all() as $category) {
      $category->update([
        'score' => Paragraph::whereIn('id', DB::table('category_paragraph')
          ->where('category_id', $category->id)->pluck('paragraph_id'))->sum('score')
      ]);
    }
  }
}
