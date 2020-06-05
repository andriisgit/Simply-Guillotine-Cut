<?php
namespace Cut;
class Build
{
    public static function getHtml($data)
    {
        $html = '';
        $style = '<style type="text/css">.cut-map-element{position:absolute;border: 2px dotted black}';
        $style .= 'output[name="cut-map-result-html"]{position:relative;display:block;width:' . $data['bar']['W'] .'px;height:' . $data['bar']['H'] .  'px;background-color:silver}';
        foreach ($data['map'] as $id => $el) {
            if ($el['U'] != 0) {
                $style .= '.cut-map-result-element-' . $id . '{top:' . $el['Y'] . 'px;left:' . $el['X'] . 'px;height:' . $el['H'] . 'px;width:' . $el['W'] . 'px;background-color:' . static::getRandomColor() . '}';
                $html .= '<div class="cut-map-element cut-map-result-element-' . $id . '" title="Element ' . ($id + 1) . '"></div>';
            }
        }
        $style .= '</style>';

        return $style . $html;
    }

    public static function getSizeErrHtml($id)
    {
        $html = '<div>Element\'s <span style="font-weight:bold;color:red;">' . (++$id) . '</span> size should be corrected.</div>';
        
        return $html;
    }

    public static function getBarErrHtml()
    {
        $html = '<div>Bar size should be corrected.</div>';
        
        return $html;
    }

    protected static function getRandomColor()
    {
        $colors =[
            'aqua',
            'blue',
            'darkred',
            'deepskyblue',
            'fuchsia',
            'green',
            'greenyellow',
            'gray',
            'indigo',
            'lime',
            'maroon',
            'navy',
            'orange',
            'orangered',
            'olive',
            'pink',
            'purple',
            'red',
            'teal',
            'violet',
            'yellow',
            'white'
        ];
        $i = rand(0, (count($colors) - 1));
        return $colors[$i];
    }
}