<?php
if (!isset($_SESSION)) session_start();

if (isset($_SESSION['username'])) {
    echo '
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
            
        .top-bar {
            background-color: #343a40;
            color: white;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            position: sticky;
            top: 0;
            z-index: 1000;
            font-family: Arial, sans-serif;
        }

        .top-bar .username {
            font-weight: bold;
        }

        .top-bar a.logout {
            color: white;
            text-decoration: none;
            font-weight: bold;
            border: 1px solid white;
            padding: 5px 10px;
            border-radius: 4px;
            margin-left: 15px;
            transition: background-color 0.3s;
        }

        .top-bar a.logout:hover {
            background-color: white;
            color: #343a40;
        }
    </style>

    <div class="top-bar">
        <div class="username">
            ' . htmlspecialchars($_SESSION['username']) . ' 
            <a href="reservation_history.php" class="logout">History</a>
        </div>
        <div>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>';
}
?>
