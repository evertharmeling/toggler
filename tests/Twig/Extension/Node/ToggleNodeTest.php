<?php

/*
 * This file is part of CSBill project.
 *
 * (c) 2013-2015 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Toggler\Twig\Extension\Node;

use Toggler\Twig\Node\ToggleNode;

if (class_exists('Twig_Test_NodeTestCase')) {
    class ToggleNodeTest extends \Twig_Test_NodeTestCase
    {
        public function testConstructor()
        {
            $t = new \Twig_Node([
                new \Twig_Node_Expression_Constant(true, 1),
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], 1);
            $else = null;
            $node = new ToggleNode('foo', $t, $else, null, 1);

            $this->assertEquals($t, $node->getNode('body'));
            $this->assertEquals('foo', $node->getNode('feature'));
            $this->assertNull($node->getNode('else'));

            $else = new \Twig_Node_Print(new \Twig_Node_Expression_Name('bar', 1), 1);
            $node = new ToggleNode('bar', $t, $else, null, 1);
            $this->assertEquals($else, $node->getNode('else'));
        }

        public function getTests()
        {
            $tests = [];

            $tests[] = $this->getToggleTest();
            $tests[] = $this->getToggleWithElseTest();
            $tests[] = $this->getToggleWithContextTest();

            return $tests;
        }

        /**
         * @return array
         */
        private function getToggleTest()
        {
            $t = new \Twig_Node([
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], null, 1);
            $else = null;
            $node = new ToggleNode(new \Twig_Node([new \Twig_Node_Expression_Constant('foo', 1)]), $t, $else, null, 1);

            return [
                $node,
                <<<EOF
// line 1
if (toggle("foo")) {
    echo {$this->getVariableGetter('foo')};
}
EOF
            ];
        }

        /**
         * @return array
         */
        private function getToggleWithElseTest()
        {
            $t = new \Twig_Node([
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], null, 1);
            $else = new \Twig_Node_Print(new \Twig_Node_Expression_Name('bar', 1), 1);
            $node = new ToggleNode(new \Twig_Node([new \Twig_Node_Expression_Constant('foo', 1)]), $t, $else, null, 1);

            return [
                $node,
                <<<EOF
// line 1
if (toggle("foo")) {
    echo {$this->getVariableGetter('foo')};
} else {
    echo {$this->getVariableGetter('bar')};
}
EOF
            ];
        }

        /**
         * @return array
         */
        private function getToggleWithContextTest()
        {
            $t = new \Twig_Node([
                new \Twig_Node_Print(new \Twig_Node_Expression_Name('foo', 1), 1),
            ], [], null, 1);

            $node = new ToggleNode(new \Twig_Node([new \Twig_Node_Expression_Constant('foo', 1)]),
                $t,
                null,
                new \Twig_Node_Expression_Array([new \Twig_Node_Expression_Constant('value1', 1), new \Twig_Node_Expression_Constant(12, 1)], 1),
                1
            );

            return [
                $node,
                <<<EOF
// line 1
if (toggle("foo", array("value1" => 12))) {
    echo {$this->getVariableGetter('foo')};
}
EOF
            ];
        }
    }
}