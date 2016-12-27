<?php
    $redirect_uri = $_GET['redirect_uri'];
    $account_linking_token = $_GET['account_linking_token'];
?>

<html>
    <body>
        <form action="validate.php" method="GET">
		
            <input type="hidden" name="redirect_uri" value="<?=$redirect_uri?>"/>
            <input type="hidden" name="account_linking_token" value="<?=$account_linking_token?>"/>
            <input type="hidden" name="authorization_code" value="my_authorization_code"/>
			
			Uusário:
            <input type="text" name="username"/>
            </br>
			Senha:
            <input type="password" name="password"/>
            </br>
			
            <input type="submit"/>
        </form>
    </body>
</html>
    