<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'NimbusDesk' ?></title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/NimbusDesk/assets/images/icon.png">
    <!-- Bootstrap 5 CSS -->
    <link href="/NimbusDesk/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="/NimbusDesk/assets/vendor/fontawesome/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/NimbusDesk/assets/css/style.css">
    <style>
        body {
            background-color: var(--nimbus-maroon); /* Dark maroon background for login */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
        }
        .login-card {
            background-color: var(--nimbus-bg);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
    </style>
</head>
<body>

    <?= $content ?>

    <!-- Bootstrap 5 JS -->
    <script src="/NimbusDesk/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
