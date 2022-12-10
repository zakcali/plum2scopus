<!DOCTYPE html>
<!-- plum2scopus V1.0: bu yazılım Dr. Zafer Akçalı tarafından oluşturulmuştur 
programmed by Zafer Akçalı, MD -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Scopus numarasından makaleyi bul</title>
</head>

<body>
<?php
// plum2scopus
// By Zafer Akçalı, MD
// Zafer Akçalı tarafından programlanmıştır
$scopusid=$doi=$ArticleTitle=$dergi=$publisher=$ISSN=$eISSN=$Year=$Volume=$Issue=$StartPage=$EndPage=$yazarlar=$PublicationType=$AbstractText=$PMID=$atif=$ISBN="";
$yazarS=0;
if (isset($_POST['scopusid'])) {
$gelenScopus=trim($_POST["scopusid"]);

if($gelenScopus!=""){
if( substr($gelenScopus,0,7) != '2-s2.0-')
	$gelenScopus='2-s2.0-'.$gelenScopus; // correct format
$preText="https://api.plu.mx/widget/elsevier/artifact?type=elsevier_id&id=";
$postText='';
$url = $preText.$gelenScopus;
$data=file_get_contents($url);
// print_r ($data);
$plumBilgi=(json_decode($data, true));
// var_dump ($plumBilgi);
// print_r ($plumBilgi);
if ( !isset ($plumBilgi['error_code'])) {// Not found
$plumId=str_replace('https://plu.mx/a/','',$plumBilgi['link']);
$plumLink='https://plu.mx/api/v1/artifact/id/'.$plumId;
$data2=file_get_contents($plumLink);
$scopusBilgi=(json_decode($data2, true));
// print_r($scopusBilgi);
// Makalenin başlığı
$ArticleTitle=$scopusBilgi['bibliographic_data']['artifact_title'];
// yayın türü, çok güvenmemek gerek, vaka takdimleri de makale olabiliyor
if (isset($scopusBilgi['artifact_type']))
	$PublicationType=$scopusBilgi['artifact_type'];
// Özet
if (isset($scopusBilgi['bibliographic_data']['description']))
	$AbstractText=$scopusBilgi['bibliographic_data']['description'];
// doi
if (isset($scopusBilgi['identifier']['doi']))
	$doi= $scopusBilgi['identifier']['doi'][0]['value'];
// PMID
if (isset($scopusBilgi['identifier']['pmid']))
	$PMID= $scopusBilgi['identifier']['pmid'][0]['value'];
// scopus numarası = eid
foreach ($scopusBilgi['identifier']['url_id'] as $eleman) {
	if (strpos ($eleman,'www.scopus.com') !== false) {
		$start=strpos ($eleman,'&scp=')+5;
		$end=strpos ($eleman,'&',$offset=$start);
		$length=$end-$start;
		$scopusid='2-s2.0-'.substr($eleman, $start,$length);
	}
}
// Dergi ismi
$dergi=$scopusBilgi['bibliographic_data']['publication_title'];
// Aldığı atıf sayısı
if (isset ($scopusBilgi['plum_print_counts']['citation']))
	$atif=$scopusBilgi['plum_print_counts']['citation']['total'];
// yayınevi
if (isset($scopusBilgi['bibliographic_data']['publisher']))
	$publisher = $scopusBilgi['bibliographic_data']['publisher'];

// issn ve eissn
if (isset($scopusBilgi['bibliographic_data']['issn'])) {
	$issntext=$scopusBilgi['bibliographic_data']['issn'];
	$ISSN=substr ($issntext,0,4).'-'.substr ($issntext,4,4);
}
// isbn, kitaplar için
if (isset($scopusBilgi['identifier']['isbn'][0]))
		$ISBN=$scopusBilgi['identifier']['isbn'][0];
if (isset($scopusBilgi['identifier']['isbn'][1]))
		$ISBN=$ISBN.'; '.$scopusBilgi['identifier']['isbn'][1];
// Derginin basıldığı / yayımlandığı yıl
if (isset($scopusBilgi['bibliographic_data']['publication_year']))
	$Year= $scopusBilgi['bibliographic_data']['publication_year'];
// cilt
if (isset($scopusBilgi['bibliographic_data']['volume']))
	$Volume= $scopusBilgi['bibliographic_data']['volume'];
// sayı
if (isset($scopusBilgi['bibliographic_data']['issue']))
	$Issue= $scopusBilgi['bibliographic_data']['issue'];

// başlangıç-bitiş sayfası
if (isset($scopusBilgi['bibliographic_data']['start_page']))
	$StartPage=$scopusBilgi['bibliographic_data']['start_page'];
if (isset($scopusBilgi['bibliographic_data']['end_page']))
	$EndPage=$scopusBilgi['bibliographic_data']['end_page'];
// başlangıç-bitiş sayfası
	if (isset ($scopusBilgi['bibliographic_data']['page_range'])) { // sayfa numaraları
		$sayfalar=explode ("-", $scopusBilgi['bibliographic_data']['page_range']);
		$StartPage= $sayfalar[0];
		if (isset ($sayfalar[1]))
			$EndPage=$sayfalar[1];
	}
// yazarlar
$yazarlar="";
// yazar sayısı
$yazarS=0;
foreach( $scopusBilgi['bibliographic_data']['authors'] as $eleman) {
		$isim='';
		$soyisim='';
		$soyadAd=explode (", ", $eleman);
		$soyisim=$soyadAd[0];
			if (isset($soyadAd[1]))
				$isim=$soyadAd[1];
		$yazarlar=$yazarlar.$isim." ".$soyisim.", ";
		$yazarS=$yazarS+1;	
		}
$yazarlar=substr ($yazarlar,0,-2);
			} // NotFound hatası gelmedi
		
	} 
}
?>
<a href="eid nerede.png" target="_blank"> Scopus numarasına nereden bakılır? </a>
<form method="post" action="">
Scopus makale numarasını (eid) giriniz<br/>
<input type="text" name="scopusid" id="scopusid" value="<?php echo $scopusid;?>" >
<input type="submit" value="Scopus yayın bilgilerini PHP ile getir">
</form>
<button id="scopusGoster" onclick="scopusGoster()">Scopus yayınını göster</button>
<button id="scopusAtifGoster" onclick="scopusAtifGoster()">Scopus yayınının atıflarını göster</button>
<button id="doiGit" onclick="doiGit()">doi ile makaleyi göster</button>
<br/>
Scopus eid: <input type="text" name="eid" size="25" id="eid" value="<?php echo $scopusid;?>" >  
doi: <input type="text" name="doi" size="55"  id="doi" value="<?php echo $doi;?>"> <br/>
Makalenin başlığı: <input type="text" name="ArticleTitle" size="85"  id="ArticleTitle" value="<?php echo $ArticleTitle;?>"> <br/>
Dergi ismi: <input type="text" name="Title" size="50"  id="Title" value="<?php echo $dergi;?>"> 
Yayınevi: <input type="text" name="publisher" size="26"  id="publisher" value="<?php echo $publisher;?>"> <br/>
ISSN: <input type="text" name="ISSN" size="8"  id="ISSN" value="<?php echo $ISSN;?>">
eISSN: <input type="text" name="eISSN" size="8"  id="eISSN" value="<?php echo $eISSN;?>">
ISBN: <input type="text" name="ISBN" size="30"  id="ISBN" value="<?php echo $ISBN;?>"> <br/>
Yıl: <input type="text" name="Year" size="4"  id="Year" value="<?php echo $Year;?>">
Cilt: <input type="text" name="Volume" size="2"  id="Volume" value="<?php echo $Volume;?>">
Sayı: <input type="text" name="Issue" size="2"  id="Issue" value="<?php echo $Issue;?>">
Sayfa/numara: <input type="text" name="StartPage" size="5"  id="StartPage" value="<?php echo $StartPage;?>">
- <input type="text" name="EndPage" size="2"  id="EndPage" value="<?php echo $EndPage;?>">
Yazar sayısı: <input type="text" name="yazarS" size="2"  id="yazarS" value="<?php echo $yazarS;?>"><br/>
Yazarlar: <input type="text" name="yazarlar" size="95"  id="yazarlar" value="<?php echo $yazarlar;?>"><br/>
Yayın türü: <input type="text" name="PublicationType" size="20"  id="PublicationType" value="<?php echo $PublicationType;?>">
PMID: <input type="text" name="PMID" size="6"  id="PMID" value="<?php echo $PMID;?>">
Aldığı atıf: <input type="text" name="citedBy" size="4"  id="citedBy" value="<?php echo $atif;?>">
<br/>
Özet <br/>
<textarea rows = "20" cols = "90" name = "ozet" id="ozetAlan"><?php echo $AbstractText;?></textarea>  <br/>
<script>
function scopusGoster() {
var	w=document.getElementById('eid').value.replace('2-s2.0-','');
	urlText = "https://www.scopus.com/inward/record.uri?partnerID=HzOxMe3b&scp=" + w+"&origin=inward";
	window.open(urlText,"_blank");
}
function scopusAtifGoster() {
var	w=document.getElementById('eid').value;
	urlText = "https://www.scopus.com/search/submit/citedby.uri?eid="+w+"&src=s&origin=resultslist";
	window.open(urlText,"_blank");
}
function doiGit() {
var	w=document.getElementById('doi').value;
	urlText = "https://doi.org/"+w;
	window.open(urlText,"_blank");
}
</script>
</body>
</html>