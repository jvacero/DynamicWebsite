<?php
session_start();
require_once __DIR__ . '/../config/mysqli_connect.php';

// Developers documentation dataset
$developers = [
    [
        'name' => 'John Victor Acero',
        'role' => 'Lead Systems Architect & Lead Backend Engineer',
        'avatar' => '../devsphotos/1000010946.png',
        'doc' => 'Designed overall system architecture, user session management, and integrated the core PHP & MySQL database connection layer.'
    ],
    [
        'name' => 'Mark Denniel Urqueza',
        'role' => 'Backend Engineer',
        'avatar' => '../devsphotos/1000010943.png',
        'doc' => 'Engineered cart-to-order processing, automated order delivery expiration logic, and MySQL cascade database constraints.'
    ],
    [
        'name' => 'Richsander Orduña',
        'role' => 'UI / UX Designer',
        'avatar' => '../devsphotos/1000010945.png',
        'doc' => 'Crafted the custom 8-bit retro arcade design language, pixel typography, responsive card containers, and DataTables styling.'
    ],
    [
        'name' => 'Andrei Mulato',
        'role' => 'UI / UX Designer',
        'avatar' => '../devsphotos/1000010942.png',
        'doc' => 'Crafted the custom 8-bit retro arcade design language, pixel typography, responsive card containers, and DataTables styling.'
    ],
    [
        'name' => 'Jeffrey Reyes',
        'role' => 'Wanted: nag-tour',
        'avatar' => '../devsphotos/1000010944.png',
        'doc' => 'Implemented input sanitization (XSS prevention), activity tracking logging, and cross-browser testing for retro components.'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Credits & Documentation - Buraot System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Press Start 2P', 'Courier New', monospace;
            image-rendering: pixelated;
            image-rendering: crisp-edges;
        }

        body {
            margin: 0;
            background-color: #2a2a3e;
            background-image: 
                linear-gradient(45deg, #1e1e2c 25%, transparent 25%), 
                linear-gradient(-45deg, #1e1e2c 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #1e1e2c 75%), 
                linear-gradient(-45deg, transparent 75%, #1e1e2c 75%);
            background-size: 20px 20px;
            background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            color: #000000;
            min-height: 100vh;
            padding: 40px 1rem 80px;
        }

        .credits-container {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            border: 5px solid #000000;
            box-shadow: 8px 8px 0 #000000;
            padding: 2rem;
        }

        /* Top Header Area */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 2rem;
            border-bottom: 4px dashed #000000;
            padding-bottom: 1.5rem;
        }

        .page-title {
            margin: 0;
            font-size: 1.1rem;
            line-height: 1.4;
            color: #000000;
            background: #ffcc00;
            padding: 10px 15px;
            border: 4px solid #000000;
            box-shadow: 4px 4px 0 #000000;
            text-transform: uppercase;
        }

        .btn-dash {
            display: inline-block;
            padding: 10px 16px;
            background: #00b0ff;
            color: #000000;
            text-decoration: none;
            border: 3px solid #000000;
            box-shadow: 4px 4px 0 #000000;
            font-size: 0.65rem;
            text-transform: uppercase;
            font-weight: bold;
        }

        .btn-dash:hover {
            background: #00e676;
        }

        .btn-dash:active {
            transform: translate(2px, 2px);
            box-shadow: 2px 2px 0 #000000;
        }

        .system-desc {
            font-size: 0.65rem;
            line-height: 1.8;
            margin-bottom: 2rem;
            background: #fff3cd;
            border: 3px solid #000000;
            padding: 15px;
            box-shadow: 4px 4px 0 #000000;
        }

        /* Developers Grid */
        .dev-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 24px;
        }

        /* Individual Developer Card */
        .dev-card {
            background: #ffffff;
            border: 4px solid #000000;
            box-shadow: 6px 6px 0 #000000;
            padding: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Pixel Picture Frame */
        .pixel-frame {
            width: 130px;
            height: 130px;
            background: #2a2a3e;
            border: 4px solid #000000;
            outline: 4px solid #ff7b00;
            box-shadow: 4px 4px 0 #000000;
            margin-bottom: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pixel-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Name Plate */
        .name-plate {
            width: 100%;
            background: #ffcc00;
            border: 3px solid #000000;
            box-shadow: 3px 3px 0 #000000;
            padding: 8px 4px;
            margin-bottom: 12px;
            text-align: center;
        }

        .name-plate .dev-name {
            font-size: 0.65rem;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin: 0;
        }

        .name-plate .dev-role {
            font-size: 0.5rem;
            color: #333333;
            margin-top: 5px;
            display: block;
        }

        /* About & Documentation Box */
        .doc-container {
            background: #fff3cd;
            border: 3px dashed #000000;
            padding: 10px;
            width: 100%;
            text-align: left;
            flex-grow: 1;
        }

        .doc-container h4 {
            font-size: 0.55rem;
            margin: 0 0 6px 0;
            text-transform: uppercase;
            color: #000000;
            text-decoration: underline;
        }

        .doc-container p {
            font-size: 0.55rem;
            line-height: 1.6;
            margin: 0;
            color: #000000;
        }
    </style>
</head>
<body>

    <main class="credits-container">
        <div class="page-header">
            <h1 class="page-title">Developer Roster</h1>
            <a href="admin_dashboard.php" class="btn-dash">&laquo; Back to Dashboard</a>
        </div>

        <div class="system-desc">
            <strong>PROJECT DOCUMENTATION & CREDITS:</strong><br>
            Commemorating the core developers behind the Buraot System application architecture, user management workflows, database integration, and retro pixel design interface.
        </div>

        <div class="dev-grid">
            <?php foreach ($developers as $dev): ?>
                <div class="dev-card">
                    <div class="pixel-frame">
                        <img src="<?php echo htmlspecialchars($dev['avatar']); ?>" alt="<?php echo htmlspecialchars($dev['name']); ?>">
                    </div>
                    <div class="name-plate">
                        <p class="dev-name"><?php echo htmlspecialchars($dev['name']); ?></p>
                        <span class="dev-role"><?php echo htmlspecialchars($dev['role']); ?></span>
                    </div>
                    <div class="doc-container">
                        <h4>Documentation</h4>
                        <p><?php echo htmlspecialchars($dev['doc']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

</body>
</html>