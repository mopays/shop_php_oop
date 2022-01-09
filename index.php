<?php 
    session_start();
    require_once('dbcontroller.php');
    $db_handel = new DBcontroller();

    if(!empty($_GET["action"])){
        switch($_GET["action"]){
            case "add":
                if(!empty($_POST["quantity"])){
                    $productByCode = $db_handel->runQuery("SELECT * FROM tblproduct WHERE code='". $_GET["code"]."'");
                    $itemArray = array($productByCode[0]["code"]=>(array('name' => $productByCode[0]["name"],
                    'code'=>$productByCode[0]["code"],
                    'quantity'=> $_POST["quantity"],
                    'price'=> $productByCode[0]["price"],
                    'image'=> $productByCode[0]["image"])));
                }
                if(!empty($_SESSION["cart_item"])){
                    if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))){
                        foreach($_SESSION["cart_item"] as $k => $v){
                            if($productByCode[0]["code"] == $k){
                                if(empty($_SESSION["cart_item"][$k]["quantity"])){
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart"][$k]["quantity"] += $_POST["quantity"];
                            }
                        }
                    }else{
                        $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                    }
                }else{
                    $_SESSION["cart_item"] = $itemArray;
                }
                break;
                case"remove":
                    if(!empty($_SESSION["cart_item"])){
                        foreach($_SESSION["cart_item"] as $k => $v){
                            if($_GET["code"] == $k)
                            unset($_SESSION["cart_item"][$k]);

                            if(empty($_SESSION["cart_item"]))
                            unset($_SESSION["cart_item"]);
                        }
                    }
                    break;
                    case "empty":
                        unset($_SESSION["cart_item"]);
                        break;
        }
 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <title>index</title>
</head>
<body>
    <div id="shopping-cart">
        <div class="text-heading">shopping-cart</div>
        <a href="index.php?action=empty" id="btnEmpty">Empty Cart</a>

        <?php if(isset($_SESSION["cart_item"])){
            $total_quantity = 0;
            $total_price = 0;
        
            ?>

        <table class="tbl-cart" cellpadding="10" cellspacting="1">
            <tbody>
                <tr>
                    <th style="text-align: left;">Name</th>
                    <th style="text-align: left;">code</th>
                    <th style="text-align: right;" width="5%">Quantity</th>
                    <th style="text-align: right;" width="10%">Unit Price</th>
                    <th style="text-align: right;" width="10%">Price</th>
                    <th style="text-align: center;"Width="10%">Remove</th>
                </tr>
                <?php
                    foreach($_SESSION["cart_item"] as $item){
                        $item_peice = $item["quantity"] * $item["price"];
                    
                ?>
                <tr>
                    <td><image sec="" class="<?php echo $item["image"];?>" alt="">
                        <?php echo $item["name"];?>
                </td>
                    <td><?php echo $item["code"];?></td>
                    <td style="text-align: right;"><?php echo $item["quantity"];?></td>
                    <td style="text-align: right;"><?php echo "$". $item["price"];?></td>
                    <td style="text-align: right;"><?php  echo "$".number_format($item["price"],2);?></td>
                    <td style="text-align: center;"><a href="index.php?action=remove&code=<?php echo $item["code"];?>" class="btnRemoveAction"><img src="assets/image/product/bin.png" alt="Remove Item"></td>
                </tr>

                        <?php
                            $total_quantity += $item["quantity"];
                            $total_price +=($item["price"]* $item["quantity"]);

                    }

                        ?>

                <tr>
                    <td colspan="2" align="right">Total:</td>
                    <td align="right"><?php echo $total_quantity;?></td>
                    <td align="right" colspan="2"><?php echo number_format($total_price,2);?></td>
                    <td ></td>
                </tr>
            </tbody>
        </table>
        <?php
        
         } else { 
        ?>

        <div class="no-records">Your cart is empty</div>

        <?php    
                }
        ?>
    </div>

    <div id="product-grid">
        <div class="text-heading">Products</div>
        <hr>
        <?php 
        $product_array = $db_handel->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
        if(!empty($product_array)){
            foreach($product_array as $key => $value){

            
    
        ?>
        <div class="product-item">
            <form action="index.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>" method="post">
                <div class="product-image">
                    <img src="<?php echo $product_array[$key]["image"];?>" alt="images">
                </div>
                <div class="product-title-footer">
                    <div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
                    <div class="product-perce"><?php echo "$ ". $product_array[$key]["price"]; ?></div>
                    <div class="cart-action">
                        <input type="text" class="product-quanity"name="quantity" value="1" size="2">
                        <input type="submit" value="add to cart" class="btnAddAction">
                    </div>
                </div>
            </form>
        </div>
        <?php
                }   
            }
        ?>
    </div>

</body>
</html>