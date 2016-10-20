<?php

namespace app\modules\export\models;
use Yii;
use app\modules\export\models\ExportUtils;

class ExportGrid {
    public static function exportButton($gridviewConfigArray,$modelname="")
    {

        file_put_contents('serialize_store', serialize($gridviewConfigArray));

        ?>

        <?php 

        if($modelname==""){
            $modelname = ExportUtils::getModelName(Yii::$app->controller->id);
            $modelname = 'app\\models\\'.$modelname;
        }
        $form = \yii\bootstrap\ActiveForm::begin(['method'=>'POST','action'=>yii\helpers\Url::to(['/export'])]); ?>
        <!--<form action="<?php //echo yii\helpers\Url::to(['site/download-excel']) ?>" method="POST">-->
    <!--        <textarea name="gridviewConfigArray">
                <?php echo serialize($gridviewConfigArray); ?>
            </textarea>-->

            <input style="display: none" name="model_name" value="<?php echo $modelname; ?>" type="text" />
            <button type="submit" class="btn btn-success"> Download CSV </button>
        <!--</form>-->
        <?php \yii\bootstrap\ActiveForm::end(); ?>
        <?php
    }
    
    
    public static function generateExportFile($config,$fileName)
    {
        $outputArray = array();
        $oCounter = 0;
        
        $columns = $config["columns"];
        /*
        display_array($config["dataProvider"]->query->select);
        display_array($columns);
        exit;

        $columnArray = array();
        foreach ($columns as $key => $value) {
            if(!is_array($value)){
                $columnArray[] = $value;
            }
        }
        */

    //    display_array($config["dataProvider"]);
    //    exit;

        $config["dataProvider"]->pagination = false;
        $dataProviders = $config["dataProvider"]->models;

        
        
//        $isModel = true;
//        if(isset($dataProviders[0]) && is_array($dataProviders[0])){
//            $isModel = false;
//        }
        

        if(file_exists($fileName)){
            unlink($fileName);
        }    
        $output = fopen($fileName, 'w');

        if(isset($dataProviders[$oCounter]))
        {

            $dataProvidersSingle = (object) $dataProviders[$oCounter];

            for($j=0;$j<count($columns);$j++)
            {
                $columnsSingle = $columns[$j];

                
                
                if(is_array($columnsSingle))
                {
                    if(isset($columnsSingle["class"]) && $columnsSingle["class"]=="yii\grid\ActionColumn")
                    {
                    }
                    else if(isset($columnsSingle["header"]))
                    {
                        $outputArray[$j] = $columnsSingle["header"]; 
                    }
//                    else if(isset($columnsSingle["content"]))
//                    {
//                        $outputArray[$j] = getLabelNameFromColumnName($columnsSingle["attribute"], $dataProvidersSingle);   
//                    }
                    else if(isset($columnsSingle["attribute"]))
                    {
                        $outputArray[$j] = ExportUtils::getLabelNameFromColumnName($columnsSingle["attribute"], $config["dataProvider"]->query->modelClass);   
                    }
                    else if(isset($columnsSingle["class"]) && $columnsSingle["class"]=="yii\grid\SerialColumn")
                    {
                        $outputArray[$j] = "S. No";
                    }
                }
                else
                {
                    $outputArray[$j] = ExportUtils::getLabelNameFromColumnName($columnsSingle, $config["dataProvider"]->query->modelClass);   
    //                $outputArray[$oCounter][$j] = $dataProvidersSingle->$columnsSingle;
                }
            }

            fputcsv($output, $outputArray);
            $oCounter++;
        }
        
        for($i=0;$i<count($config["dataProvider"]->models);$i++)
        {

            $outputArray = array();
            $dataProvidersSingle = $dataProviders[$i];

            
            
            for($j=0;$j<count($columns);$j++)
            {
                $columnsSingle = $columns[$j];

                if(is_array($columnsSingle))
                {

                    if(isset($columnsSingle["class"]) && $columnsSingle["class"]=="yii\grid\ActionColumn")
                    {
                    }
                    else if(isset($columnsSingle["content"]))
                    {
                        $outputArray[$j] = $columnsSingle["content"]($dataProvidersSingle);   
                        if($outputArray[$j]==null)
                        {
                            $outputArray[$j] = "";
                        }
                    }
                    else if(isset($columnsSingle["class"]) && $columnsSingle["class"]=="yii\grid\SerialColumn")
                    {
                        $outputArray[$j] = $i+1;
                    }
                    else if(isset($columnsSingle["attribute"]) && 
                            isset ($dataProvidersSingle->$columnsSingle["attribute"])){
                        $outputArray[$j] = $dataProvidersSingle->$columnsSingle["attribute"];
                    }
                }
                else
                {
                    $outputArray[$j] = isset($dataProvidersSingle->$columnsSingle)?$dataProvidersSingle->$columnsSingle:$dataProvidersSingle[$columnsSingle];
                }

            }


            fputcsv($output, $outputArray);

            $oCounter++;

        }
        //return $outputArray;

    }
}