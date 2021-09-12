<?php
/**
 * @author [author]
 * @email [example@mail.com]
 * @create date 2021-09-04 17:40:35
 * @modify date 2021-09-04 17:40:35
 * @desc [description]
 */

namespace SLiMSAssetmanager\Ui;

class Html
{
    public static $writeMode = 'output';

    public static function write(string $tag, string $slot, array $attributes = [], bool $endLess = false)
    {
        $tagFiltered = trim(str_replace(['<','>','/'], '', $tag));
        // compiling attribute
        $htmlAttribute = '';
        foreach ($attributes as $attribute => $value) {
            $htmlAttribute .= ' ' . preg_replace('/[^a-z\-]/', '', strtolower($attribute)) . ' = "' . str_replace('"', '', $value).'" ';
        }
        
        // set Output
        $Html  = '<' . $tagFiltered . rtrim($htmlAttribute) . ($endLess ? '/' : '') .'>';
        $Html .= $slot;
        $Html .= trim(((!$endLess) ? '</' . $tagFiltered . '>' : ''));

        if (self::$writeMode === 'output')
        {
            echo $Html;
        }
        else
        {
            return $Html;
        }
    }

    public static function js(string $script, array $attribute = [])
    {
        return Html::write('script', $script, $attribute);
    }

    public static function preview(string $html)
    {
        return htmlspecialchars($html);
    }
}