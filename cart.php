<?php
session_start();
$product_ids = array(); // empty array
//session_destroy(); // session destroyed

// check if the add to cart button has been submitted
if(filter_input(INPUT_POST, 'add_to_cart')){ // this fun check if the add to cart variable has set
	if(isset($_SESSION['shopping_cart'])){ // check if the $_SESSION shopping cart exist

		// keep track how many products are in the shopping cart
		$count=count($_SESSION['shopping_cart']);

		//create sequantial array for matching array keys to product id's
		$product_ids = array_column($_SESSION['shopping_cart'], 'id');

		//print_r($product_ids);

		if(!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
			$_SESSION['shopping_cart'][$count] = array 
		(
			'id' => filter_input(INPUT_GET, 'id'),
			'name'=> filter_input(INPUT_POST, 'name'),
			'price' => filter_input(INPUT_POST, 'price'),
			'quantity' => filter_input(INPUT_POST, 'quantity')

		);
		}
		else{//product already exist increased quantity
			//match array key to id of the product being added to the cart
			for ($i=0; $i < count($product_ids); $i++) { 
				# code...
				if($product_ids[$i] == filter_input(INPUT_GET, 'id')){
					//add item quantity to the existing product in the array
					$_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
				}
			}
		}

	}
	else{ // if $_SESSION Shopping cart not exist, create first product with array key 0
			// create array using submitted form data, start from key 0 and fill it with values.
		$_SESSION['shopping_cart'][0] = array 
		// creating session and add all product infromation into our shopping cart session.
		(
			'id' => filter_input(INPUT_GET, 'id'),
			'name'=> filter_input(INPUT_POST, 'name'),
			'price' => filter_input(INPUT_POST, 'price'),
			'quantity' => filter_input(INPUT_POST, 'quantity')

		);
	}

}
 // Delete the Product Id:
if(filter_input(INPUT_GET, 'action') == 'delete'){ // if the action is performed
	//loop throug all the products in the shopping cart until it matches with GET Id variable
	foreach ($_SESSION['shopping_cart'] as $key => $product) {
		if ($product['id'] == filter_input(INPUT_GET, 'id')) {
			// remove product from shopping cart when it matched the GET Id
			unset($_SESSION['shopping_cart'][$key]);
			echo '<script>alert("Product Remove")</script>';
			echo '<script>window.location="cart.php"</script>';

		}
	}
	//reset session array keys so that they match with $product_ids numeric array
	//$_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}


 /* create a pre_r function which take $array. show all the data in nice formate.
pre_r($_SESSION);
function pre_r($array){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}*/
?>


<!DOCTYPE html>
<html>
<head>
	<title>Cart page</title>
	<link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
	<style type="text/css">
#button{
	margin-top: 10px;
}


.products{
	border:1px solid #333;
	background-color: #f1f1f1;
	border-radius: 5px;
	padding: 16px;
	margin-bottom: 20px;
}

.button{
	background-color: #6394F8;
	color: white;
	text-align: center;
	padding: 12px;
	text-decoration: none;
	display: block;
	border-radius: 3px;
	font-size: 16px;
	margin: 25px 0 15px 0;
}
	</style>
</head>
<body>

<div class="container">
	<?php
	require_once('database.php');
	$query = 'SELECT * FROM products order by id ASC';
	$result = mysqli_query($connect,$query);

	 if($result):
	 	 if(mysqli_num_rows($result)>0):
	 	 	while($product = mysqli_fetch_assoc($result)):
	 	 	?> 
	 	 	<div class="col-sm-4 col-md-3">
	 	 		<form method="post" action="cart.php?action=add&id=<?php echo $product['id']; ?>">
	 	 			<div class="products">
	 	 				<img width="100%" src="<?php echo $product['image']; ?>">
	 	 				<h4 class="text"><?php echo $product['name']; ?></h4>
	 	 				<h4 class="text-primary">$ <?php echo $product['price'] ?></h4>
	 	 				<input type="text" name="quantity" class="form-control" value="1"></input>
	 	 				<input type="hidden" name="name" value="<?php echo $product['name']; ?>"></input>
	 	 				<input type="hidden" name="price" value="<?php echo $product['price']; ?>"></input>
	 	 				<input type="submit" name="add_to_cart" class="btn btn-danger" value="Add to Cart" id="button"></input>
	 	 			</div>
 	 			</form>
 	 			<div style="clear:both"></div>
 	 		</div>
		<?php
 	 	endwhile;
 	endif;
 endif;
?>
		<div style="clear:both"></div>
		<br />
		<div class="table-responsive">
			<table class="table">
				<tr><th colspan="5"><h3>Order Details</h3></th></tr>
				<tr>
					<th width="40%">Product Name</th>
					<th width="10%">Quantity</th>
					<th width="20%">Price</th>
					<th width="15%">Total</th>
					<th width="5%">Action</th>
				</tr>
				<?php
					if(!empty($_SESSION['shopping_cart'])):

						$total  = 0;
					foreach ($_SESSION['shopping_cart'] as $key => $product):
				?>
				<tr>
					<td><?php echo $product['name']; ?></td>
					<td><?php echo $product['quantity']; ?></td>
					<td><?php echo $product['price']; ?></td>
					<td><?php echo number_format($product['quantity'] * $product['price'], 2); ?></td>
					<td>
						 <a href="cart.php?action=delete&id<?php echo $product['id']; ?>">
						<div class="btn btn-danger"> Remove</div>
						</a>
					</td>
				</tr>

			<?php
				$total = $total + ($product['quantity'] * $product['price']);
					endforeach;
					//endif;
			?>

			<tr>
				<td colspan="3" align="right">Total</td>
				<td align="right">$<?php echo number_format($total, 2); ?></td>
				<td></td>
			</tr>

			<tr>
				<!-- Show checkout button only if the shopping cart is not empty  -->
				<td colspan="5">
					<?php 
						if(isset($_SESSION['shopping_cart'])):
							if(count($_SESSION['shopping_cart']) > 0):
					 ?>
					<a href="#" class=" button btn btn-primary" >Checkout</a>

					<?php 
						endif; endif;
					 ?>
				</td>
			</tr>

			<?php  
				endif;
			?>
			</table>
		</div>
	</div>
</body>
</html>
