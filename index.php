<!DOCTYPE html>
<!-- plum2scopus V2.4: bu yazılım Dr. Zafer Akçalı tarafından oluşturulmuştur 
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
require_once 'getPlumPublication.php';
$s=new getPlumPublication ();

if (isset($_POST['scopusid'])) {
$gelenScopus=trim($_POST["scopusid"]);

if($gelenScopus!=""){
	$s->plumPublication ($gelenScopus);
	} 
}
?>
<a href="eid nerede.png" target="_blank"> Scopus numarasına nereden bakılır? </a>
<form method="post" action="">
Scopus makale numarasını (eid) giriniz. <?php echo ' '.$s->dikkat;?><br/>
<input type="text" name="scopusid" id="scopusid" value="<?php echo $s->scopusid;?>" >
<input type="submit" value="Scopus yayın bilgilerini PHP ile getir">
</form>
<button id="scopusGoster" onclick="scopusGoster()">Scopus yayınını göster</button>
<button id="scopusAtifGoster" onclick="scopusAtifGoster()">Scopus yayınının atıflarını göster</button>
<button id="pubmedGit" onclick="pubmedGit()">pubmed ile yayına git</button>
<button id="doiGit" onclick="doiGit()">doi ile makaleyi göster</button>
<br/>
Scopus eid: <input type="text" name="eid" size="25" id="eid" value="<?php echo $s->scopusid;?>" >  
doi: <input type="text" name="doi" size="55"  id="doi" value="<?php echo $s->doi;?>"> <br/>
Başlık: <input type="text" name="ArticleTitle" size="96"  id="ArticleTitle" value="<?php echo str_replace ('"',  '&#34',$s->ArticleTitle);?>"> <br>
Dergi ismi: <input type="text" name="Title" size="80"  id="Title" value="<?php echo $s->dergi;?>"><br> 
ISSN: <input type="text" name="ISSN" size="8"  id="ISSN" value="<?php echo $s->ISSN;?>">
eISSN: <input type="text" name="eISSN" size="8"  id="eISSN" value="<?php echo $s->eISSN;?>">
ISBN: <input type="text" name="ISBN" size="30"  id="ISBN" value="<?php echo $s->ISBN;?>"> <br>
Yıl: <input type="text" name="Year" size="4"  id="Year" value="<?php echo $s->Year;?>">
Cilt: <input type="text" name="Volume" size="2"  id="Volume" value="<?php echo $s->Volume;?>">
Sayı: <input type="text" name="Issue" size="2"  id="Issue" value="<?php echo $s->Issue;?>">
Sayfa/numara: <input type="text" name="StartPage" size="5"  id="StartPage" value="<?php echo $s->StartPage;?>">
- <input type="text" name="EndPage" size="2"  id="EndPage" value="<?php echo $s->EndPage;?>">
Yazar sayısı: <input type="text" name="yazarS" size="2"  id="yazarS" value="<?php echo $s->yazarS;?>"><br>
Yazarlar: <input type="text" name="yazarlar" size="95"  id="yazarlar" value="<?php echo $s->yazarlar;?>"><br>
Yayınevi: <input type="text" name="publisher" size="26"  id="publisher" value="<?php echo $s->publisher;?>"> <br>
Yayın türü: <input type="text" name="PublicationType" size="20"  id="PublicationType" value="<?php echo $s->PublicationType;?>">
PMID: <input type="text" name="pmid" size="6"  id="pmid" value="<?php echo $s->PMID;?>">
Aldığı atıf: <input type="text" name="citedBy" size="4"  id="citedBy" value="<?php echo $s->atif;?>">
<br/>
Özet <br/>
<textarea rows = "20" cols = "100" name = "ozet" id="ozetAlan"><?php echo $s->AbstractText;?></textarea>  <br/>
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
if ( w != '')
	window.open(urlText,"_blank");
}
function pubmedGit() {
var	w=document.getElementById('pmid').value.replace(/\D/g, "");
urlText = "https://pubmed.ncbi.nlm.nih.gov/"+w;
if ( w != '')
	window.open(urlText,"_blank");
}
</script>
</body>
</html>