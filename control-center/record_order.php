<?phprequire_once '../php/linkDB.php';session_start();if(!isset($_SESSION["cid"])){	 echo "<script>window.location.href='./index.php';</script>";}?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head>	<meta charset="utf8">	<title>整合当天订单</title>	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css"/></head><body><?php	$check_cache_order = mysql_query("SELECT COUNT(ID) FROM cache_order");	$check_cache_sender = mysql_query("SELECT COUNT(ID) FROM cache_sender");	$check_result = mysql_result($check_cache_order, 0)+mysql_result($check_cache_sender, 0);//必须清空缓存才能整合		$shop_array = array();	$food_array = array();	if($check_result == 0){		$sql = mysql_query("SELECT * FROM temp_order ORDER BY ID");		while($row = mysql_fetch_array($sql)){			$t_id = $row["ID"];			$orders = $row["orders"];			$nums = $row["num"];			$user_id = $row["user_id"];			$address = $row["address"];			$note = $row["note"];			$aim_zone = $row["aim_zone"];			$order_time = $row["order_time"];			$state = $row["state"];			if(mysql_query("INSERT INTO orders (t_id,orders,num,user_id,note,address,aim_zone,order_time,state) VALUES ('$t_id','$orders','$nums','$user_id','$note','$address','$aim_zone','$order_time','$state')")){				$food_id = explode(";",$orders);					$nums = explode(";",$nums);				for($i = 0;$i < count($food_id)-1;$i++){					$sql_order = mysql_query("SELECT * FROM product WHERE ID=$food_id[$i]");					$product = mysql_fetch_array($sql_order);					$food_name = $product["name"];//菜名					$food_price = $product["price"];//价格					$shop_id = $product["shop_id"];											$sql_shop = mysql_query("SELECT * FROM restaurant WHERE ID='$shop_id'");													$shop = mysql_fetch_array($sql_shop);					$shop_name = $shop["name"];//商店名					$royalties = $shop["royalties"];//提成					$num = $nums[$i];//数量					for($j = 0;$j <= count($shop_array);$j++){						if($j == count($shop_array)){							$shop_array[$j] = $shop_name;							$food_array[$j][0] = array($food_name,$num,$food_price,$royalties);							break;						}else if($shop_name == $shop_array[$j]){							for($k = 0;$k <=count($food_array[$j]);$k++){								if($k == count($food_array[$j])){									$food_array[$j][$k] = array($food_name,$num,$food_price,$royalties);									break;								}else if($food_array[$j][$k][0] == $food_name){									$food_arrat[$j][$k][1] += $num;									break;								}							}							break;						}					}				}			}else{				echo 'error';			}		}		for($i = 0;$i<count($shop_array);$i++){			echo '<h2>'.$shop_array[$i].'</h2>';			echo '<table class="table table-bordered"><thead><td>菜名</td><td>数量</td><td>价格</td><td>总价</td><td>提成</td><td>应付</td><td>结余</td></thead><tbody>';			$sum1 = 0;			$sum2 = 0;			$sum3 = 0;						for($j = 0;$j<count($food_array[$i]);$j++){				$total = $food_array[$i][$j][1]*$food_array[$i][$j][2];//总价				$royal = $food_array[$i][$j][1]*$food_array[$i][$j][2]*(1-$food_array[$i][$j][3]);//提成				$rest = $food_array[$i][$j][1]*$food_array[$i][$j][2]*$food_array[$i][$j][3];//结余								echo '<tr><td>'.$food_array[$i][$j][0].'</td><td>'.$food_array[$i][$j][1].'</td><td>'.$food_array[$i][$j][2].'</td><td>'.$total.'</td><td>'.$food_array[$i][$j][3].'</td><td>'.$royal.'</td><td>'.$rest.'</td></tr>';				$sum1 += $total;				$sum2 += $royal;				$sum3 += $rest;			}			echo '<tr><td>总和</td><td></td><td></td><td>'.$sum1.'</td><td></td><td>'.$sum2.'</td><td>'.$sum3.'</td></tr>';			echo '</tbody></table>';		}		if(mysql_query("TRUNCATE TABLE temp_order")){			echo "已清空临时订单</br>";		}else{			echo "临时订单无法清空</br>";		}		if(mysql_query("TRUNCATE TABLE cache_order")){			echo "已重置配餐区缓存订单</br>";		}else{			echo "无法重置配餐区缓存订单</br>";		}		if(mysql_query("TRUNCATE TABLE cache_sender")){			echo "已重置送餐区缓存订单</br>";		}else{			echo "无法重置送餐区缓存订单</br>";		}		echo '<button onclick=\'window.location.href="./control_center.php"\' style="margin:30px;">返回区域选择</button>';	}else{		echo "必须清空缓存订单!请检查各个送餐区和配餐区的缓存订单";	}?></body></html>