<?php
	require __DIR__ . "/vendor/autoload.php";

	use CoffeeCode\Router\Router;

	$router = new Router("http://localhost/csv");

	$router->group(null);
	$router->get("/", function($data){
		echo '<!DOCTYPE html>
				<html>
				<head>
					<title>CSV - IMPORT</title>
					<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
				</head>
				<body>
					<div class="container">
						<form method="POST" enctype="multipart/form-data">
							<div class="form-group">
								<label>CSV</label>
								<input type="file" class="form-control" name="csv">
							</div>
							<input type="submit" name="Enviar">
						</form>
					</div>
				</body>
				</html>';
	});

	$router->get("/{endereco}", function($data){
		$endereco = $data["endereco"];

		$conn = new mysqli("localhost", "root", "", "csv");
		$table = "csv";

		
		$select = $conn->query("
					SELECT * FROM $table WHERE endereco = '$endereco'
				");

		$values = $select->fetch_assoc();

		if(empty($values))
			echo 0;
		else
			echo $values["saldo"];

		$conn->close();
	});

	$router->post("/", function($data){
		if(isset($_FILES["csv"])){
			if($_FILES["csv"]["type"] === "application/vnd.ms-excel"){
				$row = 1;
				$file = $_FILES["csv"]["name"];

				move_uploaded_file($_FILES["csv"]["tmp_name"], "arquivos/".$file);

				$dataCsv = array();

				if (($handle = fopen("arquivos/".$file, "r")) !== FALSE) {
				    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				        
				        array_push($dataCsv, $data);

				    }
				    fclose($handle);
				}

				$conn = new mysqli("localhost", "root", "", "csv");
				$table = "csv";

				foreach($dataCsv as $value){
					$conn->query("
						INSERT INTO  $table (endereco,saldo) VALUES('".$value[0]."','".$value[1]."')
					");
				}

				$conn->close();

				echo "Arquivo Importado.";
			} else {
				echo "Envie apenas arquivos csv";
			}
		}
	});

	$router->dispatch();
?>