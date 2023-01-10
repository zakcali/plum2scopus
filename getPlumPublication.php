<?php
class getPlumPublication {
	
	function __construct() {
		$this->initialize();
		}
	
	function initialize () {
		$this->scopusid=''; $this->doi=''; $this->ArticleTitle=''; $this->dergi=''; $this->publisher=''; $this->ISSN=''; $this->eISSN=''; $this->Year=''; $this->Volume=''; $this->Issue=''; $this->StartPage=''; $this->EndPage=''; $this->yazarlar=''; $this->PublicationType=''; $this->AbstractText=''; $this->PMID=''; $this->atif=''; $this->ISBN=''; $this->dikkat='';
		$this->yazarS=0; 
		}
	final function plumPublication ($sid) {
	$this->initialize();
	
	if( substr($sid,0,7) != '2-s2.0-')
		$sid='2-s2.0-'.$sid; // correct format
	$preText="https://api.plu.mx/widget/elsevier/artifact?type=elsevier_id&id=";
	$postText='';
	$url = $preText.$sid;
	$data=file_get_contents($url);
	$plumBilgi=(json_decode($data, true));
// print_r ($plumBilgi);
	if (isset ($plumBilgi['error_code'])) {// Not found
			$this->dikkat='yayın bulunamadı'; 
			return;
		}
	$plumId=str_replace('https://plu.mx/a/','',$plumBilgi['link']);
	$plumLink='https://plu.mx/api/v1/artifact/id/'.$plumId;
	$data2=@file_get_contents($plumLink);
	if (!$data2) {
		$this->dikkat='bağlantı kurulamadı';   
		return;
		}
	$scopusBilgi=(json_decode($data2, true));
	if (!$this->ArticleTitle=$scopusBilgi['bibliographic_data']['artifact_title']) {// no title
		$this->dikkat='yayın bulunamadı'; 
		return;
		}
// print_r($scopusBilgi);
// Makalenin başlığı
	$this->ArticleTitle=$scopusBilgi['bibliographic_data']['artifact_title'];
// yayın türü, çok güvenmemek gerek, vaka takdimleri de makale olabiliyor
		if (isset($scopusBilgi['artifact_type']))
			$this->PublicationType=$scopusBilgi['artifact_type'];
// Özet
		if (isset($scopusBilgi['bibliographic_data']['description']))
			$this->AbstractText=$scopusBilgi['bibliographic_data']['description'];
// doi
		if (isset($scopusBilgi['identifier']['doi']))
			$this->doi= $scopusBilgi['identifier']['doi'][0]['value'];
// PMID
		if (isset($scopusBilgi['identifier']['pmid']))
			$this->PMID= $scopusBilgi['identifier']['pmid'][0]['value'];
// scopus numarası = eid
		foreach ($scopusBilgi['identifier']['url_id'] as $eleman) {
			if (strpos ($eleman,'www.scopus.com') !== false) {
				$start=strpos ($eleman,'&scp=')+5;
				$end=strpos ($eleman,'&',$offset=$start);
				$length=$end-$start;
				$this->scopusid='2-s2.0-'.substr($eleman, $start,$length);
			}
		}
// Dergi ismi
		$this->dergi=$scopusBilgi['bibliographic_data']['publication_title'];
// Aldığı atıf sayısı
		if (isset ($scopusBilgi['plum_print_counts']['citation']))
			$this->atif=$scopusBilgi['plum_print_counts']['citation']['total'];
// yayınevi
		if (isset($scopusBilgi['bibliographic_data']['publisher']))
			$this->publisher = $scopusBilgi['bibliographic_data']['publisher'];
// issn. eissn şimdiye kadar görülmedi
		if (isset($scopusBilgi['bibliographic_data']['issn'])) {
			$issntext=$scopusBilgi['bibliographic_data']['issn'];
			$this->ISSN=substr ($issntext,0,4).'-'.substr ($issntext,4,4);
		}
// isbn, kitaplar için
		if (isset($scopusBilgi['identifier']['isbn'][0]))
			$this->ISBN=$scopusBilgi['identifier']['isbn'][0];
		if (isset($scopusBilgi['identifier']['isbn'][1]))
			$this->ISBN=$this->ISBN.'; '.$scopusBilgi['identifier']['isbn'][1];
// Derginin basıldığı / yayımlandığı yıl
		if (isset($scopusBilgi['bibliographic_data']['publication_year']))
			$this->Year= $scopusBilgi['bibliographic_data']['publication_year'];
// cilt
		if (isset($scopusBilgi['bibliographic_data']['volume']))
			$this->Volume=$scopusBilgi['bibliographic_data']['volume'];
// sayı
		if (isset($scopusBilgi['bibliographic_data']['issue']))
			$this->Issue= $scopusBilgi['bibliographic_data']['issue'];

// başlangıç-bitiş sayfası
		if (isset($scopusBilgi['bibliographic_data']['start_page']))
			$this->StartPage=$scopusBilgi['bibliographic_data']['start_page'];
		if (isset($scopusBilgi['bibliographic_data']['end_page']))
			$this->EndPage=$scopusBilgi['bibliographic_data']['end_page'];
// başlangıç-bitiş sayfası
		if (isset ($scopusBilgi['bibliographic_data']['page_range'])) { // sayfa numaraları
			$sayfalar=explode ("-", $scopusBilgi['bibliographic_data']['page_range']);
			$this->StartPage= $sayfalar[0];
			if (isset ($sayfalar[1]))
				$this->EndPage=$sayfalar[1];
	}
// yazarlar
		$this->yazarlar="";
// yazar sayısı
		$this->yazarS=0;
		if (isset ($scopusBilgi['bibliographic_data']['authors'] )) { // bu yayının yazarı yok: 85129231682
//		print_r ($scopusBilgi['bibliographic_data']['authors']);
			foreach( $scopusBilgi['bibliographic_data']['authors'] as $eleman) {
				$isim='';
				$soyisim='';
				if (strpos ($eleman, ',')) {// soyad, ad sırası gözetilmiş
					$soyadAd=explode (", ", $eleman);
					$soyisim=$soyadAd[0];
					if (isset($soyadAd[1]))
						$isim=$soyadAd[1];
					$this->yazarlar.=$isim." ".$soyisim.", ";
			} else $this->yazarlar.=$eleman.", "; // ad.soyad
				$this->yazarS=$this->yazarS+1;	
				}
		}
		$this->yazarlar=substr ($this->yazarlar,0,-2);
	} // final function plumPublication 	
}