<?php
namespace App\AppBundle\Util;

use App\AppBundle\Generic\GridArguments;


class FilterConstructHelper {
    public static function process($post): GridArguments {
        $returnArg = new GridArguments();

        if (property_exists($post, "skip")) {
            $returnArg->skip = intval($post->skip);    
        } else {
            $returnArg->skip = 0;
        }
         
        if (property_exists($post, "filter")) {
            $returnArg->filter = ($post->filter)?$post->filter:null;
        } else {
            $returnArg->filter = null;
        }
        
        if (property_exists($post, "sort")) {
            if ($post->sort) {
                $returnArg->sort = $post->sort;    
            } else {
                $returnArg->sort = $post->defaultSort;  
            }
        } else {
            $returnArg->sort = $post->defaultSort;
        }

        if (property_exists($post, "group")) {
            if (is_array($post->group)) {
                $returnArg->group = $post->group[0]->selector;
            }
        }
        $returnArg->take =  ($post->take)?$post->take:10;

        return $returnArg;
    }

}