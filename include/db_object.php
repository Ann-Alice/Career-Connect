<?php
class DatabaseObject {
    protected static $table_name;
    protected static $db_fields;
    
    public static function find_all() {
        return static::find_by_sql("SELECT * FROM " . static::$table_name);
    }
    
    public static function find_by_id($id=0) {
        global $mydb;
        $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE ID={$id} LIMIT 1");
        return !empty($result_array) ? array_shift($result_array) : false;
    }
    
    public static function find_by_sql($sql="") {
        global $mydb;
        $mydb->setQuery($sql);
        $result_set = $mydb->loadResultList();
        return $result_set;
    }
    
    public static function count_all() {
        global $mydb;
        $sql = "SELECT COUNT(*) FROM " . static::$table_name;
        $mydb->setQuery($sql);
        $result_set = $mydb->loadSingleResult();
        return array_shift($result_set);
    }
    
    protected function attributes() {
        $attributes = array();
        foreach(static::$db_fields as $field) {
            if(property_exists($this, $field)) {
                $attributes[$field] = $this->$field;
            }
        }
        return $attributes;
    }
    
    protected function sanitized_attributes() {
        global $mydb;
        $clean_attributes = array();
        foreach($this->attributes() as $key => $value) {
            $clean_attributes[$key] = $mydb->escape_string($value);
        }
        return $clean_attributes;
    }
    
    public function save() {
        return isset($this->ID) ? $this->update() : $this->create();
    }
    
    public function create() {
        global $mydb;
        $attributes = $this->sanitized_attributes();
        $sql = "INSERT INTO " . static::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        
        $mydb->setQuery($sql);
        if($mydb->executeQuery()) {
            $this->ID = $mydb->insert_id();
            return true;
        } else {
            return false;
        }
    }
    
    public function update() {
        global $mydb;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach($attributes as $key => $value) {
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE " . static::$table_name . " SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE ID=" . $mydb->escape_string($this->ID);
        
        $mydb->setQuery($sql);
        return ($mydb->executeQuery()) ? true : false;
    }
    
    public function delete() {
        global $mydb;
        $sql = "DELETE FROM " . static::$table_name;
        $sql .= " WHERE ID=" . $mydb->escape_string($this->ID);
        $sql .= " LIMIT 1";
        
        $mydb->setQuery($sql);
        return ($mydb->executeQuery()) ? true : false;
    }
}
?> 