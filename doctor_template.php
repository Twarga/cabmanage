<?php
function renderTemplate($pageTitle, $cssFile, $content) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($pageTitle); ?></title>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($cssFile); ?>">
        <link rel="icon" href="Front/imag/logo.png" type="image/x-icon">
    </head>
    <body>
        <div class="container">
            <div class="top-bar">
                <div class="logo-section">
                    <img src="Front/imag/logo.png" alt="Laboratory Logo" class="logo">
                </div>
                <div class="nav-buttons">
                    <button class="nav-button" onclick="location.href='doctor_dashboard.php'">Tableau de bord</button>
                    <button class="nav-button" onclick="location.href='patient_management.php'">Patient</button>
                    <button class="nav-button" onclick="location.href='prelevement_management.php'">Prélèvement</button>
                    <button class="nav-button" onclick="location.href='examen.php'">Examen</button>
                    <div class="user-section">
                        <img src="Front/imag/user.png" alt="User Icon" class="user-icon">
                    </div>
                    <button class="nav-button">Paramétrage</button>
                </div>
                <div class="btn-logout">
                    <button class="btn-logout" onclick="location.href='logout.php'"><img src="Front/imag/logout.png" alt="Logout Button Icon"></button>
                </div>            
            </div>
            <div class="content-area">
                <?php echo $content; ?>
            </div>
        </div>
        <script src="dashdocteur.js"></script>
    </body>
    </html>
    <?php
}
?>
