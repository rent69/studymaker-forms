<?php

/* DEMO  file for generating demo JSON select data */

/* Smart load delay when loading queries 
----------------------------------------------------- */
if (isset($_GET["smartload"])) {
    sleep(1);
}

header("Access-Control-Allow-Origin: *");

$response[""] = "--";


/* Automobile modelss series and details data 
-------------------------------------------------------- */
if (isset($_GET["series"]) && isset($_GET["model"])) {

  $response[""] = "--";

  if (in_array($_GET["series"], array("series-1", "series-3", "a3", "a4"))) {
      $response["25-petrol"] = "2.5 petrol";
  }

  if (in_array($_GET["series"], array("series-3", "series-5", "series-6", "series-7", "a3", "a4", "a5"))) {
      $response["30-petrol"] = "3.0 petrol";
  }

  if (in_array($_GET["series"], array("series-7", "a5"))) {
      $response["30-diesel"] = "3.0 diesel";
  }

  if ("series-3" == $_GET["series"] && "sedan" == $_GET["model"]) {
      $response["30-diesel"] = "3.0 diesel";
  }

  if ("series-5" == $_GET["series"] && "sedan" == $_GET["model"]) {
      $response["30-diesel"] = "3.0 diesel";
  }

} else if (isset($_GET["mark"])) {
    if ("bmw" == $_GET["mark"]) {
        $response[""] = "--";
        $response["series-1"] = "1 series";
        $response["series-3"] = "3 series";
        $response["series-5"] = "5 series";
        $response["series-6"] = "6 series";
        $response["series-7"] = "7 series";
    };

    if ("audi" == $_GET["mark"]) {
        $response[""] = "--";
        $response["a1"]  = "A1";
        $response["a3"]  = "A3";
        $response["s3"]  = "S3";
        $response["a4"]  = "A4";
        $response["s4"]  = "S4";
        $response["a5"]  = "A5";
        $response["s5"]  = "S5";
        $response["a6"]  = "A6";
        $response["s6"]  = "S6";
        $response["rs6"] = "RS6";
        $response["a8"]  = "A8";
    };
} else if (isset($_GET["series"])) {
    if ("series-1" == $_GET["series"]) {
        $response[""] = "--";
        $response["3-doors"] = "3 doors";
        $response["5-doors"] = "5 doors";
        $response["coupe"]   = "Coupe";
        $response["cabrio"]  = "Cabrio";
        $response["selected"] = "coupe";
    };

    if ("series-3" == $_GET["series"]) {
        $response[""] = "--";
        $response["coupe"]   = "Coupe";
        $response["cabrio"]  = "Cabrio";
        $response["sedan"]   = "Sedan";
        $response["touring"] = "Touring";
    };

    if ("series-5" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]   = "Sedan";
        $response["touring"] = "Touring";
        $response["gran-tourismo"] = "Gran Tourismo";
    };

    if ("series-6" == $_GET["series"]) {
        $response[""] = "--";
        $response["coupe"]   = "Coupe";
        $response["cabrio"]  = "Cabrio";
    };

    if ("series-7" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]   = "Sedan";
    };

    if ("a1" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]   = "Sedan";
    };

    if ("a3" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
        $response["sportback"] = "Sportback";
        $response["cabriolet"] = "Cabriolet";
    };

    if ("s3" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
        $response["sportback"] = "Sportback";
    };

    if ("a4" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
        $response["avant"]     = "Avant";
        $response["allroad"]   = "Allroad";
    };

    if ("s4" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
    };

    if ("a5" == $_GET["series"]) {
        $response[""] = "--";
        $response["sportback"] = "Sportback";
        $response["cabriolet"] = "Cabriolet";
        $response["coupe"]     = "Coupe";
    };

    if ("s5" == $_GET["series"]) {
        $response[""] = "--";
        $response["sportback"] = "Sportback";
        $response["cabriolet"] = "Cabriolet";
        $response["coupe"]     = "Coupe";
    };

    if ("a6" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
        $response["avant"]     = "Avant";
        $response["allroad"]   = "Allroad";
    };

    if ("s6" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
        $response["avant"]     = "Avant";
    };

    if ("rs6" == $_GET["series"]) {
        $response[""] = "--";
        $response["sedan"]     = "Sedan";
        $response["avant"]     = "Avant";
    };

};

