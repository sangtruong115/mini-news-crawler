<?php

if (!class_exists("simple_html_dom")) include_once("app/Libraries/simplehtmldom_1_9/simple_html_dom.php");
if (!class_exists("StringHelper")) include_once("app/Common/Helpers/StringHelper.php");

/**
 * CrawlNewsService Class
 * 
 * @author  Sang Truong (sangtruong115@gmail.com)
 * @version 0.1
 */
class CrawlNewsService {
	
	private $settings;

	private $exportCsvPath;

	private $limitCsvRow = 0;
	private $countinCsvRow = 0;

	private $domain = '';
	private $baseUrl = '';
	private $currentUrl = '';

	const LIMIT_CSV_ROW = 1000;

	const DOMAIN_THESAIGONTIMES = "thesaigontimes.vn";
	const DOMAIN_VNEXPRESS = "vnexpress.net";

	const SUPPORTED_DOMAIN_LIST = [
		"thesaigontimes.vn",
		"vnexpress.net",
	];

	/**
	 * Init CrawlNewsService
	 *
	 */
	public function __construct($url) 
	{
		$this->currentUrl = $url;
		$this->domain = $this->getDomain($url);
		$this->baseUrl = $this->getBaseUrl($url);

		$this->settings = include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../config/settings.php");
		$this->exportCsvPath = $this->getConfig('export_folder.csv');
		$this->limitCsvRow = self::LIMIT_CSV_ROW;
		$this->countingCsvDataRow = 0;

		// Integrity Check
	    $this->integrityCheck();
	}

	/**
	 * Integrity Check
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 */
	private function integrityCheck()
	{
	    // Prevent Bad Request when getting content from URL
		$checkContent = @file_get_contents($this->currentUrl);
		if ($checkContent == false) {
			StringHelper::printString("400 Bad Request");
			exit;
		}
		if (!$this->validSupportedUrl()) {
			StringHelper::printString("Un-supported URL");
			exit;
		}
	}

	/**
	 * Get config
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return string
	 */
	private function getConfig($string)
	{
		$settings = $this->settings;
		$data = '';

		if (isset($settings[$string])) {
			$data = $settings[$string];
		}

		return $data;
	}

	/**
	 * Get base URL
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return string
	 */
	private function getBaseUrl($string)
	{
		$parsedUrl = parse_url($string);
		
		$scheme = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'].'://' : ''; 
		$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : ''; 
		$port = isset($parsedUrl['port']) ? ':'.$parsedUrl['port'] : '';

		return $scheme . $host . $port;
	}

	/**
	 * Get domain
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return string
	 */
	private function getDomain($string)
	{
	    $parsedUrl = parse_url($string);

	    $domain = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
	    if(preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
	        return $regs['domain'];
	    }

	    return '';
	}


	/**
	 * Validate domain of URL in supported list 
	 *
	 * @return bool
	 */
	private function validSupportedUrl()
	{
		return in_array($this->domain, self::SUPPORTED_DOMAIN_LIST);
	}

	/**
	 * Get full URL from href
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return string
	 */
	private function getFullUrlFromHref($href)
	{
		$parsedHost = parse_url($href, PHP_URL_HOST);
		if (!empty($parsedHost)) {
			return $href;
		} else {
			return $this->baseUrl . $href;
		}
	}
	

	/**
	 * Get author name from string
	 *
	 * @return string
	 */
	private function getAuthorNameFromString($string)
	{
		switch ($this->domain) {
			case self::DOMAIN_THESAIGONTIMES :
				$string = str_replace("(*)", "", $string);
				break;
			
			default:
				break;
		}

		return !empty($string) ? trim($string) : $string;
	}

	/**
	 * Get posted date from string
	 *
	 * @return string
	 */
	private function getPostedDateFromString($string)
	{
		switch ($this->domain) {
			case self::DOMAIN_THESAIGONTIMES :
				$string = html_entity_decode($string);
				$string = str_replace("  ", " ", $string);
				$string = str_replace(" ", " ", $string);
				break;
			
			default:
				break;
		}

		return !empty($string) ? trim($string) : $string;
	}

	/**
	 * Export data from url
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return array
	 */
	public function exportDataFromUrl()
	{
		$data = [];

		switch ($this->domain) {
			case self::DOMAIN_THESAIGONTIMES :
				$data = $this->processHtmlContentFromTheSaigonTimes();
				break;
			case self::DOMAIN_VNEXPRESS :
				$data = $this->processHtmlContentFromVnExpress();
				break;
			
			default:
				break;
		}
		
		return $data;
	}

