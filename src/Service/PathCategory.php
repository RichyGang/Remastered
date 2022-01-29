<?php

namespace App\Service;

class PathCategory
{
    function getPathCategory($category):array
    {
        $pathCategory = [$category];
        while ($category->getMother() != null){
            array_push($pathCategory, $category->getMother());
            $category = $category->getMother();
        }
        $pathCategory = array_reverse($pathCategory);

        return $pathCategory;
    }
}
