<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Form Builder extension
 *
 * @category   Ves
 * @package    Ves_FormBuilder
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_FormBuilder_Model_Import_Modelcategory extends Mage_Core_Model_Abstract {

	private $array_delimiter = ';';
	private $delimiter = ',';
	private $header_columns;

	public function process($filepath, $stores = array()) {
		// get the file extension
		$array = pathinfo($filepath);
		switch ($array["extension"] ) {
			case "csv":
			case "txt":
			$this->importCsv($filepath, $stores);
			break;
			default:
			Mage::throwException("File is of unknown format, cannot process to import");
			break;
 		} // end
	} // end

	private function openFile($filepath) {
		$handle = null;

		if (($handle = fopen($filepath, "r")) !== FALSE) {
			return $handle;
		} else {
			throw new Exception('Error opening file ' . $filepath);
		} // end

	} // end

	public function restoreArray($default_array = array(), $import_array = array()) {
		if(!empty($import_array)) {
			$tmp = array();
			foreach($import_array as $k=>$v) {
				if(in_array($v, $default_array) || $v == 0) {
					$tmp[] = $v;
				}
			}
		}
		if(empty($tmp)) {
			$tmp = array(0);
		}
		return $tmp;

	}
	public function importCsv($filepath, $stores = array()) {

		$handle = $this->openFile($filepath);
		$row = 0;
		if ( $handle != null ) {

			// loop thru all rows
			while (($data = fgetcsv($handle, 110000, $this->delimiter)) !== FALSE) {
				$row++;

				// if this is the head row keep this as a column reference
				if ($row == 1) {
					$this->mapHeader($data);
					continue;
				}

				// make sure we have a reset model object
				//$staticblock = Mage::getSingleton($this->_modelname)->clearInstance();

				$model = Mage::getModel('ves_formbuilder/category');
				$identifier = "";
				// get the identifier column for this row
				if( $id_key = $this->getIdentifierColumnIndex() ) {
					$identifier = $data[$id_key];

					// if a static block already exists for this identifier - load the data
					$model->load($identifier);
				} else {
					$model->load(0);
				}


				// loop through each column
				foreach ($this->header_columns as $index => $keyname) {
					$keyname = strtolower($keyname);
					$keyname = str_replace(" ", "_", $keyname);
					$import_stores = $stores;
					// switch statement incase we need to do logic depending on the column name
					switch ($keyname) {
						case "category_id":
						$identifier = (int)$data[$index];
						break;
						default:
						// fgetcsv encodes everything
						$model->setData($keyname, html_entity_decode($data[$index]));
						break;

					} // end switch
				} // end for

				// save our block
				try {
					$model->save();
					if($identifier) {
						$model->updateId($identifier);
					}
				} catch (Exception $e) {
					Mage::throwException($e->getMessage());
				}
			} // end while
		}// end if

	} // end

	private function mapHeader($data_array) {
		$this->header_columns = $data_array;
	}

	private function getIdentifierColumnIndex() {
		$header = $this->header_columns;
		$index = array_search('Identifier', $header);
		return $index;
	}

}
