function stat($useragent, $ip)
	{
		$mysqli = new mysqli("localhost", "", "", "") or die("Не удалось подключиться к базе данных в функциях. <b><i>(stat)</i></b>");
		$mysqli->set_charset("utf8");

		$date = date("d.m.Y");

		$queryCheckIP = $mysqli->query("SELECT `ip` FROM `ipvisitors` WHERE `date` = '".$date."'");

		if($queryCheckIP->num_rows == 0)
		{
			$list = array(
				'rambler','googlebot','aport','yahoo','msnbot','turtle','mail.ru','omsktele',
				'yetibot','picsearch','sape.bot','sape_context','gigabot','snapbot','alexa.com',
				'megadownload.net','askpeter.info','igde.ru','ask.com','qwartabot','yanga.co.uk',
				'scoutjet','similarpages','oozbot','shrinktheweb.com','aboutusbot','followsite.com',
				'dataparksearch','google-sitemaps','appEngine-google','feedfetcher-google',
				'liveinternet.ru','xml-sitemaps.com','agama','metadatalabs.com','h1.hrn.ru',
				'googlealert.com','seo-rus.com','yaDirectBot','yandeG','yandex',
				'yandexSomething','Copyscape.com','AdsBot-Google','domaintools.com',
				'Nigma.ru','bing.com','dotnetdotcom'
			);
			foreach($list as $bots)
			{
				$checkUserAgent = stripos($useragent, $bots);
				if($checkUserAgent === true)
				{
					$u = 1;
				}
				else
				{
					$u = 0;
				}

				$ref = "all";
				$stmt = $mysqli->prepare("INSERT INTO `ipvisitors` (`ip`, `date`, `ref`, `user`) VALUES (?, ?, ?, ?)");
				$stmt->bind_param("sssi", $_SERVER['REMOTE_ADDR'], $date, $ref, $u);
				$stmt->execute();
				$stmt->close();
				return true;
			}
		}
		else
		{
			return false;
		}
	}
