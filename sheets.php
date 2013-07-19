<?Php
//First scan the front of the pages, starting from the center page
//Then scan the back of the pages, starting from the cover page
function pad($int)
{
return str_pad($int,2,'0',STR_PAD_LEFT);
}


if(!isset($sheets) && isset($pages))
	$sheets=$pages/4; //4 pages per sheet
elseif(isset($sheets) && !isset($pages))
	$pages=$sheets*4;

$scans['l'][0]=$pages/2; //The left center page is total pages/2
$scans['r'][0]=$pages/2+1; //The rigt center page is total pages/2+1
$scans2['l'][0]=$pages; //The left cover page is total pages
$scans2['r'][0]='01'; //The right cover page is the first page



for ($i=1; $i<$sheets; $i++)
{
$scans['l'][$i]=pad($scans['l'][$i-1]-2); //Left front
$scans['r'][$i]=pad($scans['r'][$i-1]+2); //Right front
$scans2['l'][$i]=pad($scans2['l'][$i-1]-2); //Left back
$scans2['r'][$i]=pad($scans2['r'][$i-1]+2); //Right back

}
//print_r($scans);