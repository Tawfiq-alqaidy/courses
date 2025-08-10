<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Connection;

class FileDbServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('db.file', function ($app) {
            return new FileDbConnection();
        });
    }

    public function boot()
    {
        // Store the database file connection globally
        $GLOBALS['filedb'] = new FileDbConnection();
    }
}

class FileDbConnection
{
    private $filePath;
    private $data;

    public function __construct()
    {
        $this->filePath = storage_path('app/database.json');
        $this->loadData();
    }

    private function loadData()
    {
        if (!file_exists($this->filePath)) {
            $this->data = [];
            $this->saveData();
        } else {
            $content = file_get_contents($this->filePath);
            $this->data = json_decode($content, true) ?: [];
        }
    }

    private function saveData()
    {
        $dir = dirname($this->filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->filePath, json_encode($this->data, JSON_PRETTY_PRINT));
    }

    public function table($table)
    {
        if (!isset($this->data[$table])) {
            $this->data[$table] = [];
        }
        return $this->data[$table];
    }

    public function insert($table, $record)
    {
        if (!isset($this->data[$table])) {
            $this->data[$table] = [];
        }
        
        // Auto-increment ID
        $maxId = 0;
        foreach ($this->data[$table] as $item) {
            if (isset($item['id']) && $item['id'] > $maxId) {
                $maxId = $item['id'];
            }
        }
        $record['id'] = $maxId + 1;
        $record['created_at'] = date('Y-m-d H:i:s');
        $record['updated_at'] = date('Y-m-d H:i:s');
        
        $this->data[$table][] = $record;
        $this->saveData();
        return $record['id'];
    }

    public function find($table, $id)
    {
        if (!isset($this->data[$table])) {
            return null;
        }
        
        foreach ($this->data[$table] as $record) {
            if (isset($record['id']) && $record['id'] == $id) {
                return $record;
            }
        }
        return null;
    }

    public function where($table, $field, $value)
    {
        if (!isset($this->data[$table])) {
            return [];
        }
        
        $results = [];
        foreach ($this->data[$table] as $record) {
            if (isset($record[$field]) && $record[$field] == $value) {
                $results[] = $record;
            }
        }
        return $results;
    }

    public function all($table)
    {
        return $this->data[$table] ?? [];
    }

    public function update($table, $id, $data)
    {
        if (!isset($this->data[$table])) {
            return false;
        }
        
        foreach ($this->data[$table] as &$record) {
            if (isset($record['id']) && $record['id'] == $id) {
                $record = array_merge($record, $data);
                $record['updated_at'] = date('Y-m-d H:i:s');
                $this->saveData();
                return true;
            }
        }
        return false;
    }

    public function delete($table, $id)
    {
        if (!isset($this->data[$table])) {
            return false;
        }
        
        foreach ($this->data[$table] as $key => $record) {
            if (isset($record['id']) && $record['id'] == $id) {
                unset($this->data[$table][$key]);
                $this->data[$table] = array_values($this->data[$table]); // Re-index
                $this->saveData();
                return true;
            }
        }
        return false;
    }
}

// Helper functions for global use
if (!function_exists('filedb')) {
    function filedb() {
        return $GLOBALS['filedb'] ?? new FileDbConnection();
    }
}
