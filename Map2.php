<?php

namespace Cut;

class Map2
{

    private $bar = [],           // size of bar to be cut
            $elements = [];      // sizes of elements


    /**
     * @param array <string, int>
     */
    public function __construct($bar)
    {
        if ($this->checkBarSize($bar)) {
            if ($bar['W'] < $bar['H']) {
                $w = $bar['W'];
                $bar['W'] = $bar['H'];
                $bar['H'] = $w;
                unset($w);
            }
            $this->bar = $bar;
        }
        unset($bar);
    }


    /**
     * @param string elements sizes to be cut
     * @return int|bool|array $map<int,int,int,int,int> of left top coordinates, sizes and ids
     */
    public function mapIt($elements)
    {
        if (empty($this->bar)) {
            // bar size error
            return false;
        }
        $this->prepareElements($elements);
        if (true !== ($id = $this->checkElementsSize())) {
            // if an number returned- was returned the id of element bigger than the bar
            return $id;
        }

        return $this->fill();

    }


    /**
     * Fill the Bar with Elements.
     * During the filling the array Map will be filled with left top coordinates and ids
     */
    private function fill()
    {
        $occupiedWidth = 0;
        $curWidth = $this->bar['W'];
        $curHeight = $this->bar['H'];
        $elements = $this->elements;

        while ($this->countUsedElements($elements) < count($elements) || $curWidth <= $this->bar['W']) {

            // put first element
            $maxWEl = $this->getMaxWidthElem($elements, $curWidth, $curHeight);
            if (is_null($maxWEl)) {
                break;
            }
            $elId = $maxWEl['id'];
            if ($maxWEl['T']) {
                $elements[$elId]['T'] = $maxWEl['T'];
                $elements[$elId]['W'] = $maxWEl['W'];
                $elements[$elId]['H'] = $maxWEl['H'];
                $this->elements[$elId] = $elements[$elId];
            }
            $curHeight = $this->bar['H'] - $this->getMaxHeightOfElems([['id' => $elId]]);
            $curWidth = $elements[$elId]['W'];
            $occupiedWidth += $curWidth;

            $this->checkInElements([[
                'id' => $elId,
                'X' => ($occupiedWidth - $elements[$elId]['W']),
                'Y' => ($this->bar['H'] - $curHeight - $elements[$elId]['H']),
                'T' => $elements[$elId]['T']
            ]]);
            $elements = $this->checkInElements([[
                            'id' => $elId,
                            'X' => ($occupiedWidth - $elements[$elId]['W']),
                            'Y' => ($this->bar['H'] - $curHeight - $elements[$elId]['H']),
                            'T' => $elements[$elId]['T']
                        ]], $elements);

            // find elements to put under the top el
            do {
                $elementsL = $elements;
                $elementsT = $elements;
                $line = $this->fillLine(
                    $curWidth,
                    $curHeight,
                    ($occupiedWidth - $elements[$elId]['W']),
                    ($this->bar['H'] - $curHeight),
                    $elementsL
                );
                $lineT = $this->fillLineTurning(
                    $curWidth,
                    $curHeight,
                    ($occupiedWidth - $elements[$elId]['W']),
                    ($this->bar['H'] - $curHeight),
                    $elementsT
                );
                if (!is_null($line) && !is_null($lineT)) {
                    $line = $this->compareOcuped($curWidth, $line, $lineT);
                }
                if (is_null($line) && !is_null($lineT)) {
                    $line = $lineT;
                }
                /*
                if (!is_null($line) && is_null($lineT)) {
                    $line = $line;                                          // the most ingenious, important part :-)
                }
                */
                if (is_null($line) && is_null($lineT)) {
                    $curHeight = $this->bar['H'];
                    $curWidth = $this->bar['W'] - $occupiedWidth;
                    break;
                }
                $this->checkInElements($line);
                $elements = $this->checkInElements($line, $elements);
                $curLineHeight = $this->getMaxHeightOfElems($line);
                $curHeight -= $curLineHeight;

            } while (
                ($this->countUsedElements() <= count($elements))          // unused element is exists
                || ($curHeight <= 0)                                      // height size is available
                || !is_null($line)                                        // found place for the element
            );

        }
        return $elements;
    }


    /**
     * @param $maxWidth int
     * @param $maxHeight int
     * @param $posX int
     * @param $posY int
     * @param $elements array
     * @return null|array of elements suitable to compact into the line bounded $maxWidth $maxHeight
     */
    private function fillLine($maxWidth, $maxHeight, $posX, $posY, $elements)
    {
        $curWidth = $maxWidth;
        $variant = 0;
        $usedElements = []; // 'variant' => [ 'id', 'T', 'W', 'H' ]
        $count = count($elements);

        for ($i = 0; $i < $count; $i++) {
            if ($curWidth <= 0) {
                $variant++;
                break;
            }
            if (
                $elements[$i]['U'] == 0
                && (empty($usedElements) || !in_array($i, $usedElements[$variant]))
                && $elements[$i]['H'] <= $maxHeight
                && $elements[$i]['W'] <= $curWidth
            ) {

                $curWidth -= $elements[$i]['W'];
                $elements[$i]['U'] = 1;
                $usedElements[$variant][] = [
                    'id' => $i,
                    'T' => $elements[$i]['T'],
                    'W' => $elements[$i]['W'],
                    'H' => $elements[$i]['H'],
                    'X' => $posX,
                    'Y' => $posY,
                    'U' => 1
                ];

                $posX += $elements[$i]['W'];

            }
        }

        if (!empty($usedElements)) {
            return $usedElements[0];
        } else {
            return null;
        }

    }