	/**
	 * Export data to csv file
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return array
	 */
	public function exportDataToCsv($filename = "mini-news-crawler.csv")
	{
		$data = $this->exportDataFromUrl();

		$filePath = '';

		if (!empty($data)) {
			// Not allow export data if it is over limitation of CSV data rows
			if (count($data) > $this->limitCsvRow) {
				return [
					'success' => false,
					'message' => 'Over limit of CSV data rows',
				];
			}
			
			$folderPath = $this->exportCsvPath;

			$filePath = $folderPath . date('Ymd-His-') . $filename;
	   		if ($file = fopen($filePath, 'w')) { 
				foreach ($data as $line) {
					fputcsv($file, $line, ',');
				}
				fclose($file);
			} else {
				return [
					'success' => false,
					'message' => 'Export data fail',
				];
			}

			return [
				'success' => true,
				'message' => 'Export data successfully',
				'filePath' => $filePath,
			];
		}

		return [
			'success' => false,
			'message' => 'Nothing to do',
		];
	}

	/**
	 * Process news content from thesaigontimes.vn
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return array
	 */
	private function processNewsContentFromTheSaigonTimes($url)
	{
		$data = [];

		// Limit CSV data row
		if ($this->countinCsvRow >= $this->limitCsvRow) {
			return $data;
		}

		if ($this->domain == self::DOMAIN_THESAIGONTIMES) {
			// Use Simple HTML Dom to get data
			$html = file_get_html($url);

		    $mainId = ".desktop #ARTICLE_DETAILS";

		    $title = '';
		    $author = '';
		    $postedDate = '';

			$titleObject = $html->find("$mainId .Title", 0);
			if (isset($titleObject) && is_object($titleObject)) {
				$title = $titleObject->plaintext;
			}

			$authorObject = $html->find("$mainId .ReferenceSourceTG", 0);
			if (isset($authorObject) && is_object($authorObject)) {
				$author = $this->getAuthorNameFromString($authorObject->plaintext);
			}


			$postedDateObject = $html->find("$mainId .Date", 0);
			if (isset($postedDateObject) && is_object($postedDateObject)) {
				$postedDate = $this->getPostedDateFromString($postedDateObject->plaintext);
			}

			if (!empty($title)) {
				$data = [
					$url,
					$title,
					$author,
					$postedDate,
				];

				// Counting CSV data row
				$this->countingCsvDataRow++;
			}

		    $html->clear(); 
		    unset($html);
		}

		return $data;
	}

	/**
	 * Process HTML content from thesaigontimes.vn
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return array
	 */
	private function processHtmlContentFromTheSaigonTimes()
	{
		$data = [];
		
		if ($this->domain == self::DOMAIN_THESAIGONTIMES) {
			$url = $this->currentUrl;
			$baseUrl = $this->baseUrl;

		    $data[] = ["URL", "Title", "Author", "Posted Date"];	// header

		    // Get main news content from URL
		    $tmpData = $this->processNewsContentFromTheSaigonTimes($url);
    		if (!empty($tmpData)) {
    			$data[] = $tmpData;
    		}

			// Use Simple HTML Dom to get data
			$html = file_get_html($url);

		    $moreUrlDataList = [];
    		$moreUrlDataList[] = $html->find(".desktop table p a");
    		$moreUrlDataList[] = $html->find(".desktop .Item1 a");
    		$moreUrlDataList[] = $html->find(".desktop a.tintieudiem");
    		$moreUrlDataList[] = $html->find(".desktop a.HomeTitlebyTime");
    		$moreUrlDataList[] = $html->find(".desktop a.HomeCategory_CaR_Title2");
    		$moreUrlDataList[] = $html->find(".desktop a.docnhieunhattitle_trangcon");
    		$moreUrlDataList[] = $html->find(".desktop a.ArticleTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.Prior1Title1");
    		$moreUrlDataList[] = $html->find(".desktop a.PhanHoiTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.ut1Title");
    		$moreUrlDataList[] = $html->find(".desktop a.diaocTitlebyTime");
    		$moreUrlDataList[] = $html->find(".desktop a.diaocCategory_CaR_Title");
    		$moreUrlDataList[] = $html->find(".desktop a.thongtinDNTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.Category2wTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.ViecgiTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.sukienTitle1");
    		$moreUrlDataList[] = $html->find(".desktop a.tinanhTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.HomeTitlebyTimewhite");
    		$moreUrlDataList[] = $html->find(".desktop a.NOtherTitle");
    		$moreUrlDataList[] = $html->find(".desktop a.NOtherTitle1");
    		$moreUrlDataList[] = $html->find("iframe a.NOtherTitle1");
    		
    		// Get more news content from other URLs in page
    		foreach ($moreUrlDataList as $key => $moreUrlData) {
	    		if (!empty($moreUrlData)) {
		    		foreach ($moreUrlData as $key => $moreUrl) {
		    			$href = isset($moreUrl->href) ? $moreUrl->href : '';
		    			if (!empty($href)) {
							$newUrl = $this->getFullUrlFromHref($href);
				    		$tmpData = $this->processNewsContentFromTheSaigonTimes($newUrl);
				    		if (!empty($tmpData)) {
				    			$data[] = $tmpData;
				    		}
		    			}
		    		}
	    		}
    		}

		    $html->clear(); 
		    unset($html);
		}

		return $data;
	}

