<?php
namespace AppBundle\Util;


class DevExFilterHelper {
    private static $AND_OP = "AND";
    private static $OR_OP = "OR";
    private static $LIKE_OP = "LIKE";
    private static $NOT_OP = "NOT";
    private static $IS_OP = "IS";
    private static function _GetSqlFieldName($field) {
        $fieldParts = explode(".", $field);
        $result = "";
        $fieldName = SQLUtils::QuoteStringValue(trim($fieldParts[0]));
        if (count($fieldParts) == 2) {
            $dateProperty = trim($fieldParts[1]);
            $sqlDateFunction = "";
            $fieldPattern = "";
            switch ($dateProperty) {
                case "year":
                case "month":
                case "day": {
                    $sqlDateFunction = strtoupper($dateProperty);
                    $fieldPattern = "%s(%s)";
                    break;
                }
                case "dayOfWeek": {
                    $sqlDateFunction = strtoupper($dateProperty);
                    $fieldPattern = "%s(%s) - 1";
                    break;
                }
                default: {
                    throw new \Exception("The \"".$dateProperty."\" command is not supported");
                }
            }
            $result = sprintf($fieldPattern, $sqlDateFunction, $fieldName);
        }
        else {
            $result = $fieldName;
        }
        return $result;
    }
    private static function _GetSimpleSqlExpr($expression) {
        $result = "";
        $itemsCount = count($expression);
        $fieldName = self::_GetSqlFieldName(trim($expression[0]));
        if ($itemsCount == 2) {
            $val = $expression[1];
            $result = sprintf("%s = %s", $fieldName, SQLUtils::QuoteStringValue($val, false));
        }
        else if ($itemsCount == 3) {
            $clause = trim($expression[1]);
            $val = $expression[2];
            $pattern = "";
            if (is_null($val)) {
                $val = SQLUtils::QuoteStringValue($val, false);
                $pattern = "%s %s %s";
                switch ($clause){
                    case "=": {
                        $clause = self::$IS_OP;
                        break;
                    }
                    case "<>": {
                        $clause = self::$IS_OP." ".self::$NOT_OP;
                        break;
                    }
                }
            }
            else {
                switch ($clause) {
                    case "=":
                    case "<>":
                    case ">":
                    case ">=":
                    case "<":
                    case "<=": {
                        $pattern = "%s %s %s";
                        $val = SQLUtils::QuoteStringValue($val, false);
                        error_log(print_r($val,true));
                        break;
                    }
                    case "startswith": {
                        $pattern = "%s %s '%s%%'";
                        $clause = self::$LIKE_OP;
                        $val = str_replace("\'", "''", $val);
                        // $val = addcslashes($val, "%_");
                        break;
                    }
                    case "endswith": {
                        $pattern = "%s %s '%%%s'";
                        // $val = addcslashes($val, "%_");
                        $val = str_replace("\'", "''", $val);
                        $clause = self::$LIKE_OP;
                        break;
                    }
                    case "contains": {
                        $pattern = "%s %s '%%%s%%'";
                        // $val = addcslashes($val, "%_");
                        $val = str_replace("\'", "''", $val);
                        $clause = self::$LIKE_OP;
                        break;
                    }
                    case "notcontains": {
                        $pattern = "%s %s '%%%s%%'";
                        // $val = addcslashes($val, "%_");
                        $val = str_replace("\'", "''", $val);
                        $clause = sprintf("%s %s", self::$NOT_OP, self::$LIKE_OP);
                        break;
                    }
                    default: {
                        $clause = "";
                    }
                }
            }
            $result = sprintf($pattern, $fieldName, $clause, $val);
        }
        return $result;
    }
    public static function GetSqlExprByArray($expression) {
        $result = "(";
        $prevItemWasArray = false;
        foreach ($expression as $index => $item) {
            
            if (is_string($item)) {
                $prevItemWasArray = false;
                if ($index == 0) {
				    if ($item == "!") {
                        $result .= sprintf("%s ", self::$NOT_OP);
						continue;
                    }
                    $result .=  (isset($expression) && is_array($expression)) ? self::_GetSimpleSqlExpr($expression) : "";
					break;
                }
				$strItem = strtoupper(trim($item));
                if ($strItem == self::$AND_OP || $strItem == self::$OR_OP) {
                    $result .= sprintf(" %s ", $strItem);
                }
                continue;
            }
            if (is_array($item)) {
                if ($prevItemWasArray) {
                    $result .= sprintf(" %s ", self::$AND_OP);
                }
                $result .= self::GetSqlExprByArray($item);
                $prevItemWasArray = true;
            }
        }
        $result .= ")";
        return $result;
    }
    public static function GetSqlExprByKey($key) {
        $result = "";
        foreach ($key as $prop => $value) {
            $templ = strlen($result) == 0 ?
                     "%s = %s" :
                     " ".self::$AND_OP." %s = %s";
            $result .= sprintf($templ,
                               SQLUtils::QuoteStringValue($prop),
                               SQLUtils::QuoteStringValue($value, false));
        }
        return $result;
    }
}?>