<?php
namespace App\AppBundle\Util;
/* Remember to change the util js file to reflect in script. */   
abstract class EnumError {
    const ALLGOOD = "00000";
    const UNIQUEID = "00101";
    const ERROR_INSERT = "00102";
    const ERROR_UPDATE = "00102";
    const ERROR_DELETE = "00103";
    const ERROR_DELETE_ADMIN = "00104";
    const ERROR_REPEATED_EVENT = "00105";
    const SEARCH_NOTFOUND = "00201";
    const ERROR = "00301";
}
?>