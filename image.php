<?Php
class png
{
	public function create($file)
	{
		return imagecreatefrompng($file);	
	}
	public function output($im,$file=NULL)
	{
		return imagepng($im,$file,9);
	}
}
class jpeg
{
	public function create($file)
	{
		return imagecreatefromjpeg($file);	
	}
	public function output($im,$file=NULL)
	{
		return imagejpeg($im,$file);
	}
}


if($format=='jpg')
	$image=new jpeg;
elseif($format=='png')
	$image=new png;
else
	die("Invalid format\n");
