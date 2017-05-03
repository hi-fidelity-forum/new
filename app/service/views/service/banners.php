<?php

// get location and set default courses
    $ip = $_SERVER["REMOTE_ADDR"];

	$cur = "USD";    
	
    if ($xmlstring = @file_get_contents('http://api.hostip.info/?ip='.$ip))
	{
		$beg_str = '<countryAbbrev>';
		$beg = mb_strpos($xmlstring,$beg_str)+strlen($beg_str);
		$end = mb_strpos($xmlstring,'</countryAbbrev>');
		$res = mb_substr($xmlstring, $beg, ((int) $end-$beg));
		
		if ($res){
			if (in_array($res, array("UA", "RU"))){
				if ($res == "UA") {
					$cur = "UAH";
				}
				elseif ($res == "RU"){
					$cur = "RUR";
				}
			}
		}
	}
	
$def_course = $cur;

//Get courses items
$cf = file_get_contents('https://www.liqpay.com/exchanges/exchanges.cgi');
$xml = simplexml_load_string($cf);
$rur = 1/((float) $xml->RUR->UAH);
$usd = 1/((float) $xml->USD->UAH);
$eur = 1/((float) $xml->EUR->UAH);
$courses = 'var courses = {"EUR":'.$eur.',"USD":'.$usd.',"RUR":'.$rur.'};';

?>

<div class="news_page">
<style type="text/css">
    .news_page a{color: #3B88C4;}
    .news_page {color: #333;}
    .condition li{list-style: circle outside !important;}
</style>

<!--h5 class="page_title">Рекламодателям</h5>
<div class="clear_page_title"></div>
<p>Предлагаем Вам воспользоваться  платными услугами на  нашем ресурсе.</p-->

Размещая рекламу на Аудиопортале, Вы получаете возможность публиковать материалы в разделах:
<a href="http://hi-fidelity-forum.com/publish/preview">Обзоры</a>,
<a href="http://hi-fidelity-forum.com/publish/post">Статьи</a>,
<a href="http://hi-fidelity-forum.com/publish/news">Новости</a>
<br />
<p class="portal_controll_buttons" style="float: none !important;"><a class="button button_red" href="/forum/ag_payment.php?uid=<?=$session->user()->get('uid');?>" target="_blank">Сформировать счет →</a></p>

<div style="text-align: right; margin-bottom: 10px;">
Стоимость услуг по курсу валют в <select name="course" class="select_course">
            <option value="USD">USD</option>
            <option value="UAH">UAH</option>
            <option value="RUR">RUR</option>
            <option value="EUR">EUR</option>
        </select>
</div>

<script type="text/javascript">

var def_course = "<?=$def_course;?>";
<?=$courses;?>

$('select.select_course option[value="'+def_course+'"]').attr('selected','true');

function change_amount(){

        var vl = $('select.select_course').val();
        vl = vl.toUpperCase();
        
        if (vl == 'UAH'){

            $(".price_value").each(function(){
                if ($(this).attr('old_price')){
                    var price = $(this).attr('old_price');
                    var amount = price;
                    $(this).text(amount);
                }
            });

        } else {
           
            $(".price_value").each(function(){
                if ($(this).attr('old_price')){
                    var price = $(this).attr('old_price');
                } else {
                    var price = parseInt($(this).text());
                    $(this).attr('old_price',price);
                }
                var amount = price * (courses[vl]);
                amount = amount.toFixed(2);
                $(this).text(amount);
            });

        }
       
}

$('select.select_course').change(function(){
     change_amount();
});

</script>

<table border="1" cellspacing="0" cellpadding="3" width="100%">
	<tr style="background: #ddd;">
		<td valign="top" width="200px"><p align="center"><strong>Размер баннера</strong></p></td>
		<td valign="top"><p align="center"><strong>Где</strong><strong> размещается</strong></p></td>
		<td valign="top"><p align="center"><strong>Стоимость в месяц</strong></p></td>
		<td valign="top"><p align="center"><strong>Технические</strong><strong> требования</strong></p></td>
	</tr>
	<tr>
		<td valign="middle"><img src="/images/plash/200x80.jpg" title="200x80" /></td>
		<td valign="top"><p>Правый столбец, отображается на всех    страницах</p></td>
		<td valign="top"><p align="center" class="price_value">2800</p></td>
		<td valign="top"><p align="center">gif/jpg/png/swf <br />
			не более 25 кБ,<br />
			содержит    ссылку</p></td>
	</tr>
	<tr>
		<td valign="top"><img src="/images/plash/200x160.jpg" title="200x160" /></td>
		<td valign="top"><p>Правый столбец,    отображается на всех    страницах</p></td>
		<td valign="top"><p align="center" class="price_value">4200</p></td>
		<td valign="top"><p align="center">gif/jpg/png/swf <br />
			не более 30 кБ,<br />
			содержит    ссылку</p></td>
	</tr>
	<tr>
		<td valign="top"><img src="/images/plash/200x360.jpg" title="200x360" /></td>
		<td valign="top"><p>Правый столбец, отображается на всех страницах</p></td>
		<td valign="top"><p align="center" class="price_value">8400</p></td>
		<td valign="top"><p align="center">gif/jpg/png/swf <br />
			не более 50 кБ,<br />
			содержит    ссылку</p></td>
	</tr>
	<tr>
		<td valign="top"><img src="/images/plash/200x80.jpg" title="200x80" /></td>
		<td valign="top"><p>Нижняя часть, отображается    на всех страницах</p></td>
		<td valign="top"><p align="center" class="price_value">800</p></td>
		<td valign="top"><p align="center">gif/jpg/png/swf <br />
			не более 20 кБ,<br />
			содержит    ссылку</p></td>
	</tr>
</table>
<!--br />
Требования к материалам, размещаемым на страницах портала:<br />
<ul class="condition">
<li>Уникальность контента – материалы не могут быть перекопированы с других интернет-источников.</li>
<li>Разрешается переводить и размещать переводы статей, если раньше они нигде не были опубликованы, кроме как на языке оригинала.</li>
<li>Изображения для аннотации материала должны быть визуально качественными, квадратными, размером строго 190 на 190 пикс.</li>
<li>Изображения в теле материала (тексте статьи) визуально качественные, минимальный размер прикрепляемого изображения не менее 1280 на 720 пикс.</li>
<li>Изображения должны иметь пояснительные неповторяющиеся подписи.
<li>Текст материала не может быть менее 3200 знаков.
<li>Если публикуемый материал заимствованный, он автоматически будет удалён модератором.</li>
<li>При перепечатке материалов с портала ссылка на сайт обязательна</li>
</ul-->

<script type="text/javascript">

change_amount();

</script>

</div><!-- news_page -->