	/**
	 * Process news content from thesaigontimes.vn
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return array
	 */
	private function processNewsContentFromVnExpress($url)
	{
		$data = [];

		// Limit CSV data row
		if ($this->countinCsvRow >= $this->limitCsvRow) {
			return $data;
		}

		if ($this->domain == self::DOMAIN_VNEXPRESS) {
			// Use Simple HTML Dom to get data
			$html = file_get_html($url);

		    $mainId = "section.container";

		    $title = '';
		    $author = '';
		    $postedDate = '';

		    if ($html !== false) {
				$titleObject = $html->find("$mainId .title_news_detail", 0);
				if (isset($titleObject) && is_object($titleObject)) {
					$title = trim($titleObject->plaintext);
				}

				$contentDetail = $html->find("$mainId .content_detail p");
				$authorObject = end($contentDetail);
				if (isset($authorObject) && is_object($authorObject)) {
					$author = $this->getAuthorNameFromString($authorObject->plaintext);
				}

				$postedDateObject = $html->find("$mainId header .time", 0);
				if (isset($postedDateObject) && is_object($postedDateObject)) {
					$postedDate = $this->getPostedDateFromString($postedDateObject->plaintext);
				}

				if (!empty($title)) {
					$data = [
						$url,
						$title,
						$author,
						$postedDate,
					];

					// Counting CSV data row
					$this->countingCsvDataRow++;
				}

			    $html->clear(); 
			    unset($html);
			}
		}

		return $data;
	}

	/**
	 * Process HTML content from vnexpress.net
	 *
	 * @author    Sang Truong <sangtruong115@gmail.com>
	 * 
	 * @return array
	 */
	private function processHtmlContentFromVnExpress()
	{
		$data = [];

		if ($this->domain == self::DOMAIN_VNEXPRESS) {
			$url = $this->currentUrl;
			$baseUrl = $this->baseUrl;

		    $data[] = ["URL", "Title", "Author", "Posted Date"];	// header

		    // Get main news content from URL
		    $tmpData = $this->processNewsContentFromVnExpress($url);
    		if (!empty($tmpData)) {
    			$data[] = $tmpData;
    		}

			// Use Simple HTML Dom to get data
			$html = file_get_html($url);

		    $moreUrlDataList = [];
    		$moreUrlDataList[] = $html->find("#box_xemnhieunhat .list_title a");
    		$moreUrlDataList[] = $html->find("#box_morelink_detail .list_title a");
    		$moreUrlDataList[] = $html->find(".box_bottom_detail .header_toppic a");
    		$moreUrlDataList[] = $html->find(".box_bottom_detail .view_more");
    		$moreUrlDataList[] = $html->find(".box_bottom_detail .list_title a");
    		$moreUrlDataList[] = $html->find(".box_bottom_detail .list_news .title_news a");
    		$moreUrlDataList[] = $html->find(".sidebar_3 .box_category .list_news .title_news a");
    		$moreUrlDataList[] = $html->find(".sidebar_3 .box_category .list_title a");
    		$moreUrlDataList[] = $html->find(".sidebar_home_2 .list_news .title_news a");
    		$moreUrlDataList[] = $html->find(".sidebar_home_2 .list_title a");
    		
    		// Get more news content from other URLs in page
    		foreach ($moreUrlDataList as $key => $moreUrlData) {
	    		if (!empty($moreUrlData)) {
		    		foreach ($moreUrlData as $key => $moreUrl) {
		    			$href = isset($moreUrl->href) ? $moreUrl->href : '';
		    			if (!empty($href)) {
							$newUrl = $this->getFullUrlFromHref($href);
				    		$tmpData = $this->processNewsContentFromVnExpress($newUrl);
				    		if (!empty($tmpData)) {
				    			$data[] = $tmpData;
				    		}
		    			}
		    		}
	    		}
    		}

		    $html->clear(); 
		    unset($html);
		}

		return $data;
	}
	
}




