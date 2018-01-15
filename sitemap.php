<?php header("Content-type: text/xml"); ?>
<?php
set_time_limit(50);

$links = array();

function crawlURL($url, $depth, $maxdepth=10) {

	global $links;
	
	$keyHash = md5($url);

	if (empty($links[$keyHash]['priority']))
		$links[$keyHash]['priority'] = round(1-(0.1*($depth)),1);

	$buf = file_get_contents($_GET['url'] . $url);

	preg_match_all("#<a .*?href=(\'|\")(.*?)(\'|\").*?>#", $buf, $matches);	

	foreach ($matches[2] as $nexturl) {
		if (preg_match("#(^\#|^mailto|^call\:|^callto\:|^Javascript\:|^skype\:|^http\:|^https\:|^/de/)#", $nexturl)) {
			//echo $nexturl . "\n";
			continue;
		}					
		
		$keyHash = md5($nexturl);

		if (($depth < $maxdepth) && (empty($links[$keyHash] )) ){
			crawlURL($nexturl, $depth + 1);
		} else if ($depth < $maxdepth) {
			if (empty($links[$keyHash]['priority']))
				$links[$keyHash]['priority'] = round(1-(0.1*($depth+1)),1);
		}
		
		$links[$keyHash]['url'] = $nexturl;
		$links[$keyHash]['changefreq'] = 'daily';

	}
}

crawlURL("/", 0);

//print_r($links); die;
echo '<?xml version="1.0" encoding="utf-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php 
	foreach ($links as $key => $url) {?>
	<url>
		<loc><?php echo $_GET['url'] . $url['url'] ?></loc>
		<changefreq><?php echo $url['changefreq'] ?></changefreq>
		<?php if (!empty($url['priority'])) { ?><priority><?php echo $url['priority'] ?></priority><?php } ?>
	</url>
<?php }	?>
</urlset>