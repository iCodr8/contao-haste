<?php

/**
 * Haste utilities for Contao Open Source CMS
 *
 * Copyright (C) 2012-2013 Codefog & terminal42 gmbh
 *
 * @package    Haste
 * @link       http://github.com/codefog/contao-haste/
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace Haste\Model;

abstract class Model extends \Model
{

    /**
     * {@inheritdoc}
     */
    public function getRelated($strKey, array $arrOptions=array())
    {
        $arrRelation = Relations::getRelation(static::$strTable, $strKey);

        if ($arrRelation !== false) {
            $strClass = static::getClassFromTable($arrRelation['related_table']);

            if (class_exists($strClass)) {
                $arrIds = \Database::getInstance()->prepare("SELECT " . $arrRelation['related_field'] . " FROM " . $arrRelation['table'] . " WHERE " . $arrRelation['reference_field'] . "=?")
                                                  ->execute($this->$arrRelation['reference'])
                                                  ->fetchEach($arrRelation['related_field']);

                if (empty($arrIds)) {
                    return null;
                }

                $objModel = $strClass::findBy(array($arrRelation['related_table'] . "." . $arrRelation['field'] . " IN('" . implode("','", $arrIds) . "')"), null, $arrOptions);
                $this->arrRelated[$strKey] = $objModel;
            }
        }

        return parent::getRelated($strKey, $arrOptions);
    }

    /**
     * Get the reference values and return them as array
     * @param string
     * @param string
     * @param mixed
     * @return array
     */
    public static function getReferenceValues($strTable, $strField, $varValue=null)
    {
        $arrRelation = Relations::getRelation($strTable, $strField);

        if ($arrRelation === false) {
            throw new \Exception('Field ' . $strField . ' does not seem to be related!');
        }

        $arrValues = (array) $varValue;

        return \Database::getInstance()->prepare("SELECT " . $arrRelation['reference_field'] . " FROM " . $arrRelation['table'] . (!empty($arrValues) ? (" WHERE " . $arrRelation['related_field'] . " IN ('" . implode("','", $arrValues) . "')") : ""))
                                       ->execute()
                                       ->fetchEach($arrRelation['reference_field']);
    }

    /**
     * Get the related values and return them as array
     * @param string
     * @param string
     * @param mixed
     * @return array
     */
    public static function getRelatedValues($strTable, $strField, $varValue=null)
    {
        $arrRelation = Relations::getRelation($strTable, $strField);

        if ($arrRelation === false) {
            throw new \Exception('Field ' . $strField . ' does not seem to be related!');
        }

        $arrValues = (array) $varValue;

        return \Database::getInstance()->prepare("SELECT " . $arrRelation['related_field'] . " FROM " . $arrRelation['table'] . (!empty($arrValues) ? (" WHERE " . $arrRelation['reference_field'] . " IN ('" . implode("','", $arrValues) . "')") : ""))
                                       ->execute()
                                       ->fetchEach($arrRelation['related_field']);
    }
}
