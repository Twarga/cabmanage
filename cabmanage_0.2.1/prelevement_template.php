<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prelevement Paper</title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-size: 12px;
            -webkit-print-color-adjust: exact;
        }
        .container {
            border: 1px solid #000;
            padding: 5mm;
            position: relative;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 18px;
        }
        .header {
            font-size: 20px;
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 5px 0;
            margin-bottom: 5px;
        }
        .header span {
            display: block;
        }
        .section {
            margin-top: 5px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
            margin-bottom: 3px;
        }
        .barcode {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 30px;
            margin-top: 5px;
        }
        .barcode img {
            width: 100px; /* Adjust the width to fit the space */
            height: auto; /* Maintain aspect ratio */
        }
        .section-content {
            margin-top: 3px;
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
            margin-top: 3px;
        }
        th, td {
            border: 1px solid #000;
            padding: 2px;
            text-align: left;
        }
        .tickets {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }
        .ticket {
            width: 19%;
            border: 2px dashed #000;
            padding: 3px;
            text-align: center;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #000;
            padding-top: 3px;
            margin-top: 5px;
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
        .footer-info {
            text-align: center;
            font-size: 12px;
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
        }
        .dotted {
            border-top: 3px dashed;
        }
        .no-print {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span style="font-weight: bold;">مخبر التشريح المرضى لتحليل الأنسجة والخلايا سلا</span>
            <span style="font-weight: bold;">LABORATOIRE D’ANATOMIE ET DE CYTOLOGIE PATHOLOGIQUES DE SALÉ</span>
            <span style="font-weight: bold;">Dr. Lalla malika Maliki - د. لالة مليكة ملكي</span>

        </div>
        <div class="title">Reçu à rapporter lors du retrait des résultats</div>
        <div class="section">
            <table>
                <tr>
                    <td>Nom et Prénom: <?php echo htmlspecialchars($_GET['patient_name']); ?></td>
                    <td>Code Patient: <?php echo htmlspecialchars($_GET['patient_code']); ?></td>
                </tr>
                <tr>
                    <td>Médecin: <?php echo htmlspecialchars($_GET['doctor_name']); ?></td>
                    <td>Référence: <?php echo htmlspecialchars($_GET['reference']); ?></td>
                </tr>
                <tr>
                    <td>Date: <?php echo htmlspecialchars($_GET['date']); ?></td>
                    <td>Date de Réception: <?php echo htmlspecialchars($_GET['date']); ?></td>
                </tr>
            </table>
            <div class="barcode">
                <img src="Front/imag/barcode.png" alt="Barcode">
            </div>
        </div>
        <hr class="dotted">
        <div class="section">
            <div class="section-title">Tickets</div>
            <div class="tickets">
                <div class="ticket">
                    <strong>C-<?php echo htmlspecialchars($_GET['patient_code']); ?>-P<?php echo htmlspecialchars($_GET['reference']); ?></strong><br>
                    <?php echo htmlspecialchars($_GET['date']); ?>
                </div>
                <div class="ticket">
                    <strong>C-<?php echo htmlspecialchars($_GET['patient_code']); ?>-P<?php echo htmlspecialchars($_GET['reference']); ?></strong><br>
                    <?php echo htmlspecialchars($_GET['date']); ?>
                </div>
                <div class="ticket">
                    <strong>C-<?php echo htmlspecialchars($_GET['patient_code']); ?>-P<?php echo htmlspecialchars($_GET['reference']); ?></strong><br>
                    <?php echo htmlspecialchars($_GET['date']); ?>
                </div>
                <div class="ticket">
                    <strong>C-<?php echo htmlspecialchars($_GET['patient_code']); ?>-P<?php echo htmlspecialchars($_GET['reference']); ?></strong><br>
                    <?php echo htmlspecialchars($_GET['date']); ?>
                </div>
                <div class="ticket">
                    <strong>C-<?php echo htmlspecialchars($_GET['patient_code']); ?>-P<?php echo htmlspecialchars($_GET['reference']); ?></strong><br>
                    <?php echo htmlspecialchars($_GET['date']); ?>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-title">Facture</div>
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
        <div class="section">
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
                <strong>Ordonnances</strong>: <br>
                <strong>Prélevements</strong>: <?php echo htmlspecialchars($_GET['prelevements']); ?><br>
                <strong>Nombre de Flacons/lames</strong>: <?php echo htmlspecialchars($_GET['num_flacons']); ?><br>
                <strong>Compléments</strong>:<br>
                <strong>Historique</strong>:
            </div>
            <div>
                <div class="barcode">
                    <img src="Front/imag/barcode.png" alt="Barcode">
                </div>
            </div>
        </div>
        <div class="footer-info">
            <hr>
            <p>
                <strong>05 37 84 46 28 | centrepathologiesale@gmail.com</strong><br>
                <strong>Résidence Saada 2, Bureau numéro 2, Avenue Benguerir, Bettana Salé</strong>
            </p>
        </div>
    </div>
</body>
</html>
