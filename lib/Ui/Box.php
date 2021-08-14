<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-08-09 23:26:05
 * @modify date 2021-08-09 23:26:05
 * @desc [description]
 */

namespace SLiMSAssetmanager\Ui;

class Box
{
    private $title = 'Box Title';
    private $buttons = [];
    private $formUrl;
    private $formMethod;
    private $customField;
    public $disableForm = false;

    public function __construct(string $formUrlAction, string $formMethod)
    {
        $this->formUrl = $formUrlAction;
        $this->formMethod = $formMethod;
    }

    public function setCustomField($rawHTML)
    {
        $this->customField = strip_tags($rawHTML, '<input><br><select><option><button>');
    }

    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function setActionButton(array $actionButton)
    {
        $this->buttons = $actionButton;
        return $this;
    }

    public function make()
    {
        // Header element
        $HTML = <<<HTML
            <div class="menuBox">
                <div class="menuBoxInner memberIcon">
        HTML;

        // Title
        $HTML .= <<<HTML
            <div class="per_title">
                <h2>{$this->title}</h2>
            </div>
        HTML;

        /* Subsection */
        $HTML .= '<div class="sub_section">';

        // Button
        if (count($this->buttons) && !$this->disableForm)
        {
            $HTML .= '<div class="btn-group">';
            
            // Loop for set button
            foreach ($this->buttons as $button) {
                $class = (isset($button['class'])) ? trim($button['class']) : 'btn btn-default';
                $HTML .= <<<HTML
                    <a href="{$button['url']}" class="{$class}">{$button['label']}</a>
                HTML;
            }

            $HTML .= '</div>';
        }

        // Form
        if (!$this->disableForm)
        {
            $label = __('Search');
            $HTML .= <<<HTML
                <form name="search" action="{$this->formUrl}" id="search" method="get" class="form-inline">{$label}
                    <input type="text" name="keywords" class="form-control col-md-3"/>
                    <input type="submit" id="doSearch" value="{$label}"class="s-btn btn btn-default"/>
                    {$this->customField}
                </form>
            HTML; 
        }
        
        /* End Subsection */
        $HTML .= '</div>';

        // End Menu
        $HTML .= '</div>';
        $HTML .= '</div>';

        echo $HTML;
    }
}