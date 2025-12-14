<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Quick start. Local server-side application with UI</title>
    <script src="//api.bitrix24.com/api/v1/"></script>
</head>
<body>
	<div id="auth-data">OAuth 2.0 data from REQUEST:
		<pre><?php
			print_r($_REQUEST);
			?>
		</pre>
	</div>
	<div id="name">
		<?php
		require_once (__DIR__.'/crestcurrent.php');

		//$result = CRest::call('user.current');
		$result = CRestCurrent::call('user.current');

		echo $result['result']['NAME'].' '.$result['result']['LAST_NAME'];
		?>
	</div>
    <script>
            BX24.callMethod(
                "crm.contact.update",
                {
                    id: 8,
                    fields: {
                        NAME: "Сергей",
                        BIRTHDATE: '11.11.1999',
                        TYPE_ID: "RECOMMENDATION",
                        SOURCE_ID: "WEB",
                        POST: "Администратор компьютерных сетей",
                        COMMENTS: "Новый комментарий",
                        OPENED: "N",
                        EXPORT: "Y",
                        ASSIGNED_BY_ID: 1,
                        COMPANY_ID: 7,
                        COMPANY_IDS: [2, 3],
                    },
                    params: {
                        REGISTER_SONET_EVENT: "N",
                        REGISTER_HISTORY_EVENT: "N",
                    },
                },
                function (result) {
                    if (result.error()) {
                        console.error(result.error());
                    } else {
                        console.log(result.data());
                    }
                }
            );
    </script>
</body>
</html>