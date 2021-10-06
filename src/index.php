<?php

namespace Php\Progect\Lvl2\Differ;

function compare($content1, $content2)
{
    $keys = array_keys(array_merge((array)$content1, (array)$content2));
    sort($keys);
    $result = [];
    foreach ($keys as $key) {
        if (!isset($content1->$key)) {
            $result[$key] = ['oldValue' => '', 'newValue' => $content2->$key, 'status' => 'added'];
            continue;
        }
        if (!isset($content2->$key)) {
            $result[$key] = ['oldValue' => $content1->$key, 'newValue' => '', 'status' => 'deleted'];
            continue;
        }
        if (is_object($content1->$key) && is_object($content2->$key)) {
            $result[$key] = compare($content1->$key, $content2->$key);
            continue;
        }
        if ($content1->$key == $content2->$key) {
            $result[$key] = ['oldValue' => $content1->$key, 'newValue' => $content2->$key, 'status' => 'no_changed'];
            continue;
        }
        $result[$key] = ['oldValue' => $content1->$key, 'newValue' => $content2->$key, 'status' => 'updated'];
    }
    return $result;
}

function valToStr($val)
{
    if (is_bool($val)) {
        return $val ? 'true' : 'false';
    }
    return "$val";
}


function diffToString($diff, $lvl = 1)
{
    $res = ['{'];
    $indent = str_repeat(' ', $lvl);
    foreach ($diff as $key => $value) {
        switch ($value['status']) {
            case 'added':
                $res[] = "$indent+ $key: " . valToStr($value['newValue']);
                break;
            case 'deleted':
                $res[] = "$indent- $key: " . valToStr($value['oldValue']);
                break;
            case 'updated':
                $res[] = "$indent- $key: " . valToStr($value['oldValue']) . "\n" . "$indent+ $key: " . valToStr($value['newValue']);
                break;
            case 'no_changed':
                $res[] = "$indent  $key: " . valToStr($value['oldValue']);
                break;
        }
    }
    $res[] = '}';
    return implode("\n", $res);
}



function genDiff(string $path1, string $path2): string
{
    if (!file_exists($path1)) {
        return "The file $path1 does not exist";
    }
    if (!file_exists($path2)) {
        return "The file $path2 does not exist";
    }

    $content1 = json_decode(file_get_contents($path1));
    $content2 = json_decode(file_get_contents($path2));
    return diffToString(compare($content1, $content2));
}
