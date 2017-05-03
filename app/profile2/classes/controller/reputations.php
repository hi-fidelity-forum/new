<?php defined('SYSPATH') or die('No direct access allowed.');

// Описание класса
class Controller_Reputations extends Profile
{
	
	function action_index()
	{

		$display_group = $this->user->getDisplayGroupId();
		if ($this->user && !($this->user->isAdmin() || $this->user->isModer() || $display_group == '6'))
		{
			$this->page_title_prefix = $this->page_title_prefix.' - Репутация';
			$this->breadcrumbs->add('Репутация',$this->request->controller_uri().'/reputations');
			
			$reputations = $this->user->getReputations();
			$reputations_all = $reputations->getAll();
			
			$reputation_neg = $reputations->countNegative();
			
			$this->content = '';
			
			$user_group_display = $this->session->user()->getDisplayGroup();
			$user_group_activity = $this->session->user()->getGroupByActivity();
			
			$ud = $this->user->getDisplayGroup();
			//var_export($ud['canusercp']);
			
			if ($this->session->isAuth() && ($this->session->user()->get('uid') != $this->user->get('uid')) && $user_group_display['cangivereputations'] && $ud['canusercp'] || $this->session->user()->isAdmin())
			{
				$max_rep_display = (int) $user_group_display['reputationpower'];
				$max_rep_activity = (int) $user_group_activity['reputationpower'];
				
				$max_rep = $max_rep_display>=$max_rep_activity?$max_rep_display:$max_rep_activity;
				
				if ($max_rep > 0)
				{
					if ($_POST && !($this->user->isModer()))
					{
						if (isset($_POST['change']) && isset($_POST['comments']))
						{
							$author_id = $this->session->user()->get('uid');
							$comments = (string) $_POST['comments'];
							$rep = (int) $_POST['reputation'];
							
							if ($reputations->put($author_id, $rep, $comments))
							{
								$this->request->redirect('/'.$this->request->uri());
							}
							else 
							{
								$this->content .= 'Произошла ошибка, репутация не изменена';
							}
						}
					}
					
					if ($_GET && isset($_GET['delete']) && ($rid = (int) $_GET['delete']))
					{
						if ($reputations->remove($rid))
						{
							$this->request->redirect('/'.$this->request->uri());
						}
						else 
						{
							$this->content .= 'Произошла ошибка, не удалось удалить отзыв';
						}
					}
					
					$user_rep = $reputations->getByAuthor($this->session->user()->get('uid'));
					$this->content .= View::factory('reputations/reputation_change')
										->set('user_reputation', $user_rep)->set('user', $this->user)
										->set('max_rep', $max_rep);
				}
			}
			
			$this->content .= View::factory('reputations/reputations')
				->set('reputations', $reputations_all)->set('user', $this->user)
				->set('reputation_neg', $reputation_neg);
		}
		else 
		{
			$this->content = 'Страница не доступна';
		}
	}
	
}