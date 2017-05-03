<? 
class Paging extends DL 
{ 

	private $page_size = 20; 
	private $link_padding = 10;
	private $page_link_separator = ' '; 
	private $page_var = 'page'; 

	private $q; 
	private $total_rows; 
	private $total_pages; 
	private $cur_page; 
	
	private $req_result = false;

	public function __construct($q='', $page_var='page') 
	{ 
		if ($q) $this->setQuery($q); 
		$this->page_var = $page_var; 
		$this->cur_page = isset($_GET[$this->page_var]) && (int)$_GET[$this->page_var] > 0 ? (int)$_GET[$this->page_var] : 1; 
		
		if ($this->session->isAuth())
		{
			$tpp = $this->session->user()->get('tpp')?$this->session->user()->get('tpp'):25;
		}
		else 
			$tpp = 25;
		$this->setPageSize($tpp);
	} 

	public function setQuery($q) 
	{ 
		$this->q = (string) $q;
		return $this;
	} 

	public function setPageCur($cr_page = 1)
	{
		$this->cur_page = abs((int) $cr_page);
		return $this;
	}

	public function getPageCur($cr_page = 1)
	{
		return $this->cur_page;
	}

	public function setPageSize($page_size) 
	{ 
		$this->page_size = abs((int)$page_size); 
		
		return $this;
	} 
	
	public function getTotalCount()
	{
		return $this->total_rows;
	}

	public function execute($q = false) 
	{ 
		if ($q) $this->setPageQuery($q); 

		$q = $this->getQueryPaging();
		
		$this->req_result = $req = DB::query($q);
		$page_res = DB::query('SELECT FOUND_ROWS()')->fetch();
		$this->total_rows = array_pop($page_res); 

		if ($this->page_size !== 0) $this->total_pages = ceil($this->total_rows/$this->page_size); 
		 
		if ($this->cur_page > $this->total_pages) 
		{ 
			return false;
		} 

		return $this;
	}

	public function result()
	{
			return $this->req_result;
	}

	private function getQueryPaging() 
	{ 
		$q = $this->q; 

		if ($this->page_size != 0) 
		{ 
			//calculate the starting row 
			$start = ($this->cur_page-1) * $this->page_size; 
			//insert SQL_CALC_FOUND_ROWS and add the LIMIT 
			$this->q = trim($this->q);
			$q = preg_replace('/^SELECT\s+/i', 'SELECT SQL_CALC_FOUND_ROWS ', $this->q)." LIMIT {$start},{$this->page_size}"; 
		} 

		return $q; 
	} 

	public function getTotalPages()
	{
		return $this->total_pages;
	}

	function createPageLinks($parse_link = '?page={page}')
	{
		//<a href="/forum/forum-'.$fid.'-page-{page}.html" class="pagination_page">{page}</a>
		$total_pages = $this->total_pages;
		$cur_page =  $this->cur_page;
		
		$start = $cur_page - 2;
		if ( $start < 1 ) $start = 1; 
		
		$end = ($cur_page==1)?($cur_page + 3):($cur_page+2);
		
		$paging_block = '';
		
		if ($end > $total_pages ) $end = $total_pages; 
		if ($start > 1 ) {
			$paging_block .= '<a href="'.str_replace('{page}', 1, $parse_link).'" class="pagination_page">1</a> ';
			$paging_block .= $start - 2 > 0 ? ' ... ' : '';
		}
		
		for ($i=$start; $i <= $end; $i++)
		{
			if ($i==$cur_page){
				$paging_block .= '<span class="pagination_current">'.$i.'</span> ';
			} else {
				$paging_block .= '<a href="'.str_replace('{page}', $i, $parse_link).'" class="pagination_page">'.$i.'</a> ';
			}
		}
		if ($end + 1 < $total_pages) $paging_block .= ($end + 2 == $total_pages ? '' : ' ... ' ); 
		if ($end + 1 <= $total_pages) $paging_block .= '<a href="'.str_replace('{page}', $total_pages, $parse_link).'" class="pagination_page">'.$total_pages.'</a> ';
		
		if ($cur_page>1)
		{
			$paging_block = '<a href="'.str_replace('{page}', ($cur_page-1), $parse_link).'" class="pagination_previous">« Предыдущая страница</a> '.$paging_block;
		}

		if ($cur_page<$total_pages)
		{
			$paging_block .= ' <a href="'.str_replace('{page}', ($cur_page+1), $parse_link).'" class="pagination_next">Следующая страница »</a>';
		}
		
		return $paging_block;
		
	}


}
?>