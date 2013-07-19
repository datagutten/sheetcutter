<?Php
error_reporting(E_ALL);
ini_set('display_errors', True);  

$path=$argv[1];
if(!file_exists($path))
	die("$path could not be found\n");
$removewhite=false; //set to true if whitespace should be removed or false to use the size of the reference page as it is
$dir=scandir($path);
$format='png';
$format_in='png';
$rotate=90;
$rotate_reference=true;
$debug=false;

if($debug==true)
{
	mkdir($path.'/debug');
	mkdir($path.'/debug/sheets');
}


unset($dir[array_search('..',$dir)],$dir[array_search('.',$dir)],$dir[array_search('Thumbs.db',$dir)]);

sort($dir);
print_r($dir);
$sheets=count($dir);

include 'sider.php';
if (strpos($path,'outside')!==false)
{
	$scans=$scans2;
	$mode='outside';
}
else
	$mode='inside';
$dir=array_diff($dir,array("reference $mode.$format_in"));



if(strpos($dir[0],'-')!==false) //Check if the filenames contains the page numbers
	$pagefiles=true;
else
	$pagefiles=false;

require 'image.php';

$image_in=new png;


if(($exist=file_exists($referencefile=$path."/reference $mode.$format_in")) && !$removewhite) //If there is a reference file, use it
{
	echo "Using file $referencefile as size reference\n";
	if(!$rotate)
	{
		$size=getimagesize($referencefile);
		$width=$size[0];
		$height=$size[1];
	}
	else
	{
		$ref_im=$image_in->create($referencefile);
		if($rotate_reference)
			$ref_im=imagerotate($ref_im,$rotate,0); //Rotate the reference file
		$width=imagesx($ref_im);
		$height=imagesy($ref_im)+55;
		imagedestroy($ref_im); //We don't need the reference image after measuring the size
	}

}
elseif($removewhite)
	echo "Reference file $referencefile not found, finding size by removing whitespace\n";
else
die("Reference file $referencefile not found and whitespace is not set to be removed\n");

//for ($i=1; $i<=$pages/4; $i++)
foreach ($dir as $i=>$infile)
{
	if(is_dir($infile) || strpos($infile,'.psd'))
		continue;
	$outdir=$path.'/cutted'.$format;
	if(!file_exists($outdir))
		mkdir($outdir);
	if(preg_match('^([0-9]+)\-([0-9]+)^',$infile,$pagenumbers))
		list(,$leftpage,$rightpage) = $pagenumbers;
	else
	{
		$leftpage=$scans['l'][$i];
		$rigtpage=$scans['r'][$i];
	}
	//$leftfile=$outdir."/{$scans['l'][$i]}.$format";
	//$rightfile="$outdir/{$scans['r'][$i]}.$format";
	$leftfile=$outdir.'/'.$leftpage.'.'.$format;
	$rightfile=$outdir.'/'.$rightpage.'.'.$format;
	
	$infile=$path.'/'.$infile;
		
	if (!file_exists($leftfile) || !file_exists($rightfile))
	{
		echo "Reading file ".$dir[$i].': '.$leftpage.'/'.$rightpage."<br>\n";
		//continue;
		$input=$image_in->create($infile); //Create image resource from the input file
		if($rotate!==false)
		{
			echo "Rotating image\n";
			$im=imagerotate($input,$rotate,0);
			if($debug)
				$image->output($im,$path.'/debug/sheets/'.$leftpage.'-'.$rightpage.'.'.$format);
		}
		else
			$im=$input;
		if(!isset($height) || !isset($width))
		{
			echo "Removing white space\n";
			require 'removewhite.php';
			list($cropped,$width,$height)=remove_white_space($im,0xF0F0F0); //Remove white space to find the sheet size
			if($debug)
				imagejpeg($cropped,$path.'/debug/whitespace.jpg');
			imagedestroy($cropped);

		}
		$halfwidth=$width/2; //The output page should have half width

		if (!file_exists($leftfile))
		{
			$leftpart = imagecreatetruecolor($halfwidth, $height);
			imagefill($leftpart,0,0,imagecolorallocate($leftpart,255,255,255));
			imagecopy($leftpart,$im,0,0,0,0,$width,$height); //Get the left page
			echo "Writing page $leftpage<br>\n";

			$image->output($leftpart,$leftfile); //Write to file
			//imagejpeg($leftpart,$leftfile);
			imagedestroy($leftpart);
		}
		if (!file_exists($rightfile))
		{
			$rightpart = imagecreatetruecolor($halfwidth, $height);
			imagefill($rightpart,0,0,imagecolorallocate($rightpart,255,255,255));
			imagecopy($rightpart,$im,0,0,$halfwidth,0,$width,$height); //Get the right page
			echo "Writing page $rightpage<br>\n";
			$image->output($rightpart,$rightfile);
			//imagejpeg($rightpart,$rightfile);
			imagedestroy($rightpart);
		}
		imagedestroy($im);
	}
}