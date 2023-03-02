<?php
/**
 * PHP Component
 *
 * @link https://github.com/rufovS
 * @copyright Copyright (c) 2023 RufovS
 * @license https://opensource.org/license/bsd-3-clause/
 */

declare(strict_types=1);

namespace RufovS\ConverterArrayXML;

use Spatie\ArrayToXml\ArrayToXml;
use DOMDocument;
use DOMXPath;
use DOMText;

use function iterator_to_array;
use function array_filter;
use function array_merge;
use function strip_tags;
use function is_array;
use function is_null;
use function count;

/**
 * The class is designed to convert an XML document into a PHP array and vice versa
 *
 * @author Sergej Rufov <me@rufov.ru>
 * @since 1.0
 */
class ConverterArrayXML
{
    /**
     * Internal method for converting to an array
     *
     * @param array $tags The information received from the execution of the DOMDocument class
     * @param DOMXPath $xpath The namespace of the specified tag
     * @return array Converted XML document into an array
     */
    private static function domNodesToArray(array $tags, DOMXPath $xpath): array
    {
        $tagNameToArr = [];

        $preValue = '';
        $preElement = '';
        foreach ($tags as $tag) {
            $tagData = [];
            $attrs = $tag->attributes ? iterator_to_array($tag->attributes) : [];
            $subTags = $tag->childNodes ? iterator_to_array($tag->childNodes) : [];
            foreach ($xpath->query('namespace::*', $tag) as $nsNode) {
                // the only way to get xmlns:*, see https://stackoverflow.com/a/2470433/2750743
                if ($tag->hasAttribute($nsNode->nodeName)) {
                    $attrs[] = $nsNode;
                }
            }

            foreach ($attrs as $attr) {
                $tagData['_attributes'][$attr->nodeName] = $attr->nodeValue;
            }
            if (count($subTags) === 1 && $subTags[0] instanceof DOMText) {
                $text = $subTags[0]->nodeValue;
            } elseif (count($subTags) === 0) {
                $text = '';
            } else {
                // ignore whitespace (and any other text if any) between nodes
                $isNotDomText = function ($node) {
                    return !($node instanceof DOMText);
                };
                $realNodes = array_filter($subTags, $isNotDomText);
                $subTagNameToArr = self::domNodesToArray($realNodes, $xpath);
                $tagData = array_merge($tagData, $subTagNameToArr);
                $text = null;
            }
            if (!is_null($text)) {
                $attr = '_value';
                if ($text != strip_tags($text)) {
                    $attr = '_cdata';
                }

                if ($attrs || $attr == '_cdata') {
                    if ($text) {
                        $tagData[$attr] = $text;
                    }
                } else {
                    $tagData = $text;
                }
            }
            $keyName = $tag->nodeName;

            if (is_array($tagData)) {
                $tagNameToArr[$keyName][] = $tagData;
            } elseif ($keyName == $preElement) {
                if (!is_array($tagNameToArr[$keyName])) {
                    $tagNameToArr[$keyName] = [];
                    $tagNameToArr[$keyName][] = $preValue;
                }
                $tagNameToArr[$keyName][] = $tagData;
            } else {
                $tagNameToArr[$keyName] = $tagData;
            }

            $preElement = $keyName;
            $preValue = $tagData;
        }

        if (isset($tagNameToArr['#comment'])) unset($tagNameToArr['#comment']);

        return $tagNameToArr;
    }

    /**
     * Converts an XML file into an array
     *
     * @param string $xml XML document in the form of a string
     * @return array
     */
    public static function xmlToArr(string $xml): array
    {
        $doc = new DOMDocument();
        $doc->loadXML($xml);
        $xpath = new DOMXPath($doc);
        $tags = $doc->childNodes ? iterator_to_array($doc->childNodes) : [];
        $array = self::domNodesToArray($tags, $xpath);
        foreach ($array as $key => $value) {
            if (is_array($value) && count($value) == 1) {
                $array[$key] = $value[0];
            }
        }
        return $array;
    }

    /**
     * Reverse conversion of an array to an XML document
     *
     * @param array $array The array to be converted
     * @return string
     */
    public static function arrayToXml(array $array): string
    {
        foreach ($array as $key => $value) {
            $_attributes = $value['_attributes'] ?? [];
            unset($value['_attributes']);
            return ArrayToXml::convert($value, [
                'rootElementName' => $key,
                '_attributes' => $_attributes,
            ], true, 'UTF-8');
        }
        return '';
    }
}
