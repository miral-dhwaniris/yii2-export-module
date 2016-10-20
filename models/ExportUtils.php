<?php

namespace app\modules\export\models;
use Yii;


class ExportUtils {
    
    public static function getLabelNameFromColumnName($columnName,$model)
    {
        $model = new $model();        
        if(method_exists($model,"attributeLabels") && key_exists($columnName, $model->attributeLabels()))
        {
            return $model->attributeLabels()[$columnName];
        }
        else 
        {
            $columnLabelString = "";
            $columnArray = explode("_", $columnName);
            foreach ($columnArray as $key => $value) {
                $columnLabelString = $columnLabelString." ".ucfirst($value) ;
            }
            return $columnLabelString;   
        }
    }
    
    public static function getModelName($modelName)
    {
        $modelNameCapital = "";
        $modelNameArray = explode("-", $modelName);
        foreach ($modelNameArray as $key => $value) {
            $modelNameCapital = $modelNameCapital.ucfirst($value);
        }
        return $modelNameCapital;
    }
    
}