<?php

if(! defined('MINICRAFT')) die;


	if(isset($_SESSION['user'])){
		?>
		
			<a href="index.php?action=ajax&method=getResources" target="_blank">getRessources</a><br />
		
		<?php 
	}

?>
		
	</body>
</html>