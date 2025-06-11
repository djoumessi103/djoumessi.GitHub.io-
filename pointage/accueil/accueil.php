<?php
session_start();

// 1. Authentication Check
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Veuillez vous connecter.';
    header('Location: index.php');
    exit;
}

// 2. Role Check (Assuming you have a 'role' column in your users table)
if (isset($_SESSION['user_role'])) {
    $user_role = $_SESSION['user_role'];
    // You can implement role-based access control here if needed.
} else {
    echo "Erreur: Rôle utilisateur non trouvé dans la session!";
    // Log the error for debugging
    error_log("Missing user role for user ID: " . $_SESSION['user_id']);
    // Optionally redirect to an error page
    // header("Location: error.php");
    exit;
}

// 3. Display Errors
if (isset($_SESSION['error'])) {
    echo '<script>alert("' . $_SESSION['error'] . '");</script>';
    unset($_SESSION['error']);
}

// Database Connection Configuration
$host = 'localhost';
$dbname = 'bdpointage';
$username = 'root';
$password = '';
$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Fetch Total Employees
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM employe");
    $totalEmployees = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalEmployees = 'Erreur';
    error_log("Erreur lors de la récupération du nombre total d'employés : " . $e->getMessage());
}

// Fetch Total Pointages
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pointage");
    $totalPointages = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalPointages = 'Erreur';
    error_log("Erreur lors de la récupération du nombre total de pointages : " . $e->getMessage());
}

// Fetch Total Absences (Assuming you have an 'absence' table)
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM absence");
    $totalAbsences = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalAbsences = 'Erreur';
    error_log("Erreur lors de la récupération du nombre total d'absences : " . $e->getMessage());
}

// Fetch Total Justificatifs (Added this block to define totalJustificatifs)
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM justificatif"); // Assuming 'justificatif' is your table name
    $totalJustificatifs = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalJustificatifs = 'Erreur';
    error_log("Erreur lors de la récupération du nombre total de justificatifs : " . $e->getMessage());
}

// Assuming you have a way to track received emails in your database
// Here's a placeholder, you'll need to adjust this based on your actual email tracking mechanism
$totalEmailsReceived = 'Non implémenté';
// Example if you had an 'emails' table:
/*
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM emails WHERE reception_date >= CURDATE() - INTERVAL 7 DAY"); // Example: emails received in the last 7 days
    $totalEmailsReceived = $stmt->fetchColumn();
} catch (PDOException $e) {
    $totalEmailsReceived = 'Erreur';
    error_log("Erreur lors de la récupération du nombre total d'e-mails reçus : " . $e->getMessage());
}
*/

// Fetch Employee Data
$employes = []; // Initialize to avoid errors if the query fails
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM employe");
        $stmt->execute();
        $employes = $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des employés : " . $e->getMessage();
        // You might choose not to terminate execution here and display an error message in the table.
        $employes = []; // Ensure $employes is always an array.
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>Tableau de Bord Moderne</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>

