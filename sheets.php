<?Php
class sheets
{
	//First scan the front of the pages, starting from the center page
	//Then scan the back of the pages, starting from the cover page
	private function pad($int)
	{
		return str_pad($int,2,'0',STR_PAD_LEFT);
	}
	
	function __construct($sheets)
	{
		$sheets=$sheets;
		$pages=$sheets*4;
		
		$this->inside['l'][0]=$pages/2; //The left center page is total pages/2
		$this->inside['r'][0]=$pages/2+1; //The rigt center page is total pages/2+1
		$this->outside['l'][0]=$pages; //The left cover page is total pages
		$this->outside['r'][0]='01'; //The right cover page is the first page
		
		for ($i=1; $i<$sheets; $i++)
		{
				$this->inside['l'][$i]=$this->pad($this->inside['l'][$i-1]-2); //Left front
				$this->inside['r'][$i]=$this->pad($this->inside['r'][$i-1]+2); //Right front
				$this->outside['l'][$i]=$this->pad($this->outside['l'][$i-1]-2); //Left back
				$this->outside['r'][$i]=$this->pad($this->outside['r'][$i-1]+2); //Right back
		
		}
	}
}