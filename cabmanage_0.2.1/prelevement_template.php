<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prelevement Paper</title>
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
                font-size: 12px;
            }
            .container {
                border: 1px solid #000;
                padding: 10px;
                page-break-after: always;
                position: relative;
                min-height: 100vh;
                box-sizing: border-box;
            }
            .title {
                text-align: center;
                font-weight: bold;
                margin-bottom: 10px;
            }
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
                padding: 5px 0;
            }
            .header div {
                width: 48%;
            }
            .header div:first-child {
                border-right: 1px solid #000;
                padding-right: 5px;
            }
            .header div:last-child {
                padding-left: 5px;
            }
            .section {
                margin-top: 10px;
            }
            .section-title {
                font-weight: bold;
                border-bottom: 1px solid #000;
                padding-bottom: 5px;
                margin-bottom: 5px;
            }
            .barcode {
                display: flex;
                justify-content: center;
                align-items: center;
                height: 30px;
                margin-top: 10px;
            }
            .section-content {
                margin-top: 5px;
            }
            .table-container {
                display: flex;
                justify-content: space-between;
            }
            .table-container div {
                width: 48%;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 5px;
            }
            th, td {
                border: 1px solid #000;
                padding: 3px;
                text-align: left;
            }
            .footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                border-top: 1px solid #000;
                padding-top: 5px;
                margin-top: 10px;
                position: absolute;
                bottom: 0;
                width: 100%;
                box-sizing: border-box;
            }
            .footer div {
                width: 48%;
            }
            .footer div:first-child {
                border-right: 1px solid #000;
                padding-right: 5px;
            }
            .footer div:last-child {
                padding-left: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">
            <span style="font-weight: bold;">مخبر التشريح المرضى لتحليل الأنسجة والخلايا سلا</span><br>
            <span style="font-weight: bold;">LABORATOIRE D’ANATOMIE ET DE CYTOLOGIE PATHOLOGIQUES DE SALÉ</span>
        </div>
        <div class="header">
            <div>
                <strong>Reçu à rapporter lors du retrait des résultats</strong><br>
                Nom et Prénom: <?php echo htmlspecialchars($_GET['patient_name']); ?><br>
                Code Patient: <?php echo htmlspecialchars($_GET['patient_code']); ?><br>
                Date: <?php echo htmlspecialchars($_GET['date']); ?><br>
                Référence: <?php echo htmlspecialchars($_GET['reference']); ?><br>
                Médecin: <?php echo htmlspecialchars($_GET['doctor_name']); ?><br>
                Net à payer: <?php echo htmlspecialchars($_GET['total_price']); ?> DH<br>
                Avance: <?php echo htmlspecialchars($_GET['advance']); ?> DH<br>
                Solde: <?php echo htmlspecialchars($_GET['balance']); ?> DH<br>
                <div class="barcode">
                    <img src="barcode-placeholder.png" alt="Barcode">
                </div>
            </div>
            <div>
                <strong>CARTE DE DOSSIER</strong><br>
                Nom et Prénom: <?php echo htmlspecialchars($_GET['patient_name']); ?><br>
                Code: <?php echo htmlspecialchars($_GET['patient_code']); ?><br>
                <div class="barcode">
                    <img src="barcode-placeholder.png" alt="Barcode">
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Facture</div>
            <div class="section-content">
                <table>
                    <tr>
                        <th>Net à payer</th>
                        <th>Avance</th>
                        <th>Solde</th>
                    </tr>
                    <tr>
                        <td><?php echo htmlspecialchars($_GET['total_price']); ?> DH</td>
                        <td><?php echo htmlspecialchars($_GET['advance']); ?> DH</td>
                        <td><?php echo htmlspecialchars($_GET['balance']); ?> DH</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Ordonnances</div>
            <div class="table-container">
                <div>
                    <table>
                        <tr>
                            <th>Réception</th>
                            <td><?php echo htmlspecialchars($_GET['date']); ?></td>
                        </tr>
                        <tr>
                            <th>Référence</th>
                            <td><?php echo htmlspecialchars($_GET['reference']); ?></td>
                        </tr>
                        <tr>
                            <th>Créé par</th>
                            <td><?php echo htmlspecialchars($_GET['created_by']); ?></td>
                        </tr>
                    </table>
                </div>
                <div>
                    <table>
                        <tr>
                            <th>Code Patient</th>
                            <td><?php echo htmlspecialchars($_GET['patient_code']); ?></td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td><?php echo htmlspecialchars($_GET['date']); ?></td>
                        </tr>
                        <tr>
                            <th>Age</th>
                            <td><?php echo htmlspecialchars($_GET['age']); ?></td>
                        </tr>
                        <tr>
                            <th>Tél</th>
                            <td><?php echo htmlspecialchars($_GET['telephone']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="footer">
            <div>
                <strong>Prélevements</strong>: <?php echo htmlspecialchars($_GET['prelevements']); ?><br>
                <strong>Nombre de Flacons/lames</strong>: <?php echo htmlspecialchars($_GET['num_flacons']); ?><br>
                <strong>Compléments</strong>: <?php echo htmlspecialchars($_GET['complements']); ?><br>
                <strong>Historique</strong>: <?php echo htmlspecialchars($_GET['history']); ?>
            </div>
            <div>
                <div class="barcode">
                    <img src="barcode-placeholder.png" alt="Barcode">
                </div>
            </div>
        </div>
        <div class="footer-info">
            <hr>
            <p style="text-align: center;">
                <strong>05 37 84 46 28 | centrepathologiesale@gmail.com</strong><br>
                <strong>Résidence Saada 2, Bureau numéro 2, Avenue Benguerir, Bettana Salé</strong>
            </p>
        </div>
    </div>
</body>
</html>
