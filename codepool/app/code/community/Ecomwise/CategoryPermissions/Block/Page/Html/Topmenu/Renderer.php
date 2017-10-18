<?php
class Ecomwise_CategoryPermissions_Block_Page_Html_Topmenu_Renderer extends Mage_Page_Block_Html_Topmenu_Renderer
{
	protected function _toHtml()
	{		
		$this->_addCacheTags();
		$menuTree = $this->getMenuTree();
		
		
		$arrr = $menuTree->getAllChildNodes();
		$menuTree = $this->removeNotAllowed($arrr, $menuTree);		
				
		
		$childrenWrapClass = $this->getChildrenWrapClass();
		if (!$this->getTemplate() || is_null($menuTree) || is_null($childrenWrapClass)) {
			throw new Exception("Top-menu renderer isn't fully configured.");
		}
	
		$includeFilePath = realpath(Mage::getBaseDir('design') . DS . $this->getTemplateFile());
		if (strpos($includeFilePath, realpath(Mage::getBaseDir('design'))) === 0 || $this->_getAllowSymlinks()) {
			$this->_templateFile = $includeFilePath;
		} else {
			throw new Exception('Not valid template file:' . $this->_templateFile);
		}
		return $this->render($menuTree, $childrenWrapClass);
	}
	
	public function removeNotAllowed($arrr, $menuTree){
		foreach($arrr as $key => $category){			
			$catId = str_replace("category-node-","",$category->getId());		
			//$cat = Mage::getModel('catalog/category')->loadByAttribute('name', $category->getData('name'));					
			$cat = Mage::getModel('catalog/category')->load($catId);			
			$array = $category->getAllChildNodes();						
			if(!empty($array)){
				$this->removeNotAllowed($array, $category);
			}
			if(!Mage::helper('categorypermissions/data')->customerIsAllowed($cat)){					
				$menuTree->removeChild($category);
			}									
		}		
		return $menuTree;
	}
	
} 