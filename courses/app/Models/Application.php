<?php

namespace App\Models;

use App\Providers\FileDbConnection;

class Application
{
    protected $fillable = [
        'name', 'email', 'phone', 'course_id', 'status', 'waiting_position'
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
        return self::getDb()->insert('applications', $data);
    }

    public static function find($id)
    {
        $record = self::getDb()->find('applications', $id);
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
        return self::getDb()->where('applications', $field, $value);
    }

    public static function all()
    {
        return self::getDb()->all('applications');
    }

    public static function count()
    {
        return count(self::getDb()->all('applications'));
    }

    public static function whereIn($field, $values)
    {
        $results = [];
        $all = self::getDb()->all('applications');
        
        foreach ($all as $record) {
            if (isset($record[$field]) && in_array($record[$field], $values)) {
                $results[] = $record;
            }
        }
        
        return $results;
    }

    public static function orderBy($field, $direction = 'asc')
    {
        $all = self::getDb()->all('applications');
        
        usort($all, function($a, $b) use ($field, $direction) {
            if ($direction === 'desc') {
                return $b[$field] <=> $a[$field];
            }
            return $a[$field] <=> $b[$field];
        });
        
        return $all;
    }

    public static function updateApplication($id, $data)
    {
        return self::getDb()->update('applications', $id, $data);
    }

    public function course()
    {
        $courseId = $this->course_id ?? null;
        if ($courseId) {
            return Course::find($courseId);
        }
        return null;
    }
}