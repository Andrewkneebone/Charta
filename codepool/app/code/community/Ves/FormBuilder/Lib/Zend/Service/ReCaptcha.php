<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage ReCaptcha
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** @see Zend_Service_Abstract */
#require_once 'Zend/Service/Abstract.php';

/** @see Zend_Json */
#require_once 'Zend/Json.php';

/** @see Zend_Service_ReCaptcha_Response */
#require_once 'Zend/Service/ReCaptcha/Response.php';

/**
 * Zend_Service_ReCaptcha
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage ReCaptcha
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Ves_FormBuilder_Lib_Zend_Service_ReCaptcha extends Zend_Service_ReCaptcha
{
    protected $_callback = false;

    public function setCallBack($callback_function = false) {
        $this->_callback = $callback_function;
        return $this;
    }
    /**
     * Get the HTML code for the captcha
     *
     * This method uses the public key to fetch a recaptcha form.
     *
     * @param  null|string $name Base name for recaptcha form elements
     * @return string
     * @throws Zend_Service_ReCaptcha_Exception
     */
    public function getHtml($name = null)
    {
        if ($this->_publicKey === null) {
            /** @see Zend_Service_ReCaptcha_Exception */
            #require_once 'Zend/Service/ReCaptcha/Exception.php';

            throw new Zend_Service_ReCaptcha_Exception('Missing public key');
        }

        $host = self::API_SERVER;

        if ((bool) $this->_params['ssl'] === true) {
            $host = self::API_SECURE_SERVER;
        }

        $htmlBreak = '<br>';
        $htmlInputClosing = '>';

        if ((bool) $this->_params['xhtml'] === true) {
            $htmlBreak = '<br />';
            $htmlInputClosing = '/>';
        }

        $errorPart = '';

        if (!empty($this->_params['error'])) {
            $errorPart = '&error=' . urlencode($this->_params['error']);
        }

        $reCaptchaOptions = '';

        if (!empty($this->_options)) {
            $encoded = Zend_Json::encode($this->_options);
            $reCaptchaOptions = <<<SCRIPT
<script type="text/javascript">
    var RecaptchaOptions = {$encoded};
</script>
SCRIPT;
        }
        $challengeField = 'recaptcha_challenge_field';
        $responseField  = 'recaptcha_response_field';
        if (!empty($name)) {
            $challengeField = $name . '[' . $challengeField . ']';
            $responseField  = $name . '[' . $responseField . ']';
        }

        $return = $reCaptchaOptions;

        if($this->_callback) {
        $return .= <<<HTML
<script type="text/javascript"
   src="{$host}.js?onload={$this->_callback}&render=explicit" async defer>
</script>
HTML;
        } else {
        $return .= <<<HTML
<script type="text/javascript"
   src="{$host}/challenge?k={$this->_publicKey}{$errorPart}">
</script>
HTML;
        }
        $return .= <<<HTML
<noscript>
   <iframe src="{$host}/noscript?k={$this->_publicKey}{$errorPart}"
       height="300" width="500" frameborder="0"></iframe>{$htmlBreak}
   <textarea name="{$challengeField}" rows="3" cols="40">
   </textarea>
   <input type="hidden" name="{$responseField}"
       value="manual_challenge"{$htmlInputClosing}
</noscript>
HTML;

        return $return;
    }
}
