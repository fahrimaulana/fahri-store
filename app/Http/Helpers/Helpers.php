<?php

function getSql($model)
{
    $replace = function ($sql, $bindings)
    {
        $needle = '?';
        foreach ($bindings as $replace){
            $pos = strpos($sql, $needle);
            if ($pos !== false) {
                if (gettype($replace) === "string") {
                     $replace = ' "'.addslashes($replace).'" ';
                }
                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }
        return $sql;
    };
    $sql = $replace($model->toSql(), $model->getBindings());

    return $sql;
}
