<?php

namespace App\Models;

use App\Providers\FileDbConnection;

class Category
{
    protected $fillable = [
        'name', 'description'
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
        return self::getDb()->insert('categories', $data);
    }

    public static function find($id)
    {
        $record = self::getDb()->find('categories', $id);
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
        return self::getDb()->where('categories', $field, $value);
    }

    public static function all()
    {
        return self::getDb()->all('categories');
    }

    public static function count()
    {
        return count(self::getDb()->all('categories'));
    }

    public function courses()
    {
        return Course::where('category_id', $this->id);
    }
}
