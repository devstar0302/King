<?php

namespace App\Listeners\ParagraphSaved;

use App\Events\ParagraphSaved;
use App\Models\Malfunction;
use App\Models\Risk;

class ChangeMalfunctionRisk
{
  public function handle(ParagraphSaved $event)
  {
    $paragraph = $event->paragraph;

    if ($paragraph->isDirty('type') && $paragraph->type == 'severe') {
      $malfunctions = Malfunction::all();

      foreach ($malfunctions as $malfunction) {
        if (isset($malfunction->data['paragraphs']) && !empty($malfunction->data['paragraphs'])) {
          if (in_array($paragraph->id, array_pluck(array_collapse($malfunction->data['paragraphs']), 'id'))) {
            Risk::where('malfunction_id', $malfunction->id)
              ->update([
                'level' => 'HIGH'
              ]);
          }
        }
      }
    }
  }
}
