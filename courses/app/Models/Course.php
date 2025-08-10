<?php

namespace App\Models;

use App\Providers\FileDbConnection;

class Course
{
    protected $fillable = [
        'title', 'description', 'category_id', 'max_students', 'price', 'duration'
    ];

    private static function getDb()
    {
        static $db = null;
        if ($db === null) {
            $db = new FileDbConnection();
        }
        return $db;
    }

    public static function create($data)
    {
        return self::getDb()->insert('courses', $data);
    }

    public static function find($id)
    {
        $record = self::getDb()->find('courses', $id);
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
        return self::getDb()->where('courses', $field, $value);
    }

    public static function all()
    {
        return self::getDb()->all('courses');
    }

    public static function count()
    {
        return count(self::getDb()->all('courses'));
    }

    public function category()
    {
        return Category::find($this->category_id ?? 0);
    }
}
