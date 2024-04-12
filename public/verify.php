<?php

session_start();

$arr = explode('-', $_GET['key']);
// print_r($arr);
include '../dbConnect.php';

$rq = $pdo->prepare('SELECT id FROM users WHERE id LIKE :id AND password like :code');
$rq->bindValue(':id', $arr[0]);
$rq->bindValue(':code', '%' . $arr[1]);
$rq->execute();
$result = $rq->fetchAll()[0];

if(isset($result)){
    if($result != [] && $result != ''){
        // echo $result['id'];
        $apiKey = strtoupper(hash('sha256', $_GET['key']));
        // echo $apiKey;
        $rq = $pdo->prepare('UPDATE users SET verified=:apiKey WHERE id=:id');
        $rq->bindValue(':id', $arr[0]);
        $rq->bindValue(':apiKey', $apiKey);
        $rq->execute();

        $_SESSION['verified'] = $apiKey;

        $rq = $pdo->prepare("INSERT INTO apikeys(api_key) VALUES ('$apiKey')");
        $rq->execute();

        $_SESSION['uses'] = 0;

        header("Location: " . "http://" . $_SERVER['HTTP_HOST']);

    }else{
        echo 'bad link';
    }
}else{
    echo 'bad link';
}


// unset($_SESSION['verified']);





