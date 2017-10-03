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
class Ves_FormBuilder_Helper_Recaptcha extends Mage_Core_Helper_Abstract {

	/**
	 * @var string $privateKey
	 *
	 * @access protected
	 */
	protected $privateKey = '';

	/**
	 * @var string $publicKey
	 *
	 * @access protected
	 */
	protected $publicKey = '';

	protected $theme = '';
	protected $lang = '';
	protected $call_back = false;

	public function setKeys( $privateKey, $publicKey ){

		$this->privateKey = ( $privateKey );
		$this->publicKey  =( $publicKey );

		return $this;
	}

	public function setTheme( $theme="" ){
		$this->theme = $theme;
		return $this;
	}

	public function setCallBack($call_back = false) {
		$this->call_back = $call_back;
		return $this;
	}

	public function setLang( $lang="" ){
		$this->lang = $lang;
		return $this;
	}

	public function getReCapcha() {
		$reCaptcha = '';
		if( $this->publicKey && $this->privateKey ) {
			$reCaptcha = new Ves_FormBuilder_Lib_Zend_Service_ReCaptcha( $this->publicKey, $this->privateKey );
			if( $this->theme ) {
				$reCaptcha->setOptions(array('theme' => $this->theme) );
			}

			if( $this->lang ) {
				$reCaptcha->setOptions(array('lang' => $this->lang) );
			}
		}

		if($this->call_back) {
			$reCaptcha->setCallBack($this->call_back);
		}
		return $reCaptcha;
	}

	public function isValid( $challengeField, $responseField ) {
		$response = $this->getReCapcha()->verify( $challengeField, $responseField );
		return $response->isValid();
	}
}