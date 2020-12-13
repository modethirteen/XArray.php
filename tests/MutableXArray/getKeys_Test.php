<?php declare(strict_types=1);
/**
 * XArray
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace modethirteen\XArray\Tests\MutableXArray;

use modethirteen\XArray\MutableXArray;

class getKeys_Test extends \modethirteen\XArray\Tests\XArrayBase\getKeys_Test  {

    /**
     * @var string
     */
    protected static string $class = MutableXArray::class;

    /**
     * @test
     */
    public function Can_get_keys_if_source_array_is_mutated() : void {

        // arrange
        $source = [
            'foo' => [
                'bar' => 'baz'
            ]
        ];
        $x = new MutableXArray($source);

        // act
        $source['qux'] = ['plugh' => 'xyzzy'];
        $result = $x->getKeys();

        // assert
        static::assertEquals([
            'foo',
            'foo/bar',
            'qux',
            'qux/plugh'
        ], $result);
    }
}