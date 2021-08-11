<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-10 18:48:33
 * @modify date 2021-08-10 18:48:33
 * @desc [description]
 */

namespace SLiMSAssetmanager\View;

use \simbio_form_table_AJAX as Form;
use \simbio_form_element as FE;

class popPattern
{
    public static function setPreview()
    {
        $HTML = <<<HTML
            <strong>Pratinjau</strong>
            <div class="w-full p-2 bg-primary font-bold">
                <h3 id="result" class="block text-center text-white">
                    <b id="prefPrev">BR</b>
                    <b id="numPrev">00000</b>
                    <b id="sufPrev">I</b>
                </h3>
            </div>
        HTML;

        return $HTML;
    }

    public static function js()
    {
        $JS = <<<HTML
            <script>
                async function setPreview(e, id)
                {
                    switch (id) {
                        case 0:
                            document.querySelector('#prefPrev').innerHTML = e.value
                            break;
                        case 1:
                            document.querySelector('#sufPrev').innerHTML = e.value
                            break;
                        default:
                            let zero = '';
                            for (let idx = 0; idx < e.value; idx++) {
                                zero += '0'
                            }
                            document.querySelector('#numPrev').innerHTML = zero
                            break;
                    }
                }
            </script>
        HTML;

        return $JS;
    }

    public static function render()
    {
        // Set form instance
        $Form = new Form('itemForm', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'post');

        // set prop
        $property = [
            'submit_button_attr' => 'name="saveData" value="' . __('Save') . '" class="s-btn btn btn-default"',
            'table_attr' => 'id="dataList" cellpadding="0" cellspacing="0"',
            'table_header_attr' => 'class="alterCell"',
            'table_content_attr' => 'class="alterCell2"'
        ];

        $fields = [
            ['addHidden' => ['handler', 'Record']],
            ['addHidden' => ['method', 'savePattern']],
            ['addTextField' => ['text', 'prefix', 'Awalan' . '*', 'BR', 'class="form-control" onkeyup="setPreview(this, 0)"', 'Isikan awalan']],
            ['addTextField' => ['text', 'suffix', 'Akhiran' . '*', 'I', 'class="form-control" onkeyup="setPreview(this, 1)"', 'Isikan akhiran']],
            ['addTextField' => ['text', 'numLong', 'Panjang nomor seri	' . '*', '5', 'class="form-control" onkeyup="setPreview(this, 2)"', 'Isikan Panjang nomor seri	']]
        ];

        // register property
        foreach ($property as $prop => $value) {
            $Form->{$prop} = $value;
        }

        // Register field
        foreach ($fields as $field) {
            $method = array_key_first($field);
            $parameter = array_values($field)[0];
            call_user_func_array([$Form, $method], $parameter);
        }

        // set preview
        echo self::setPreview();

        // make form
        echo $Form->printOut();

        // set JS
        echo self::js();
    }
}