    /**
     * @param $maxWidth int
     * @param $maxHeight int
     * @param $posX int
     * @param $posY int
     * @param $elements array
     * @return null|array of elements suitable to compact into the line bounded $maxWidth $maxHeight
     */
    protected function fillLineTurning($maxWidth, $maxHeight, $posX, $posY, $elements)
    {
        $usedElements = [];
        $curWidth = $maxWidth;
        $countEl = count($elements);

        do {

            $maxWEl = $this->getMaxWidthElem($elements, $curWidth, $maxHeight);
            if (!is_null($maxWEl)) {

                $id = $maxWEl['id'];
                $elements[$id]['U'] = 1;
                $elements[$id]['W'] = $maxWEl['W'];
                $elements[$id]['H'] = $maxWEl['H'];
                $elements[$id]['T'] = $maxWEl['T'];

                $curWidth -= $elements[$id]['W'];
                $usedElements[] = [
                    'id' => $id,
                    'W' => $elements[$id]['W'],
                    'H' => $elements[$id]['H'],
                    'T' => $elements[$id]['T'],
                    'X' => $posX,
                    'Y' => $posY,
                    'U' => 1
                ];
                $posX += $elements[$id]['W'];
            }


        } while ($this->countUsedElements($usedElements) <= $countEl && !is_null($maxWEl) && $curWidth >= 0);

        if (empty($usedElements)) {
            return null;
        }
        return $usedElements;

    }


    /**
     * @param $width0 int
     * @param $line1 array [id, T, W, H]
     * @param $line2 array [id, T, W, H]
     * @return array
     * @todo refactor, improve
     */
    private function compareOcuped($width0, $line1, $line2)
    {
        $heightSum1 = $heightSum2 = $widthSum1 = $widthSum2 = $heightMax1 = $heightMax2 = $select = 0;
        foreach ($line1 as $line) {
            $heightSum1 += $line['H'];
            $widthSum1 += $line['W'];
            if ($heightMax1 <= $line['H']) $heightMax1 = $line['H'];
        }
        foreach ($line2 as $line) {
            $heightSum2 += $line['H'];
            $widthSum2 += $line['W'];
            if ($heightMax2 <= $line['H']) $heightMax2 = $line['H'];
        }
        $heightAv1 = $heightSum1 / count($line1);
        $heightAv2 = $heightSum2 / count($line2);

        $velvet1 = $heightMax1 / $heightAv1 + $width0 / $widthSum1;
        $velvet2 = $heightMax2 / $heightAv2 + $width0 / $widthSum2;

        if ($velvet1 == $velvet2) {
            if (count($line1) <= count($line2)) {
                $select = 1;
            } else {
                $select = 2;
            }
        }

        if ($velvet1 < $velvet2) {
            $select = 1;
        }
        if ($velvet1 > $velvet2) {
            $select = 2;
        }

        if ($select == 1) {
            // Turn back elements, turned during fillLineTurning
            foreach ($line2 as $line) {
                if ($line['T'] == true) {
                    $elWidth = $this->elements[$line['id']]['W'];
                    $this->elements[$line['id']]['W'] = $this->elements[$line['id']]['H'];
                    $this->elements[$line['id']]['H'] = $elWidth;
                    $this->elements[$line['id']]['T'] = false;
                }
            }

            return $line1;
        }
        if ($select == 2) {
            return $line2;
        }

    }


    /**
     * Just checkin the used element
     * @param $usedElements array having 'id' of element
     * @param $elements array of elements if omitted, will be used global
     * @return array $elements-array or nothing if elements was omitted
     */
    private function checkInElements($usedElements, $elements = null)
    {
        if (is_null($elements)) {
            foreach ($usedElements as $el) {
                $this->elements[$el['id']]['U'] = 1;
                if (isset($el['X'])) {
                    $this->elements[$el['id']]['X'] = $el['X'];
                }
                if (isset($el['Y'])) {
                    $this->elements[$el['id']]['Y'] = $el['Y'];
                }
                if (isset($el['T'])) {
                    $this->elements[$el['id']]['T'] = $el['T'];
                }
                if (isset($el['W'])) {
                    $this->elements[$el['id']]['W'] = $el['W'];
                }
                if (isset($el['H'])) {
                    $this->elements[$el['id']]['H'] = $el['H'];
                }
            }
        } else {
            foreach ($usedElements as $el) {
                $elements[$el['id']]['U'] = 1;
                if (isset($el['X'])) {
                    $elements[$el['id']]['X'] = $el['X'];
                }
                if (isset($el['Y'])) {
                    $elements[$el['id']]['Y'] = $el['Y'];
                }
                if (isset($el['T'])) {
                    $elements[$el['id']]['T'] = $el['T'];
                }
                if (isset($el['W'])) {
                    $elements[$el['id']]['W'] = $el['W'];
                }
                if (isset($el['H'])) {
                    $elements[$el['id']]['H'] = $el['H'];
                }
            }
            return $elements;
        }
    }


