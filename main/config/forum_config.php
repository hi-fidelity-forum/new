<?php defined('SYSPATH') or die('No direct access allowed.');

return array(

	'table_prefix' => 'mybb_',
	'admin_groups' => 4,
    'permfields' => array(
        'canview',
        'canviewthreads',
        'candlattachments',
        'canpostthreads',
        'canostreplys',
        'canpostattachments',
        'canratethreads',
        'caneditposts',
        'candeleteposts',
        'candeletethreads',
        'caneditattachments',
        'canpostpolls',
        'canvotepolls',
        'cansearch'
    ),
    'threadreadcut' => 7,
    'spiders' => array(
        array('Google', 'Google'),
        array('msnbot', 'MSN'),
        array('Rambler', 'Rambler'),
        array('Yahoo', 'Yahoo'),
        array('AbachoBOT', 'AbachoBOT'),
        array('accoona', 'Accoona'),
        array('AcoiRobot', 'AcoiRobot'),
        array('ASPSeek', 'ASPSeek'),
        array('CrocCrawler', 'CrocCrawler'),
        array('Dumbot', 'Dumbot'),
        array('FAST-WebCrawler', 'FAST-WebCrawler'),
        array('GeonaBot', 'GeonaBot'),
        array('Gigabot', 'Gigabot'),
        array('Lycos', 'Lycos spider'),
        array('MSRBOT', 'MSRBOT'),
        array('Scooter', 'Altavista robot'),
        array('AltaVista', 'Altavista robot'),
        array('IDBot', 'ID-Search Bot'),
        array('eStyle', 'eStyle Bot'),
        array('Scrubby', 'Scrubby robot')
    ),
    'wolcutoff' => 900,

);
