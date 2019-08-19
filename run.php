<?php
declare(strict_types=1);
		
/**
 * ---------------------------------------------------------------------------------------
 * NOTICE: 
 * This a mini program to crawl news data from an online newspaper.
 * It is just a demo of a programming class. Nothing will be re-posted to another site.
 * ---------------------------------------------------------------------------------------
 * Author: Sang Truong <sangtruong115@gmail.com>
 * ---------------------------------------------------------------------------------------
 */

date_default_timezone_set("Asia/Bangkok");

// Set timeout
set_time_limit(-1);

if (!class_exists("StringHelper")) include_once("app/Common/Helpers/StringHelper.php");
if (!class_exists("CrawlNewsService")) include_once("app/Services/CrawlNewsService.php");

try {
	echo StringHelper::getCommonString(StringHelper::TYPE_WELCOME_STRING);
	echo StringHelper::getCommonString(StringHelper::TYPE_USAGE_STRING);

	$goNextMessage = "Do you want to try this? (Y/N)";
	$goNext = StringHelper::readStringFromInput($goNextMessage);
	if (strtoupper($goNext) == "Y") {

		$inputMessage = "Please enter an URL of news
			NOW support crawling new data from thesaigontimes.vn, vnexpress.net, tuoitre.vn
				- E.g.: https://www.thesaigontimes.vn/121624/Cuoc-cach-mang-dau-khi-da-phien.html
				- E.g.: https://vnexpress.net/the-gioi/nguoi-bieu-tinh-hong-kong-tuan-hanh-duoi-mua-3969363.html
				- E.g.: https://tuoitre.vn/mua-tuu-truong-cua-ba-20190818110210021.htm
		";
		$inputUrl = StringHelper::readStringFromInput($inputMessage);
		StringHelper::printString($inputUrl, "You input URL");

		$url = StringHelper::checkUrl($inputUrl);
		if (!empty($url)) {
			echo StringHelper::getCommonString(StringHelper::TYPE_PROCESSING_STRING);

			/**
			 * Call service to export data from URL
			 *
			 * @author Sang Truong <sangtruong115@gmail.com>
			 */
			$crawlNewsService = new CrawlNewsService($url);
	    	$data = $crawlNewsService->exportDataToCsv();
	    	if (!empty($data)) {
	    		$status = isset($data['status']) ? $data['status'] : false;
	    		$message = isset($data['message']) ? $data['message'] : '';
	    		$filePath = isset($data['filePath']) ? $data['filePath'] : '';
				
				if (empty($filePath)) {
					StringHelper::printString($message);
				} else {
					StringHelper::printString($filePath, $message);
				}
	    	} else {
				StringHelper::printString("Un-supported URL");
	    	}
		} else {
			StringHelper::printString("Invalid URL");
		}

	}

	echo StringHelper::getCommonString(StringHelper::TYPE_THANKS_STRING);

} catch (Throwable $e) {
	StringHelper::printString($e->getMessage(), "Throwable message");
	StringHelper::printString($e->getFile(), "File");
	StringHelper::printString($e->getLine(), "Line");

} catch (Exception $e) {
	StringHelper::printString($e->getMessage(), "Exception message");
	StringHelper::printString($e->getFile(), "File");
	StringHelper::printString($e->getLine(), "Line");
}


