<?php

namespace app\modules\export\controllers;

use yii\web\Controller;

/**
 * Default controller for the `export` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if(file_exists("serialize_store"))
        {
            $s = file_get_contents('serialize_store');
            if($s!="")
            {
                $gridviewConfigArray = unserialize($s);
                
                $functionName = "getColumnConfigOfIndex";
                if(isset($gridviewConfigArray['config_address'])){
                    $functionName = $gridviewConfigArray['config_address'];
                }
                
                $config = $_POST['model_name']::$functionName($gridviewConfigArray);
                \app\modules\export\models\ExportGrid::generateExportFile($config,"tempData.csv");
                header('Content-Type: application/csv');
                $fileNameString = str_replace("\\", "", $_POST['model_name']);
                header('Content-Disposition: attachment; filename="'.$fileNameString.'.csv"');
                readfile('tempData.csv');
            }
        }
    }
}
