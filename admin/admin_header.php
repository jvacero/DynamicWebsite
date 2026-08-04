<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../assets/admin_header.css" type="text/css" media="screen">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class="admin-header">
        <span class="logo-container">
            <img src="../uploads/ChatGPT Image Aug 5, 2026, 12_33_18 AM.png" alt="Admin logo" class="logo" height="50" width="50">
        </span>     
            <span class="nav-links">
               <a href="../index.php">Home</a>
               <a href="admin_delete.php">Delete</a>
               <button type="button" class="nav-btn" onclick="if (typeof openAdminUpdateModal === 'function') { openAdminUpdateModal(); }">Update Admin</button>
               <a href="admin_up_item.php">Upload Item</a>
            </span>
        <span>
            Buraot System Admin
        </span>
    </div>
    
</body>
</html>