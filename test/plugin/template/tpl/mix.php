<html>
	<head>
		<title>template {$title}</title>
	</head>
	<body>
		<include file="inc/inc.php"/>
		<hr>
		
		hello world, i am {$name}<br/>
		
		<foreach list="$user_list" key="$user_key" item="$user">
			<if condition="$user['user_name'] == 'bob'">
				bob: key is {$user_key}, name = {$user["user_name"]}, age = {$user['user_age']} <br/>
			<elseif condition="$user['user_name'] == 'jacky'"/>
				jacky: key is {$user_key}, name = {$user["user_name"]}, age = {$user['user_age']} <br/>
			<else/>
				default: key is {$user_key}, name = {$user["user_name"]}, age = {$user['user_age']} <br/>
			</if>
		</foreach>
		<hr>
		
		<if condition="($num == 5) && ($num != 3)">
			ok, ($num == 5) && ($num != 3)
		</if>
		<hr>
		
		<foreach list="$num_list" key="$key" item="$val">
			<if condition="$val == 3">
				3 key is {$key}, val = {$val}<br/>
			<elseif condition="$val == 4"/>
				4 key is {$key}, val = {$val}<br/>
			<elseif condition="$val == 5"/>
				5 key is {$key}, val = {$val}<br/>
			<elseif condition="($val == 6) || ($val == 7)"/>
				6,7 key is {$key}, val = {$val}<br/>
			<else/>
				default key is {$key}, val = {$val}<br/>
			</if>
		</foreach>
		<hr>
		
		<hr>
		
		<include file="inc/inc.php"/>
	</body>
</html>