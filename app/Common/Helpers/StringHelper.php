<?php

/**
 * StringHelper Class
 * 
 * @author  Sang Truong (sangtruong115@gmail.com)
 * @version 0.1
 */
class StringHelper {
	/**
	 * Constants
	 */
	
	const TYPE_DEFAULT_STRING = 'default';
	const TYPE_WELCOME_STRING = 'welcome';
	const TYPE_USAGE_STRING = 'usage';
	const TYPE_THANKS_STRING = 'thanks';
	const TYPE_PROCESSING_STRING = 'processing';

	const VALUE_DEFAULT_STRING = "
		It is a Mini News Crawler.
		";

	const VALUE_PROCESSING_STRING = "
		It is processing. Please wait a minute...
		";

	const VALUE_WELCOME_STRING = "
		*************************
		*   Mini News Crawler   *
		*************************
		This a mini program to crawl news data from an online newspaper.
		";

	const VALUE_USAGE_STRING = "
		+-----------+
		|   Usage   |
		+-----------+
		INPUT: You will input an URL of news from online newspaper The Saigon Times (https://www.thesaigontimes.vn)
		OUTPUT: It will download a CSV of news data including: URL, Title, Author, Posted Date
		";

	const VALUE_THANKS_STRING = "

			+------------+
			|   Thanks   |
			+------------+
			Thanks for your time!
			See you again!

		";

	/**
	 * Get common string
	 *
	 * @return string
	 */
	public static function getCommonString($type = self::TYPE_DEFAULT_STRING)
	{
		$string = self::VALUE_DEFAULT_STRING;

		switch ($type) {
			case self::TYPE_DEFAULT_STRING :
				$string = self::VALUE_DEFAULT_STRING;
				break;
			case self::TYPE_WELCOME_STRING :
				$string = self::VALUE_WELCOME_STRING;
				break;
			case self::TYPE_USAGE_STRING :
				$string = self::VALUE_USAGE_STRING;
				break;
			case self::TYPE_THANKS_STRING :
				$string = self::VALUE_THANKS_STRING;
				break;
			case self::TYPE_PROCESSING_STRING :
				$string = self::VALUE_PROCESSING_STRING;
				break;
			
			default:
				break;
		}

		return $string;
	}

	/**
	 * Read string from input
	 *
	 * @return string
	 */
	public static function readStringFromInput($message)
	{
		// User input URL
		$readlineString = "
		>> $message 
		";
		return readline($readlineString);
	}

	/**
	 * Print string
	 *
	 * @return string
	 */
	public static function printString($string, $title = "Message")
	{
		// Check URL
		$string = "
		>> $title:   $string   
		";

		echo $string;
	}

	/**
	 * Check URL from string
	 *
	 * @return string
	 */
	public static function checkUrl($string)
	{
		// Check URL
		$host = parse_url($string, PHP_URL_HOST);

		if (!empty($host)) {
			return $string;
		}

		return '';
	}
	
}