<body>
    <input type="checkbox" id="menu-toggle">
    <div class="sidebar">
        <div class="side-header">
            <h3>Tech<span>vision</span></h3>
        </div>

        <div class="side-content">
            <div class="profile">
                <div class="profile-img bg-img" style="background-image: url(img.png)"></div>
                <h4><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur'; ?></h4>
                <small><?php echo isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'Rôle inconnu'; ?></small>
            </div>
            <div class="side-menu">
                <ul>
                    <li>
                        <a href="accueil.php" class="active">
                            <span class="las la-home"></span>
                            <small>Home</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/employe/employe.php" class="btn ">
                            <span class="las la-users"></span>
                            <small>Employés</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/horaire/horaire.php">
                            <span class="las la-calendar"></span>
                            <small>Horaire</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/justificatif/justificatif.php">
                            <span class="las la-file-alt"></span>
                            <small>Justificatifs</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/log/log.php">
                            <span class="las la-history"></span>
                            <small>Logs</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/absence/absence.php">
                            <span class="las la-user-slash"></span>
                            <small>Absences</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/parametre/parametre.php">
                            <span class="las la-cog"></span>
                            <small>Parametres</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/pointage/pointage.php">
                            <span class="las la-fingerprint"></span>
                            <small>Pointages</small>
                        </a>
                    </li>
                    <li>
                        <a href="http://localhost/pointage/utilisateur/utilisateur.php">
                            <span class="las la-user-cog"></span>
                            <small>Utilisateurs</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="main-content">

        <header>
    <div class="header-content">
        <label for="menu-toggle">
            <span class="las la-bars"></span>
        </label>

        <div class="header-left-items">
            <div class="search-box">
                <input type="text" id="employeeSearch" placeholder="Rechercher un employé...">
                <span class="las la-search"></span>
            </div>
        </div>

        <div class="header-right-items">
            <div class="notify-icon">
                <a href="https://mail.google.com/mail/u/0/#inbox">
                    <span class="las la-envelope"></span>
                    <span class="notify">2</span>
                </a>
            </div>

            <div class="notify-icon">
                <a href="https://mail.google.com/mail/u/0/#inbox">
                    <span class="las la-bell"></span>
                    <span class="notify">4</span>
                </a>
            </div>

            <div class="user">
                <div class="bg-img" style="background-image: url(img.png)"></div>
                <a href="deconnexion.php">
                    <span class="las la-power-off"></span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>
    </div>
