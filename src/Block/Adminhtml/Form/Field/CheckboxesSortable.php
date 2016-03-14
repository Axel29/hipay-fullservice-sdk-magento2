<?php 
/*
 * HiPay fullservice Magento2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @copyright      Copyright (c) 2016 - HiPay
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 *
 */
namespace HiPay\FullserviceMagento\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;

class CheckboxesSortable extends Field
{
	
	/**
     * Add js to sort checkboxes
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		 $javaScript = '
            <script type="text/javascript">
			 	require(["jquery","jquery/ui"], function($){
				    
		 		var options = $("#row_'.$element->getHtmlId().' td.value div.nested div");
		 		options.each(function(){
		 				var input = $(this).find("input").first();
		 				var nameArray = input.attr("name") + \'[]\';
		 				input.attr("name",nameArray);
		 				
		 				var label = $(this).find("label").first();
		 				label.css("cursor","move");
		 				label.attr("for",false);
	
				});
		 		
					$( "#row_'.$element->getHtmlId().' td.value div.nested" ).sortable();
					  	 
				});
            </script>';
		$element->setData('after_element_html',$javaScript.$element->getAfterElementHtml());
		
		return parent::_getElementHtml($element);
	}
	
	
}