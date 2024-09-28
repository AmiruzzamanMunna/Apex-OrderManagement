<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcom</title>
</head>
<style>
    $brandColor: darkorchid;

    body {
    font-family: system-ui, sans-serif;
    background: linear-gradient(to bottom,
        $brandColor,
        darken($brandColor, 15%)
    );
    color: white;
    height: 100vh;
    margin: 0;
    display: grid;
    place-items: center;
    }
    h1{
        text-align : center;
        padding-top : 15%;
    }
</style>
<body>
    <h1>👋 Hello, I'm Amiruzzaman</h1>
</body>
</html>
<script>
    document.getElementsByTagName("h1")[0].style.fontSize = "3vw";
</script>