</header>

        <main>

            <div class="page-header">
                <h1>Tableau de Bord</h1>
                <small>Accueil / Tableau de Bord</small>
            </div>

            <div class="page-content">

                <div class="analytics">

                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $totalEmployees; ?></h2>
                            <span class="las la-user-friends"></span>
                        </div>
                        <div class="card-progress">
                            <small>Nombre total d'employés</small>
                            <div class="card-indicator">
                                <div class="indicator one" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $totalPointages; ?></h2>
                            <span class="las la-clock"></span>
                        </div>
                        <div class="card-progress">
                            <small>Nombre total de pointages</small>
                            <div class="card-indicator">
                                <div class="indicator two" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $totalAbsences; ?></h2>
                            <span class="las la-user-slash"></span>
                        </div>
                        <div class="card-progress">
                            <small>Nombre total d'absences</small>
                            <div class="card-indicator">
                                <div class="indicator three" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2><?php echo $totalJustificatifs; ?></h2>
                            <span class="las la-file-alt"></span>
                        </div>
                        <div class="card-progress">
                            <small>Nombre total de justification reçus</small>
                            <div class="card-indicator">
                                <div class="indicator four" style="width: 90%"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <div>
                    <center>
                        <h2>Liste des employés</h2>
                    </center>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Matricule</th>
                                <th>Fonction</th>
                                <th>Departement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($employes)) :
                                $i = 0;
                                foreach ($employes as $employe) :
                                    $i++;
                            ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $employe->nom ?></td>
                                        <td><?= $employe->prenom ?></td>
                                        <td><?= $employe->matricule ?></td>
                                        <td><?= $employe->fonction ?></td>
                                        <td><?= $employe->departement ?></td>
                                        </td>
                                    </tr>
                                <?php
                                endforeach;
                            else :
                                ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucun employé trouvé.</td>
                                </tr>
                            <?php
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>


    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather+Sans:wght@300;400;500;600&display=swap');

        :root {
            --main-color: #22BAA0;
            --color-dark: #34425A;
            --text-grey: #B0B0B0;
        }

        * {
            margin: 0;
            padding: 0;
            text-decoration: none;
            list-style-type: none;
            box-sizing: border-box;
            font-family: 'Merriweather', sans-serif;
        }

        #menu-toggle {
            display: none;
        }

        .sidebar {
            position: fixed;
            height: 100%;
            width: 165px;
            left: 0;
            bottom: 0;
            top: 0;
            z-index: 100;
            background: var(--color-dark);
            transition: left 300ms;
        }

        .side-header {
            box-shadow: 0px 5px 5px -5px rgb(0 0 0 /10%);
            background: var(--main-color);
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .side-header h3,
        side-head span {
            color: #fff;
            font-weight: 400;
        }

        .side-content {
            height: calc(100vh - 60px);
            overflow: auto;
        }

        /* width */
        .side-content::-webkit-scrollbar {
            width: 5px;
        }

        /* Track */
        .side-content::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
            border-radius: 10px;
        }

        /* Handle */
        .side-content::-webkit-scrollbar-thumb {
            background: #b0b0b0;
            border-radius: 10px;
        }

        /* Handle on hover */
        .side-content::-webkit-scrollbar-thumb:hover {
            background: #b30000;
        }

        .profile {
            text-align: center;
            padding: 2rem 0rem;
        }

        .bg-img {
            background-repeat: no-repeat;
            background-size: cover;
            border-radius: 50%;
            background-size: cover;
        }

        .profile-img {
            height: 80px;
            width: 80px;
            display: inline-block;
            margin: 0 auto .5rem auto;
            border: 3px solid #899DC1;
        }

        .profile h4 {
            color: #fff;
            font-weight: 500;
        }

        .profile small {
            color: #899DC1;
            font-weight: 600;
        }

        .sidebar {
            /*overflow-y: auto;*/
        }

        .side-menu ul {
            text-align: center;
        }

        .side-menu a {
            display: block;
            padding: 1.2rem 0rem;
        }

        .side-menu a.active {
            background: #2B384E;
        }

        .side-menu a.active span,
        .side-menu a.active small {
            color: #fff;
        }

        .side-menu a span {
            display: block;
            text-align: center;
            font-size: 1.7rem;
        }

        .side-menu a span,
        .side-menu a small {
            color: #899DC1;
        }

        #menu-toggle:checked~.sidebar {
            width: 60px;
        }

        #menu-toggle:checked~.sidebar .side-header span {
            display: none;
        }

        /* CSS for when menu-toggle is checked and main-content is not a sibling */
        #menu-toggle:checked~.sidebar+.main-content {
            margin-left: 60px;
            width: calc(100% - 60px);
        }

        #menu-toggle:checked~.sidebar+.main-content header {
            left: 60px;
        }

        #menu-toggle:checked~.sidebar .profile,
        #menu-toggle:checked~.sidebar .side-menu a small {
            display: none;
        }

        #menu-toggle:checked~.sidebar .side-menu a span {
            font-size: 1.3rem;
        }


        .main-content {
            margin-left: 165px;
            width: calc(100% - 165px);
            transition: margin-left 300ms;
        }

        header {
            position: fixed;
            right: 0;
            top: 0;
            left: 165px;
            z-index: 100;
            height: 60px;
            box-shadow: 0px 5px 5px -5px rgb(0 0 0 /10%);
            background: #fff;
            transition: left 300ms;
        }
.header-content {
    display: flex;
    justify-content: space-between; /* This is good for pushing items to the ends */
    align-items: center;
    padding: 0rem 1rem;
    height: 60px;
    width: 100%; /* Ensure it takes full width of its parent */
}

.header-left-items {
    display: flex;
    align-items: center;
    /* If you add more items here, you might want a gap, e.g., gap: 1rem; */
}

.header-right-items {
    display: flex;
    align-items: center;
    gap: 1rem; /* Consistent spacing between notification icons and user info */
}

/* Optional: Refine search box alignment if needed */
.search-box {
    display: flex;
    align-items: center;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 5px 10px;
    background-color: #f9f9f9;
}

.search-box input {
    border: none;
    outline: none;
    padding: 5px;
    font-size: 0.9rem;
    flex-grow: 1; /* Allow input to take available space */
    min-width: 150px; /* Ensure a minimum width for the input */
}

