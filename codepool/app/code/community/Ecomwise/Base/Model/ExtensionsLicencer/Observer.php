<?php 
class Ecomwise_Base_Model_ExtensionsLicencer_Observer {

	const EXTENSION_USE_HTTPS_PATH = 'base/licencer/use_https';
	const EXTENSION_LICENCER_URL_PATH = 'base/licencer/url';
	const EXTENSION_FREQUENCY_PATH = 'base/licencer/check_frequency';

	public function checkExtensionLicence(){
		if(($this->getFrequency() + $this->getLastUpdate()) > time()){
			return $this;
		}

		$domain = parse_url(Mage::getStoreConfig(Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL));
		$skipped_extensions = array();
		$extensions = $this->getInstalledExtensions();
		if($extensions){
			foreach($extensions as $extension => $extension_data){
				if(in_array($extension, $skipped_extensions)){
					continue;
				}

				$license_number = Mage::getStoreConfig($extension_data["code"] . "/settings/serial");
				$response = $this->getServerResponse($extension_data["code"], $domain["host"], $license_number);
				$this->setExtensionOutput($extension, $response);
				$this->setExtensionStatus($extension_data["code"], $response);
				$this->setExtensionWarning($extension_data["code"], $response);

				if($extension == "Ecomwise_B2BSuite"){
					if($extension_data["current_output_status"] == "" || $extension_data["current_output_status"] == "0"){
						if(isset($response["code"]) && $response["code"] == "100"){
							Mage::app()->getStore()->resetConfig();
							$reloaded_extensions = $this->getInstalledExtensions();
							if(!empty($reloaded_extensions)){
								$extensions_part_of_b2b_suite = Mage::helper("b2bsuite")->getExtensionsPartOfB2BSuite();
								if(is_array($extensions_part_of_b2b_suite) && !empty($extensions_part_of_b2b_suite)){
									foreach($reloaded_extensions as $reloaded_extension => $reloaded_extension_data){
										if(in_array($reloaded_extension, $extensions_part_of_b2b_suite)){
											$license_number = Mage::getStoreConfig($reloaded_extension_data["code"] . "/settings/serial");
											$response = $this->getServerResponse($reloaded_extension_data["code"], $domain["host"], $license_number);
											$this->setExtensionOutput($reloaded_extension, $response);
											$this->setExtensionStatus($reloaded_extension_data["code"], $response);
											$this->setExtensionWarning($reloaded_extension_data["code"], $response);
										}
									}
								}
							}
						}
					}else if($extension_data["current_output_status"] == "1"){
						if(isset($response["code"]) && $response["code"] == "200"){
							$extensions_part_of_b2b_suite = Mage::helper("b2bsuite")->getExtensionsPartOfB2BSuite();
							if(is_array($extensions_part_of_b2b_suite) && !empty($extensions_part_of_b2b_suite)){
								foreach($extensions as $extension => $extension_data){
									if(in_array($extension, $extensions_part_of_b2b_suite)){
										$skipped_extensions[] = $extension;
										$response = array("code" => "200", "message" => "");
										$this->setExtensionOutput($extension, $response);
										$this->setExtensionStatus($extension_data["code"], $response);
										$this->setExtensionWarning($extension_data["code"], $response);
									}
								}
							}
						}
					}
				}
			}
		}

		Mage::app()->getStore()->resetConfig();
		$this->setLastUpdate();

		return $this;
	}

	public function showAdminMessages(){
		$disabled_extensions = array();
		$warned_extensions = array();

		$this->checkExtensionLicence();
		$extensions = $this->getInstalledExtensions();
		if($extensions){
			foreach($extensions as $extension => $extension_data){
				$status = Mage::getStoreConfig($extension_data["code"] . '/status/valid');
				if($status == "disable"){
					$disabled_extensions[] = ucfirst($extension_data["code"]);
				}

				$warning = Mage::getStoreConfig($extension_data["code"] . '/status/warning');
				if($warning != "" && $warning != NULL){
					$warned_extensions[ucfirst($extension_data["code"])] = $warning;
				}
			}
		}

		if(!empty($warned_extensions)){
			if(count($warned_extensions) > 1){
				$text1 = array();
				$text2 = array();
				if(count($warned_extensions) == 2){
					foreach($warned_extensions as $key => $value){
						$text1[] = $key;
						$text2[] = $key . " extension will be disabled in " . $value;
					}
					$text1 = implode(" and ", $text1);
					$text2 = implode(" and the ", $text2);
				}else{
					foreach($warned_extensions as $key => $value){
						$text1[] = $key;
						$text2[] = $key . " extension will be disabled in " . $value;
					}
					$last_element = array_pop($text1);
					$text1 = implode(", ", $text1) . " and " . $last_element;
					$last_element = array_pop($text2);
					$text2 = implode(", the ", $text2) . " and the " . $last_element;
				}

				$message = Mage::Helper("base")->__("Please insert the license key for the " . $text1 . " extensions. The " . $text2 . ".");
			}else{
				$text1 = min(array_keys($warned_extensions));
				$text2 = $warned_extensions[$text1];
				$message = Mage::Helper("base")->__("Please insert the license key for the " . $text1 . " extension. The extension will be disabled in " . $text2 . ".");
			}

			Mage::getSingleton('adminhtml/session')->addError($message);
		}

		if(!empty($disabled_extensions)){
			if(count($disabled_extensions) > 1){
				$text = "";
				if(count($disabled_extensions) == 2){
					$text = implode(" and ", $disabled_extensions);
				}else{
					$last_element = array_pop($disabled_extensions);
					$text = implode(", ", $disabled_extensions) . " and " . $last_element;
				}
				$message = Mage::Helper("base")->__("The " . $text . " extensions are disabled. Please insert the license key.");
			}else{
				$text = $disabled_extensions[0];
				$message = Mage::Helper("base")->__("The " . $text . " extension is disabled. Please insert the license key.");
			}

			Mage::getSingleton('adminhtml/session')->addError($message);
		}

		return $this;
	}

