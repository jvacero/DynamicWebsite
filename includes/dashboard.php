<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        div {
            width: 100;
            border-opacity:1;
            border: black;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


    <div>
        <table>
            <tr>
                PICTURE HERE
            </tr>
            <th>
            <p>Item Name<br>$1000</p>
            </th>
        </table>
    </div>

    <?php
        
        require_once 'config/mysqli_connect.php';
        while($row = $result->fetch_assoc()){
    ?>
            <div class="card">
                <img src="uploads/<?php echo htmlspecialchars($row['productimage']); ?> picture" alt="Product">
                <h3><?php echo htmlspecialchars($row['productname']);?></h3>
                <p> Price:
                    $<?php echo number_format($row['price'],2);?>
                </p>
                <p> Stock
                    <?php echo $row['stock'];?>
                </p>

                <?php if(isset($_SESSION['user_id'])){?>
                    <form action="cart/addtocart.php" method="POST">
                        <input 
                        type="hidden" 
                        name="product_id"
                        value="<?php echo $row['id']; ?>">
                        >
                        <button type="submit">
                            Add To Cart
                        </button>
                    </form>

                    <?php } else{ ?>
                        <a href="admin_login.php">
                        <button>Login to Buy</button></a>
                        <?php } ?>
                }
            </div>
        <?php } ?>
    ?>

    <hr>
    <a href="../admin/dashboard.php" class="button"></a>
    <form action="admin/dashboard.php"><button type="submit">Admin Dashboard</button></form>
    <form action="config/dbquery_localdb.php"><button type="submit">Local Database</button></form>
    <hr>


    





</body>
</html>