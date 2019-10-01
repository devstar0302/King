<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use File;
use Session;

class FileItem extends Model
{
    protected $table = "files";
    protected $guarded = [];

    //file type information
    public function type(){
        return $this->hasOne('App\Models\FileType', 'id', 'type_id');
    }

    public static function getSortArray(){
        $array = [
            'column' => 'files.created_at',
            'by' => 'DESC'
        ];

        if(Session::has('sort')){
            switch (Session::get('sort')){
                case 'date_desc':
                    $array = [
                        'column' => 'files.created_at',
                        'by' => 'DESC'
                    ];
                    break;
                case 'date_asc':
                    $array = [
                        'column' => 'files.created_at',
                        'by' => 'ASC'
                    ];
                    break;
                case 'type_asc':
                    $array = [
                        'column' => 'file_types.extension',
                        'by' => 'ASC'
                    ];
                    break;
                case 'type_desc':
                    $array = [
                        'column' => 'file_types.extension',
                        'by' => 'DESC'
                    ];
                    break;
                case 'name_asc':
                    $array = [
                        'column' => 'files.name',
                        'by' => 'ASC'
                    ];
                    break;
                case 'name_desc':
                    $array = [
                        'column' => 'files.name',
                        'by' => 'DESC'
                    ];
                    break;
            }
        }

        return $array;
    }

    public static function setImages( $objArray ){
        if(isset($objArray) && count($objArray)){
            foreach ($objArray as $key => $item) {
                $imagePatch = 'types/'.((isset($item->type->image) && !empty($item->type->image)) ? $item->type->image : 'file.png');
                if(isset( $item->type->is_image ) && $item->type->is_image == 1 ){
                    $imagePatch = $item->filename;
                }

                $objArray[$key]->image = $imagePatch;
            }
        }

        return $objArray;
    }

    public static function saveFile( $file ){
        $input = [];
        $extension = $file->getClientOriginalExtension();
        $name = substr(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 0, 50);
        $fileType = FileType::firstOrCreate(['extension' => $extension], [
            'name'  => $extension . ' Document',
            'image' => ''
        ]);
        $fileName = self::str2url($name).'.'.$extension;
        $size = self::formatBytes($file->getClientSize());

        $input['type_id'] = isset($fileType) ? $fileType->id : 0;
        $input['name'] = $name;
        $input['filename'] = $fileName;
        $input['size'] = $size;

        //save file
        $file->move('uploads/frontend/', $fileName);

        return self::create($input);
    }

    public static function destroy($id)
    {
        $old_filename = self::find( $id )->filename;
        File::delete( public_path() .'/uploads/frontend/' . $old_filename );
        return parent::destroy($id);
    }

    /**

     * Format bytes to kb, mb, gb, tb

     *

     * @param  integer $size

     * @param  integer $precision

     * @return integer

     */

    public static function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }

    public static function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );

        return strtr($string, $converter);

    }

    public static function str2url($str) {
        // переводим в транслит
        $str = self::rus2translit($str);
        // в нижний регистр
        $str = strtolower($str);
        // заменям все ненужное нам на "-"
        $str = preg_replace('~[^-a-z0-9_]+~u', '-', $str);
        // удаляем начальные и конечные '-'
        $str = trim($str, "-");

        return $str;
    }
}
