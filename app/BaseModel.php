<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class BaseModel extends Model {

    public static function boot() {
        static::creating(function ($model) {
//            echo '<pre>';print_r($model->fillable);die;

            if (in_array('created_by', $model->fillable)):
                $model->created_by = (!empty(Auth::user()->id)) ? Auth::guard($gaurdname)->user()->id : '0';
            endif;
            if (in_array('updated_by', $model->fillable)):
                $model->updated_by = (!empty(Auth::user()->id)) ? Auth::guard($gaurdname)->user()->id : '0';
            endif;
            if (in_array('state', $model->fillable)):
                $model->state = 1;
            endif;
            $model->created_at = \Carbon\Carbon::now();
            $model->updated_at = \Carbon\Carbon::now();
        });

        static::updating(function ($model) {
            // bleh bleh
        });

        static::deleting(function ($model) {
            // bluh bluh
        });

        parent::boot();
    }

    /**
     * Use to return Multi-dimensional array to single array
     *
     * @author ergauravsethi376@gmail.com
     * @param array  $array  multi-dimensional array.
     */
    protected static function array_flatten($datas) {
        $return = [];
        foreach ($datas as $k => $data):
            foreach ($data as $key => $dataz):
                $return[] = $dataz;
            endforeach;
        endforeach;
        return $return;
    }

    /**
     * Use to search a string in array.
     *
     * @author ergauravsethi376@gmail.com
     * @param string  $needle  string to be searched.
     * @param array  $haystack  multi-dimensional array.
     */
    protected static function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Insert Multiple data into table using Default Methods
     *
     * @author ergauravsethi376@gmail.com
     * @param string $table Table name in DB.
     * @param array  $data  Multi-Dimensional array of Data to insert in dab corresponding to attributes.
     * @param Boolean  $sendData  send False to not get data in response.
     */
    protected static function insertMultipleDataInDB($table, $data, $sendData = true, $user = null) {
        $userID = $user === null ? Auth::id() : $user;
        $insertData = [];
        foreach ($data as $d):
            $insertD = $d;
            $insertD['state'] = !isset($data['state']) ? "1" : $data['state'];
            $insertD['created_by'] = $userID;
            $insertD['updated_by'] = $userID;
            $insertD['created_at'] = \Carbon\Carbon::now();
            $insertD['updated_at'] = \Carbon\Carbon::now();
            $insertData[] = $insertD;
        endforeach;
        if (DB::table($table)->insert($insertData)):
            return $sendData == true ? DB::table($table)->latest('id')->first() : true;
        else:
            return false;
        endif;
    }

    /**
     * Insert data into table using Default Methods
     *
     * @author ergauravsethi376@gmail.com
     * @param string $table Table name in DB.
     * @param array  $data  Data to insert in dab corresponding to attributes.
     * @param Boolean  $sendData  send False to not get data in response.
     */
    protected static function insertDataInDB($table, $data, $sendData = true) {
        $insertData = [];
        $insertData[$table] = $data;
        $insertData[$table]['state'] = !isset($data['state']) ? 1 : $data['state'];
        $insertData[$table]['created_by'] = Auth::id();
        $insertData[$table]['updated_by'] = Auth::id();
        $insertData[$table]['created_at'] = \Carbon\Carbon::now();
        $insertData[$table]['updated_at'] = \Carbon\Carbon::now();
        if (DB::table($table)->insert($insertData)):
            return $sendData == true ? DB::table($table)->latest('id')->first() : true;
        else:
            return false;
        endif;
    }

    /**
     * Update data in table using Default Methods
     *
     * @author ergauravsethi376@gmail.com
     * @param string $table Table name in DB.
     * @param array  $data  Data to insert in dab corresponding to attributes.
     * @param int  $id  Primary Key of the table.
     * @param Boolean  $returnData  send False to not get data in response.
     */
    protected static function updateDataInDB($table, $data, $id, $returnData = true) {
        $updateData = $data;
        $updateData['state'] = !isset($data['state']) ? 1 : $data['state'];
        $updateData['updated_by'] = Auth::id();
        $updateData['updated_at'] = \Carbon\Carbon::now();
        if (DB::table($table)->where('id', $id)->update($updateData)):
            return $returnData == true ? DB::table($table)->where('id', $id)->get() : true;
        else:
            return false;
        endif;
    }

    /**
     * Filter Array in $filterArray format
     *
     * @author ergauravsethi376@gmail.com
     * @param array  $array  Array which you want to filter according to $filterArray.
     * @param array  $filterArray  Attributes which you want to return.
     */
    protected static function filterArray($array, $filterArray) {
        $return = [];
        $arrayData = (array) $array;
        foreach ($filterArray as $filterKey => $filterValue) {
            $return[$filterKey] = $arrayData[$filterValue];
        }
        return $return;
    }

    /**
     * Filter MultiDimenstional Array in $filterArray format
     *
     * @author ergauravsethi376@gmail.com
     * @param array  $array  MultiDimenstional Array which you want to filter according to $filterArray.
     * @param array  $filterArray  Attributes which you want to return.
     */
    protected static function filterMultiDArray($array, $filterArray) {
        $return = [];
        $returns = [];
        if (count($array)):
            foreach ($array as $data):
                $data = (array) $data;
                foreach ($filterArray as $filterKey => $filterValue) :
                    if (isset($data[$filterValue])):
                        $return[$filterKey] = $data[$filterValue];
                    endif;
                endforeach;
                $returns[] = $return;
            endforeach;
        endif;
        return $returns;
    }

    /**
     * Use to return errors
     *
     * @author ergauravsethi376@gmail.com
     * @param string $code Return code According to response.
     */
    protected static function error($code) {
        return ['error' => $code];
    }

}
