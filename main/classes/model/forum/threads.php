<?php

Class Model_Forum_Threads extends DL
{
	
	public function moveThreadsToFid($threads, $fid)
	{
		$ids = false;
		foreach ($threads as $th)
		{
			if ($thid = (int) $th)
			{
				$ids[] = $thid;
			}
		}
		if ($ids) 
		{
			$ids = implode(',', $ids);
			if (DB::beginTransaction())
			{
				$fid = (int) $fid;
				$data = array('fid'=>$fid);
				if (DB::update('mybb_threads', $data, 'tid IN ('.$ids.')'))
				{
					DB::commitTransaction();
					return true;
				}
			}
		}
	}
	
}

?>