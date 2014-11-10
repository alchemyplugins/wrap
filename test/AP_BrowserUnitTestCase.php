<?php

// http://facebook.github.io/php-webdriver/

abstract class AP_BrowserUnitTestCase extends AP_UnitTestCase
{
	protected static $browser;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();

		$host = 'http://localhost:8910';
		$caps = array(WebDriverCapabilityType::BROWSER_NAME => 'phantomjs');
		self::$browser = RemoteWebDriver::create($host, $caps);
		self::$browser->manage()->window()->setSize(new WebDriverDimension(1024,768));
		// vectors: wordpress, wp-version, browser, br-version, dimension (1024-laptop, 767-tablet, 460-mobile-landscape, 320-mobile-portrait)
	}

	public static function tearDownAfterClass()
	{
		self::$browser->close();

		parent::tearDownAfterClass();
	}

	public function login()
	{
		self::$browser->get( $this->host . '/wp-login.php' );

		$this->el('#user_login')->click()->sendKeys("tester");

		$this->clearFocus();

		$this->el('#user_pass')->click()->sendKeys("Abcd1234!");

		$this->clearFocus();

		$this->el('wp-submit', 'name')->click();
	}

	public function clearFocus()
	{
		$this->el('body', 'tagName')->click();
	}

	public function logout()
	{
		self::$browser->get( $this->host . '/wp-login.php?action=logout' );

		$this->el('a', 'tagName')->click();
	}

	public function el($selector, $by='cssSelector')
	{
		return $this->findElementBy($selector, $by);
	}

	public function findElementBy($selector, $by='cssSelector')
	{
		return self::$browser->findElement( WebDriverBy::$by( $selector ) );
	}

	public function els($selector, $by='cssSelector')
	{
		return $this->findElementsBy($selector, $by);
	}

	public function findElementsBy($selector, $by='cssSelector')
	{
		return self::$browser->findElements( WebDriverBy::$by( $selector ) );
	}

	public function el_in($el, $selector, $by='cssSelector') 
	{
		return $el->findElement( WebDriverBy::$by( $selector ) );
	}

	public function test_that_true_is_true()
	{
		$this->assertTrue(true);
	}
}
