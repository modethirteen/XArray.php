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
namespace modethirteen\XArray\Serialization;

use modethirteen\TypeEx\StringEx;
use modethirteen\XArray\IArray;
use modethirteen\XArray\XArray;

class XmlSerializer implements ISerializer {

    /**
     * @var string|null
     */
    private ?string $root = null;

    public function serialize(IArray $array): string {
        return $this->toXml($array, $this->root);
    }

    /**
     * Add a tag to wrap the entire XML document (the root document element)
     *
     * @note this tag is useful for serializing valid XML if the array has no root element itself
     * @param string $tag
     * @return static
     */
    public function withRootElement(string $tag) : object {
        $instance = clone $this;
        $instance->root = $tag;
        return $instance;
    }

    /**
     * Return the array as an XML string
     *
     * @param IArray $array
     * @param string|null $outer - optional output tag, used for recursion
     * @return string - xml string representation of the array
     */
    private function toXml(IArray $array, string $outer = null) : string {
        $result = '';
        foreach($array->toArray() as $key => $value) {
            $key = StringEx::stringify($key);

            /** @noinspection PhpStatementHasEmptyBodyInspection */
            if(strncmp($key, '@', 1) === 0) {

                // skip attributes
            } else {
                $encodedTag = htmlspecialchars($outer ? $outer : $key, ENT_QUOTES);
                if(is_array($value) && (count($value) > 0) && isset($value[0])) {

                    // numeric array found => child nodes
                    $result .= (new XArray($value))
                        ->withSerializer((new XmlSerializer())->withRootElement($key))
                        ->toString();
                } else {
                    if(is_array($value)) {

                        // attribute list found
                        $attrs = '';
                        foreach($value as $attrKey => $attrValue) {
                            $attrKey = StringEx::stringify($attrKey);
                            if(strncmp($attrKey, '@', 1) === 0) {
                                $attrValue = StringEx::stringify($attrValue);
                                $attrs .= ' ' . htmlspecialchars(substr($attrKey, 1), ENT_QUOTES) . '="' . htmlspecialchars($attrValue, ENT_QUOTES) . '"';
                            }
                        }
                        $result .= "<{$encodedTag}{$attrs}>" . (new XArray($value))->withSerializer(new XmlSerializer())->toString() . "</{$encodedTag}>";
                    } else {
                        $value = StringEx::stringify($value);
                        if($encodedTag !== '#text') {
                            $result .= "<{$encodedTag}>" . htmlspecialchars($value, ENT_QUOTES) . "</{$encodedTag}>";
                        } else {
                            $result .= htmlspecialchars($value, ENT_QUOTES);
                        }
                    }
                }
            }
        }
        return $result;
    }
}