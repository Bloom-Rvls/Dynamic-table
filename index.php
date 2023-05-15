<?php

use App\NumberHelper;

define('PER_PAGE', 20);

require 'vendor/autoload.php';
$pdo = new PDO('mysql:host=localhost:3306;dbname=biens_db;charset=utf8','root','',[
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$query = "SELECT * FROM liste";
$queryCount = "SELECT COUNT(id) as count FROM liste";
$params = [];

//searching by city name
if(!empty($_GET['q'])) {
    $query .= " WHERE city LIKE :city";
    $params['city'] = '%' . $_GET['q'] . '%';
}

//Pagination
$page = (int)($_GET['p'] ?? 1);
$offset = ($page-1) * PER_PAGE;

$query .= " LIMIT " . PER_PAGE . " OFFSET $offset";

$statement = $pdo->prepare($query);
$statement->execute($params);
$products = $statement->fetchAll();

$statement = $pdo->prepare($queryCount);
$statement->execute();
$count = (int)$statement->fetch()['count'];
$pages = ceil($count / PER_PAGE);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Biens immobibliers</title>
</head>
<body class="p-4">
    <h2>Les biens immobliers</h2>
    <form action="" class="mb-4">
        <div class="form-group mb-4">
            <input type="text" class="form-control" name="q" placeholder="Rechercher par ville" value="<?= htmlentities($_GET['q'] ?? null)?>">
        </div>
        <button class="btn btn-primary">Rechercher</button>

    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prix</th>
                <th>Ville</th>
                <th>Adresse</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $product): ?>
                <tr>
                    <td>#<?= $product["id"]?></td>
                    <td><?= $product["name"]?></td>
                    <td><?= NumberHelper::price($product["price"])?></td>
                    <td><?= $product["city"]?></td>
                    <td><?= $product["address"]?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if($pages > 1 && $page >1): ?>
        <a href="?p=<?= $page - 1?>" class="btn btn-primary">Page precedente</a>
    <?php endif; ?>
    <?php if($pages > 1 && $page < $pages): ?>
        <a href="?p=<?= $page + 1?>" class="btn btn-primary">Page suivante</a>
    <?php endif; ?>
    
</body>
</html>