    /**
     * @param $elements array of IDs
     * @return int max height of elements from $elements
     */
    private function getMaxHeightOfElems($elements)
    {
        $maxHeight = 0;
        foreach ($elements as $item) {
            if ($this->elements[$item['id']]['H'] > $maxHeight) {
                $maxHeight = $this->elements[$item['id']]['H'];
            }
        }
        return $maxHeight;
    }


    /**
     * Find element with max side size bounded with $boundWidth and $boundHeight.
     * If it needed, element will be turned.
     * @param $elements array
     * @param $boundWidth int
     * @param $boundHeight int
     * @return array|null
     */
    private function getMaxWidthElem($elements, $boundWidth, $boundHeight)
    {
        $id = null;
        $max = $elWidth = 0;
        $turned = [];
        foreach ($elements as $i => $element) {
            if ($element['U'] == 0) {

                if ($element['W'] >= $element['H']) {

                    if ($element['W'] <= $boundWidth && $element['H'] <= $boundHeight) {
                        $elWidth = $element['W'];
                    } elseif ($element['H'] <= $boundWidth && $element['W'] <= $boundHeight) {
                        $elWidth = $element['H'];
                        $turned[] = $i;
                    } else {
                        $elWidth = false;
                    }

                }

                if ($element['W'] < $element['H']) {

                    if ($element['H'] <= $boundWidth && $element['W'] <= $boundHeight) {
                        $elWidth = $element['H'];
                        $turned[] = $i;
                    } elseif ($element['W'] <= $boundWidth && $element['H'] <= $boundHeight) {
                        $elWidth = $element['W'];
                    } else {
                        $elWidth = false;
                    }

                }


                if ($elWidth && $elWidth > $max) {
                    $max = $elWidth;
                    $id = $i;
                }

            }

        }

        if (is_null($id)) {
            return null;
        }
        if (in_array($id, $turned)) {
            $elements[$id]['H'] = $elements[$id]['W'];
            $elements[$id]['W'] = $max;
            $elements[$id]['T'] = true;
        }
        return [
            'id' => $id,
            'W' => $elements[$id]['W'],
            'H' => $elements[$id]['H'],
            'T' => $elements[$id]['T'],
            'U' => $elements[$id]['U']
        ];
    }


    /**
     * @param array <string, int> ['W' => 10, 'H' => 20]
     * @return bool
     */
    private function checkBarSize($bar)
    {
        if (
            !isset($bar['H'])
            || !isset($bar['W'])
            || $bar['H'] <= 0
            || $bar['W'] <= 0
            || !is_numeric($bar['H'])
            || !is_numeric($bar['W'])
        ) {
            return false;
        }
        return true;
    }


    /**
     * Check if size of element does not exceed size of bar
     * @return true|int
     */
    private function checkElementsSize()
    {
        foreach ($this->elements as $id => $element) {
            if ( (!isset($element['H']) || !isset($element['W']))
                || ($element['H'] == 0 || $element['W'] == 0)
                || (!is_numeric($element['H']) || !is_numeric($element['W']))
                || ($this->bar['H'] < $element['H'] && $this->bar['W'] < $element['H'])
                || ($this->bar['H'] < $element['H'] && $this->bar['W'] > $element['H'] && $this->bar['H'] < $element['W'])
                || ($this->bar['H'] < $element['W'] && $this->bar['W'] > $element['W'] && $this->bar['H'] < $element['H'])
            ) {
                return $id;
            }
        }
        return true;
    }


    /**
     * Add additional fields to Elements
     * @param string
     */
    private function prepareElements($data)
    {
        $sizes = explode(',', $data);

        foreach ($sizes as $el) {
            $size = explode('*', $el);
            $h = trim($size[0]);
            $w = trim($size[1]);
            $this->elements[] = [
                'H' => $h,
                'W' => $w,
                //'S' => ($h * $w), // unused
                'T' => false,       // is turned
                'U' => 0,           // is used
                'X' => null,        // css position
                'Y' => null         // css position
            ];
        }

    }


    /**
     * Count used Elements
     * @param array if omitted, will be used global array of elements
     * @return int
     */
    public function countUsedElements($elements = null)
    {
        if (is_null($elements)) {
            $elements = $this->elements;
        }
        $used = 0;
        foreach ($elements as $element) {
            if ($element['U'] != 0) {
                $used++;
            }
        }
        return $used;
    }

}