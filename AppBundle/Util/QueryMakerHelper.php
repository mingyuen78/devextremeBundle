<?php
namespace AppBundle\Util;

class QueryMakerHelper {
    public static function translate($columns,$whereSQL) {
        for($i = 0; $i < count($columns); $i++) {
            $fieldToBeReplaced = "(".$columns[$i]["dataField"];
            $replaceValue = "(".$columns[$i]["sField"];
            $newWhereSQL = str_replace($fieldToBeReplaced,$replaceValue,$whereSQL);
            $whereSQL = $newWhereSQL;

        }
        return $newWhereSQL;
    }
    
    public static function findmatch($columns, $linqColumn) {
        $returnFind = "";
        for($i = 0; $i < count($columns); $i++) {
            if ($columns[$i]["dataField"] == $linqColumn) {
                $returnFind = $columns[$i]["sField"];
                break;
            }
        }
        return $returnFind;
    }

    public static function convert($field,$operator,$value) {
        $strReturn = $field;
        switch($operator) {
            case "contains":
                $strReturn .= " LIKE '%".$value."%'";
            break;
            case "notcontains":
                $strReturn .=  " IS NOT LIKE '%".$value."%'";
            break;
            case "startswith":
                $strReturn .=  " LIKE '%".$value."'";
            break;
            case "endswith":
                $strReturn .=  " LIKE '".$value."%'";
            break;
            case "=":
                $strReturn .=  " = '".$value."'";
            break;

            case ">=":
                $strReturn .=  " >= '".$value."'";
            break;

            case "<":
                $strReturn .=  " < '".$value."'";
            break;
            case "<>":
                $strReturn .= " IS NOT '".$value."'";
            break;
        }
        return $strReturn;
    }
}
?>