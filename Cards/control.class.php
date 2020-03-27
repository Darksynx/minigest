<?php
class control {
	
	private $_head = '';
	private $_body = '';
	
	private $_db;

	public function __construct($id, &$db, &$dbip) {
		//$this->_db = &$db;
	//echo time(), '<br/>';
	

	
	$resultx = $dbip->query('SELECT * FROM "ip" WHERE "ip" LIKE \'%' . $_SERVER['REMOTE_ADDR'] . '%\' ESCAPE \'\\\' ORDER BY "id" ASC LIMIT 0, 49999;');
	$valx = $resultx->fetchArray(SQLITE3_ASSOC);	
	
	if($valx['inc'] >= 10 and $valx['date'] > time()) {
		$this->loadpage('page de ban',['ban']);		
		exit;
	}
	
	$results = $db->query('SELECT * FROM "validate" WHERE "id" ORDER BY "id" ASC LIMIT 0, 49999');
	while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
		if($row['expire'] < time()) {
			$db->exec('DELETE FROM "users" WHERE "id" LIKE \'%' . $row['id'] . '%\'');	
			$db->exec('DELETE FROM "validate" WHERE "id" LIKE \'%' . $row['id'] . '%\'');
		}
	}
		
		$nolog = true;
		//echo '['.$_SESSION['ciu'].']' . '<br/>';
		//echo '>'.$_COOKIE['ciu'].'<' . '<br/>';
		//echo '>'.$_COOKIE['ctr'].'<' . '<br/>';
		
			if(isset($_SESSION['ciu'])){

				$result = $db->query('SELECT id,idsess,unum,valid FROM "users" WHERE "idsess" LIKE \'%' . $id . '%\' ESCAPE  \'\\\' AND "unum" LIKE \'%' . $_SESSION['ciu'] . '%\' ESCAPE \'\\\' ORDER BY "id" DESC LIMIT 0, 49999;');
				$val = $result->fetchArray(SQLITE3_ASSOC);			
				//var_dump($val);
				
				if($val['idsess'] == $id && $val['unum'] == $_SESSION['ciu'] && $_SESSION['ciu'] == $_COOKIE['ciu'] && $val['valid'] == '1') {
					$nolog = false;
					$this->loadpage('page de salon',['salon']);
				}
			}
			
			
			if( $nolog ) {
				if(isset($_COOKIE['ctr']) && $_COOKIE['ctr'] == 'register'){
					
					setcookie('ctr', 'null');
					$this->loadpage('page de d\'enregistrement',['register']);
					
						
				} else {
					$this->loadpage('page de login',['login']);
				}
			}
		
	}
	
	private function loadpage($name,$mypagex=[],$style='') {
		$link = ['jquery-3.4.1.min.js','jadc.js']; 
		$this->head($name,$link,$style);
		
		foreach($mypagex as $mypage) {
			switch($mypage) {
				case 'ban' :
					$this->_body .= file_get_contents('html/ban.html');
				break;
				case 'login' :
					$this->_body .= file_get_contents('html/login.html');
				break;
				case 'register':
					$this->_body .= file_get_contents('html/register.html');
				break;
				case 'salon';
					$this->_body .= file_get_contents('html/salon.html');
				break;
		}}
		$this->page();
	}
	
	private function head($name='',$link=[],$style='',$links='') {
		foreach($link as $l){ $links .= "<script type=\"text/javascript\" src=\"js/$l\"></script>\r\n";}
		$links .= '<link href="css/style.css" type="text/css" rel="stylesheet" />';
		$this->_head .= "<title>$name</title>$links<style>$style</style>";
	}
	
	
	
	
	/*------------------------------------------------------*/
	
	private function page() {
		$header = file_get_contents('html/header.html');
		$footer = file_get_contents('html/footer.html');	
		echo '<html><head>' . $this->_head . '</head><body>' . $header  . '<div id="center">' . $this->_body . '</div>' .  $footer . '</body></html>';
	}


	
	
}
?>