	private function getInstalledExtensions(){
		$ecomwise_extensions = array();
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		sort($modules);
		foreach($modules as $extension){
			if($extension != "Ecomwise_Base" && strstr($extension, 'Ecomwise_') !== false){
				$is_module_enable = Mage::helper('core')->isModuleEnabled($extension);
				if($is_module_enable){
					$extensionName = explode("_", $extension);
					$ecomwise_extensions[$extension] = array("code" => strtolower($extensionName[1]), "current_output_status" => Mage::getStoreConfig('advanced/modules_disable_output/' . $extension));
				}
			}
		}

		$ecomwise_extensions = $this->excludeB2BSuiteExtensions($ecomwise_extensions);

		return $ecomwise_extensions;
	}

	private function excludeB2BSuiteExtensions($ecomwise_extensions){
		if(!empty($ecomwise_extensions) && array_key_exists("Ecomwise_B2BSuite", $ecomwise_extensions)){
			if($ecomwise_extensions["Ecomwise_B2BSuite"]["current_output_status"] == "" || $ecomwise_extensions["Ecomwise_B2BSuite"]["current_output_status"] == "0"){
				$extensions_part_of_b2b_suite = Mage::helper("b2bsuite")->getExtensionsPartOfB2BSuite();
				if(is_array($extensions_part_of_b2b_suite) && !empty($extensions_part_of_b2b_suite)){
					reset($ecomwise_extensions);
					$first_key = key($ecomwise_extensions);
					if($first_key != "Ecomwise_B2BSuite"){
						$ecomwise_b2b_suite_extension = $ecomwise_extensions["Ecomwise_B2BSuite"];
						unset($ecomwise_extensions["Ecomwise_B2BSuite"]);
						$ecomwise_extensions = array_merge(array("Ecomwise_B2BSuite" => $ecomwise_b2b_suite_extension), $ecomwise_extensions);
					}

					foreach($ecomwise_extensions as $extension => $extension_data){
						if(in_array($extension, $extensions_part_of_b2b_suite)){
							unset($ecomwise_extensions[$extension]);
						}
					}
				}
			}
		}

		return $ecomwise_extensions;
	}

	private function getLicenserUrl(){
		$licencer_url = (Mage::getStoreConfigFlag(self::EXTENSION_USE_HTTPS_PATH) ? 'https://' : 'http://') . Mage::getStoreConfig(self::EXTENSION_LICENCER_URL_PATH);
		return $licencer_url;
	}

	private function getServerResponse($extension, $domain, $license_number){
		$curl = new Varien_Http_Adapter_Curl();
		$curl->setConfig(array('timeout' => 2));

		$ip_address = $this->getIpAddress();
		$base_version = Mage::getConfig()->getModuleConfig("Ecomwise_Base")->version;
		$parametars = "extension/$extension/domain/$domain/ip_address/$ip_address/licence_number/$license_number/base_version/$base_version";

		$curl->write(Zend_Http_Client::GET, $this->getLicenserUrl() . $parametars, '1.0');
		$data = $curl->read();
		if ($data === false){
			return array("code" => "503", "message" => "");
		}

		$data = preg_split('/^\r?$/m', $data, 2);
		$data = str_replace("\n", "", trim($data[1]));
		$curl->close();

		return json_decode($data, true);
	}

	private function setExtensionOutput($extension, $response){
		$response = isset($response["code"]) && $response["code"] == "100" ? "1" : "0";
		Mage::getConfig()->saveConfig('advanced/modules_disable_output/' . $extension, $response);
		return $this;
	}

	private function setExtensionStatus($extension, $response){
		$response = isset($response["code"]) && $response["code"] == "100" ? "disable" : "active";
		Mage::getConfig()->saveConfig($extension . '/status/valid', $response);
		return $this;
	}

	private function setExtensionWarning($extension, $response){
		$response = isset($response["code"]) && $response["code"] == "500" ? $response["message"] : "";
		Mage::getConfig()->saveConfig($extension . '/status/warning', $response);
		return $this;
	}

	private function getIpAddress(){
		return isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : "";
	}

	private function getFrequency(){
		return Mage::getStoreConfig(self::EXTENSION_FREQUENCY_PATH);
	}

	private function getLastUpdate(){
		return Mage::app()->loadCache('base_extensions_lastcheck');
	}

	private function setLastUpdate(){
		Mage::app()->saveCache(time(), 'base_extensions_lastcheck');
		return $this;
	}
}