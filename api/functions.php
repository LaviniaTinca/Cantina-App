<?php

function unique_id()
{
    $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charLength = strlen($chars);
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $randomString .= $chars[mt_rand(0, $charLength - 1)];
    }
    return $randomString;
}

function check_login($conn)
{
    if (isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$email]);

        if ($select_user->rowCount() > 0) {
            $user_data = $select_user->fetch(PDO::FETCH_ASSOC);
            return $user_data;
        }
    }
    //redirect to login
    header("Location: login.php");
    die;
}
//function for user_type select
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_set_menu($conn, $user_data)
{
    // is set the daily menu?
    $query = "SELECT * FROM `daily_menu` WHERE `date` = CURDATE()";
    $stmt = $conn->prepare($query);

    if ($stmt->execute()) {
        $daily_menu = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$daily_menu) {
            $query_clear_cart = "DELETE FROM cart WHERE user_id = ?";
            $stmt_clear_cart = $conn->prepare($query_clear_cart);

            if ($stmt_clear_cart->execute([$user_data['id']])) {
            } else {
                $error_info = $stmt_clear_cart->errorInfo();
                $error_msg[] = "Error: " . $error_info[2];
            }
        } else {
            $query_products = "SELECT p.id FROM products AS p
                        JOIN daily_menu_items AS dmi ON dmi.product_id = p.id 
                        JOIN daily_menu AS dm ON dmi.daily_menu_id = dm.id
                        WHERE dm.date = CURDATE()";
            $stmt_products = $conn->prepare($query_products);
            $stmt_products->execute();
            $daily_products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
            if (count($daily_products) > 0) {
                $query_clear_cart = "DELETE FROM cart WHERE user_id = ? AND product_id != ?";
                $stmt_clear_cart = $conn->prepare($query_clear_cart);

                foreach ($daily_products as $product) {
                    if ($stmt_clear_cart->execute([$user_data['id'], $product['id']])) {
                    } else {
                        $error_info = $stmt_clear_cart->errorInfo();
                        $error_msg[] = "Error: " . $error_info[2];
                    }
                }
            }
        }
    } else {
        $error_info = $stmt->errorInfo();
        $error_msg = 'Error: ' . $error_info[2];
    }
}


function widget_query($conn, $table)
{
    try {
        $stmt = $conn->prepare("SELECT * FROM $table");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $num_of = count($result);
    } catch (PDOException $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    } catch (Exception $e) {
        $error_msg[] = "Eroare: " . $e->getMessage();
    }
    return $num_of;
}

function check_product_for_delete($conn, $delete_id)
{
    $query = "SELECT product_id FROM daily_menu_items WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$delete_id]);
    $product_id_to_delete = $stmt->fetchColumn();

    $query_check_cart = "SELECT cart.id FROM cart WHERE product_id = ?";
    $stmt_check_cart = $conn->prepare($query_check_cart);
    $stmt_check_cart->execute([$product_id_to_delete]);

    $query_check_order = "SELECT oi.id FROM order_items oi
                      INNER JOIN orders o ON oi.order_id = o.id
                      WHERE oi.product_id = ? AND o.order_date = CURDATE()";

    $stmt_check_order = $conn->prepare($query_check_order);
    $stmt_check_order->execute([$product_id_to_delete]);

    if ($stmt_check_cart->rowCount() > 0 || $stmt_check_order->rowCount() > 0) {
        return false;
    } else {
        return true;
    }
}
