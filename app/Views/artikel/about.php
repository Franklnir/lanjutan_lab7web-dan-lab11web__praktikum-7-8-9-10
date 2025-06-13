<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
</head>

<body>
    <div id="container">
        <?= $this->include('template/header'); ?>
        <article>
            <h1>
                <?= $title; ?>
                <h2>Halaman About.</h2>
                <?= $conten; ?>

            </h1>
        </article>
        <aside>
            <?= $this->include('template/footer'); ?>
        </aside>
    </div>
</body>

</html>