.search-box .las {
    color: #888;
    margin-left: 5px;
}
  .notify-icon {
    position: relative; /* Essential for positioning the 'notify' span */
    display: inline-block; /* Or block, depending on desired flow */
    margin-right: 1rem; /* Adjust as needed for spacing between icons */
}

.notify-icon a {
    position: relative; /* If the 'a' tag wraps both, it needs to be relative */
    display: flex; /* Helps align icon and number if they are direct children */
    align-items: center; /* Vertically center them */
}

/* Style for the notification count */
.notify-icon .notify {
    position: absolute; /* Position relative to the parent with position: relative; */
    top: -5px; /* Adjust as needed to move it above the icon */
    right: -10px; /* Adjust as needed to move it to the right of the icon */
    background-color: #ff0000; /* Red background for visibility */
    color: white; /* White text color */
    border-radius: 50%; /* Makes it circular */
    padding: 2px 6px; /* Adjust padding to control size */
    font-size: 0.7rem; /* Smaller font size for the number */
    min-width: 20px; /* Ensures a minimum size for single digits */
    text-align: center; /* Center the text */
    line-height: 1; /* Adjust line height for vertical centering */
    box-shadow: 0 1px 3px rgba(0,0,0,0.2); /* Subtle shadow for depth */
    z-index: 10; /* Ensure it appears above other elements */
}      
        .user {
            display: flex;
            align-items: center;
        }

        .user div,
        .client-img {
            height: 40px;
            width: 40px;
            margin-right: 1rem;
        }

        .user span:last-child {
            display: inline-block;
            margin-left: .3rem;
            font-size: .8rem;
        }

        main {
            margin-top: 60px;
        }

        .page-header {
            padding: 1.3rem 1rem;
            background: #E9edf2;
            border-bottom: 1px solid #dee2e8;
        }

        .page-header h1,
        .page-header small {
            color: #74767d;
        }

        .page-content {
            padding: 1.3rem 1rem;
            background: #f1f4f9;
        }

        .analytics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-gap: 2rem;
            margin-top: .5rem;
            margin-bottom: 2rem;
        }

        .card {
            box-shadow: 0px 5px 5px -5px rgb(0 0 0 / 10%);
            background: #fff;
            padding: 1rem;
            border-radius: 3px;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-head h2 {
            color: #333;
            font-size: 1.8rem;
            font-weight: 500;
        }

        .card-head span {
            font-size: 3.2rem;
            color: var(--text-grey);
        }

        .card-progress small {
            color: #777;
            font-size: .8rem;
            font-weight: 600;
        }

        .card-indicator {
            margin: .7rem 0rem;
            height: 10px;
            border-radius: 4px;
            background: #e9edf2;
            overflow: hidden;
        }

        .indicator {
            height: 10px;
            border-radius: 4px;
        }

        .indicator.one {
            background: #22baa0;
        }

        .indicator.two {
            background: #11a8c3;
        }

        .indicator.three {
            background: #f6d433;
        }

        .indicator.four {
            background: #f25656;
        }

        .records {
            box-shadow: 0px 5px 5px -5px rgb(0 0 0 / 10%);
            background: #fff;
            border-radius: 3px;
        }

        .record-header {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .add,
        .browse {
            display: flex;
            align-items: center;
        }

        .add span {
            display: inline-block;
            margin-right: .6rem;
            font-size: .9rem;
            color: #666;
        }

        input,
        button,
        select {
            outline: none;
        }

        .add select,
        .browse input,
        .browse select {
            height: 35px;
            border: 1px solid #b0b0b0;
            border-radius: 3px;
            display: inline-block;
            width: 75px;
            padding: 0rem .5rem;
            margin-right: .8rem;
            color: #666;
        }

        .add button {
            background: var(--main-color);
            color: #fff;
            height: 37px;
            border-radius: 4px;
            padding: 0rem 1rem;
            border: none;
            font-weight: 600;
        }

        .browse input {
            width: 150px;
        }

        .browse select {
            width: 100px;
        }

        .table-responsive {
            width: 100%;
            overflow: auto;
        }

        table {
            border-collapse: collapse;
        }

        table thead tr {
            background: #e9edf2;
        }

        table thead th {
            padding: 1rem 0rem;
            text-align: left;
            color: #444;
            font-size: .9rem;
        }

        table thead th:first-child {
            padding-left: 1rem;
        }

        table tbody td {
            padding: 1rem 0rem;
            color: #444;
        }

        table tbody td:first-child {
            padding-left: 1rem;
            color: var(--main-color);
            font-weight: 600;
            font-size: .9rem;
        }

        table tbody tr {
            border-bottom: 1px solid #dee2e8;
        }

        .client {
            display: flex;
            align-items: center;
        }

        .client-img {
            margin-right: .5rem;
            border: 2px solid #b0b0b0;
            height: 45px;
            width: 45px;
        }

        .client-info h4 {
            color: #555;
            font-size: .95rem;
        }

        .client-info small {
            color: #777;
        }

        .actions span {
            display: inline-block;
            font-size: 1.5rem;
            margin-right: .5rem;
        }

        .paid {
            display: inline-block;
            text-align: center;
            font-weight: 600;
            color: var(--main-color);
            background: #e5f8ed;
            padding: .5rem 1rem;
            border-radius: 20px;
            font-size: .8rem;
        }

        @media only screen and (max-width: 1200px) {
            .analytics {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media only screen and (max-width: 768px) {
            .analytics {
                grid-template-columns: 100%;
            }

            .sidebar {
                left: -165px;
                z-index: 90;
            }

            header {
                left: 0;
                width: 100%;
            }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            #menu-toggle:checked~.sidebar {
                left: 0;
            }

            #menu-toggle:checked~.sidebar {
                width: 165px;
            }

            #menu-toggle:checked~.sidebar .side-header span {
                display: inline-block;
            }

            #menu-toggle:checked~.sidebar .profile,
            #menu-toggle:checked~.sidebar .side-menu a small {
                display: block;
            }

            #menu-toggle:checked~.sidebar .side-menu a span {
                font-size: 1.7rem;
            }

            /* Adjusted CSS for when menu-toggle is checked and main-content is not a sibling */
            #menu-toggle:checked~.sidebar+.main-content header {
                left: 0px;
            }

            table {
                width: 900px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('employeeSearch');
            const employeeTableBody = document.querySelector('table tbody');
            const employeeRows = employeeTableBody.querySelectorAll('tr');

            searchInput.addEventListener('keyup', function(event) {
                const searchTerm = event.target.value.toLowerCase();

                employeeRows.forEach(row => {
                    const employeeName = row.children[1].textContent.toLowerCase(); // Assuming 'Nom' is the second column (index 1)
                    const employeeFirstName = row.children[2].textContent.toLowerCase(); // Assuming 'Prénom' is the third column (index 2)
                    const employeeMatricule = row.children[3].textContent.toLowerCase(); // Assuming 'Matricule' is the fourth column (index 3)
                    const employeeFunction = row.children[4].textContent.toLowerCase(); // Assuming 'Fonction' is the fifth column (index 4)
                    const employeeDepartment = row.children[5].textContent.toLowerCase(); // Assuming 'Departement' is the sixth column (index 5)

                    if (employeeName.includes(searchTerm) ||
                        employeeFirstName.includes(searchTerm) ||
                        employeeMatricule.includes(searchTerm) ||
                        employeeFunction.includes(searchTerm) ||
                        employeeDepartment.includes(searchTerm)) {
                        row.style.display = ''; // Show the row
                    } else {
                        row.style.display = 'none'; // Hide the row
                    }
                });
            });
        });
    </script>
</body>

</html>