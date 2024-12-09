<?php
namespace Moowgly\Lib\Storage;

abstract class Storage
{

    protected static $_instance = null;

    protected $db;

    /**
     *
     * @return Database instance.
     */
    public static function getInstance()
    {
        return self::$_instance;
    }

    /**
     * Return database not deleted results matching $id on $table.
     *
     * @param $id :
     *            searched id.
     * @param $table :
     *            table name.
     */
    public abstract function get($id, $table, $query = null);

    /**
     * Insert resource in database.
     *
     * @param $data
     *            : array containing resource id
     * @param $table :
     *            table name
     *
     * @return true on insert success, false otherwise.
     */
    public abstract function post($data, $table);

    /**
     * Update resource in database.
     *
     * @param $data
     *            : array containing resource
     * @param $table :
     *            table name
     *
     * @return true on update success, false otherwise.
     */
    public abstract function put($data, $table);

    /**
     * Delete matching $id on $table.
     *
     * @param $id
     *            : string containing resource id
     * @param $table :
     *            table name
     *
     * @return true on update success, false otherwise.
     */
    public abstract function delete($id, $table);

    /**
     * Extract a field from database.
     *
     * @param $id
     *            : string containing resource id
     * @param $table table
     *            name
     * @param $field field
     *            containing requested value
     *
     * @return requested field value on success, false otherwise.
     */
    public abstract function getField($id, $table, $field);
}
