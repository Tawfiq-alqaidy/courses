<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSimple extends Model
{
    protected $fillable = [
        'name', 'description', 'capacity', 'enrolled_count', 'category_id'
    ];

    public static function create($data)
    {
        return filedb()->insert('courses', $data);
    }

    public static function find($id)
    {
        $record = filedb()->find('courses', $id);
        if ($record) {
            $instance = new static();
            foreach ($record as $key => $value) {
                $instance->$key = $value;
            }
            return $instance;
        }
        return null;
    }

    public static function where($field, $value)
    {
        return filedb()->where('courses', $field, $value);
    }

    public static function all()
    {
        return filedb()->all('courses');
    }

    public function category()
    {
        $categoryId = $this->category_id ?? null;
        if ($categoryId) {
            return Category::find($categoryId);
        }
        return null;
    }

    public function hasAvailableSpots()
    {
        $enrolled = $this->enrolled_count ?? 0;
        $capacity = $this->capacity ?? 0;
        return $enrolled < $capacity;
    }
}
