<?php

use PHPUnit\Framework\TestCase;

require_once './Map2.php';

final class Map2Test extends TestCase
{
    /**
     * Find element with max side size bounded with $boundWidth and $boundHeight.
     * If it needed, element will be turned.
     */
    public function testGetMaxWidthElem()
    {
        // Data
        $bar = ['H' => 250, 'W' => 410];
        $datas = [
            [  'boundWidth' => 15, 'boundHeight' => 14,
                'array' => [
                    ['id' => 0, 'W' => 12, 'H' => 4, 'U' => 0, 'T' => false],
                    ['id' => 1, 'W' => 3, 'H' => 13, 'U' => 0, 'T' => false]]
            ],
            [  'boundWidth' => 15, 'boundHeight' => 10,
                'array' => [
                    ['id' => 0, 'W' => 4, 'H' => 4, 'U' => 0, 'T' => false],
                    ['id' => 1, 'W' => 3, 'H' => 3, 'U' => 0, 'T' => false]]
            ],
            [  'boundWidth' => 15, 'boundHeight' => 15,
                'array' => [
                    ['id' => 0, 'W' => 12, 'H' => 4, 'U' => 0, 'T' => false],
                    ['id' => 1, 'W' => 3, 'H' => 13, 'U' => 0, 'T' => false]]
            ],
            [  'boundWidth' => 15, 'boundHeight' => 10,
                'array' => [
                    ['id' => 0, 'W' => 1, 'H' => 1, 'U' => 0, 'T' => false],
                    ['id' => 1, 'W' => 3, 'H' => 3, 'U' => 0, 'T' => false]]
            ],
            [  'boundWidth' => 15, 'boundHeight' => 10,
                'array' => [
                    ['id' => 0, 'W' => 1, 'H' => 4, 'U' => 0, 'T' => false],
                    ['id' => 1, 'W' => 3, 'H' => 1, 'U' => 0, 'T' => false]]
            ],
            [  'boundWidth' => 5, 'boundHeight' => 1,
                'array' => [
                    ['id' => 0, 'W' => 5, 'H' => 4, 'U' => 0, 'T' => false],
                    ['id' => 1, 'W' => 3, 'H' => 10, 'U' => 0, 'T' => false]]
            ]
        ];
        $result = [1, 0, 1, 1, 0, null];

        // Init
        $class = new ReflectionClass('Cut\Map2');
        $method = $class->getMethod('getMaxWidthElem');
        $method->setAccessible(true);
        $obj = new Cut\Map2($bar);

        // Test
        foreach ($datas as $i => $data) {
            $return = $method->invoke($obj, $data['array'], $data['boundWidth'], $data['boundHeight']);
            $this->assertEquals($result[$i], $return['id']);
        }
    }
}
