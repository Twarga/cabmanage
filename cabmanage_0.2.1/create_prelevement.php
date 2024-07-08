<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prélèvement</title>
    <link rel="stylesheet" href="Front/prelevment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="Front\imag\logo.png" type="image/x-icon">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .dropdown-search {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .dropdown-search input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .dropdown-search-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 100%;
            overflow: auto;
            border: 1px solid #ddd;
            z-index: 1;
        }

        .dropdown-search-content a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-search a:hover {
            background-color: #ddd;
        }

        .action-buttons a {
            margin: 0 5px;
            color: #4ACCD1;
            text-decoration: none;
        }

        .action-buttons a:hover {
            color: #007BFF;
        }
    </style>
    <script>
        $(document).ready(function () {
            $('#search_docteur_exterieur').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                $('.dropdown-search-content.docteur_exterieur a').each(function () {
                    if ($(this).text().toLowerCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#search_docteur_exterieur').on('focus', function () {
                $('.dropdown-search-content.docteur_exterieur').show();
            });

            $('#search_docteur_exterieur').on('blur', function () {
                setTimeout(function () {
                    $('.dropdown-search-content.docteur_exterieur').hide();
                }, 200);
            });

            $('.dropdown-search-content.docteur_exterieur a').on('click', function () {
                $('#search_docteur_exterieur').val($(this).text());
                $('#docteur_exterieur_id').val($(this).data('id'));
                $('.dropdown-search-content.docteur_exterieur').hide();
            });

            $('#search_examen').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                $('.dropdown-search-content.examen a').each(function () {
                    if ($(this).text().toLowerCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#search_examen').on('focus', function () {
                $('.dropdown-search-content.examen').show();
            });

            $('#search_examen').on('blur', function () {
                setTimeout(function () {
                    $('.dropdown-search-content.examen').hide();
                }, 200);
            });

            $('.dropdown-search-content.examen a').on('click', function () {
                $('#search_examen').val($(this).text());
                $('#examen_id').val($(this).data('id'));
                $('.dropdown-search-content.examen').hide();
                updateFacture();
            });

            $('#search_template').on('keyup', function () {
                var filter = $(this).val().toLowerCase();
                $('.dropdown-search-content.template a').each(function () {
                    if ($(this).text().toLowerCase().indexOf(filter) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            $('#search_template').on('focus', function () {
                $('.dropdown-search-content.template').show();
            });

            $('#search_template').on('blur', function () {
                setTimeout(function () {
                    $('.dropdown-search-content.template').hide();
                }, 200);
            });

            $('.dropdown-search-content.template a').on('click', function () {
                $('#search_template').val($(this).text());
                $('#rapport_template').val($(this).data('id'));
                $('.dropdown-search-content.template').hide();
                loadTemplate();
            });
        });

        function loadTemplate() {
            const templateId = $('#rapport_template').val();
            if (templateId) {
                $.get('load_template.php', { template_id: templateId }, function(data) {
                    const template = JSON.parse(data);
                    CKEDITOR.instances.rapport_txt.setData(template.content);
                });
            }
        }

        function updateFacture() {
            const examenId = $('#examen_id').val();
            const prixReduit = parseFloat($('#prix_reduit').val()) || 0;
            const avance = parseFloat($('#avance').val()) || 0;

            // Fetch the selected examen price from the dropdown
            const totalPrix = parseFloat($('.dropdown-search-content.examen a[data-id="' + examenId + '"]').data('prix'));

            const montantDu = totalPrix - prixReduit - avance;
            const rest = montantDu;

            $('#total_prix').val(totalPrix);
            $('#montant_du').val(montantDu);
            $('#rest').val(rest);
        }

        function confirmDelete(prelevement_id, patient_id) {
            if (confirm('Are you sure you want to delete this prelevement?')) {
                window.location.href = 'delete_prelevement.php?id=' + prelevement_id + '&patient_id=' + patient_id;
            }
        }

        function saveTemplate() {
            const templateName = prompt('Enter template name:');
            if (templateName) {
                const rapportContent = CKEDITOR.instances.rapport_txt.getData();
                $.post('save_template.php', {
                    template_name: templateName,
                    rapport_txt: rapportContent  // Ensure the key name matches
                }, function(data) {
                    alert(data);
                    location.reload();
                });
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <button id="retour" onclick="window.history.back()">
            <i class="retour-btn"><img src="Front\imag\left-arrow.png"></i>
        </button>
        <form method="post" enctype="multipart/form-data" action="create_prelevement.php?patient_id=<?php echo $patient_id; ?>">
            <div class="form-section">
                <div class="left-form">
                    <h2>Prélèvement : <?php echo htmlspecialchars($patient_data['name'] . ' ' . $patient_data['prenom']); ?></h2>
                    <div class="form-group">
                        <label for="typePrelevement">Type Prélèvement</label>
                        <select id="typePrelevement" name="type_prelevement" required>
                            <option value="Biopsie">Biopsie</option>
                            <option value="Cytologie">Cytologie</option>
                            <option value="Pièce opératoire">Pièce opératoire</option>
                            <option value="Immuno Histochimique">Immuno Histochimique</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dateReception">Date Réception</label>
                        <input type="date" id="dateReception" name="date_reception" required>
                    </div>
                    <div class="form-group">
                        <label for="dateReponse">Date Réponse</label>
                        <input type="date" id="dateReponse">
                    </div>
                    <div class="form-group">
                        <label for="nombreFlacon">Nombre de flacon</label>
                        <input type="number" id="nombreFlacon" name="nombre_flacons" required>
                    </div>
                    <div class="form-group">
                        <label for="pieceJointe">Ordonnance</label>
                        <input type="file" id="pieceJointe" name="ordonnance">
                    </div>
                    <div class="form-group">
                        <label for="medecin">Médecin</label>
                        <div class="dropdown-search">
                            <input type="text" id="search_docteur_exterieur" name="search_docteur_exterieur" placeholder="Search Docteur Exterieur">
                            <div class="dropdown-search-content docteur_exterieur">
                                <?php foreach ($docteurs_exterieurs as $docteur_exterieur): ?>
                                    <a href="#" data-id="<?php echo htmlspecialchars($docteur_exterieur['docteur_id']); ?>"><?php echo htmlspecialchars($docteur_exterieur['full_name']); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <input type="hidden" id="docteur_exterieur_id" name="docteur_exterieur_id">
                    </div>
                    <div class="form-group">
                        <label for="examen">Examen</label>
                        <div class="dropdown-search">
                            <input type="text" id="search_examen" placeholder="Search Examen">
                            <div class="dropdown-search-content examen">
                                <?php foreach ($examens as $examen): ?>
                                    <a href="#" data-id="<?php echo htmlspecialchars($examen['examen_id']); ?>" data-prix="<?php echo htmlspecialchars($examen['prix']); ?>"><?php echo htmlspecialchars($examen['sub_type']); ?></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <input type="hidden" id="examen_id" name="examen_id" required>
                    </div>
                </div>
                <div class="right-form">
                    <h2>Facturation</h2>
                    <div class="form-facture">
                        <label for="total_prix">Total Prix</label>
                        <input type="text" id="total_prix" readonly>
                    </div>
                    <div class="form-facture">
                        <label for="prix_reduit">Prix Réduit</label>
                        <input type="number" id="prix_reduit" name="prix_reduit" onchange="updateFacture()" required>
                    </div>
                    <div class="form-facture">
                        <label for="avance">Avance</label>
                        <input type="number" id="avance" name="avance" onchange="updateFacture()" required>
                    </div>
                    <div class="form-facture">
                        <label for="montant_du">Montant Du</label>
                        <input type="text" id="montant_du" name="montant_du" readonly>
                    </div>
                    <div class="form-facture">
                        <label for="rest">Reste</label>
                        <input type="text" id="rest" readonly>
                    </div>
                </div>
            </div>
            <div class="report-section">
                <h2>Rapport</h2>
                <div class="form-group">
                    <label for="rapportTemplate">Rapport Template</label>
                    <div class="dropdown-search">
                        <input type="text" id="search_template" placeholder="Search Template">
                        <div class="dropdown-search-content template">
                            <?php foreach ($templates as $template): ?>
                                <a href="#" data-id="<?php echo htmlspecialchars($template['template_id']); ?>"><?php echo htmlspecialchars($template['name']); ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <input type="hidden" id="rapport_template" name="rapport_template">
                </div>
                <textarea name="rapport_txt" id="rapport_txt"></textarea>
                <script>
                    CKEDITOR.replace('rapport_txt');
                </script>
                <div class="report-buttons">
                    <button type="submit" name="create_prelevement">Create</button>
                    <button type="button" onclick="saveTemplate()">Enregistrer un modèle</button>
                </div>
            </div>
        </form>
        <div class="history-section">
            <h2>Historique de Prélèvement</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>Date Réception</th>
                        <th>Date Création</th>
                        <th>N° de Flacons</th>
                        <th>Docteur Extérieur</th>
                        <th>État de paiement</th>
                        <th>Reste</th>
                        <th>Ordonnance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prelevements_history as $history):
                        $facture_data = $facture->readOne($history['prelevement_id']);
                        $docteur_exterieur_data = $docteurExterieur->readOne($history['docteur_exterieur_id']);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($history['prelevement_id']); ?></td>
                            <td><?php echo htmlspecialchars($history['type_prelevement']); ?></td>
                            <td><?php echo htmlspecialchars($history['date_reception']); ?></td>
                            <td><?php echo htmlspecialchars($history['date_creation']); ?></td>
                            <td><?php echo htmlspecialchars($history['nombre_flacons']); ?></td>
                            <td><?php echo htmlspecialchars($docteur_exterieur_data['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($facture_data['etat_paiement'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($facture_data['rest'] ?? 'N/A'); ?></td>
                            <td><?php echo $history['ordonnance'] ? '<a href="download_ordonance.php?id=' . $history['prelevement_id'] . '">Download</a>' : 'No Ordonnance'; ?></td>
                            <td class="action-buttons">
                                <a href="edit_prelevement.php?id=<?php echo $history['prelevement_id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="print_prelevement.php?id=<?php echo $history['prelevement_id']; ?>" title="Print"><i class="fas fa-print"></i></a>
                                <a href="javascript:confirmDelete(<?php echo $history['prelevement_id']; ?>, <?php echo $patient_id; ?>)" title="Delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
