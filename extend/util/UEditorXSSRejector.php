<?php


namespace util;


class UEditorXSSRejector
{
    private $allowParams;
    public function parse($ueData)
    {
        $ueData = str_replace("&#", "", $ueData);
        $ueData = preg_replace("/<<+/", "&lt;<", $ueData);
        $this->allowParams = array(
            'a' => array('target', 'href', 'title', 'class', 'style'),
            'abbr' => array('title', 'class', 'style'),
            'address' => array('class', 'style'),
            'area' => array('shape', 'coords', 'href', 'alt'),
            'article' => array(),
            'aside' => array(),
            'audio' => array('autoplay', 'controls', 'loop', 'preload', 'src', 'class', 'style'),
            'b' => array('class', 'style'),
            'bdi' => array('dir'),
            'bdo' => array('dir'),
            'big' => array(),
            'blockquote' => array('cite', 'class', 'style'),
            'br' => array(),
            'caption' => array('class', 'style'),
            'center' => array(),
            'cite' => array(),
            'code' => array('class', 'style'),
            'col' => array('align', 'valign', 'span', 'width', 'class', 'style'),
            'colgroup' => array('align', 'valign', 'span', 'width', 'class', 'style'),
            'dd' => array('class', 'style'),
            'del' => array('datetime'),
            'details' => array('open'),
            'div' => array('class', 'style'),
            'dl' => array('class', 'style'),
            'dt' => array('class', 'style'),
            'em' => array('class', 'style'),
            'font' => array('color', 'size', 'face'),
            'footer' => array(),
            'h1' => array('class', 'style'),
            'h2' => array('class', 'style'),
            'h3' => array('class', 'style'),
            'h4' => array('class', 'style'),
            'h5' => array('class', 'style'),
            'h6' => array('class', 'style'),
            'header' => array(),
            'hr' => array(),
            'i' => array('class', 'style'),
            'img' => array('src', 'alt', 'title', 'width', 'height', 'id', '_src', 'loadingclass', 'class', 'data-latex', 'style'),
            'ins' => array('datetime'),
            'li' => array('class', 'style'),
            'mark' => array(),
            'nav' => array(),
            'ol' => array('class', 'style'),
            'p' => array('class', 'style'),
            'pre' => array('class', 'style'),
            's' => array(),
            'section' => array(),
            'small' => array(),
            'span' => array('class', 'style'),
            'sub' => array('class', 'style'),
            'sup' => array('class', 'style'),
            'strong' => array('class', 'style'),
            'table' => array('width', 'border', 'align', 'valign', 'class', 'style'),
            'tbody' => array('align', 'valign', 'class', 'style'),
            'td' => array('width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'),
            'tfoot' => array('align', 'valign', 'class', 'style'),
            'th' => array('width', 'rowspan', 'colspan', 'align', 'valign', 'class', 'style'),
            'thead' => array('align', 'valign', 'class', 'style'),
            'tr' => array('rowspan', 'align', 'valign', 'class', 'style'),
            'tt' => array(),
            'u' => array(),
            'text' => array(),
            'ul' => array('class', 'style'),
            'video' => array('autoplay', 'controls', 'loop', 'preload', 'src', 'height', 'width', 'class', 'style'),
        );
        $ueData = "<div>$ueData</div>";
        $dom = str_get_html($ueData);
        $doms = $dom->root->children;
        $this->dfs($doms);
        $html = $dom->outertext;
        $dom->clear();
    }

    public function dfs($doms)
    {
        foreach ($doms as $domitem) {
            if ($domitem->tag == 'text') {
                $domitem->innertext = htmlspecialchars($domitem->innertext);
            }
            if (!in_array($domitem->tag, array_keys($this->allowParams))) {
                $domitem->outertext="";
            } else {
                foreach ($domitem->attr as $key => $value) {
                    $d=strtolower($value);
                    if ($domitem->tag == "a" && $key == "href" && strpos($d, "http") !== 0)
                        $domitem->removeAttribute($key);
                    else if (!in_array($key, $this->allowParams[$domitem->tag]) || strpos($d, 'script') !== false || strpos($d, '&#x') !== false || ($domitem->tag == 'img' && $key == "style" && strpos($d, 'expression') !== false))
                        $domitem->removeAttribute($key);
                }
                if (!empty($domitem->children))
                    $this->dfs($domitem->nodes);
            }
        }
    }

}