/* Companies and corresponding products details data 
-------------------------------------------------------- */
if (isset($_GET["company"])) {
    if ("apple" == $_GET["company"]) {
        $response[""]     = "Apple products";
		$response["imac"] = "iMac";
        $response["macbook"] = "MacBook";
		$response["macbook-air"] = "MacBook Air";
		$response["macbook-pro"] = "MacBook Pro";
		$response["imac-retina"] = "iMac Retina 5K";
		$response["apple-watch"] = "Apple Watch";
		$response["iphone"] = "iPhone 6";
		$response["ipad"] = "iPad Air 2";
		$response["ipod"] = "iPod Classic";
		$response["apple-tv"] = "Apple TV";
    };

    if ("microsoft" == $_GET["company"]) {
        $response[""] = "Microsoft products";
		$response["windows"] = "Microsoft Windows";
		$response["office"] = "Microsoft Office";
		$response["windows-phone"] = "Windows Phone";
		$response["surface"] = "Surface Tablet";
		$response["skype"] = "Skype";
		$response["xbox"] = "Xbox";
		$response["msn"] = "MSN";
    };

    if ("samsung" == $_GET["company"]) {
        $response[""] = "Samsung products";
		$response["galaxy-s6"] = "Samsung Galaxy S6";
		$response["galaxy-s6-edge"] = "Samsung Galaxy S6 Edge";
		$response["samsung-gear"] = "Samsung Gear S";
		$response["galaxy-note"] = "Samsung Galaxy Note 4";
		$response["galaxy-note-edge"] = "Samsung Galaxy Note Edge";
		$response["galaxy-tab"] = "Samsung Galaxy Tab";
		$response["curved-tv"] = "4K UHD Curved Smart TV";
		$response["suhd-tv"] = "4K SUHD Smart TV";
		$response["antiv-book"] = "Antiv Book 9";
		$response["chrome-book"] = "Chrome Book 2";
    };
	
    if ("google" == $_GET["company"]) {
        $response[""] = "Google products";
		$response["mail"] = "Gmail";
		$response["docs"] = "Google Docs";
		$response["drive"] = "Google Drive";
		$response["translate"] = "Google Translate";
		$response["plus"] = "Google Plus";
		$response["maps"] = "Google Maps";
		$response["search"] = "Google Search";
		$response["books"] = "Google Books";
		$response["wallet"] = "Google Wallet";
		$response["adsense"] = "Google AdSense";
		$response["earth"] = "Google Earth";
		$response["chrome"] = "Google Chrome";
		$response["analytics"] = "Google Analytics";
    };
	
    if ("lg" == $_GET["company"]) {
        $response[""] = "LG products";
		$response["3dtv"] = "3D TVs";
		$response["ledtv"] = "LED TVs";
		$response["lcdtv"] = "LCD TVs";
		$response["plasmatv"] = "Plasma TVs";
		$response["oledtv"] = "OLED TVs";
		$response["ultrahd"] = "Ultra HD TVs";
		$response["smarttv"] = "Smart Tvs";
		$response["bottom-freezer"] = "Bottom Freezer Refrigerators";
		$response["single-door"] = "Single Door Refrigerators";
		$response["double-door"] = "Double Door Refrigerators";
		$response["air-conditioners"] = "Air Conditioners";
		$response["led-lighting"] = "LED Lighting";
		$response["security-cameras"] = "Security Cameras";
		$response["smartphones"] = "LG Smartphones";
		$response["mini-audio"] = "Mini Hi-Fi Systems";
		$response["micro-audio"] = "Micro Hi-Fi Systems";
		$response["home-theatre"] = "Blu-ray Home Theatre Systems";
    };		
}

print json_encode($response);
