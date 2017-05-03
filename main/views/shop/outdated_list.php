<?php 

if (isset($outdated) && $outdated->getTotalCount()>0)
{
?>

<?php 
	foreach ($outdated->result() as $item)
	{
?>
<?php 
	}
?>
</table>
<?php 
}

?>